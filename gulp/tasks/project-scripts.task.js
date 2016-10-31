var gulp = require('gulp');
var filter = require('gulp-filter');
var uglify = require('gulp-uglify');
var concatSourcemaps = require('gulp-concat-sourcemap');
var concat = require('gulp-concat');
var gulpIf = require('gulp-if');

var Elixir = require('laravel-elixir');

var Task = Elixir.Task;

Elixir.extend('projectScripts', function(jsOutputFile, jsOutputFolder) {

    var jsFileName = jsOutputFile || 'project.js';

    var jsSources = [
        './resources/assets/js/**/*.js',
		'!./resources/assets/js/pages/**/*.js',
		'!./resources/assets/js/partials/**/*.js'
	];

    var jsPages = './resources/assets/js/pages/**/*.js';
    var jsPartials = './resources/assets/js/partials/**/*.js';

    if(!Elixir.config.production) {
        concat = concatSourcemaps;
    }

    new Task('project-scripts', function() {
        return gulp.src(jsSources)
            .pipe(concat(jsFileName, {sourcesContent: true}))
            .pipe(gulpIf(Elixir.config.production, uglify()))
            .pipe(gulp.dest(jsOutputFolder || Elixir.config.js.outputFolder));

    }).watch(jsSources);

    new Task('project-pages-scripts', function() {
        return gulp.src(jsPages)
            .pipe(gulpIf(Elixir.config.production, uglify()))
            .pipe(gulp.dest(jsOutputFolder || Elixir.config.js.outputFolder));

    }).watch(jsPages);

	new Task('project-partials-scripts', function() {
		return gulp.src(jsPartials)
			.pipe(gulpIf(Elixir.config.production, uglify()))
			.pipe(gulp.dest(jsOutputFolder || Elixir.config.js.outputFolder));

	}).watch(jsPartials);

});