var gulp = require('gulp');
var mainBowerFiles = require('main-bower-files');
var uglify = require('gulp-uglify');
var concatSourcemaps = require('gulp-concat-sourcemap');
var concat = require('gulp-concat');
var gulpIf = require('gulp-if');
var debug = require('gulp-debug');

var Elixir = require('laravel-elixir');

var Task = Elixir.Task;

var ignoredBowerPkgs 	= [
    // Bootbox has a dependency on the "raw" bootstrap, we dont want it imported twice. -dhni
    '!**/bower_components/bootstrap/dist/js/bootstrap.js',
    // We don't use the templating features of blueimp fileupload -dhni
    '!**/bower_components/blueimp-tmpl/js/tmpl.js'
];

Elixir.extend('vendorScripts', function(jsOutputFile, jsOutputFolder) {

    var jsFile = jsOutputFile || 'vendor.js';

    var filterFiles = ['**/*.js'].concat(ignoredBowerPkgs);

    if(!Elixir.config.production) {
        concat = concatSourcemaps;
    }

    new Task('vendor-scripts', function() {

        return gulp.src(mainBowerFiles({
            filter: filterFiles
        }))
            .pipe(concat(jsFile, {sourcesContent: true}))
            .pipe(gulpIf(Elixir.config.production, uglify()))
            .pipe(gulp.dest(jsOutputFolder || Elixir.config.js.outputFolder));

    }).watch('bower.json');

});