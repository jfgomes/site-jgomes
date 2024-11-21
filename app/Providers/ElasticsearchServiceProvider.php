<?php

namespace App\Providers;

use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;

class ElasticsearchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Elasticsearch', function ($app) {
            $host     = env('ELASTICSEARCH_HOST');
            $port     = env('ELASTICSEARCH_PORT');
            $username = env('ELASTICSEARCH_USERNAME');
            $password = env('ELASTICSEARCH_PASSWORD');

            $hosts = [
                [
                    'host'   => $host,
                    'port'   => $port,
                    'scheme' => 'http',
                    'user'   => $username,
                    'pass'   => $password
                ]
            ];

            return ClientBuilder::create()
                ->setHosts($hosts)
                ->setBasicAuthentication($username, $password)
                ->build();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
