'use strict';

/**
 *
 * Configuration Files
 *
 */
var elixir = require('laravel-elixir');
var gulp = require('gulp');

var config					= require('./gulp/config.json');
var pkg						= require('./package.json');

var projectName 			= pkg.name;
var projectUrl				= 'https://' + pkg.name + '.local-like.st';

var filesToWatch = [
	'./public/**/*',
	'./resources/views/**/*',
	'./vendor/nodesagency/backend/resources/views/**/*',
	'./nodes/backend/resources/views/**/*',
];

elixir.config.js.outputFolder = 'public/js';
elixir.config.css.outputFolder = 'public/css';

require(config.tasksPath + 'vendor-scripts.task.js');

require(config.tasksPath + 'project-scripts.task.js');
require(config.tasksPath + 'project-styles.task.js');
require(config.tasksPath + 'project-scss-wiredep.task.js');

elixir(function(mix) {

	mix.vendorScripts();

	mix.projectScssWiredep();
	mix.projectStyles();
	mix.projectScripts();

	mix.browserSync({
		files: filesToWatch,
		proxy: projectUrl,
		https: true
	});

});

gulp.task('build', [
	'project-scss-wiredep',
	'vendor-scripts',
	'project-scripts',
	'project-pages-scripts',
	'project-partials-scripts',
	'project-styles'
]);