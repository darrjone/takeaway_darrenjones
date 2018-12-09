var gulp        = require('gulp');
var browserSync = require('browser-sync').create();
var sass        = require('gulp-sass');
var concat      = require('gulp-concat');

// Compile our sass into CSS & auto-inject into browsers
gulp.task('sass', function() {
    return gulp.src(['web/assets/scss/*.scss'])
        .pipe(sass())
        .pipe(concat("style.css"))
        .pipe(gulp.dest("web/assets/css"))
        .pipe(browserSync.stream())
});

// Compile sass vendors into CSS & auto-inject into browsers
gulp.task('vendors-css', function() {
    return gulp.src(['node_modules/bootstrap/scss/bootstrap.scss'])
        .pipe(sass())
        .pipe(concat("vendors.css"))
        .pipe(gulp.dest("web/assets/css"))
        .pipe(browserSync.stream())
});

// Move the javascript files into our /web/assets/js folder
gulp.task('vendors-js', function() {
    return gulp.src(['node_modules/jquery/dist/jquery.min.js', 'node_modules/tether/dist/js/tether.min.js', 'node_modules/bootstrap/dist/js/bootstrap.min.js'])
        .pipe(concat("vendors.js"))
        .pipe(gulp.dest("web/assets/js"))
        .pipe(browserSync.stream());
});

// Static Server + watching scss files
gulp.task('watch', ['sass'], function() {
    gulp.watch(['web/assets/scss/*.scss'], ['sass']);
});

gulp.task('default', ['vendors-js', 'vendors-css', 'watch']);