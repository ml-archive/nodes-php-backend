var gulp = require('gulp');
var filter = require('gulp-filter');
var uglify = require('gulp-uglify');
var concatSourcemaps = require('gulp-concat-sourcemap');
var concat = require('gulp-concat');
var gulpIf = require('gulp-if');

var Elixir = require('laravel-elixir');

var Task = Elixir.Task;

Elixir.extend('projectScripts', function(jsOutputFile, jsOutputFolder) {

    var jsFile = jsOutputFile || 'project.js';

    var jsSources = ['./resources/assets/js/**/*.js', '!./resources/assets/js/pages/**/*.js'];
    var jsPages = './resources/assets/js/pages/**/*.js';

    if(!Elixir.config.production) {
        concat = concatSourcemaps;
    }

    new Task('project-scripts', function() {

        return gulp.src(jsSources)
            .pipe(concat(jsFile, {sourcesContent: true}))
            .pipe(gulpIf(Elixir.config.production, uglify()))
            .pipe(gulp.dest(jsOutputFolder || Elixir.config.js.outputFolder));

    }).watch(jsSources);

    new Task('project-pages-scripts', function() {

        return gulp.src(jsPages)
            .pipe(gulpIf(Elixir.config.production, uglify()))
            .pipe(gulp.dest(jsOutputFolder || Elixir.config.js.outputFolder));

    }).watch(jsPages);

});