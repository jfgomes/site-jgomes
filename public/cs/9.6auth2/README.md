![Message Authentication ](https://jgomes.site/images/cs/Laravel-Sanctum-Authentication-logo.jpg)

## Introduction
- This is a solution for an authentication system.
- The idea is to have an API service that only returns data in JSON format (backend) and a serverless system that fetches the data from the API and presents the information to the user through the frontend.
- The chosen authentication system is Sanctum.
- The other option would be Passport, which provides a complete implementation of OAuth2. However, since we want to test a mixed authentication system (API + website sessions), and considering that Sanctum also manages tokens and can be used for APIs, the choice fell on the latter.
- There is a component where even though the user is authenticated, the display of certain pages is only for administrators.
- It was defined to store the access token in local storage because it provides a convenient and efficient method for client-side storage, allowing easy access and retrieval of the token for authentication purposes across different parts of the application without the need for server-side sessions. Additionally, local storage offers more storage space compared to cookies, and it is not sent with every HTTP request, reducing the risk of interception by malicious parties.
  
  ![local_storage.png](https://jgomes.site/images/cs/local_storage.png)

- All API routes have security measures such as rate limiting, where a maximum number of requests is defined. If the user exceeds this limit, the API returns 429 (Too Many Requests).
- In the frontend during development, all scripts and styles are loaded separately file by file. In production, all documents are compressed and minified into a single app.js and app.css file, resulting in only 2 loads because they are then cached.
- A JavaScript module with AJAX was developed to handle communication and request management with the API.
- It was defined the storage of scripts and styles are organized based on a definition of local/prod environment, where in the prod environment there is only one JS and one CSS document, and in dev all files are separated and not minified.
- It was also defined that style in the storage of scripts and styles within the previous step, are also inside a sub-category where it is also organized based on a definition of public/private, representing documents loaded in public and private areas.

  ![frontend_files.png](https://jgomes.site/images/cs/frontend_files_css.png)
  ![frontend_files.png](https://jgomes.site/images/cs/frontend_files_js.png)

- Users who are already authenticated with a valid token, if they reach the login page, are automatically redirected to the private area.
- A TTL (Time-to-Live) was defined in the frontend to renew the access token.
- A TTL in the API side is a development in progress to renew the access token, as Sanctum don´t has a native way to renew the access_tokens.

## API diagram overview:
![Authorization.png](https://jgomes.site/images/diagrams/auth.drawio.png)

### Postman views:
#### Login:
![login_normal_user.png](https://jgomes.site/images/cs/postman/normal_user_login.png)

#### Normal private pages:
![Normal private pages](https://jgomes.site/images/cs/postman/normal_private_area.png)

#### Normal user try to access admin private pages:
![Normal private pages](https://jgomes.site/images/cs/postman/normal_user_try_admin_page.png)

#### Refresh:
![Refresh](https://jgomes.site/images/cs/postman/refresh.png)

#### Logout:
![Logout](https://jgomes.site/images/cs/postman/logout.png)

#### Admin login:
![login_admin_user.png](https://jgomes.site/images/cs/postman/admin_login.png)

#### Admin private pages:
![Admin private pages](https://jgomes.site/images/cs/postman/admin_user_try_admin.png)

### API Auth controller:
```
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
  
    /**
     * @param Request $request
     * @return JsonResponse
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

```

### API routes
```
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
        Route::middleware('throttle:10,1')->group(function () {
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
        // This route will be cached... No need more than 1 non cached access per minute
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

        // Allow only admin
        Route::middleware(['checkRole:admin', 'throttle:20,1'])->group(function () {
            Route::get('/admin',
                [
                    AdminController::class, 'index'
                ]
            )->name('admin');
        });
    });
});
```

##### Route example form admin access:
```
        // Allow only admin
        Route::middleware(['checkRole:admin', 'throttle:20,1'])->group(function () {
            Route::get('/admin',
                [
                    AdminController::class, 'index'
                ]
            )->name('admin');
        });
```

##### Middleware file to allow only admin users to get admin data:
```
<?php

namespace App\Http\Middleware;

use Closure;

class CheckUserRole
{
    public function handle($request, Closure $next, ...$roles)
    {
        $user = auth()->user();

        if ($user && in_array($user->role, $roles)) {
            return $next($request);
        }

        abort(403, 'Unauthorized.');
    }
}

```

##### In DB the $user->role needs to be 'admin' like this:
![admin user_db.png](https://jgomes.site/images/cs/postman/admin_role_db.png)

## Frontend diagram overview:
![frontend.png](https://jgomes.site/images/diagrams/auth_implementation.drawio.png)

#### Server less module:
```
// Access token name
let access_token_str  = 'access_token';

// Access token ttl
let access_token_ttl_minutes = 15;

// Login page
let login_page           = '/login';

// Home page
let home_page            = '/home';

// Forbidden page
let forbidden_page       = '/403';

// Too many requests page
let many_requests_page   = '/429';

// Ajax requests module definition
let serverLessRequests = (function($)
{
    // Login function
    function doLogin()
    {
        // Had the overlay
        $("#overlay").show();

        // Authenticate
        $.ajax({
            type: 'POST',
            url: '/api/v1/login',
            data: $('#loginForm').serialize(),
            success: function(response)
            {
                // Store the access_token at localStorage
                setTokenWithExpiry(access_token_str, response.access_token, access_token_ttl_minutes);

                // Handle successful login - redirect to home
                window.location.href = `${home_page}`;
            },
            error: function(xhr)
            {
                // Remove the overlay
                $("#overlay").hide();

                // Inform client about 429 ( Too many requests )
                if (xhr.status !== 429)
                {
                    // Show error to client
                    showFlashMessage(
                        'error',
                        'Invalid login'
                    );
                    return;
                }

                // Show error to client
                showFlashMessage(
                    'error',
                    'Too many request at this moment'
                );
            }
        });
    }

    // Bypass login case client has a valid token
    function checkAuthAndByPassLogin()
    {
        // Wait for the promise to be resolved
        getToken(access_token_str)
            .then((token) => {
                // Case no token stored or token is invalid, redirect to home_page
                if (!token) {
                    window.location.href = `${login_page}`;
                } else {
                    // Token is valid, redirect to home_page or perform other actions
                    window.location.href = `${home_page}`;
                }
            })
            .catch(() => {
                // Here we can handle the error appropriately (e.g., redirect to somewhere)
            });
    }

    // Logout function
    function doLogout()
    {
        // Had the overlay
        $("#overlay").show();

        // Wait for the promise to be resolved
        getToken(access_token_str)
            .then((token) => {
                $.ajax({
                    type: 'POST',
                    url: '/api/v1/logout',
                    headers: {
                        'Authorization': `Bearer ${token}`
                    },
                    success: function() {
                        // Handle success if needed
                    },
                    error: function(xhr) {
                        // Handle error if needed
                    },
                    complete: function() {

                        // Remove client token stored in the browser
                        localStorage.removeItem(access_token_str);

                        // Redirect to login page
                        window.location.href = `${login_page}?` + btoa('b64=true&success=Logout done with success');
                    }
                });
            })
            .catch(() => {
                // Here we can handle the error appropriately (e.g., redirect to an error page)
            });
    }

    // Get data if authorized
    function checkAuthAndGetData(url)
    {
        // Had the overlay
        $("#overlay").show();

        // Wait for the promise to be resolved
        getToken(access_token_str)
            .then(token => getData(url, token))
            .then(response => {
                // Handle successful data retrieval
                // console.log(response);
                if(response.result.user.role === 'admin')
                {
                    $(".adminLink").show();
                }
            })
            .catch(error => {
                // Case forbidden (403) errors go to forbidden page
                if (error === 403)
                {
                    window.location.href = forbidden_page;

                // Case too many requests (429) errors go to many requests page
                } else if (error === 429)
                {
                    window.location.href = many_requests_page;

                } else {
                    // Case other errors redirect to login page
                    window.location.href = `${login_page}?` + btoa(`b64=true&error=${error}`);
                }
            })
            .finally(() => {
                // Hide the overlay regardless of success or failure
                $("#overlay").hide();
            });
    }

    // Function to get data from backend with a valid token
    function getData(url, token)
    {
        return new Promise(function(resolve, reject)
        {
            $.ajax({
                type: 'GET',
                url: url,
                headers: {
                    'Authorization': `Bearer ${token}`
                },
                success: function(response)
                {
                    resolve(response);
                },
                error: function(xhr)
                {
                    // Case Forbidden go back
                    if (xhr.status === 403)
                    {
                        reject(403);
                        return;
                    }

                    // Inform client about 429 ( Too many requests )
                    if (xhr.status === 429)
                    {
                        // Reject the promise for too many requests
                        reject(429);
                        return;
                    }

                    // xhr.statusText || 'Cannot load data. Please try again.'
                    reject('Cannot load data');
                }
            });
        });
    }

    // Function to create and show errors
    function showFlashMessage(type, message) {
        $('<div>', {
            class: 'flashMessage ' + type,
            text: message
        }).prependTo('#loginMsg').delay(3000).fadeOut(1000, function() {
            $(this).remove();
        });
    }

    // Function to set the token in localStorage with an expiration time
    function setTokenWithExpiry(key, value, ttlInMinutes)
    {
        let now = new Date();
        let item = {
            value: value,
            expiry: now.getTime() + ttlInMinutes * 60 * 1000, // Converting minutes to milliseconds
        };

        localStorage.setItem(key, JSON.stringify(item));
    }

    // Function to retrieve a new token from localStorage
    function doRefreshToken(oldToken)
    {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: 'POST',
                url: '/api/v1/refresh',
                headers: {
                    'Authorization': `Bearer ${oldToken}`
                },
                success: function (response) {

                    // Store the access_token at localStorage
                    setTokenWithExpiry(access_token_str, response.access_token, access_token_ttl_minutes);

                    // Return the new token
                    resolve(response.access_token);
                },
                error: function (xhr) {

                    // Inform client about 429 ( Too many requests )
                    if (xhr.status !== 429) {

                        // Reject the promise as the token refresh failed
                        reject('Authentication needed');
                        return;
                    }

                    reject('Too many requests');
                }
            });
        });
    }

    // Function to return the current token from localStorage and case it is expired, create a new one
    function getToken(key)
    {
        return new Promise((resolve, reject) => {

            // Check if token is stored in localStorage
            let accessToken = localStorage.getItem(key);

            // If localStorage is empty, reject the promise as the no token was not found
            if (!accessToken)
            {
                reject('Authentication needed');
                return;
            }

            // Extract token and expiration date
            let access_token = JSON.parse(accessToken);

            // Current date
            let now = new Date();

            // Get current token
            let token = access_token.value;

            // Compare current date with the token validation date. If the token expired, delete the current localStorage
            if (now.getTime() > access_token.expiry) {

                // Use the promise returned by doRefreshToken to create a new token and store it in localStorage
                doRefreshToken(token)
                    .then((newToken) => {
                        resolve(newToken);
                    })
                    .catch((error) => {
                        reject(error);
                    })

            } else {

                // Last check... check if the current token is valid
                $.ajax({
                    type: 'GET',
                    url: '/api/v1/check',
                    headers: {
                        'Authorization': `Bearer ${token}`
                    },
                    success: function() {
                        resolve(token);
                    },
                    error: function(xhr) {
                        // Remove the token fom localStorage if it is invalid.
                        // Allow to keep 429 ( Too many requests )
                        if (xhr.status !== 429) {
                            // Remove the token from localStorage if it is invalid.
                            localStorage.removeItem(key);

                            // Reject the promise as the refresh token is invalid
                            reject('Authentication needed');
                            return;
                        }

                        // Reject the promise for too many requests
                        reject('Too many requests');
                    }
                });
            }
        });
    }

    // Init the module...
    // Assigning events to HTML elements, initializing other functions, etc...
    function init()
    {
        // Login btn via click
        $('#loginBtn').on('click', doLogin);

        // Login btn via btn enter
        $(document).on('keypress', function(event) {
            if (event.which === 13) { // Enter btn code
                event.preventDefault();
                doLogin(); // Call the func doLogin
            }
        });

        // Bypass login page case user has a valid access_token
        if (window.location.pathname === `${login_page}`)
        {
            checkAuthAndByPassLogin();
        }

        // Set time to remove flash messages
        $('.flashMessage').delay(3000).fadeOut(1000);
    }

    // Return init
    return {
        init: init,
        checkAuthAndGetData: checkAuthAndGetData,
        doLogout: doLogout
    };

})(jQuery);
```
#### Frontend routes:
```
// Route to authenticate
Route::get('/login', function () {
    return view('auth.login);
})
    ->name('auth.login');

// Route for authenticated user
Route::get('/home', function () {
    return view('home.index');
})
    ->name('home.index');

// Route for 'admin' rule
Route::get('/admin', function () {
    return view('admin.index');
})
    ->name('admin.index');

// Route for '403' message
Route::get('/403', function () {
    return view('errors.403');
})
    ->name('error.403');
    
// Route for '429' message
Route::get('/429', function () {
    return view('errors.429');
})
    ->name('error.429');
```
## Requirement list ( All tested! ): 

- Test to access private areas without login. Check the response ( It should be 401 )
- Try to log in and receive invalid because the user is not created
- Create normal private user. This user cannot access admin area
- Try to log in with the created user
- After log in try to access the login page and if it redirects to home page
- After log in try to access the admin page and check if the access is denied ( Receive 401 )
- Change the token and refresh page. Check if it is redirected to log in page
- Change the token and replace it again to the correct one and refresh page. Check if it is redirected to home page as the token is valid. This is not possible as the app when finds an invalid token, it removes it.
- Delete the token and refresh page. Check if is redirected to log in page with no possibility to access private area
- Test the Logout
- Test to access private areas without login after logout
- Create admin user
- Try to log in with the created admin user
- After log in try to access the login page and if it redirects to home page
- After log in try to access the admin page and check if the access is allowed ( receive 200 )
- Change the admin token and refresh page. Check if it is redirected to log in page
- Change the admin token and replace it again to the correct one and refresh page. Check if it is redirected to home page as the token is valid
- Delete the admin token and refresh page. Check if is redirected to log in page with no possibility to access private area
- Test refresh token
- Test Rate Limit


## Demonstration ( Click on the image to watch the demo video )
##### Note - Bug on min 23:07: In the video, it shows many redirects instead of stop at the first 429 returned from the API with the message 'too may requests'. Its fixed now.
[![Demonstration video](https://jgomes.site/images/cs/git-branch-protection-video-thumbnail.jpg)](http://www.youtube.com/watch?v=k4PLPwl9Xxc)
