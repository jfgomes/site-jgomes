![Site down Logo](https://jgomes.site/images/cs/cookie-editor.jpg)

## Introduction

- This project has some routes that I don´t want to be available in production for users.

- One of them is to disable the site ( /deactivate ), and the other is to put the site back up and running ( /activate ).

- However, what is intended is to have the possibility of having the site down for all users except those who have a special development cookie, to keep the site navigable for only these users. This is used by developers and product owners since they, during a rollout, intend to see the site before opening it to the public.

## How to do

#### 1) Create a new middleware to in case the site 'isDownForMaintenance()', look for the special this cookie on the browser ( this is set on the .env file ) and case it exists, allow the user to navigate..

```
    <?php
    
    namespace App\Http\Middleware;
    
    use Closure;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Cookie;
    
    class CheckAllowedVisibilityMaintenance
    {
        public function handle(Request $request, Closure $next)
        {
            // Check if the application is in maintenance mode
            if (app()->isDownForMaintenance())
            {
                // Checks if allowed
                if ($this->isAllowed())
                {
                    return $next($request);
                }
    
                // Returns a custom response to indicate that the application is under maintenance
                $message = 'The application is undergoing maintenance!';
                return response()->view('maintenance', ['message' => $message], 503);
            }
    
            return $next($request);
        }
    
        private function isAllowed(): bool
        {
            // Check if is allowed
            $conditionalFlag = env('APP_ROUTE_COOKIE_FLAG');
            return ($conditionalFlag && Cookie::has($conditionalFlag));
        }
    }
``` 

#### 2) Added this class to the Kernel file on the '$middleware' list

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
       
        \App\Http\Middleware\CheckAllowedVisibilityMaintenance::class,  <--- ADDED HERE ####
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
            \Illuminate\Routing\Middleware\ThrottleRequests::class,
        ],

        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    ];
}
```

#### 3) Added also the following code into the frontend to alert the developers that the site is under Maintenance

```
@if(app()->isDownForMaintenance())
    <div class="maintenance-mode">
        <p>The application is currently under maintenance! <br /> Is only visible for you and all the other users have the application under maintenance mode page.</p>
    </div>
@endif
```
## Requirements

- Firefox
- Cookie-editor extension
- Have the special cookie

## How to set up the cookie editor

- Install the Cookie Editor extension:

![Cookie Editor Extension](  https://jgomes.site/images/cs/cookie-protection-extension.png)

- Protect the routes in the Laravel routes file and set the cookie in the env file:

![cookie-protection-routes.png](  https://jgomes.site/images/cs/cookie-protection-routes.png)

- Set the cookie in the .env file:

![cookie-protection-env.png](  https://jgomes.site/images/cs/cookie-protection-env.png)

- Route to deactivate

```
    Route::get('/deactivate',
        [
            MaintenanceController::class, 'deactivate'
        ]
    )->name('deactivate');

```

- Route to activate

```
    Route::get('/activate',
        [
            MaintenanceController::class, 'activate'
        ]
    )->name('activate');

```
## How it works

- Protected deactivate route WITHOUT the defined cookie:

![cookie-protection-deny.png](  https://jgomes.site/images/cs/route-access-no-cookie.png)

- Protected deactivate route WITH the defined cookie:

![cookie-protection-allow.png](  https://jgomes.site/images/cs/route-access-with-cookie.png)

- Users WITH special route will see:

![cookie-protection-allow.png](  https://jgomes.site/images/cs/users-with-cookie.png)

- Users WITHOUT special route will see:

![cookie-protection-allow.png](  https://jgomes.site/images/cs/users-without-cookie.png)

- Protected activate site WITH the defined cookie:

![cookie-protection-allow.png](  https://jgomes.site/images/cs/route-access-with-cookie.png)

- Site up and running for all:

![cookie-protection-allow.png](  https://jgomes.site/images/cs/all-users-back-to-normal.png)

## Logical diagram

![Cookie Protection diagram](  https://jgomes.site/images/diagrams/cookie.drawio.png)

## Demonstration
#### ( Click on the image to watch the video )
[![Demonstration video](  https://jgomes.site/images/cs/git-branch-protection-video-thumbnail.jpg)](http://www.youtube.com/watch?v=2uEk8lHgCHk)
