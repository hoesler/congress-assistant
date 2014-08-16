define(["jquery", "json2", "backbone", "log4javascript"], function(jquery, json2, backbone, log4javascript) {
	
	var logger = log4javascript.getLogger("PosterApp");
	logger.addAppender(new log4javascript.BrowserConsoleAppender());

	var PosterApp = {
		Config: {
			base_url: '',
			max_selections: 5
		}
	};

	PosterApp.ParticipantModel = Backbone.Model.extend({
	});


	PosterApp.ParticipantList = Backbone.Collection.extend({
		model: PosterApp.ParticipantModel,
		
		selector: "",
		
		participantUUID: undefined,
			
		initialize: function (models, options) {
			_.bindAll(this, "setSelector");
			
			this.participantUUID = options.uuid;
			if (options.uuid === undefined) {
				throw new Error("uuid is undefined");
			}
		},
		
		comparator: function (model) {
		    return model.get('lastName');
		},
		
		setSelector: function (selector) {
			this.selector = selector;
		},
		
		url: function () {
			var url = PosterApp.Config.base_url + 'participants/?unlock=1&level=STUDENT,SENIOR&lastName=' + this.selector;
			
			if (this.participantUUID) {
				url += '&exclude_uuid=' + this.participantUUID;
			}
			logger.debug(url);
			return url;
		}
	});

	PosterApp.SelectedParticipants = Backbone.Collection.extend({
		model: PosterApp.ParticipantModel,
		
		participantUUID: undefined,
		
		initialize: function (models, options) {
			_.bindAll(this, "save", 'triggerSavedEvent');
			
			this.participantUUID = options.uuid;
			if (options.uuid === undefined) {
				throw new Error("uuid is undefined");
			}
			//this.bind('change', this.save);
		},
		
		url: function () {
			return (this.participantUUID !== undefined) ? PosterApp.Config.base_url + 'poster/get/' + this.participantUUID : "";
		},
		
		save: function (options) {
			if (this.participantUUID === undefined) {
				return;
			}
			
			$.post(
				PosterApp.Config.base_url + 'poster/save/' + this.participantUUID,
				{visitors: this.pluck('id')},
				this.triggerSavedEvent
			);
		},
		
		triggerSavedEvent: function () {
			logger.debug("saved");
			this.trigger('saved', this);
		}
	});

	PosterApp.ParticipantView = Backbone.View.extend({

	    tagName: "li",

	    className: "PosterApp_participant",

	    template: _.template($('#poster_participant_template').html()),
	    
	    selected: false,
	    
	    events: {
	        "change input":          "syncSelectionToModel",
	    },
	    
	    initialize: function () {
	    	_.bindAll(this, "render", "setSelected", "syncSelectionFromModel", "syncSelectionToModel");
	    	    	
	    	this.options.selectedParticipants.bind('add', this.updateSelected);
	    	this.options.selectedParticipants.bind('remove', this.updateSelected);
	    	this.options.selectedParticipants.bind('reset', this.updateSelected);
	    	
	    	this.syncSelectionFromModel();
	    },
	    
	    render: function () {
	        $(this.el).html(this.template(this.model.toJSON()));
	        this.$('input').prop("checked", this.selected);
	        return this;
	    },
	    
	    setSelected: function (value) {
	    	this.selected = value;
	    },
	    
	    syncSelectionFromModel: function () {
	    	this.setSelected(
	    		this.options.selectedParticipants.any(
	    			function (el) {
	    				return el.id === this.model.id;
	    			},
	    			this
	    		)
	    	);
	    },
	    
	    syncSelectionToModel: function () {
	    	var modelFromCollection = this.options.selectedParticipants.get(this.model.id),
	    		action = (this.$('input').prop("checked")) ? 'add' : 'remove';
	    	    	
	    	if (action === 'add' && this.options.selectedParticipants.length >= PosterApp.Config.max_selections) {
	    		alert("Maximum number of selections reached. Remove one first to add one.");
	    		this.$('input').prop("checked", false);
	    	}
	    	else {
				if (action === 'add' && modelFromCollection === undefined) {
					this.options.selectedParticipants.add(this.model);
				}
				else if (action === 'remove' && modelFromCollection !== undefined) {
					this.options.selectedParticipants.remove(this.model);
				}
			}
	    }
	});

	PosterApp.SelectedParticipantView = Backbone.View.extend({

	    tagName: "li",

	    className: "PosterApp_selected_participant",

	    template: _.template($('#poster_selected_participant_template').html()),
	    
	    events: {
	        "click .remove":          "remove",
	    },
	    
	    initialize: function () {
	    	_.bindAll(this, "render");
	    },
	    
	    render: function () {
	        $(this.el).html(this.template(this.model.toJSON()));
	        return this;
	    },
	    
	    remove: function () {
	    	this.trigger('remove', this);
	    }
	});

	PosterApp.SelectedParticipantsListView = Backbone.View.extend({
		
		el: '#selected_participants',
		
		initialize: function () {
			_.bindAll(this, "render", "modelAdded", "modelRemoved", "removeRequested");
			
			this.model.bind('add', this.modelAdded);
			this.model.bind('remove', this.modelRemoved);
			this.model.bind('reset', this.render);
		},
		
		render: function () {
			var domElement = $(this.el);
			domElement.empty();
			
			if (this.model.length === 0) {
				domElement.text('List is empty');
			}
			else {
				this.model.each(function(element) {
					var view = new PosterApp.SelectedParticipantView({model: element});
					view.bind('remove', this.removeRequested);
					domElement.append(view.render().el);
				}, this);
			}
			return this;
		},
		
		modelAdded: function (model) {
			logger.debug('modelAdded');
			this.render();
		},
		
		modelRemoved: function (model) {
			logger.debug('modelRemoved');
			this.render();
		},
		
		removeRequested: function (view) {
			this.model.remove(view.model);
		}
	});

	PosterApp.ParticipantListView = Backbone.View.extend({

		el: '#participants',
		
		initialize: function () {
			_.bindAll(this, "render");
			
			this.model.bind('reset', this.render);
		},
		
		render: function () {
			$(this.el).empty();
			if (this.model.length == 0 && this.model.selector != "") {
				$(this.el).append('<div class="no_results">None of the participants last name starts with "'+this.model.selector+'"</div>');
			}
			else {
				this.model.each(function(element) {
					var view = new PosterApp.ParticipantView({model: element, selectedParticipants: this.options.selectedParticipantList});
					$(this.el).append(view.render().el);
				}, this);
			}
			$(this.el).scrollTop(0);
		}
	});

	PosterApp.AppView = Backbone.View.extend({

	    el: '#PosterApp_app',

		events: {
		  "change #search input":  "rebuildParticipantList",
		  "click #alphabet_selector .letter": "rebuildParticipantList",
		  "click input.save" : "saveSelection"
		},
		
		subviews: {
			selectedParticipantsView: undefined,
			participantListView: undefined
		},
		
		models: {
			selectedParticipantsModel: undefined,
			participantList: undefined
		},	
		
	    initialize: function () {
	    	_.bindAll(this, "rebuildParticipantList", 'selectionChanged', 'saveSelection', 'selectionSaved', 'rebuildParticipantList');
	    	
	    	if (this.options.uuid === undefined) {
	    		error("uuid is undefined");
	    	}
	    	
	    	this.models.selectedParticipantsModel = new PosterApp.SelectedParticipants([], {uuid: this.options.uuid});
	    	this.models.selectedParticipantsModel.fetch();
	    	this.models.selectedParticipantsModel.bind('add', this.selectionChanged);
	    	this.models.selectedParticipantsModel.bind('remove', this.selectionChanged);
	    	this.models.selectedParticipantsModel.bind('saved', this.selectionSaved);
	    	
	    	this.models.participantList = new PosterApp.ParticipantList([], {uuid: this.options.uuid});
	    	
	    	this.subviews.selectedParticipantsView = new PosterApp.SelectedParticipantsListView(
	    		{model: this.models.selectedParticipantsModel}
	    	);
	    	
	    	this.subviews.participantListView = new PosterApp.ParticipantListView(
	    		{model: this.models.participantList, selectedParticipantList: this.models.selectedParticipantsModel}
	    	);
	    	
	    	this.render();
	    },
	    
	    rebuildParticipantList: function (event) {
	    	var target = $(event.target);
	    	$('#alphabet_selector li.letter').each(function(index) {$(this).removeClass("active")});
	    	if (target.is('li.letter')) {
	    		target.addClass('active');
	    		this.$('#search input').val('');
	    		this.models.participantList.setSelector(target.text());
	    	}
	    	else if (target.is('#search input')) {
				var searchString = this.$('#search input').val();
				if (searchString != '') {
					this.models.participantList.setSelector(searchString);
				}
			}
			
			this.models.participantList.fetch();
	    },
	    
	    render: function () {
	    	this.subviews.selectedParticipantsView.render();
	    	this.subviews.participantListView.render();
	    	return this;
	    },
	    
	    selectionChanged: function (caller) {
	    	var target = this.$('input.save');
	    	target.val('save');
	    	target.prop('disabled', false);
	    },
	    
	    selectionSaved: function (caller) {
	    	var target = this.$('input.save');
	    	target.val('saved');
	    	target.prop("disabled", true);
	    },
	    
	    saveSelection: function (event) {  	
	    	this.models.selectedParticipantsModel.save();
	    }
	});

	PosterApp.ApplicationController = Backbone.Router.extend({
		
	  initialize: function (options) {
		if (jQuery.isPlainObject(options.config)) {
			$.extend(SlideEditor.Config, options.config);
		}

		this.route(
			/poster\/index\/(.*)/,
			"start",
			function(uuid) {
				window.App = new PosterApp.AppView({uuid: uuid});
			}
		);
	  }
	});

	return PosterApp;
}
