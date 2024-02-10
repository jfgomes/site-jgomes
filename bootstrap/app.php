<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

// For dev configs to avoid prod configs
// Note: everytime you change something in this clean caches and reboot the env
// Note: .env.dev is the default file and is auto generated. if It does not exist, it will use .env.test file
$filePath = '.env.dev';
if (file_exists($filePath))
{
    // For Artisan, for dev...
    if ($app->runningInConsole()) {
        $app->loadEnvironmentFrom('.env.dev');
    }

} else {

    $app->loadEnvironmentFrom('.env');
}

// To run jobs in prod manually using APP_ENV var like:
// APP_ENV=prod php artisan env
// This will avoid to set the env as local, as we  are run it throw command line
$environment = getenv('APP_ENV');
if ($environment === 'prod') {
    $app->loadEnvironmentFrom('.env');
}

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
