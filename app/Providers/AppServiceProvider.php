<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        app()->singleton('urlToDocs', function () {
            return env('APP_URL') . '/docs/api-docs.json';
        });

        app()->singleton('swaggeruibundle', function () {
            return env('APP_URL') . '/docs/asset/swagger-ui-bundle.js';
        });

        app()->singleton('swaggeruistandalonepreset', function () {
            return env('APP_URL') . '/docs/asset/swagger-ui-standalone-preset.js';
        });

        app()->singleton('swagger-ui', function () {
            return env('APP_URL') . '/docs/asset/swagger-ui.css';
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
