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
], 'public/css/prod/app.css')
    .scripts([
    'public/js/jquery-3.7.1.js',
    'public/js/local/private/serverLessRequests.js'
], 'public/js/prod/app.js')
    .version();
