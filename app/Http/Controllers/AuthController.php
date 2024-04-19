<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/v1/login",
     * summary="Sign in via api",
     * description="Login by username email and password",
     * operationId="authLoginApi",
     * tags={"Authorization"},
     * @OA\RequestBody(
     *    required=true,
     *    description="User authentication",
     *    @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", format="text", example="test@test.test"),
     *       @OA\Property(property="password", type="string", format="text", example="Test@123"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response - Password is invalid",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, wrong password. Please try again")
     *        )
     *     )
     * )
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials))
        {
            // Create token
            $accessToken = Auth::user()
                ->createToken('MyApp')
                ->plainTextToken;

            return response()->json([
                'access_token'  => $accessToken
            ]);
        }

        // Invalid credentials
        return response()->json(
            [
                'error' => 'Unauthorized'
            ],
            401
        );
    }

    /**
     * @OA\Post(
     * path="/api/v1/logout",
     * summary="Logout",
     * security={{ "apiAuth": {} }},
     * description="Logout",
     * operationId="Logout",
     * tags={"Authorization"},
     * @OA\Response(
     *    response=401,
     *    description="Not authenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Need to the login first.")
     *        )
     *     )
     *   )
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        // Revoke all tokens
        $request->user()->tokens()
            ->delete();

        return response()->json(
            [
                'message' => 'Successfully logged out'
            ]
        );
    }

    /**
     * @OA\Post(
     * path="/api/v1/refresh",
     * summary="Refresh token",
     * security={{ "apiAuth": {} }},
     * description="Refresh token",
     * operationId="RefreshToken",
     * tags={"Authorization"},
     * @OA\Response(
     *    response=401,
     *    description="Not authenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Need to the login first.")
     *        )
     *     )
     *   )
     * )
     */
    public function refresh(Request $request): JsonResponse
    {
        // Verifica se há um token de acesso atual
        $currentAccessToken = $request->user()->currentAccessToken();
        if ($currentAccessToken) {
            // Revoke all tokens except the current one
            $request->user()->tokens()
                ->where('id', '<>', $currentAccessToken->id)
                ->delete();
        }

        // Create a new token
        $accessToken = $request->user()
            ->createToken('MyApp')
            ->plainTextToken;

        return response()->json(['access_token' => $accessToken]);
    }

    /**
     * @OA\Post(
     * path="/api/v1/user",
     * summary="Get user info",
     * security={{ "apiAuth": {} }},
     * description="Get user info",
     * operationId="GetUserInfo",
     * tags={"Authorization"},
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Success.")
     *      )
     *    ),
     * @OA\Response(
     *    response=401,
     *    description="Unauthorized",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthorized.")
     *      )
     *    ),
     * @OA\Response(
     *    response=429,
     *    description="Too many requests",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Too many requests.")
     *        )
     *     )
     *   )
     * )
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    /**
     * @OA\Get(
     * path="/api/v1/check",
     * summary="Check if user is authenticated",
     * security={{ "apiAuth": {} }},
     * description="Check if user is authenticated",
     * operationId="CheckIfUserIsAuthenticated",
     * tags={"Authorization"},
     * @OA\Response(
     *    response=401,
     *    description="Not authenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Need to the login first.")
     *        )
     *     )
     *   )
     * )
     */
    public function check(Request $request): JsonResponse
    {
        $result = false;
        if (!is_null($request->user())) {
            $result = true;
        }
        return response()->json($result);
    }
}

