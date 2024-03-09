![Rate limit Logo](https://jgomes.site/images/cs/rate-limit.jpg)

## Introduction

- The objective of this case study is to control the number of accesses to API routes as a measure of access control and service security.

- This is useful and important for protecting the application from request overload and denial-of-service (DoS) attacks.

- Laravel provides native support for limiting access to application routes using throttle middleware.

- The goal is to create a limit of accesses to each route based on a weighting and an expected estimate of accesses to them. Whenever there are more accesses than expected, they are considered excess accesses and as such, we can assume that there may be an attempt of DoS, whether malicious or unintentional (for example, a crawler), which can also cause a DoS as well.

- Whenever the request limit for a specific route is exceeded, the user is temporarily prevented from accessing its result, receiving a status code 429.

- This control is done at the route file level.

## How to configure:
#### Ensure the kernel file is loading the throttle middleware
```
<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\CheckAllowedVisibilityMaintenance::class,
        \App\Http\Middleware\AccessLogRequests::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            
            
            
            \Illuminate\Routing\Middleware\ThrottleRequests::class, <------------- Ensure this!
            
            
            
        ],
        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];
}

```

## API routes file 
#### ( The comments in the file explain the expected behavior )
```
<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

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
    });
});

```

## How it works

- In Laravel, rate limiting is used to control the rate of requests that a user can make within a certain period of time.
- In this case study is defined that each request expires after 1 minute ( Look at the second param of, for instance, ' throttle:30,1 ' that represents 30 requests per 1 minute )

#### User identification: 
- Throttle is usually applied in conjunction with user authentication. Laravel identifies users based on their IP or authentication token, depending on the configuration.

#### Access control: 
- When a user makes a request to a route protected by throttle, Laravel checks if the rate limit has been reached. If the limit has not been reached, the request is allowed and the counter is incremented. If the limit has been reached, Laravel responds with an error code 429 ( Too Many Requests ).

#### Limit expiration: 
- The rate limit counter is reset after the configured expiration period. This means users can make new requests after the counter expires.

- In summary, throttle in Laravel allows to control the rate of requests from users, helping to protect the application against overload and attacks. 

- It is configured through middleware and can be customized according to the specific needs of the application.


## Demonstration
#### ( Click on the image to watch the video )
[![Demonstration video](https://jgomes.site/images/cs/git-branch-protection-video-thumbnail.jpg)](http://www.youtube.com/watch?v=UJc0J425O84)
