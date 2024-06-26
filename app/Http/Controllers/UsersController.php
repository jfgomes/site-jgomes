<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ElasticsearchService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UsersController extends Controller
{
    protected ElasticsearchService $elasticsearch;

    public function __construct(ElasticsearchService $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;

        // Check if the index exists and create it if it doesn't
        $this->ensureIndexExists('users');
    }

    /**
     * @param string $index
     * @return void
     */
    protected function ensureIndexExists(string $index): void
    {
        try {

            $exists = $this->elasticsearch
                ->getClient()
                ->indices()
                ->exists(['index' => $index]);

            if (!$exists->asBool())
            {
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
                                    'type' => 'keyword'
                                ],
                                'email' => [
                                    'type' => 'keyword'
                                ],
                                'role' => [
                                    'type' => 'keyword'
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

                $this->elasticsearch
                    ->getClient()
                    ->indices()
                    ->create($params);
            }
        }
        catch (\Exception $e)
        {
            throw new \RuntimeException('Error checking or creating index: ' . $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json([
            'result'  => compact('user')
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get(Request $request): JsonResponse
    {
        try {

            // Validate the request parameters
            $request->validate([
                'query'     => 'nullable|string',
                'page'      => 'nullable|integer|min:1',
                'limit'     => 'nullable|integer|min:1|max:100',
                'sortField' => 'nullable|string',
                'sortOrder' => 'nullable|in:asc,desc',
                'draw'      => 'nullable|integer'
            ]);

            // DataTables specific parameters
            $draw = $request->input('draw');

            // Number of items per page
            $limit = $request->input('limit', 10);

            // Page number
            $page  = $request->input('page', 1);

            // Offset calculation
            $offset  = ($page - 1) * $limit;

            // Create a query builder instance
            $query = User::query();

            // If there is a search query
            if ($request->has('query')) {
                $search = $request->input('query');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('role', 'like', '%' . $search . '%');
                });
            }

            // Sorting (Default sorting field)
            $sortField = $request->input('sortField', 'created_at');

            // Default sorting order
            $sortOrder = $request->input('sortOrder', 'asc');

            $query->orderBy($sortField, $sortOrder);

            // Total number of records without applying pagination
            $recordsTotal = User::count();

            // Total number of filtered records
            $recordsFiltered = $query->count();

            // Apply pagination and offset
            $users = $query->offset($offset)
                ->limit($limit)
                ->get();

            // Get the authenticated user
            $user = $request->user(); // This needs to be cached!

            return response()->json([
                'draw'            => intval($draw),
                'recordsTotal'    => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $users,
                'result'          => compact('user'),
                'source'          => 'db'
            ]);
        } catch (\Exception $e)
        {
            Log::error('Error in get function:',
                [
                    'error' => $e->getMessage()
                ]
            );
            return response()
                ->json(
                    [
                        'error' => $e->getMessage()
                    ],
                    500
                );
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getEs(Request $request): JsonResponse
    {
        try
        {
            // Validate the request parameters
            $request->validate([
                'query'     => 'nullable|string',
                'page'      => 'nullable|integer|min:1',
                'limit'     => 'nullable|integer|min:1|max:100',
                'sortField' => 'nullable|string',
                'sortOrder' => 'nullable|in:asc,desc',
            ]);

            // Get request parameters
            $query     = $request->input('query', null);
            $page      = $request->input('page', 1);
            $limit     = $request->input('limit', 10);
            $offset    = ($page - 1) * $limit;
            $sortField = $request->input('sortField', 'created_at');
            $sortOrder = $request->input('sortOrder', 'asc');
            $draw      = $request->input('draw');

            // Prepare search filter case filter not empty
            $queryBody = !empty($query) ? [
                'bool' => [
                    'should' => [
                          ['wildcard' => ['email' => "*$query*"]],
                          ['wildcard' => ['name'  => "*$query*"]],
                          ['wildcard' => ['role'  => "*$query*"]],
                    ]
                ]
            ] : ['match_all' => (object)[]];

            // Prepare ES Obj
            $params = [
                'index' => 'users',
                'body'  => [
                    'from'  => $offset,
                    'size'  => $limit,
                    'query' => $queryBody,
                    'sort'  => [
                        [
                            $sortField => ['order' => $sortOrder]
                        ]
                    ]
                ]
            ];

            // Get ES results based on ES Obj
            $results = $this->elasticsearch
                ->getClient()
                ->search($params);

            // Prepare frontend obj based on ES results
            $formattedResults = array_map(function($hit) {
                return [
                    'id'         => $hit['_id'],
                    'name'       => $hit['_source']['name'],
                    'email'      => $hit['_source']['email'],
                    'created_at' => $hit['_source']['created_at'],
                    'updated_at' => $hit['_source']['updated_at'],
                    'role'       => $hit['_source']['role']
                ];
            }, $results['hits']['hits']);

            // Get user (need to cache this info)
            $user = $request->user();

            // Get total results
            $recordsTotal = $this->elasticsearch
                ->getClient()
                ->count(
                    [
                        'index' => 'users'
                    ]
                )['count'];

            $recordsFiltered = $results['hits']['total']['value'];

            // Create data obj to be return to frontend
            return response()->json([
                'result'          => compact('user'),
                'source'          => 'es',
                'draw'            => intval($draw),
                'recordsTotal'    => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $formattedResults
            ]);

        }
        catch (\Exception $e)
        {
            Log::error('Error in getEs function:',
                [
                    'error' => $e->getMessage()
                ]
            );
            return response()
                ->json(
                    [
                        'error' => $e->getMessage()
                    ],
                    500
                );
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function post(Request $request): JsonResponse
    {
        // Request data validation
        $validatedData = $request->validate([
            'name'     => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'role'     => 'required|string|max:255',
        ]);

        // Create the new user
        $user = User::create([
            'name'     => $validatedData['name'],
            'password' => Hash::make($validatedData['password']),
            'email'    => $validatedData['email'],
            'role'     => $validatedData['role'],
        ]);

        // Return a JSON response with the newly created user
        return response()->json([
            'message' => 'User created successfully',
            'user'    => $user,
        ], 201); // 201 means the resource was successfully created
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function put(Request $request, $id): JsonResponse
    {
        // Find the user with the provided ID
        $user = User::findOrFail($id);

        // Request data validation
        $validatedData = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role'     => 'required|string|max:255',
            'password' => 'max:255'
        ]);

        // Prepare the update data
        $updateData = [
            'name'  => $validatedData['name'],
            'email' => $validatedData['email'],
            'role'  => $validatedData['role'],
        ];

        // Check if the password is provided and not null
        if (!empty($validatedData['password']))
        {
            $updateData['password'] = bcrypt($validatedData['password']); // Hash the password before saving
        }

        // Update user data
        $user->update($updateData);

        // Return a JSON response with a message indicating the user was successfully updated
        return response()->json([
            'message' => 'User updated successfully',
            'user'    => $user,
        ], 200); // 200 means the resource was successfully updated
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function delete(Request $request, $id): JsonResponse
    {
        // Find the user with the provided ID
        $user = User::findOrFail($id);

        // Delete the user
        $user->delete();

        // Return a JSON response with a message indicating the user was successfully deleted
        return response()->json([
            'message' => 'User deleted successfully',
            'user'    => $user,
        ], 204); // 204 means the resource was successfully deleted
    }
}
