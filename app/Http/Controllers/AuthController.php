<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
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
     * @param Request $request
     * @return JsonResponse
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
     * @param Request $request
     * @return JsonResponse
     */
    public function refresh(Request $request): JsonResponse
    {
        // Revoke all tokens except the current one
        $request->user()->tokens()
            ->where('id', '<>', $request->user()->currentAccessToken()->id)
            ->delete();

        // Create a new token
        $accessToken = $request->user()
            ->createToken('MyApp')
            ->plainTextToken;

        return response()->json(
            ['access_token' => $accessToken]
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}

