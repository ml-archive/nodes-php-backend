var gulp = require('gulp');
var path 					= require('path');
var sass 					= require('gulp-sass');
var postcss					= require('gulp-postcss');
var sourcemaps				= require('gulp-sourcemaps');
var autoprefixer			= require('autoprefixer');
var cssmin 					= require('gulp-cssmin');
var rename 					= require('gulp-rename');

var Elixir = require('laravel-elixir');

var gulpConfig = require('../config.json');

var Task = Elixir.Task;

Elixir.extend('projectStyles', function(jsOutputFile, jsOutputFolder, cssOutputFile, cssOutputFolder) {

    var cssFileName = cssOutputFile || 'project.css';

	var scssRoot = gulpConfig.project.src.scss;
    var scssSources = path.join(scssRoot, '**/*.scss');
    var scssMainFile = path.join(scssRoot, 'project.scss');

    new Task('project-styles', function() {

        return gulp.src(scssMainFile)
            .pipe(sourcemaps.init())
            .pipe(sass({
                outputStyle: Elixir.config.production ? 'compressed' : 'expanded',
                includePaths: ['bower_components']
            })).on('error', sass.logError)
            .pipe(postcss([
                autoprefixer({
                    browsers: ['last 2 versions', 'ie >= 10']
                })
            ]))
            .pipe(sourcemaps.write())
            .pipe(rename(cssFileName))
            .pipe(gulp.dest(cssOutputFolder || Elixir.config.css.outputFolder));

    }).watch(scssSources);

});