/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var gulp = require('gulp');

var connect = require('gulp-connect-php'),
  path = require('path'),
  browserSync = require('browser-sync');

gulp.task('default', function () {
  // place code for your default task here
});

gulp.task('watch-webroot-templates', function () { 
  gulp.watch('packages/rm/public/templates/**/*.css').on('change', function () {
    //browserSync.reload();
  });
});

gulp.task('start-developing',['watch-webroot-templates'], function () {
//  browserSync.init({
//    port: 80,
//    proxy: 'localhost/EverythingWidget'
//  });
//
//  gulp.watch('**/*.php').on('change', function () {
//    browserSync.reload();
//  });
});