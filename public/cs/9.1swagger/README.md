![Swagger Logo](https://jgomes.site/images/cs/swagger.jpg)

## Introduction

- The goal of this case study is to configure Swagger in this project in order to have a way available for easily testing various API requests without the need to install or use extra software.
- Swagger is the chosen tool for it, and it is an open-source software used to define, create, document, and consume RESTful APIs.
- It helps and allows the developers to define the structure of the API.
- Swagger can automatically generate interactive documentation for the API, allowing developers to view and test the different routes, parameters, and responses of the API directly in a web browser.
- The package used to install and configure Swagger in this Laravel project is the ' darkaonline/l5-swagger '.
- The ' darkaonline/l5-swagger ' is a popular package in PHP, specifically for Laravel applications, that simplifies the generation of interactive documentation using Swagger/OpenAPI.
- It performs Automatic Documentation Generation, where this package can automatically generate API documentation based on the routes and controllers defined in the Laravel application.
- It integrates with Swagger UI, including the Swagger UI interface.
- It allows the customization of documentation allowing to customize the appearance and behavior of the documentation as needed, using additional settings and annotations.

## Init configuration of Swagger
#### Step 1: Install Required Packages
- ```composer require darkaonline/l5-swagger``` 
- ```composer require tymon/jwt-auth```

#### Step 2: Configure JWT Authentication
- After installing the `tymon/jwt-auth` package, we need to publish the configuration file by running the following command in the terminal which will create a file `config/jwt.php`.
- ```php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider" ```
- ```php artisan jwt:secret```

#### Step 3: Configure Swagger
- To publish the configuration file for `darkaonline/l5-swagger` is needed to run the following command:
- ```php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"```

## Controllers configuration
#### First add to following annotation in the main Controller to create the securityScheme in order to activate the authentication for the routes that are not open:
```
/**
 *
 * @OA\Info(
 *    title="JGomes site API",
 *    version="1.0.0",
 * ),
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Login with email and password to get the authentication token",
 *     name="Token based on user credentials",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="apiAuth",
 * )
 */
```

- This will activate the authorize button, like this:

  ![Authorize button](https://jgomes.site/images/cs/swagger/auth-btn.png)

#### Annotation example of a Controller function that does not need authentication ( open route to public ):
```
    /**
     * @OA\Post(
     *     path="/api/send",
     *     summary="Send a message",
     *     tags={"Message"},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="User's name",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="subject",
     *         in="query",
     *         description="User's subject",
     *         required=false,
     *         @OA\Schema(type="string")
            ),
     *      @OA\Parameter(
     *          name="content",
     *          in="query",
     *          description="User's content",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *     @OA\Response(response="201", description="Message sent successfully"),
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */
```

- For this example there's no need to have a valid token:

  ![No need to be authenticated](https://jgomes.site/images/cs/swagger/non-auth-request.png)

#### Annotation of the Controller function that does the login with email / password:
```
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
```

- When the login is done with success, we receive a valid token, like this:
  ![auth-request](https://jgomes.site/images/cs/swagger/auth-request.png)
  ![auth-result](https://jgomes.site/images/cs/swagger/auth-result.png)

- After, we need to copy the token received in the last request, and create an authorization by clicking on the button authorize to paste the token there:

  ![create an authorization](https://jgomes.site/images/cs/swagger/add-token.png)

#### Annotation example of a Controller function that needs authentication ( private route that needs a valid token to do the request ):
```
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
```

- Authenticated result with a valid token:

  ![custom-swagger-view-dir](https://jgomes.site/images/cs/swagger/success-authenticated-result.png)

- Do logout:

  ![custom-swagger-view-dir](https://jgomes.site/images/cs/swagger/logout.png)

- Unauthenticated result:

  ![custom-swagger-view-dir](https://jgomes.site/images/cs/swagger/non-authenticated-result.png)

## Extra configurations
#### Customize Swagger view:
- As the Swagger template is not suppose to be editable, it is very hard to do optimizations on the UI. 
- Touching on the template in vendor is a big NO NO. 
- To solve this let's create a copy of the template and paste it in the resources dir like this:

  ![custom-swagger-view-dir](https://jgomes.site/images/cs/swagger/custom-swagger-view-dir.png)

- The reason why this project needs some customization is because it is served by a proxy reverse, so the URL keeps with the local IP ( 127.0.0.1 even in prod ) instead the domain, which I don't like.  
- In order to put the correct url in place, it was needed to define the following vars containing the correct URL according the environment in ' app -> Providers -> AppServiceProvider.php ', like this:

```
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        app()->singleton('urlToDocs', function () {
            return env('APP_URL') . '/docs/api-docs.json';
        });

        app()->singleton('swaggeruibundle', function () {
            return env('APP_URL') . '/docs/asset/swagger-ui-bundle.js';
        });

        app()->singleton('swaggeruistandalonepreset', function () {
            return env('APP_URL') . '/docs/asset/swagger-ui-standalone-preset.js';
        });

        app()->singleton('swagger-ui', function () {
            return env('APP_URL') . '/docs/asset/swagger-ui.css';
        });
    }

    public function boot()
    {
        //
    }
}
```
- This config is located here:

  ![defined-vars](https://jgomes.site/images/cs/swagger/defined-vars.png)

- And let's use this vars ' urlToDocs ', ' swaggeruibundle ', ' swaggeruistandalonepreset ', ' swagger-ui ' in the following customized view:

```
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{config('l5-swagger.documentations.'.$documentation.'.api.title')}}</title>
    <link rel="stylesheet" type="text/css" href="{!! app('swagger-ui') !!}">      <--------- HERE
    <style>
    html
    {
        box-sizing: border-box;
        overflow: -moz-scrollbars-vertical;
        overflow-y: scroll;
    }
    *,
    *:before,
    *:after
    {
        box-sizing: inherit;
    }

    body {
      margin:0;
      background: #fafafa;
    }
    </style>
</head>

<body>
<div id="swagger-ui"></div>
<script src="{!! app('swaggeruibundle') !!}"></script>      <--------- HERE
<script src="{!! app('swaggeruistandalonepreset') !!}"></script>      <--------- HERE

<script>
    window.onload = function() {
        // Build a system
        const ui = SwaggerUIBundle({
            dom_id: '#swagger-ui',
            url: "{!! app('urlToDocs') !!}",      <--------- HERE
            operationsSorter: {!! isset($operationsSorter) ? '"' . $operationsSorter . '"' : 'null' !!},
            configUrl: {!! isset($configUrl) ? '"' . $configUrl . '"' : 'null' !!},
            validatorUrl: {!! isset($validatorUrl) ? '"' . $validatorUrl . '"' : 'null' !!},
            oauth2RedirectUrl: "{{ route('l5-swagger.'.$documentation.'.oauth2_callback', [], $useAbsolutePath) }}",

            requestInterceptor: function(request) {
                request.headers['X-CSRF-TOKEN'] = '{{ csrf_token() }}';
                return request;
            },

            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ],

            plugins: [
                SwaggerUIBundle.plugins.DownloadUrl
            ],

            layout: "StandaloneLayout",
            docExpansion : "{!! config('l5-swagger.defaults.ui.display.doc_expansion', 'none') !!}",
            deepLinking: true,
            filter: {!! config('l5-swagger.defaults.ui.display.filter') ? 'true' : 'false' !!},
            persistAuthorization: "{!! config('l5-swagger.defaults.ui.authorization.persist_authorization') ? 'true' : 'false' !!}",

        })

        window.ui = ui

        @if(in_array('oauth2', array_column(config('l5-swagger.defaults.securityDefinitions.securitySchemes'), 'type')))
        ui.initOAuth({
            usePkceWithAuthorizationCodeGrant: "{!! (bool)config('l5-swagger.defaults.ui.authorization.oauth2.use_pkce_with_authorization_code_grant') !!}"
        })
        @endif
    }
</script>
</body>
</html>
```

- This customized template is located at the dir ' resources -> views -> vendor -> I5-swagger '
- In order to override the default view located inside the dir vendor, we need to change the config file and update the key 'views' with base_path('resources/views/vendor/l5-swagger'), like this:

```
return [
    'default' => 'default',
    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'L5 Swagger UI',
            ],
            .
            .
            .
            .
        ],
    ],
    'defaults' => [
        'routes' => [
            'docs' => 'docs',
            'oauth2_callback' => 'api/oauth2-callback',
            'middleware' => [
                'api' => [],
                'asset' => [],
                'docs' => [],
                'oauth2_callback' => [],
            ],
            'group_options' => [],
        ],

        'paths' => [
            'docs' => storage_path('api-docs'),
            'views' => base_path('resources/views/vendor/l5-swagger'),      <----------- HERE
            'base' => env('L5_SWAGGER_BASE_PATH', null),
            'swagger_ui_assets_path' => env('L5_SWAGGER_UI_ASSETS_PATH', 'vendor/swagger-api/swagger-ui/dist/'),
            'excludes' => [],
        ],

        .
        .
        .
        .
      
        ],
    ],
];
```
  ![config-file](https://jgomes.site/images/cs/swagger/config-file.png)

- Also, because the Sanctum ( when the user is not authenticated ) is returning a 405, it was needed to add a fallback in the API to everytime the user dont't has a valid token it returns a 401 for unauthenticated requests.

```
<?php

Route::prefix('v1')->group(function () {

    .
    .
    .
    .

    // Protected routes by Sanctum
    Route::middleware('auth:sanctum')->group(function ()
    {
          .
          .
          . ( For the routes defined here under the middleware 'auth:sanctum', the Swagger returns a 405 )
          .
          .
          
    });

    // Middleware de fallback to return 401 for unauthenticated requests
    Route::fallback(function () {
        return response()->json(['error' => 'Unauthorized'], 401);      <------- To solve this problem just add this HERE
    });
});
```

  ![api-route](https://jgomes.site/images/cs/swagger/api-route.png)

## Every time we change something on the Swagger annotations we need to tun:
- ```php artisan l5-swagger:generate```

## Demonstration
#### ( Click on the image to watch the demo video )
[![Demonstration video](https://jgomes.site/images/cs/git-branch-protection-video-thumbnail.jpg)](http://www.youtube.com/watch?v=1a8Jm8vdGbo)
