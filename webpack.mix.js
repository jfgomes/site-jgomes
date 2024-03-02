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
    // local - public
    .js([
        'public/js/local/public/init.js'

    // prod output - public
    ], 'public/js/prod/public/app.js') // Combine all public area js into prod/public/app.js

    // local - private
    .js([
        'public/js/local/private/serverLessRequests.js'

    // prod output - private
    ],'public/js/prod/private/app.js') // Combine all private area js into prod/private/app.js

    .version();
