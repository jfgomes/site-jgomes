<?php

namespace App\Http\Controllers;

use Elastic\Elasticsearch\Exception\AuthenticationException;
use Illuminate\Http\Request;
use Elastic\Elasticsearch\ClientBuilder;

class SearchController extends Controller
{
    protected \Elastic\Elasticsearch\Client $elasticsearch;

    /**
     * @throws AuthenticationException
     */
    public function __construct()
    {
        $host = env('ELASTICSEARCH_HOST');
        $port = env('ELASTICSEARCH_PORT');
        $username = env('ELASTICSEARCH_USERNAME');
        $password = env('ELASTICSEARCH_PASS');

        $this->elasticsearch = ClientBuilder::create()
            ->setHosts(['localhost:' . env('ELASTICSEARCH_PORT')])
            ->setBasicAuthentication($username, $password)
            ->build();
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
                                'name' => [
                                    'type' => 'text'
                                ],
                                'email' => [
                                    'type' => 'text'
                                ],
                                'role' => [
                                    'type' => 'text'
                                ],
                                'created_at' => [
                                    'type' => 'date'
                                ],
                                'updated_at' => [
                                    'type' => 'date'
                                ]
                            ]
                        ]
                    ]
                ];

                $this->elasticsearch->indices()->create($params);
            }
        } catch (\Exception $e) {
            throw new \RuntimeException('Erro ao verificar ou criar o índice: ' . $e->getMessage());
        }
    }


    public function search(Request $request): \Illuminate\Http\JsonResponse
    {
        try {

        // Validação para garantir que 'query' não é null
        $request->validate([
            'query' => 'required|string'
        ]);

        $query = $request->input('query');

            // Verificar se o índice existe e criar se não existir
            $this->ensureIndexExists('users');

            $params = [
            'index' => 'users',
            'body'  => [
                'query' => [
                    'multi_match' => [
                        'query'  => '*',
                        'fields' => ['title', 'role']
                    ]
                ]
            ]
        ];


            $results = $this->elasticsearch->search($params);
            return response()->json($results->asArray());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
