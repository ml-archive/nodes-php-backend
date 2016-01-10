var gulp = require('gulp');
var modernizr				= require('gulp-modernizr');
var browserSync = require('browser-sync');

var Elixir = require('laravel-elixir');

var Task = Elixir.Task;

Elixir.extend('modernizr', function(jsOutputFile, jsOutputFolder) {

    var sources = ['./public/css/*.css', 'public/js/**/*.js'];

    new Task('modernizr', function() {

        return gulp.src(sources)
            .pipe(modernizr())
            .pipe(gulp.dest(jsOutputFolder || Elixir.config.js.outputFolder));

    });

});