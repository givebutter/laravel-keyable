<?php

namespace Givebutter\LaravelKeyable;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

use Givebutter\LaravelKeyable\Http\Middleware\AuthenticateApiKey;

use Givebutter\LaravelKeyable\KeyableClass;
use Givebutter\LaravelKeyable\Console\Commands\GenerateApiKey;

class KeyableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $this->registerMiddleware($router);
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->app->bind('KeyableClass', function () {
            return new KeyableClass;
        });
        if ($this->app->runningInConsole()) {
	        $this->commands([
	            GenerateApiKey::class,
	        ]);
	    }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
    
    /**
     * Register middleware
     *
     * Support added for different Laravel versions
     *
     * @param Router $router
     */
    protected function registerMiddleware(Router $router)
    {
        $versionComparison = version_compare(app()->version(), '5.4.0');
        if ($versionComparison >= 0) {
            $router->aliasMiddleware('auth.apikey', AuthenticateApiKey::class);
        } else {
            $router->middleware('auth.apikey', AuthenticateApiKey::class);
        }
    }
}
