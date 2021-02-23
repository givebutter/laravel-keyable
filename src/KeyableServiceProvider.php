<?php

namespace Givebutter\LaravelKeyable;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Givebutter\LaravelKeyable\Console\Commands\DeleteApiKey;
use Givebutter\LaravelKeyable\Console\Commands\GenerateApiKey;
use Givebutter\LaravelKeyable\Http\Middleware\AuthenticateApiKey;

class KeyableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @param Router $router
     * @return void
     */
    public function boot(Router $router)
    {
        $this->publishFiles();
        $this->registerMiddleware($router);
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateApiKey::class,
                DeleteApiKey::class
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
     * Publish files.
     *
     * @return void
     */
    private function publishFiles()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->publishes([
            __DIR__.'/../config/keyable.php' => config_path('keyable.php'),
        ]);
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
