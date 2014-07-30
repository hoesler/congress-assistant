LectureHallClient = {
	Config: {
		base_url: '',
		log_level: 4, // 0 => Off, 1 => ERROR, 2 => WARN, 3 => INFO, 4 => DEBUG/LOG
		slideDuration: 20000, // in ms
		windowTitlePrefix: "LectureHallClient",
		version: "v20130222/1600"
	},
	Soundmanager: {
		music: ['/assets/audio/Los_Sundayers_-_03_-_Qu_Paciencia.mp3'],
		discussion: '/assets/audio/discussion.mp3',
		oneMinute: '/assets/audio/one_minute.mp3',
		gong: '/assets/audio/gong.mp3'
	},
	Logger: {
		log: function (message) { window.console && console.log && LectureHallClient.Config.log_level >= 4 && console.log(message); },
		debug: function (message) { window.console && console.debug && LectureHallClient.Config.log_level >= 4  && console.debug(message); },
		info: function (message) { window.console && console.info && LectureHallClient.Config.log_level >= 3  && console.info(message); },
		warn: function (message) { window.console && console.warn && LectureHallClient.Config.log_level >= 2  && console.warn(message); },
		error: function (message) { window.console && console.error && LectureHallClient.Config.log_level >= 1  && console.error(message); }
	},
	Helper: {
		millies2length : function (millies) {
			return millies / 6000;
		},
		logToServer: function (message) {
			$.post(LectureHallClient.Config.base_url + 'lhc/log', { message: message});
		}
	}
};


LectureHallClient.ClockModel = Backbone.Model.extend({

	defaults: {
		time: new Date().getTime(),
		serverOffset: 0,
		simulatedOffset: 0,
		syncStatus: undefined
	},
	
	serverOffsetAccumulated: 0,
	serverSyncCount: 0,

	initialize: function () {
		_.bindAll(this, "tick", "getTime", 'syncToLocal', 'syncToServer', 'setSimulatedTime', 'format', 'checkDateSwitch', 'setBaseTime', 'setServerOffset');

		setInterval(this.tick, 1000);
	},

	getTime: function () {
		return this.get('time') + this.get('serverOffset') + this.get('simulatedOffset');
	},

	setBaseTime: function (time) {
		var old = this.getTime();
		this.set({time: time});
		this.checkDateSwitch(old, this.getTime());
	},

	setServerOffset: function (offset) {
		var old = this.getTime();
		this.set({serverOffset: offset});
		this.checkDateSwitch(old, this.getTime());
	},

	checkDateSwitch: function (oldTime, newTime) {
		var oldDate = new Date(oldTime),
		newDate = new Date(newTime);
		if (oldDate.getYear() !== newDate.getYear() ||
			oldDate.getMonth() !== newDate.getMonth() ||
			oldDate.getDay() !== newDate.getDay()) {
			this.trigger('date');
	}
},

tick: function () {
	this.setBaseTime(this.get('time') + 1000);
},

syncToServer: function () {
	$.ajax({
		url: LectureHallClient.Config.base_url + 'lhc/time',
		dataType: 'json',
		context: this,
		success: function (data, textStatus, jqXHR) {
			var offset = parseInt(data.time - this.get('time'));

			this.serverOffsetAccumulated += offset;
			this.serverSyncCount += 1;

			this.setServerOffset(Math.floor(this.serverOffsetAccumulated / this.serverSyncCount));
			this.set({syncStatus: "success"});
		},
		error: function (jqXHR, textStatus, errorThrown) {
			LectureHallClient.Logger.warn('Sync failed: '+ textStatus);
			this.set({syncStatus: "error"});
		}
	});
},

syncToLocal: function () {
	this.setBaseTime(new Date().getTime());
	this.setServerOffset(0);
},

    /*
     * date: a Date object or false
     */
     setSimulatedTime: function (date) {
     	var old = this.getTime();
     	offset = (date) ? parseInt(date.getTime() - this.get('time')) : 0;
     	this.set({simulatedOffset: offset});
     	this.checkDateSwitch(old, this.getTime());
     },

     format: function (expression) {
     	return new Date(this.getTime()).format(expression);
     }

 });

LectureHallClient.EventModel = Backbone.Model.extend({

	initialize: function () {
		_.bindAll(this, "isActive", "endDate", "startDate", 'getDuration', 'endDateWithDiscussion', 'getDiscussionDuration', 'getTalkDuration', 'getTotalDuration');
	},

	startDate: function () {
		return Date.fromMySQL(this.get('startTime'));
	},

	/*
	 * returns the Date object which holds the time of the end date for this talk without discussion time
	 */
	 endDate: function () {
	 	return Date.fromMySQL(this.get('endTime'));
	 },

    /*
     * returns the duration of this talk in ms without discussion time
     */
     getDuration: function () {    
     	return this.endDate().getTime() - this.startDate().getTime();
     },

     getTalkDuration: function () {
     	return this.getDuration();
     },

     getTotalDuration: function () {
     	return this.getTalkDuration() + this.getDiscussionDuration();
     },

     getDiscussionDuration: function () {
     	return this.endDateWithDiscussion().getTime() - this.endDate().getTime();
     },

     endDateWithDiscussion: function () {
     	return new Date(this.endDate().getTime() + 2 * 60 * 1000);
     },

	/*
	 * Checks whether the given timestamp is between startDate and endDate + discussionDuration
	 */
	 isActive: function (timestamp) {
	 	var startTime = this.startDate().getTime(),
	 	endDate = this.endDateWithDiscussion().getTime();
	 	return timestamp >= startTime && timestamp <= endDate;
	 },

	 isInTalkWindow: function (timestamp) {
	 	var startTime = this.startDate().getTime(),
	 	endDate = this.endDate().getTime();
	 	return timestamp >= startTime && timestamp <= endDate;
	 }
	});

LectureHallClient.EventModelList = Backbone.Collection.extend({

	model: LectureHallClient.EventModel,

	activeModel: undefined,

	nextModel: undefined,

	room: undefined,

	initialize: function (models, options) {
		_.bindAll(this, "setRoom", "getActive", "update", "getMilliesTillNext", "getNext", "fetch");

		this.clockModel = options.clockModel;
		this.clockModel.bind('change', this.update);
		this.clockModel.bind('date', this.fetch);
		this.bind('reset', this.update);
	},

	comparator: function (model) {
		return model.get('startTime');
	},

	getActive: function () {
		return this.activeModel;
	},

	getNext: function () {
		return this.nextModel;
	},

	getMilliesTillNext: function(index) {
		if (index < 0 || index >= this.length -1) {
			throw new Error("Index out of bounds");
		} else {
			var model_1 = this.at(index),
			model_2 = this.at(index+1);
			return model_2.startDate().getTime() - (model_1.startDate().getTime() + model_1.getTotalDuration());
		}
	},

	update: function () {
		var now = this.clockModel.getTime(),
		active = this.detect( function (model) {
			return model.isActive(now);
		});
		next = this.detect( function (model) {
			return model.startDate().getTime() > now;
		});

		if (this.activeModel !== active) {
			this.activeModel = active;
			this.trigger('active_model', active);
		}

		if (this.nextModel !== next) {
			this.nextModel = next;
			this.trigger('next_model', active);
		}
	},

	setRoom: function (room) {
		this.room = room;
		this.fetch({
			error: function () {
				LectureHallClient.Logger.error("Unable to fetch contributions");
			}
		});
	},

	url: function () {
		return LectureHallClient.Config.base_url + 'lhc/contributions?room=' + this.room + '&day=' + this.clockModel.format('yyyy-mm-dd');
	}
});

LectureHallClient.EventModelView = Backbone.View.extend({

	tagName: "li",

	className: "lhc_event",

	template: _.template($('#lhc_event_template').html()),

	initialize: function () {
		_.bindAll(this, "render");
		this.options.eventModel.bind('change', this.render);
	},

	render: function () {
		$(this.el).html(this.template(this.options.eventModel.toJSON()));        
		return this;
	}
});

LectureHallClient.EventTimelineView = Backbone.View.extend({

	el: "#lhc_event_list",
	
	initialize: function () {
		_.bindAll(this, "render", "updateRender", "toggleBreakInfo");
		this.options.clockModel.bind('change', this.updateRender);
		this.options.clockModel.bind('change:syncStatus', function() {
			$('#header #right #time').toggleClass('syncFailed', arguments[1] === "error")});		
		this.model.bind('reset', this.render);
		this.model.bind('active_model', this.toggleBreakInfo);
	},
	
	render: function () {
		$(this.el).empty();
		
		for (var i = 0; i < this.model.length; i++) {
			var view = new LectureHallClient.EventModelView({
				eventModel: this.model.at(i),
				clockModel: this.options.clockModel
			});
			
			$(this.el).append(view.render().el);
			
			// set width
			$('.lhc_event_info', view.el).css('width', this.model.at(i).getTalkDuration() / 6000);			
			$('.discussion', view.el).css('width', this.model.at(i).getDiscussionDuration() / 6000);
			
			// set margin
			if (i < this.model.length -1) {
				var diff = this.model.getMilliesTillNext(i);
				$(view.el).css('margin-right', LectureHallClient.Helper.millies2length(diff));
			}
			
			$(view.el).css('visibility', "visible");
		}
		
		this.toggleBreakInfo();
	},
	
	updateRender: function () {
		if (this.model.length > 0) {
			var firstModel = this.model.at(0),
			offset = firstModel.startDate().getTime() - this.options.clockModel.getTime();
			$(this.el).css("margin-left", LectureHallClient.Helper.millies2length(offset));
		}
		
		if (this.model.getActive() === undefined) {
			
			var nextModel = this.model.getNext(); 
			if (nextModel !== undefined) {
				$('#next_event_info .time').show();
				$('#next_event_info .description_next').show();
				$('#next_event_info .description_none').hide();
				$('#next_event_info .right').show();
				var timeDiff = nextModel.startDate().getTime() - this.options.clockModel.getTime();
				$('#next_event_info .time').text(new Date(timeDiff).format("isoTime", true));
				$('#next_event_info .bar').css('width', LectureHallClient.Helper.millies2length(timeDiff));
			}
			else {
				$('#next_event_info .time').hide();
				$('#next_event_info .description_next').hide();
				$('#next_event_info .description_none').show();
				$('#next_event_info .right').hide();
			}			
		}
		
		// update clock upper right corner
		$('#header #right #time').text(this.options.clockModel.format("ddd, HH:MM:ss", true));
	},
	
	toggleBreakInfo: function () {
		$('#next_event_info').toggle(this.model.getActive() === undefined);
	}
	
}); 

LectureHallClient.TalkView = Backbone.View.extend({

	el: "#countdown",

	template: _.template($('#talk_template').html()),

	initialize: function () {
		if (this.options.clockModel === undefined || !(this.options.clockModel instanceof LectureHallClient.ClockModel)) {
			console.error('CountdownView: clockModel is undefined or of wrong type');
		}

		_.bindAll(this, "render", "updateRender", "setVisible", "setEventModel", 'playRandomSong');
		this.options.clockModel.bind('change:time', this.updateRender);
	},

	render: function () {
		if (this.options.eventModel !== undefined) {
			$(this.el).html(this.template(this.options.eventModel.toJSON()));
			this.updateRender();
		}
		return this;
	},

	updateRender: function () {
		if (this.options.eventModel === undefined) {
			return;
		}

		if ($(this.el).is(":hidden")) {
			return;
		}

		var inTalk = this.options.eventModel.isInTalkWindow(this.options.clockModel.getTime());

		if (inTalk) {
			var timeDiff = this.options.eventModel.endDate().getTime() - this.options.clockModel.getTime(),
			countdown = new Date(timeDiff);

			this.$('.countdown_time').toggleClass('discussion', false);
			this.$('.countdown_time').text(countdown.format("MM:ss", true));

			if (timeDiff >= 0) {
    			// one minute
    			if (countdown.getUTCMinutes() == 1 && countdown.getUTCSeconds() == 0) {
    				LectureHallClient.Soundmanager.oneMinute.play() || LectureHallClient.Logger.error("Could not play " + LectureHallClient.Soundmanager.oneMinute);
    			}
    			
    			// discussion
    			if (countdown.getUTCMinutes() == 0 && countdown.getUTCSeconds() == 0) {
    				LectureHallClient.Soundmanager.discussion.play() || LectureHallClient.Logger.error("Could not play " + LectureHallClient.Soundmanager.discussion);
    			}
    		}
    	}
    	else { /* inDiscussion */
    		var timeDiff = this.options.eventModel.endDateWithDiscussion().getTime() - this.options.clockModel.getTime(),
    		countdown = new Date(timeDiff);
    		this.$('.countdown_time').toggleClass('discussion', true);
    		this.$('.countdown_time').text(countdown.format("MM:ss", true));

			// discussion
			if (countdown.getUTCMinutes() == 0 && countdown.getUTCSeconds() == 0) {
				this.playRandomSong();
			}
		}   
	},

	setVisible: function (bool) {
		$(this.el).toggle(bool);
	},

	setEventModel: function (model) {
		if (model === undefined || !(model instanceof LectureHallClient.EventModel)) {
			console.error('CountdownView: eventModel is undefined or of wrong type');
		}
		else {
			this.options.eventModel = model;
			this.render();
		}
	},

	playRandomSong: function () {
		if (LectureHallClient.Soundmanager.music.length == 0) {
			LectureHallClient.Logger.debug('No songs available');
		}

		var date = new Date(this.options.clockModel.getTime());
		var index = (date.getDay() + date.getHours() * 3 + Math.floor(date.getMinutes() / 20)) % LectureHallClient.Soundmanager.music.length;
		var track = LectureHallClient.Soundmanager.music[index];

		if (track !== undefined) {
			LectureHallClient.Logger.debug('Starting track ' + track);
			track.play() || LectureHallClient.Logger.error("Could not play " + track);
		}
		else {
			LectureHallClient.Logger.warn('Breaksong at index ' + index + ' is undefined');
		}
	}
});

LectureHallClient.BreakModel = Backbone.Model.extend({

	defaults: {
		"content":  "Some (default) important information you should know!",
	}
});

LectureHallClient.BreakSlides = Backbone.Collection.extend({

	model: LectureHallClient.BreakModel,	
	startTime: undefined, // timestamp in ms
	endTime: undefined, // timestamp in ms
	
	initialize: function () {
		_.bindAll(this, 'fetchForTimeWindow');
	},
	
	url: function () {
		return LectureHallClient.Config.base_url + 'lhc/slides?startTime=' + this.startTime + '&endTime=' + this.endTime;
	},
	
	fetchForTimeWindow: function (startTime, endTime) {
		this.startTime = parseInt(startTime / 1000);
		this.endTime = parseInt(endTime / 1000);
		this.fetch();
	}
});


LectureHallClient.BreakView = Backbone.View.extend({

	tagName: "li",	
	className: "slide",	    
	template: _.template($('#break_template').html()),
	
	initialize: function () {
		_.bindAll(this, 'render');
		
		if (this.model === undefined) {
			LectureHallClient.Logger.error("View has no model");
		}
		else {
			this.model.bind('change', this.render);
		}
	},
	
	render: function () {
		if (this.model !== undefined) {
			$(this.el).html(this.template(this.model.toJSON()));
		}
		else {
			LectureHallClient.Logger.warn('No eventModel defined');
		}
		return this;
	}
});

LectureHallClient.BreakSlidesView = Backbone.View.extend({
	
	el: 'ul#break_slides',	
	viewList: $(),
	nextTalk: undefined,
	
	initialize: function () {
		_.bindAll(this, 'render', 'updateRender', 'setVisible', 'switchSlide');
		
		if (this.model === undefined) {
			LectureHallClient.Logger.error("BreakSlidesView initialized without a model");
		}
		else {
			this.model.bind('reset', this.render);
		}
		
		this.options.clockModel.bind('change', this.updateRender);
	},
	
	render: function () {
		$(this.el).empty();
		this.model.each(function (slide) {
			var view = new LectureHallClient.BreakView({model: slide});
			$(this.el).append(view.render().el);
		}, this);
		
		this.viewList = this.$('li');
		
		this.viewList.each(function () {$(this).hide();});	
		
		if (this.model.length == 1) {
			this.viewList.first().show();
		}
		else if (this.model.length > 1) {			
			this.switchSlide(this.viewList.first());
		}
		
		return this;
	},
	
	updateRender: function (element) {		
		if (this.nextTalk !== undefined) {
			var timeDiff = this.nextTalk.startDate().getTime() - this.options.clockModel.getTime(),
			countdown = new Date(timeDiff);
			
			if (countdown.getUTCHours() == 0 && countdown.getUTCMinutes() == 5 && countdown.getUTCSeconds() == 0) {
				LectureHallClient.Soundmanager.gong && LectureHallClient.Soundmanager.gong.play() || LectureHallClient.Logger.error("Could not play " + LectureHallClient.Soundmanager.gong);
			}
			
			if (countdown.getUTCHours() == 0 && countdown.getUTCMinutes() == 3 && countdown.getUTCSeconds() == 1) {
				if (LectureHallClient.Soundmanager.gong) {
					LectureHallClient.Soundmanager.gong.play() || LectureHallClient.Logger.error("Could not play " + LectureHallClient.Soundmanager.gong);
					setTimeout(LectureHallClient.Soundmanager.gong.play, 5000);
				}
			}

			if (countdown.getUTCHours() == 0 && countdown.getUTCMinutes() == 1 && countdown.getUTCSeconds() == 20) {
				LectureHallClient.Soundmanager.oneMinute && LectureHallClient.Soundmanager.oneMinute.play() || LectureHallClient.Logger.error("Could not play " + LectureHallClient.Soundmanager.oneMinute);
			}
		}
	},
	
	switchSlide: function (element) {
		if (this.viewList !== undefined) {	// TODO: can cause promblem during reset
			var nextElement = (element.is(this.viewList.last())) ? this.viewList.first() : element.next(),
			self = this;
			element.fadeIn(400).delay(LectureHallClient.Config.slideDuration).fadeOut(400,
				function () {
					self.switchSlide(nextElement);	
				}
				);
		}
	},
	
	setVisible: function (bool) {
		$(this.el).toggle(bool);
	},
	
	setNextTalk: function (model) {
		this.nextTalk = model;
	}
});

LectureHallClient.AppView = Backbone.View.extend({

	el: '#lhc_app',    
	subviews : {
		timelineView: undefined,
		slidesView: undefined,
		talkView: undefined
	},

	initialize: function () {

		_.bindAll(this, "render", "setRoom", "rebuildCountdownView", "simulateTime", 'enableTimeSync', 'checkResetRequested');

		this.options.clockModel = new LectureHallClient.ClockModel();
		this.options.eventListModel = new LectureHallClient.EventModelList([], {
			clockModel: this.options.clockModel
		}); 

		this.options.eventListModel.bind('reset', this.rebuildCountdownView);
		this.options.eventListModel.bind('active_model', this.rebuildCountdownView);

		this.subviews.timelineView = new LectureHallClient.EventTimelineView({
			model: this.options.eventListModel,
			clockModel: this.options.clockModel
		});
		
		this.subviews.slidesView = new LectureHallClient.BreakSlidesView({
			model: new LectureHallClient.BreakSlides(),
			clockModel: this.options.clockModel
		});
		
		this.subviews.talkView = new LectureHallClient.TalkView({
			clockModel: this.options.clockModel
		});
		
		setInterval(this.checkResetRequested, 60000);
		
		$('#header #right #version').text(LectureHallClient.Config.version);
	},

	render: function () {
		this.subviews.timelineView.render();
		
		this.subviews.talkView.setVisible(false);
		this.subviews.slidesView.render();
		
		this.subviews.talkView.setVisible(false);
		this.subviews.talkView.render();

		return this;
	},

	rebuildCountdownView: function () {
		var activeModel = this.options.eventListModel.getActive();

		if (activeModel !== undefined) {

			this.subviews.slidesView.setVisible(false);

			this.subviews.talkView.setEventModel(activeModel);
			this.subviews.talkView.setVisible(true);

			LectureHallClient.Helper.logToServer(this.options.eventListModel.room + ": Started talk " + activeModel.get('contributionKey'));
		}
		else {
			var nextModel = this.options.eventListModel.getNext(),
			startTime = this.options.clockModel.getTime(),
			endTime = (nextModel) ? nextModel.startDate().getTime() : startTime + 1000 * 60 * 60;

			this.subviews.slidesView.model.fetchForTimeWindow(startTime, endTime); 

			this.subviews.talkView.setVisible(false);

			this.subviews.slidesView.setNextTalk(nextModel);
			this.subviews.slidesView.setVisible(true);

			LectureHallClient.Helper.logToServer(this.options.eventListModel.room + ": Break started");
		}
	},

	setRoom: function (room) {
    	//this.options.clockModel.syncToServer();
    	this.options.eventListModel.setRoom(room);
    	document.title = LectureHallClient.Config.windowTitlePrefix + " | " + room;
    	LectureHallClient.Helper.logToServer(room + ': Loaded version ' + LectureHallClient.Config.version);
    },
    
    simulateTime: function (mySQLTimedate) {
    	this.options.clockModel.setSimulatedTime(Date.fromMySQL(mySQLTimedate));
    },
    
    enableTimeSync: function (bool) {
    	if (bool) {
    		this.options.clockModel.syncToServer();
    		setInterval(this.options.clockModel.syncToServer, 10000);
    	}
    	else {
    		LectureHallClient.Logger.error('Not implemented yet');
    	}
    },
    
    checkResetRequested: function () {
    	$.ajax({
    		url: LectureHallClient.Config.base_url + 'lhc/reset',
    		dataType: 'json',
    		context: this,
    		success: function (data) {
    			if (data['reset'] === 1) {
    				window.location.reload();
    			}
    		}
    	});
    }
});

LectureHallClient.ApplicationController = Backbone.Router.extend({

	initialize: function (options) {
		if (jQuery.isPlainObject(options.config)) {
			$.extend(LectureHallClient.Config, options.config);
		}
		soundManager.onready(function() {
			$.each(['gong', 'oneMinute', 'discussion'], function(index, value) {
				LectureHallClient.Soundmanager[value] = soundManager.createSound({
					id: value,
					url: LectureHallClient.Config.base_url + LectureHallClient.Soundmanager[value],
					autoLoad: true,
					autoPlay: false,
					onload: function() {
						LectureHallClient.Logger.debug('Loaded sound '+this.sID+'!');
					},
					volume: 70
				});
			});

			LectureHallClient.Soundmanager.music = $.map(LectureHallClient.Soundmanager.music, function(value, index) {
				return soundManager.createSound({
					id: 'breaksong_' + index,
					url: LectureHallClient.Config.base_url + value,
					autoLoad: true,
					autoPlay: false,
					onload: function() {
						LectureHallClient.Logger.debug('Loaded sound ' + this.sID + '!');
					},
					volume: 50
				});
			});
		});
		this.view = new LectureHallClient.AppView();
		this.route(":room", "switchRoom", function (room) {
			this.view.enableTimeSync(true);
			this.view.setRoom(room);
		});
		this.route(":room/:time", "switchRoomAndSimulateTime", function (room, time) {  
        this.view.simulateTime(time.replace(/_/, ' ')); // e.g. 2011-09-24_12:34:03
        this.view.setRoom(room);
    });
	}
});
