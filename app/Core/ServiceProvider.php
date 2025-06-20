<?php

namespace App\Core;

/**
 * Base ServiceProvider class
 * Service providers are responsible for registering and bootstrapping services
 */
abstract class ServiceProvider
{
    /**
     * The application instance
     *
     * @var App
     */
    protected $app;

    /**
     * The container instance
     *
     * @var Container
     */
    protected $container;

    /**
     * Create a new service provider instance
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Register any application services
     *
     * @return void
     */
    abstract public function register();

    /**
     * Bootstrap any application services
     * This method is called after all services are registered
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Bind a service to the container
     *
     * @param string $abstract
     * @param mixed $concrete
     * @return void
     */
    protected function bind($abstract, $concrete = null)
    {
        $this->container->bind($abstract, $concrete);
    }

    /**
     * Bind a singleton service to the container
     *
     * @param string $abstract
     * @param mixed $concrete
     * @return void
     */
    protected function singleton($abstract, $concrete = null)
    {
        $this->container->singleton($abstract, $concrete);
    }
}