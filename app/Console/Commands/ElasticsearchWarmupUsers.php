<?php

namespace App\Console\Commands;

use App\Models\User;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Illuminate\Console\Command;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticsearchWarmupUsers extends Command
{
    protected $signature = 'elasticsearch:warmupusers';
    protected $description = 'Popula o Elasticsearch com os users existentes';

    protected $elasticsearch;

    /**
     * @throws AuthenticationException
     */
    public function __construct()
    {
        parent::__construct();

        $host = env('ELASTICSEARCH_HOST');
        $port = env('ELASTICSEARCH_PORT');
        $username = env('ELASTICSEARCH_USERNAME');
        $password = env('ELASTICSEARCH_PASS');

        $this->elasticsearch = ClientBuilder::create()
            ->setHosts(["$host:$port"])
            ->setBasicAuthentication($username, $password)
            ->build();
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Exception
     */
    public function handle(): int
    {
        // Limpar o índice antes de inserir os dados
        $this->clearIndex('users');

        // Verificar se o índice existe e criar se não existir
        $this->ensureIndexExists('users');

        // Buscar dados do banco de dados
        $users = User::all();

        foreach ($users as $user) {
            // Select specific fields
            $attributes = $user->only(['name', 'email', 'role', 'created_at', 'updated_at']);


            // Convert the created_at field to ISO 8601 format
            if (isset($attributes['created_at'])) {
                $attributes['created_at'] = (new \DateTime($attributes['created_at']))->format('c'); // ISO 8601 format
            }

            // Convert the updated_at field to ISO 8601 format
            if (isset($attributes['updated_at'])) {
                $attributes['updated_at'] = (new \DateTime($attributes['updated_at']))->format('c'); // ISO 8601 format
            }

            $params = [
                'index' => 'users',
                'id'    => $user->getKey(),
                'body'  => $attributes,
            ];

            try {
                $this->elasticsearch->index($params);
            } catch (\Exception $e) {
                $this->error('Erro ao inserir no Elasticsearch: ' . $e->getMessage());
            }
        }

        $this->info('Dados inseridos no Elasticsearch com sucesso.');
        return 0;
    }

    /**
     * Verifica se o índice existe. Se não, cria-o.
     *
     * @param string $index
     * @return void
     */
    protected function ensureIndexExists(string $index): void
    {
        try {
            $exists = $this->elasticsearch->indices()->exists(['index' => $index]);

            if (!$exists->asBool()) {
                $params = [
                    'index' => $index,
                    'body' => [
                        'settings' => [
                            'number_of_shards' => 1,
                            'number_of_replicas' => 0
                        ],
                        'mappings' => [
                            'properties' => [
                                'title' => [
                                    'type' => 'text'
                                ],
                                'role' => [
                                    'type' => 'text'
                                ],
                            ]
                        ]
                    ]
                ];

                $this->elasticsearch->indices()->create($params);
            }
        } catch (\Elastic\Elasticsearch\Exception\ClientResponseException $e) {
            if ($e->getCode() != 404) {
                throw new \RuntimeException('Erro ao verificar ou criar o índice: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            throw new \RuntimeException('Erro ao verificar ou criar o índice: ' . $e->getMessage());
        }
    }


    /**
     * Limpa o índice especificado.
     *
     * @param string $index
     * @return void
     */
    protected function clearIndex(string $index): void
    {
        try {
            $this->elasticsearch->deleteByQuery([
                'index' => $index,
                'body' => [
                    'query' => [
                        'match_all' => (object) []
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            $this->error('Erro ao limpar o índice: ' . $e->getMessage());
        }
    }
}
