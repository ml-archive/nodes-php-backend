var gulp = require('gulp');
var filter = require('gulp-filter');
var uglify = require('gulp-uglify');
var concatSourcemaps = require('gulp-concat-sourcemap');
var concat = require('gulp-concat');
var gulpIf = require('gulp-if');

var Elixir = require('laravel-elixir');

var Task = Elixir.Task;

Elixir.extend('backendScripts', function(jsOutputFile, jsOutputFolder) {

    var jsFile = jsOutputFile || 'backend.js';

    var jsSources = './vendor/nodesagency/backend/resources/assets/js/**/*.js';

    if(!Elixir.config.production) {
        concat = concatSourcemaps;
    }

    new Task('backend-scripts', function() {

        return gulp.src(jsSources)
            .pipe(concat(jsFile, {sourcesContent: true}))
            .pipe(gulpIf(Elixir.config.production, uglify()))
            .pipe(gulp.dest(jsOutputFolder || Elixir.config.js.outputFolder));

    }).watch(jsSources);

});