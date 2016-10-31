var gulp = require('gulp');
var path = require('path');
var wiredep = require('wiredep').stream;

var Elixir = require('laravel-elixir');

var gulpConfig = require('../config.json');

var Task = Elixir.Task;

Elixir.extend('projectScssWiredep', function(jsOutputFile, jsOutputFolder, cssOutputFile, cssOutputFolder) {

	var scssRoot = gulpConfig.project.src.scss;
	var scssMainFile = path.join(scssRoot, 'project.scss');

	new Task('project-scss-wiredep', function() {
		return gulp.src(scssMainFile)
			.pipe(wiredep())
			.pipe(gulp.dest(scssRoot));

	}).watch('bower.json');

});