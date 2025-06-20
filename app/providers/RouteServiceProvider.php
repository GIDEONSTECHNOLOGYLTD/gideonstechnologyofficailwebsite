<?php

namespace App\Providers;

use App\Core\ServiceProvider;
use App\Core\Router;
use App\Core\Config;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register the route service
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('router', function ($app) {
            return new Router($app);
        });
    }

    /**
     * Bootstrap the routing services
     * Loads route files from the configuration
     *
     * @return void
     */
    public function boot()
    {
        $router = $this->app->make('router');
        
        // Load web routes
        $webRoutesPath = dirname(__DIR__) . '/../app/config/routes.php';
        if (file_exists($webRoutesPath)) {
            require $webRoutesPath;
        }
        
        // Load API routes if they exist separately
        $apiRoutesPath = dirname(__DIR__) . '/../app/config/api_routes.php';
        if (file_exists($apiRoutesPath)) {
            require $apiRoutesPath;
        }
    }
}