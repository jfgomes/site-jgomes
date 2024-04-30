const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
    .styles([
    'public/css/local/private/flashMessages.css',
    'public/css/local/private/loadingOverlay.css',
    'public/css/local/private/login.css',
    'public/css/local/private/home.css',
    'public/css/local/private/general.css',
    'public/css/cookies.css',
    'public/css/animate.min.css',
    'public/css/leaflet.css',
    'public/css/local/private/locations.css',
    'public/css/local/private/lang.css',
], 'public/css/prod/app.css')
    .scripts([
    'public/js/jquery-3.7.1.js',
    'public/js/local/private/serverLessRequests.js',
    'public/js/cookies.js',
    'public/js/leaflet.min.js',
    'public/js/local/private/locations.js',
    'public/js/local/private/lang.js',
], 'public/js/prod/app.js')
    .version();
