var path = require('path');
var fs = require('fs');

var gulp = require('gulp');
var mainBowerFiles = require('main-bower-files');
var uglify = require('gulp-uglify');
var concatSourcemaps = require('gulp-concat-sourcemap');
var concat = require('gulp-concat');
var gulpIf = require('gulp-if');

var Elixir = require('laravel-elixir');

var gulpConfig = require('../config.json');
var NODES_BOWER_PACKAGES = require('../../bower_components/nodes-ui/bower.json');
var PROJECT_BOWER_PACKAGES = require('../../bower.json');

var Task = Elixir.Task;

Elixir.extend('vendorScripts', function(jsOutputFile, jsOutputFolder) {

	var MERGED_PKGS = PROJECT_BOWER_PACKAGES;

	for(var pkg in NODES_BOWER_PACKAGES.dependencies) {
		if(!PROJECT_BOWER_PACKAGES.dependencies.hasOwnProperty(pkg)) {
			MERGED_PKGS.dependencies[pkg] = NODES_BOWER_PACKAGES.dependencies[pkg];
		}
	}

	try {
		fs.writeFileSync('bower.json', JSON.stringify(MERGED_PKGS, null, '\t'));
	} catch(err) {
		return console.log('Error updating project bower.json file!', err);
	}

	var jsFileName = jsOutputFile || 'vendor.js';

	var filterFiles = ['**/*.js'].concat(gulpConfig.ignoredBowerPkgs);

	if(!Elixir.config.production) {
		concat = concatSourcemaps;
	}

	new Task('vendor-scripts', function() {

		return gulp.src(mainBowerFiles({
			filter: filterFiles
		}))
			.pipe(concat(jsFileName, {sourcesContent: true}))
			.pipe(gulpIf(Elixir.config.production, uglify()))
			.pipe(gulp.dest(jsOutputFolder || Elixir.config.js.outputFolder));

	}).watch('bower.json');

});