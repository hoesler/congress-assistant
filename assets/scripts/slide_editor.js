define(["jquery", "ckeditor", "backbone"], function() {

	var SlideEditor = {
		Config: {
			base_url: '',
			log_level: 1 // 0 => Off, 1 => ERROR, 2 => WARN, 3 => INFO, 4 => DEBUG/LOG
		},
		Logger: {
			log: function (message) { window.console && console.log && SlideEditor.Config.log_level >= 4 && console.log(message); },
			debug: function (message) { window.console && console.debug && SlideEditor.Config.log_level >= 4  && console.debug(message); },
			info: function (message) { window.console && console.info && SlideEditor.Config.log_level >= 3  && console.info(message); },
			warn: function (message) { window.console && console.warn && SlideEditor.Config.log_level >= 2  && console.warn(message); },
			error: function (message) { window.console && console.error && SlideEditor.Config.log_level >= 1  && console.error(message); }
		}
	};

	SlideEditor.SlideModel = Backbone.Model.extend({

		defaults: {
			'days': [],
			'startTime': '12:00',
			'endTime': '13:00'
		}
	});

	SlideEditor.SlideListModel = Backbone.Collection.extend({

		model: SlideEditor.SlideModel,

		initialize: function () {
		},
		
		url: function () {
			return SlideEditor.Config.base_url + 'admin/edit_slides';
		}

	});

	SlideEditor.SlideView = Backbone.View.extend({
	    tagName: "li",

	    className: "bse_slide",

	    template: _.template($('#bse_slide_template').html()),

		events: {
		    "click":			"edit",
		    "click .delete":	"askDestroy",
		},

	    initialize: function () {
	    	_.bindAll(this, "render", "edit");
	    	this.model.bind('change', this.render);
	    },

	    render: function () {
	        $(this.el).html(this.template(this.model.toJSON()));        
	        return this;
	    },
	    
	    edit: function () {
	    	this.trigger('edit_request', this.model);
	    },
	    
	    askDestroy: function () {
	    	// TODO: Show confirmation dialog
	    	this.model.destroy();
	    }
	});

	SlideEditor.SlideListView = Backbone.View.extend({
		
		el: "#slideList",
		
		selectedModel: undefined,
		
		initialize: function () {
		    _.bindAll(this, "render", "appendModel", 'removeModel');
		    
		    this.model.bind('reset', this.render);
		    this.model.bind('add', this.appendModel);
		    this.model.bind('remove', this.removeModel);
		},
		
		render: function () {
			$(this.el).empty();
			this.model.each( this.appendModel );
		},
		
		appendModel: function (slideModel) {
			if (slideModel === undefined) {
				SlideEditor.Logger.error('slideModel is undefined');
				return;
			}
			
			var view = new SlideEditor.SlideView({
				    model: slideModel,
				    id: this.idForModel(slideModel)
				}),
				self = this;
			
			view.bind('edit_request', 
				function (slideModel) {
					self.setSelectedElement(slideModel);
				}
			);
			
			$(this.el).append(view.render().el);
		},
		
		scrollToElement: function (slideModel) {
			if (slideModel === undefined || !this.model.contains(slideModel)) {
				return;
			}
			ESlideEditor.Logger.warn('Not implemented yet');
		},
		
		scrollToSelectedElement: function () {
			this.scrollToElement(this.selectedModel);
		},
		
		setSelectedElement: function (model) {
			if (model !== undefined &&
				!this.model.contains(model)) {
					SlideEditor.Logger.warn('model not an element of this views collection');
					return;
			}
			
			if (this.selectedModel !== undefined) {
				this.$('#'+this.idForModel(this.selectedModel)).toggleClass('selected', false);
			}
			
			this.selectedModel = model;
			this.trigger('select', model);
			
			if (this.selectedModel !== undefined) {
				this.$('#'+this.idForModel(this.selectedModel)).toggleClass('selected', true);
			}
		},
		
		removeModel: function (model) {
			this.$('#'+this.idForModel(model)).detach();
			this.trigger('select', undefined);
		},
		
		idForModel: function (model) {
			return 'slide_' + model.cid;
		}	
	});

	SlideEditor.EditorView = Backbone.View.extend({
		
		el: '#right',
		
		events: {
			'click #save': "save",
			'change :input': 'setModified',
		},
		
		slideModel: undefined,
		
		initialize: function () {
		    _.bindAll(this, "render", "setSlide", "setModified", "save", "readOnly");
		},
		
		render: function () {
			var editor = CKEDITOR.replace( 'editor1', {readOnly: true});
			editor.on('saveSnapshot', function(event) { this.setModified(true); }, this);
		},
		
		setSlide: function (slideModel) {
			if (this.slideModel == slideModel) {
				return;
			}
			
			this.slideModel = slideModel;
			
			if (slideModel !== undefined) {
				this.$(':input').prop('disabled', false);
							
				this.setModified(slideModel.isNew());
				
				this.$('#editor1').val(slideModel.get('content'));
				CKEDITOR.instances.editor1.setData(slideModel.get('content'));
				this.$(':input[name="title"]').val(slideModel.get('title'));
				this.$('select[name="days"] option').each( function () { $(this).prop('selected', _.contains(slideModel.get('days'), this.value)) });
				this.$(':input[name="starttime_hh"]').val(slideModel.get('startTime').substr(0, 2));
				this.$(':input[name="starttime_mm"]').val(slideModel.get('startTime').substr(3, 2));
				this.$(':input[name="endtime_hh"]').val(slideModel.get('endTime').substr(0, 2));
				this.$(':input[name="endtime_mm"]').val(slideModel.get('endTime').substr(3, 2));
						
				this.readOnly(false);
			}
			else {
				this.setModified(false);
				
				this.$('#editor1').val('');
				this.$(':input[name="title"]').val('');

				this.readOnly(true);
			}
		},
		
		readOnly: function (bool) {
			this.$(':input').prop('disabled', bool);
			if (CKEDITOR.instances.editor1.readOnly != bool) {
				CKEDITOR.instances.editor1.setReadOnly(bool);
			}
		},
		
		setModified: function (bool) {
			if (bool === undefined) {
				bool = true;
			} 
			this.$('#save').text( (bool) ? 'save' : 'saved');
			this.$('#save').prop('disabled', ! bool);
		},
		
		save: function () {
			if (this.slideModel !== undefined) {
				var self = this;
				
				this.slideModel.set({'title': this.$(':input[name="title"]').val() });
				this.slideModel.set({'content': this.$('#editor1').val() });	
				
				this.slideModel.set({'days': this.$('select[name="days"] option:selected').map(function () { return this.value; }).get() });
				this.slideModel.set({'startTime': this.$(':input[name="starttime_hh"]').val() + ':' + this.$(':input[name="starttime_mm"]').val() });
				this.slideModel.set({'endTime': this.$(':input[name="endtime_hh"]').val() + ':' + this.$(':input[name="endtime_mm"]').val() });
				
				this.slideModel.save({}, {
					success: function (model, response) {
						SlideEditor.Logger.debug("Model was saved");
						self.setModified(false);
					},
					error: function (model, response) {
						SlideEditor.Logger.error("Model could not be saved");
					}
				});
			}
			else {
				SlideEditor.Logger.debug('slideModel is undefined');
			}
		}
	});

	SlideEditor.AppView = Backbone.View.extend({
		
		el: '#slide_editor_app',
		
		subviews: {
			slidelist: undefined,
			editor:undefined
		},
		
		events: {
			'click #add': "addSlide",
		},
		
		initialize: function () {
			var slideListModel = new SlideEditor.SlideListModel;
			
			this.subviews.slidelist = new SlideEditor.SlideListView({model: slideListModel});
			
			this.subviews.editor = new SlideEditor.EditorView();
			
			this.subviews.slidelist.bind('select', this.subviews.editor.setSlide);
			
			_.bindAll(this, "render", 'addSlide', 'start');
		},
		
		render: function () {
			this.subviews.slidelist.render();
			this.subviews.editor.render();
		},
		
		start: function () {
			this.subviews.slidelist.model.fetch();
		},	
		
		addSlide: function () {
			var newModel = this.subviews.slidelist.model.create({title: "New Slide"});
			this.subviews.slidelist.setSelectedElement(newModel);
		},
	});

	SlideEditor.ApplicationController = Backbone.Router.extend({

	  initialize: function (options) {
		if (jQuery.isPlainObject(options.config)) {
			$.extend(SlideEditor.Config, options.config);
		}
	  	this.view = new SlideEditor.AppView();
	  	this.view.render();
	  	
	  	this.route(".*", "start", function (room) {
	  	    this.view.start();
	  	});
	  }

	});

	return SlideEditor;
}
