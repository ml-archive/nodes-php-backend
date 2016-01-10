// ------------------------------------
// Required components
// ------------------------------------
var gulp = require('gulp'),
    sass = require('gulp-ruby-sass'),
    minifyCss = require('gulp-minify-css'),
    concatCss = require('gulp-concat-css'),
    minifyJs = require('gulp-uglify'),
    concatJs = require('gulp-concat'),
    rename = require('gulp-rename'),
    chmod = require('gulp-chmod');

// ------------------------------------
// Path variables
// ------------------------------------
var paths = {
    'resources': {
        'assets': './resources/assets/',
        'css': './resources/assets/css/',
        'scss': './resources/assets/scss/',
        'js': './resources/assets/js/'
    },
    'public': {
        'css': './public/css/',
        'js': './public/js/'
    }
};

// ------------------------------------
// Tasks
// ------------------------------------
gulp.task('default', function() {
    // ------------------------------------
    // Styles
    // ------------------------------------
    // Compile, minify and save SCSS files
    sass(paths.resources.scss, {
        force: true,
        noCache: true
    }).pipe(gulp.dest(paths.resources.css));

    // Concatenate and minify compiled SCSS files
    gulp.src(paths.resources.css + '*.css')
        .pipe(concatCss('styles.min.css'))
        .pipe(minifyCss())
        .pipe(gulp.dest(paths.public.css));

    // ------------------------------------
    // Modernizr
    // ------------------------------------
    gulp.src(paths.resources.assets + 'modernizr/modernizr.js')
        .pipe(minifyJs())
        .pipe(rename({ suffix: '.min' }))
        .pipe(chmod(644))
        .pipe(gulp.dest(paths.public.js));

    // ------------------------------------
    // Javascript
    // ------------------------------------
    // Copy 3rd party JS files
    gulp.src([
        paths.resources.assets + 'jquery/jquery.js',
        paths.resources.assets + 'bootstrap/original/javascripts/bootstrap.js',
        paths.resources.assets + 'bootbox/bootbox.js',
        paths.resources.assets + 'jquery-file-upload/jquery.ui.widget.js',
		paths.resources.assets + 'jquery-file-upload/jquery.fileupload.js',
		paths.resources.assets + 'jquery-file-upload/jquery.iframe-transport.js'
    ]).pipe(gulp.dest(paths.resources.js));

    // Copy Nodes JS file
    gulp.src(paths.resources.assets + 'nodes/javascripts/nodes.js')
        .pipe(gulp.dest(paths.resources.js));

    // Minify and concatenate JS files
    gulp.src([
        paths.resources.js + 'jquery.js',
        paths.resources.js + 'bootstrap.js',
        paths.resources.js + 'bootbox.js',
        paths.resources.js + 'jquery.ui.widget.js',
        paths.resources.js + 'jquery.fileupload.js',
		paths.resources.js + 'jquery.iframe-transport.js',
        paths.resources.js + 'nodes.js',
        paths.resources.js + 'scripts.js'
    ]).pipe(concatJs({
          path: 'scripts.min.js',
          stat: {
              mode: 0644
          }
      }))
      .pipe(minifyJs())
      .pipe(gulp.dest(paths.public.js));
});

// ------------------------------------
// Watch
// ------------------------------------
gulp.task('watch', function() {
    gulp.watch([
        paths.resources.assets + 'nodes/stylesheets/*.scss',
        paths.resources.assets + 'nodes/stylesheets/helpers/*.scss',
        paths.resources.assets + 'nodes/stylesheets/mixins/*.scss',
        paths.resources.assets + 'nodes/stylesheets/pages/*.scss',
        paths.resources.assets + 'nodes/javascripts/nodes.js',
        paths.resources.scss + '*.scss',
        paths.resources.js + 'scripts.js'
    ], ['default']);
});
