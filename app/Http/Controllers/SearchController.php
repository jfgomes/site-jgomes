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
        $password = env('ELASTICSEARCH_PASSWORD');

        $this->elasticsearch = ClientBuilder::create()
            ->setHosts(["$host:$port"])
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
                            'number_of_replicas' => 0,
                            'analysis' => [
                                'analyzer' => [
                                    'email_analyzer' => [
                                        'type' => 'custom',
                                        'tokenizer' => 'uax_url_email'
                                    ]
                                ]
                            ]
                        ],
                        'mappings' => [
                            'properties' => [
                                'name' => [
                                    'type' => 'text'
                                ],
                                'email' => [
                                    'type' => 'text',
                                    'analyzer' => 'email_analyzer'
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

    /*
  ´
    protected function recreateIndex()
    {
        $indexName = 'users';

        // Verificar se o índice existe
        if ($this->elasticsearch->indices()->exists(['index' => $indexName])) {
            // Se o índice existir, excluí-lo
            $this->elasticsearch->indices()->delete(['index' => $indexName]);
        }

        $params = [
            'index' => $indexName,
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
                    'analysis' => [
                        'analyzer' => [
                            'email_analyzer' => [
                                'type' => 'custom',
                                'tokenizer' => 'uax_url_email'
                            ]
                        ]
                    ]
                ],
                'mappings' => [
                    'properties' => [
                        'name' => [
                            'type' => 'keyword'
                        ],
                        'email' => [
                            'type' => 'text',
                            'analyzer' => 'email_analyzer'
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
    } */

    public function search(Request $request)
    {
        try {
            $request->validate([
                'query' => 'nullable|string',
                'page' => 'nullable|integer|min:1',
                'perPage' => 'nullable|integer|min:1|max:100'
            ]);

            $query = $request->input('query');
            $page = $request->input('page', 1);
            $perPage = $request->input('perPage', 10);
            $offset = ($page - 1) * $perPage;

            // Verificar se o índice existe e criar se não existir
            $this->ensureIndexExists('users');

            // Definir a consulta
            $params = [
                'index' => 'users',
                'body'  => [
                    'from' => $offset,
                    'size' => $perPage,
                    'query' => $query ? [ // Se houver uma consulta, use-a
                        'bool' => [
                            'should' => [
                                [
                                    'match_phrase' => [
                                        'email' => $query
                                    ]
                                ],
                                [
                                    'match_phrase' => [
                                        'name' => $query
                                    ]
                                ],
                                [
                                    'multi_match' => [
                                        'query'  => $query,
                                        'fields' => ['role']
                                    ]
                                ]
                            ]
                        ]
                    ] : [ // Se não houver consulta, corresponder a todos os documentos
                        'match_all' => (object) []
                    ]
                ]
            ];

            // Realizar a pesquisa
            $results = $this->elasticsearch->search($params);
            return response()->json($results['hits']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


}
