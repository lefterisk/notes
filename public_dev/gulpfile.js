//GUlp components
var gulp        = require('gulp'),
    concat      = require('gulp-concat'),      //concats files
    uglify      = require('gulp-uglify'),      //minifies files
    ngAnnotate  = require('gulp-ng-annotate'), //annotates (angular brakes without this)
    runSequence = require('run-sequence'),     //runs gulp tasks in sequence (tasks as array)
    sass        = require('gulp-sass'),        //sass compiler
    sourcemaps  = require('gulp-sourcemaps');  //creates sourcemaps for debugging


/**
 * IF GULP IS TYPED WITH NO OTHER ARGUMENTS THIS WILL BE EXECUTED
 */
gulp.task('default', function() {
    runSequence(
        //everything in square brackets executes in parallel
        //to force sequential execution put your task outside brackets
        [
            'vendor-js',
            'vendor-css',
            'app-js',
            'app-scss'
        ]
    );
});

/**
 * START ALL THE WATCHERS
 */
gulp.task('watch-init', function() {
    runSequence(
        //everything in square brackets executes in parallel
        //to force sequential execution put your task outside brackets
        [
            'watch-app-js',
            'watch-app-css'
        ]
    );
});

/**
 * JAVASCRIPT RELATED TASKS
 */
//Concat & compress vendor files
gulp.task('vendor-js', function () {
    gulp.src(
        [
            'bower_components/angular/angular.min.js',
            'bower_components/angular-animate/angular-animate.min.js',
            'bower_components/angular-aria/angular-aria.min.js',
            'bower_components/angular-cookies/angular-cookies.min.js',
            'bower_components/angular-http-auth/angular-http-auth.min.js',
            'bower_components/angular-loader/angular-loader.min.js',
            'bower_components/angular-messages/angular-messages.min.js',
            'bower_components/angular-resource/angular-resource.min.js',
            'bower_components/angular-sanitize/angular-sanitize.min.js',
            'bower_components/angular-touch/angular-touch.min.js',
            'bower_components/angular-ui-router/release/angular-ui-router.min.js',
            'bower_components/angular-http-auth/src/http-auth-interceptor.js',
            //bootstrap components translated as angular directives (include only the ones we need)
            'bower_components/angular-bootstrap/ui-bootstrap.min.js',
            'bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js',

            'bower_components/angular-toastr/dist/angular-toastr.min.js',
            'bower_components/angular-toastr/dist/angular-toastr.tpls.min.js',

            'bower_components/angular-loading-bar/build/loading-bar.min.js'
        ]
    )
        .pipe(concat('vendor.min.js'))
        .pipe(ngAnnotate())
        .pipe(uglify())
        .pipe(gulp.dest('../public/js'));
});

//Concat & compress application specific files
gulp.task('app-js', function () {
    gulp.src(['js/**/*.js'])
        .pipe(concat('app.min.js'))
        .pipe(ngAnnotate())
        .pipe(uglify())
        .pipe(gulp.dest('../public/js'));
});

//File watcher: if an app specific js is edited run concat compressor again
gulp.task('watch-app-js', ['app-js'], function () {
    gulp.watch('js/**/*.js', ['app-js']);
});

/**
 * END OF JAVASCRIPT RELATED TASKS
 */

/**
 * CSS RELATED TASKS
 */

//compile app specific scss to css
gulp.task('app-scss', function() {
    gulp.src('scss/*.scss')
        .pipe(sourcemaps.init())
        //The onError handler prevents Gulp from crashing when you make a mistake in your SASS
        //compile & compress
        .pipe(sass({onError: function(e) { console.log(e); }, outputStyle: 'compressed'}))
        //make source-maps but put them in a different folder & not include content
        .pipe(sourcemaps.write('maps', {includeContent: false}))
        .pipe(gulp.dest('../public/css'));
});

//watch app specific scss
gulp.task('watch-app-css', ['app-scss'], function () {
    gulp.watch('scss/*.scss', ['app-scss']);
});

//concat vendor css files MUST BE MINIFIED AT THIS STAGE
gulp.task('vendor-css', function() {

    gulp.src(
        [
            'bower_components/animate.css/animate.min.css',
            'bower_components/angular-toastr/dist/angular-toastr.min.css',
            'bower_components/angular-loading-bar/build/loading-bar.min.css',
        ]
    )
        .pipe(concat('vendor.min.css'))
        .pipe(gulp.dest('../public/css/'));
});
/**
 * END OF CSS RELATED TASKS
 */
