<?php
namespace App\Services;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;

class ElasticsearchService
{
    protected \Elastic\Elasticsearch\Client $client;

    /**
     * @throws AuthenticationException
     */
    public function __construct()
    {
        $host     = env('ELASTICSEARCH_HOST');
        $port     = env('ELASTICSEARCH_PORT');
        $username = env('ELASTICSEARCH_USERNAME');
        $password = env('ELASTICSEARCH_PASSWORD');

        $this->client = ClientBuilder::create()
            ->setHosts(["$host:$port"])
            ->setBasicAuthentication($username, $password)
            ->build();
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }
}
