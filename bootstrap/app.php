<?php
require_once __DIR__.'/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->withFacades();
$app->withEloquent();
$app->configure('services');
$app->configure('auth');
$app->alias('auth', \Illuminate\Auth\AuthManager::class);
$app->alias('auth', \Illuminate\Contracts\Auth\Factory::class);
$app->alias('auth', \Illuminate\Contracts\Auth\Guard::class);
/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Config Files
|--------------------------------------------------------------------------
|
| Now we will register the "app" configuration file. If the file exists in
| your configuration directory it will be loaded; otherwise, we'll load
| the default version. You may register other files below as needed.
|
*/

$app->configure('app');


/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

// $app->middleware([
//     App\Http\Middleware\ExampleMiddleware::class
// ]);

$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
    'client_credentials' => \Laravel\Passport\Http\Middleware\CheckClientCredentials::class,
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(Illuminate\Encryption\EncryptionServiceProvider::class);

//$app->register(Laravel\Passport\PassportServiceProvider::class);
//$app->register(Dusterio\LumenPassport\PassportServiceProvider::class);
//\Dusterio\LumenPassport\LumenPassport::routes($app->router);
if (class_exists('\Dusterio\LumenPassport\LumenPassport')) {
    \Laravel\Passport\Passport::loadKeysFrom(storage_path());

    $app->singleton(\League\OAuth2\Server\Repositories\ClientRepositoryInterface::class,\Laravel\Passport\Bridge\ClientRepository::class);
    $app->singleton(\League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface::class,\Laravel\Passport\Bridge\AccessTokenRepository::class);
    $app->singleton(\League\OAuth2\Server\Repositories\ScopeRepositoryInterface::class,\Laravel\Passport\Bridge\ScopeRepository::class);
    $app->singleton(\League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface::class,\Laravel\Passport\Bridge\RefreshTokenRepository::class);
    $app->singleton(\League\OAuth2\Server\Repositories\UserRepositoryInterface::class,\Laravel\Passport\Bridge\UserRepository::class);

    $app->singleton(\League\OAuth2\Server\AuthorizationServer::class, function ($app) {
        $privateKey = new \League\OAuth2\Server\CryptKey( 'file://' . storage_path('oauth-private.key'), null, false );
        $server = new \League\OAuth2\Server\AuthorizationServer(
            $app->make(\League\OAuth2\Server\Repositories\ClientRepositoryInterface::class),
            $app->make(\League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface::class),
            $app->make(\League\OAuth2\Server\Repositories\ScopeRepositoryInterface::class),
            $privateKey,
            app('encrypter')->getKey() // This provides the encryption key
        
        );

    

    $server->enableGrantType(new \League\OAuth2\Server\Grant\ClientCredentialsGrant(), new \DateInterval('P1Y'));

    return $server;
    });
    $app->singleton(\League\OAuth2\Server\ResourceServer::class, function ($app) {
        $publicKey = new \League\OAuth2\Server\CryptKey(
            'file://' . storage_path('oauth-public.key'), null, false
        );
        return new \League\OAuth2\Server\ResourceServer(
            $app->make(\League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface::class),
            $publicKey
        );
    });


    \Dusterio\LumenPassport\LumenPassport::routes($app->router);

    $app->router->post('/oauth/token', [
    'uses' => '\App\Http\Controllers\AccessTokenController@issueToken',]);
}
/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__.'/../routes/web.php';
});
return $app;
