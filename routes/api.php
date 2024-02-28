<?php

use App\Http\Controllers\MessagesController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

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
    });
});

