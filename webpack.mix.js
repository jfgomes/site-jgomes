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
    .js([
        // local - public
        'public/js/local/public/init.js',
        // local - private
        'public/js/local/private/serverLessRequests.js'
    // prod output - private
    ],'public/js/prod/private/app.js') // Combine all local js into prod/private/app.js

    .version();
