<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\I18nController;
use App\Http\Controllers\LocationsController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

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
    Route::middleware('throttle:100,1')->group(function () {
        Route::post('/login',
            [
                AuthController::class, 'login'
            ]
        )->name('login');
    });

    // Allow 10 requests per min
    Route::middleware('throttle:20,1')->group(function () {
        Route::get('/users-count', function ()
        {
            return DB::table('users')->count();
        });
    });

    // Allow 10 request per min
    Route::middleware('throttle:20,1')->group(function () {
        Route::get('/locations-count', function ()
        {
            return DB::table('locations_pt')->count();
        });
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
            Route::get('/locations',
                [
                    LocationsController::class, 'getLocations'
                ]
            )->name('locations.test');
        });

        // Allow only admin. But let's define 30 request per min
        Route::middleware(['checkRole:admin', 'throttle:30,1'])->group(function () {
            Route::get('/admin',
                [
                    AdminController::class, 'index'
                ]
            )->name('admin');
        });

        // Allow only admin. But let's define 30 request per min
        Route::middleware(['checkRole:admin', 'throttle:30,1'])->group(function () {
            Route::get('/translations',
                [
                    I18nController::class, 'getTranslations'
                ]
            )->name('translations');
        });

        // Allow only admin. But let's define 30 request per min
        Route::middleware(['checkRole:admin', 'throttle:30,1'])->group(function () {
            Route::post('/translations',
                [
                    I18nController::class, 'postTranslations'
                ]
            )->name('translations');
        });


        // Allow only admin. Users list. Let's allow for now 30 accesses per min per user.
        Route::middleware(['checkRole:admin', 'throttle:30,1'])->group(function ()  {

            Route::get('/users',
                [
                    UsersController::class, 'get'
                ]
            )->name('get-users');
        });

        // Allow only admin. Users list. Let's allow for now 30 accesses per min per user.
        Route::middleware(['checkRole:admin', 'throttle:100,1'])->group(function ()  {

            Route::get('/users-es',
                [
                    UsersController::class, 'getEs'
                ]
            )->name('get-users-es');
        });

        // Allow only admin. User post. Let's allow for now 5 posts per min per user.
        Route::middleware(['checkRole:admin', 'throttle:30,1'])->group(function ()  {

            Route::post('/users',
                [
                    UsersController::class, 'post'
                ]
            )->name('post-user');
        });

        // Allow only admin. User put. Let's allow for now 10 posts per min per user.
        Route::middleware(['checkRole:admin', 'throttle:30,1'])->group(function ()  {

            Route::put('/users/{id}',
                [
                    UsersController::class, 'put'
                ]
            )->name('put-user');
        });

        // Allow only admin. User put. Let's allow for now 10 deletes per min per user.
        Route::middleware(['checkRole:admin', 'throttle:50,1'])->group(function ()  {

            Route::delete('/users/{id}',
                [
                    UsersController::class, 'delete'
                ]
            )->name('delete-user');
        });
    });

    // ES search Let's allow for now 100 accesses per min
    Route::get('/search',
        [
            SearchController::class, 'search'
        ]
    )->name('search');

    // Middleware de fallback to return 401 for unauthenticated requests
    Route::fallback(function () {
        return response()->json(['error' => 'Unauthorized'], 401);
    });
});

