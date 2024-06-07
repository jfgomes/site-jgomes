<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ElasticsearchService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

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

            $exists = $this->elasticsearch->getClient()->indices()
                ->exists(['index' => $index]);

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

                $this->elasticsearch->getClient()->indices()
                    ->create($params);
            }
        } catch (\Exception $e)
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
        // Number of items per page
        $limit = $request->input('limit', 10);

        // Page number
        $page  = $request->input('page', 1);

        // Offset calculation
        $offset  = ($page - 1) * $limit;

        $query = User::query();

        // If there is a search by name
        if ($request->has('query'))
        {
            $search = $request->input('query');
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
        }

        // Sorting ( Default sorting field )
        $sortField = $request->input('sortField', 'created_at');

        // ( Default sorting order )
        $sortOrder = $request->input('sortOrder', 'asc');

        $query->orderBy($sortField, $sortOrder);

        // Total number of records without applying pagination
        $total = $query->count();

        // Apply pagination and offset
        $users = $query->offset($offset)->limit($limit)->get();

        $user  = $request->user(); // this needs to be cached!
        return response()->json([
            'result'  => compact('user'),
            'data' =>
            [
                'source' => 'db',
                'users'  => $users,
                'total'  => $total
            ]
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getEs(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'query'     => 'nullable|string',
                'page'      => 'nullable|integer|min:1',
                'limit'     => 'nullable|integer|min:1|max:100',
                // Field to sort by
                'sortField' => 'nullable|string',
                // Sorting order: 'asc' for ascending, 'desc' for descending
                'sortOrder' => 'nullable|in:asc,desc',

            ]);

            $query     = $request->input('query');
            $page      = $request->input('page', 1);
            $limit     = $request->input('limit', 10);
            $offset    = ($page - 1) * $limit;
            // Default field for sorting
            $sortField = $request->input('sortField', 'created_at');
            // Default field for sorting
            $sortOrder = $request->input('sortOrder', 'asc');

            // Set the query
            $params = [
                'index' => 'users',
                'body'  => [
                    'from' => $offset,
                    'size' => $limit,
                    'query' => $query ? [ // If there is a query, use it
                        'bool' => [
                            'should' => [
                                [
                                    'match_phrase' => [ // Use full text matching
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
                    ] : [ // If there is no query, match all documents
                        'match_all' => (object) []
                    ],
                    'sort' => [ // Sorting parameters
                        [
                            $sortField =>
                                [
                                    'order' => $sortOrder
                                ]
                        ] // Sort by the specified field in the specified order
                    ]
                ]
            ];

            // Perform the search
            $results = $this->elasticsearch->getClient()->search($params);

            // Map the results to the desired format
            $formattedResults = [];
            foreach ($results['hits']['hits'] as $hit)
            {
                $formattedResults[] = [
                    'id'         => $hit['_id'],
                    'name'       => $hit['_source']['name'],
                    'email'      => $hit['_source']['email'],
                    'created_at' => $hit['_source']['created_at'],
                    'updated_at' => $hit['_source']['updated_at'],
                    'role'       => $hit['_source']['role']
                ];
            }

            $user  = $request->user(); // this needs to be cached!
            return response()->json([
                'result'  => compact('user'),
                'data' =>
                    [
                        'source' => 'es',
                        'users'  => $formattedResults,
                        'total'  => null
                    ]
            ]);
        }
        catch (\Exception $e)
        {
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
            'role'     => 'nullable|string|max:255',
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
            'name'  => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role'  => 'nullable|string|max:255',
        ]);

        // Update user data
        $user->update([
            'name'  => $validatedData['name'],
            'email' => $validatedData['email'],
            'role'  => $validatedData['role'],
        ]);

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
