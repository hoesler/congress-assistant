'use strict';

module.exports = function(grunt) {
	
	var shell = require('shelljs');

	grunt.initConfig({
	    pkg: grunt.file.readJSON('package.json'),
	    php: {
	        test: {
	            options: {
	                keepalive: true,
	                port: 4567,
	                open: true
	            }
	        }
	    }
	});

	grunt.loadNpmTasks('grunt-php');
	
	grunt.registerTask('db:migrate', 'Run database migration', function() {
	  var cmd = shell.exec('php index.php migrate', { silent: true });
	  if (cmd.code != 0) {
	  	grunt.log.error(cmd.output);
	  	return false;
	  }

	  return true;
	});
};