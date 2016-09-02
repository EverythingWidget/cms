/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var gulp = require('gulp');

var connect = require('gulp-connect-php'),
        path = require('path'),
        sass = require('gulp-sass'),
        rename = require('gulp-rename'),
        sourcemaps = require('gulp-sourcemaps'),
        uncss = require('gulp-uncss'),
        browserSync = require('browser-sync').create();
var minify = require('gulp-minify');
var pump = require('pump');
var concat = require('gulp-concat');
var include = require('gulp-inject-stringified-html');

gulp.task('default', function () {
  // place code for your default task here
});

gulp.task('compile:scss', function (a) {
  return gulp.src("packages/rm/public/templates/**/scss/template.scss")
          .pipe(sourcemaps.init())
          .pipe(sass({
            outputStyle: 'compressed'
          }).on('error', sass.logError))
          .pipe(rename(function (path) {
            path.dirname += "/..";
          }))
          .pipe(sourcemaps.write('./'))
          .pipe(gulp.dest('packages/rm/public/templates/'))
          .pipe(browserSync.stream({
            match: "**/*.css"
          }));

//return gulp.src("packages/rm/public/templates/**/scss/*.scss")
//    //.pipe(sourcemaps.init())
//    .pipe(sass({
//      outputStyle: 'compressed'
//    }).on('error', sass.logError))
//    .pipe(gulp.dest('.'))
//    .pipe(browserSync.stream({
//      match: "**/*.css"
//    }));
});

gulp.task('admin:compile:base:scss', function (a) {
  return gulp.src("packages/admin/public/css/base.scss")
          .pipe(sourcemaps.init())
          .pipe(sass({
            outputStyle: 'compressed'
          }).on('error', sass.logError))
          .pipe(rename(function (path) {
            //path.dirname += "/..";
          }))
//          .pipe(sourcemaps.write('./'))
          .pipe(gulp.dest('packages/admin/public/css/'));
//          .pipe(browserSync.stream({
//            match: "**/*.css"
//          }));
});

gulp.task('admin:compile:scss', function (a) {
  return gulp.src("packages/admin/html/**/*.scss")
          .pipe(sourcemaps.init())
          .pipe(sass({
            outputStyle: 'compressed'
          }).on('error', sass.logError))
          .pipe(rename(function (path) {
            //path.dirname += "/";
          }))
          //.pipe(sourcemaps.write())
          .pipe(gulp.dest('packages/admin/html/'))
          .pipe(browserSync.stream({
            match: "**/*.css"
          }));
});

gulp.task('watch-webroot-templates', [
  'compile:scss'
], function () {
  gulp.watch('packages/rm/public/templates/**/scss/*.scss', [
    'compile:scss'
  ]).on('change', function () {
    browserSync.reload();
  });
});

gulp.task('style:admin', [
  'admin:compile:scss', 'admin:compile:base:scss'
], function () {
  gulp.watch('packages/admin/**/*.scss', [
    'admin:compile:scss', 'admin:compile:base:scss'
  ]).on('change', function () {
    browserSync.reload();
  });
});

gulp.task('clean-up-admin', function () {
  return gulp.src('packages/admin/public/css/base.css')
          .pipe(uncss({
            html: ['packages/admin/html/**/*.php', 'http://127.0.0.1:8000/EverythingWidget/~admin/']
          }))
          .pipe(gulp.dest('packages/admin/public/css/min'));
});

gulp.task('ser-dev-backend', [
  'watch-webroot-templates'
], function () {
  browserSync.init({
    port: 5555,
    proxy: 'localhost:8000/EverythingWidget',
    logFileChanges: true
  });

  gulp.watch('**/*.php').on('change', function () {
    browserSync.reload();
  });
});

var mocha = require('gulp-mocha');

gulp.task('test-mochas', function () {
  return gulp.src('packages/*/html/**/*.mocha.js', {read: false})
          // gulp-mocha needs filepaths so you can't have any plugins before it 
          .pipe(mocha());
});

gulp.task('watch-and-test', ['test-mochas'], function () {
  gulp.watch('packages/*/html/**/*.js', ['test-mochas']);
});

// ------ Sart Developing ------ //

gulp.task('script:system.js', function (cb) {
  return pump([
    gulp.src([
      'packages/admin/public/js/system/system.js',
      'packages/admin/public/js/system/system-ui.js',
      'packages/admin/public/js/system/system-module.js',
      'packages/admin/public/js/system/system-domain.js',
      'packages/admin/public/js/system/system-tags.js',
      'packages/admin/public/js/system/system-animations.js'
    ]),
    concat('build.js'),
    minify({
      mangle: false
    }),
    gulp.dest('packages/admin/public/js/system/build/')
  ]);
});

gulp.task('script:ew-elements.js', function (cb) {
  return pump([
    gulp.src(['packages/admin/public/js/ew-elements/*.js']),
    include(),
    concat('build.js'),
    minify({
      mangle: false
    }),
    gulp.dest('packages/admin/public/js/ew-elements/build/')
  ]);
});

gulp.task('Start Developing Admin', ['script:system.js', 'script:ew-elements.js', 'style:admin'], function () {
  gulp.watch(['packages/admin/public/js/**/*.*', '!packages/admin/public/js/**/build/*.*'], ['script:system.js', 'script:ew-elements.js']);
});
