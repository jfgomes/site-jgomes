<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json([
            'result'  => compact('user')
        ]);
    }
}
