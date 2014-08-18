requirejs.config({
  baseUrl: "/assets",
  shim: {
    'soundmanager2': {
      exports: 'soundManager',
      deps: ['log4javascript'],
      init: function (log4javascript) {
        logger = log4javascript.getLogger();
        soundManager.setup({
          url: 'assets/bower_components/soundmanager2/swf',
          onready: function() {
            logger.debug('Started OK');
          },
          ontimeout: function() {
            logger.error('Loaded OK, but unable to start: unsupported/flash blocked, etc.');
          }
        })
        soundManager.beginDelayedInit();
        // The following may help Flash see the global.
        window.soundManager = soundManager;
      }
    },
    'log4javascript': {
        exports: 'log4javascript',
        init: function() {
            log4javascript.setDocumentReady();
        }
    }
  },
  paths: {
    backbone: "bower_components/backbone/backbone",
    underscore: "bower_components/underscore/underscore",
    ckeditor: "bower_components/ckeditor/ckeditor",
    "date.format": "bower_components/date.format/date.format",
    jquery: "bower_components/jquery/dist/jquery",
    "jquery-ui": "bower_components/jquery-ui/jquery-ui",
    json2: "bower_components/json2/json2",
    less: "bower_components/less/dist/less-1.7.4",
    requirejs: "bower_components/requirejs/require",
    'soundmanager2': "bower_components/soundmanager2/script/soundmanager2-jsmin",
    'log4javascript': "bower_components/log4javascript/log4javascript",
    'moment': "bower_components/moment/moment"
  },
  packages: [

  ]
});