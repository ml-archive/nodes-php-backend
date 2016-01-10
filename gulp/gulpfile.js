'use strict';

/**
 *
 * Configuration Files
 *
 */
var elixir = require('laravel-elixir');

var config					= require('./vendor/nodesagency/backend/gulp/config.json');
var pkg						= require('./package.json');

var projectName 			= pkg.name;
var projectUrl				= 'https://' + pkg.name + '.local-like.st';

var filesToWatch = [
    './public/**/*',
    './resources/views/**/*',
    './resources/assets/**/*',
    './vendor/nodesagency/backend/resources/views/**/*'
];

require(config.tasksPath + 'vendor-scripts.task.js');
require(config.tasksPath + 'backend-scripts.task.js');
require(config.tasksPath + 'backend-styles.task.js');
require(config.tasksPath + 'project-scripts.task.js');
require(config.tasksPath + 'project-styles.task.js');
require(config.tasksPath + 'modernizr.task.js');

elixir.config.js.outputFolder = 'public/js';
elixir.config.css.outputFolder = 'public/css';

elixir(function(mix) {

	mix.vendorScripts();
	mix.backendScripts();
	mix.backendStyles();
	mix.projectStyles();
	mix.projectScripts();
	mix.modernizr();

	mix.browserSync(filesToWatch, {
		proxy: projectUrl,
		https: true
	});

});