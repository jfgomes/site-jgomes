<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\MessagesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Rate limit on ( 5 post requests per 5 min )
Route::middleware('throttle:5,5')->group(function () {
    Route::post('/send',
        [
            MessagesController::class, 'send'
        ]
    )->name('send');
});

Route::prefix('v1')->group(function () {

    // Allow 10 tries to log in per min
    Route::middleware('throttle:10,1')->group(function () {
        Route::post('/login',
            [
                AuthController::class, 'login'
            ]
        )->name('login');
    });

    // Protected routes by Sanctum
    Route::middleware('auth:sanctum')->group(function ()
    {
        // Allow a margin of 3 logouts per min as it should run once a time
        Route::middleware('throttle:3,1')->group(function () {
            Route::post('/logout',
                [
                    AuthController::class, 'logout'
                ]
            )->name('logout');
        });

        // Allow a margin of 5 refresh per min, as it only suppose to run rarely
        Route::middleware('throttle:5,1')->group(function () {
            Route::post('/refresh',
                [
                    AuthController::class, 'refresh'
                ]
            )->name('refresh');
        });

        // Allow 5 refresh per min, as it will be cached
        Route::middleware('throttle:5,1')->group(function () {
            Route::post('/user',
                [
                    AuthController::class, 'user'
                ]
            )->name('user');
        });

        // Check if user is authenticated...
        // This route will be cached as well... No need more than 1 non cached access per minute
        Route::get('/check',
            [
                AuthController::class, 'check'
            ]
        )->name('check');

        // Private home page. Let's allow 30 accesses per min
        Route::middleware('throttle:30,1')->group(function () {
            Route::get('/home',
                [
                    HomeController::class, 'index'
                ]
            )->name('home.index');
        });

        // Private map+cache page. Let's allow 100 accesses per min
        Route::middleware('throttle:100,1')->group(function () {
            Route::get('/api-map-caches',
                [
                    MapController::class, 'testMapCaches'
                ]
            )->name('map.test');
        });

        // Allow only admin. But let's define 30 request per min
        Route::middleware(['checkRole:admin', 'throttle:30,1'])->group(function () {
            Route::get('/admin',
                [
                    AdminController::class, 'index'
                ]
            )->name('admin');
        });
    });

    // Middleware de fallback to return 401 for unauthenticated requests
    Route::fallback(function () {
        return response()->json(['error' => 'Unauthorized'], 401);
    });
});

