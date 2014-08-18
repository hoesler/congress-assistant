module.exports = function(grunt) {
  	'use strict';
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

	grunt.registerTask('test', ['php']);
};