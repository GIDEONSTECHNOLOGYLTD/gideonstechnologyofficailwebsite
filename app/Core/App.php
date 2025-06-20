<?php

namespace App\Core;

use App\Core\Container;
use App\Core\Config;

/**
 * Main Application class
 * Serves as the central point for the application
 */
class App
{
    /**
     * The application instance
     *
     * @var App
     */
    private static $instance;

    /**
     * The IoC container instance
     *
     * @var Container
     */
    private $container;

    /**
     * All registered service providers
     *
     * @var array
     */
    private $serviceProviders = [];

    /**
     * Indicates if the application has been bootstrapped
     *
     * @var bool
     */
    private $booted = false;

    /**
     * Create a new application instance
     */
    public function __construct()
    {
        $this->container = new Container();
        $this->container->instance('app', $this);
        
        // Set up the Config class with root path
        Config::setRootPath(dirname(__DIR__, 2));
        
        // Register core services
        $this->registerBaseServices();
    }

    /**
     * Register the core service providers
     *
     * @return void
     */
    private function registerBaseServices()
    {
        // Core providers that must be registered first
        $this->register(\App\Providers\DatabaseServiceProvider::class);
        $this->register(\App\Providers\RouteServiceProvider::class);
        
        // Register additional providers from config
        $providers = Config::get('app.providers', []);
        foreach ($providers as $provider) {
            $this->register($provider);
        }
    }

    /**
     * Register a service provider
     *
     * @param string|ServiceProvider $provider
     * @return App
     */
    public function register($provider)
    {
        // If it's a string, resolve it
        if (is_string($provider)) {
            $provider = new $provider($this);
        }

        $provider->register();
        $this->serviceProviders[] = $provider;

        // If the application has already booted, boot the provider
        if ($this->booted) {
            $provider->boot();
        }

        return $this;
    }

    /**
     * Boot the application
     *
     * @return void
     */
    public function boot()
    {
        if ($this->booted) {
            return;
        }

        // Boot all service providers
        foreach ($this->serviceProviders as $provider) {
            $provider->boot();
        }

        $this->booted = true;
    }

    /**
     * Get the IoC Container
     *
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Get an instance from the container
     *
     * @param string $abstract
     * @return mixed
     */
    public function make($abstract)
    {
        return $this->container->make($abstract);
    }

    /**
     * Bind a type to the container
     *
     * @param string $abstract
     * @param mixed $concrete
     * @return void
     */
    public function bind($abstract, $concrete = null)
    {
        $this->container->bind($abstract, $concrete);
    }

    /**
     * Bind a singleton to the container
     *
     * @param string $abstract
     * @param mixed $concrete
     * @return void
     */
    public function singleton($abstract, $concrete = null)
    {
        $this->container->singleton($abstract, $concrete);
    }

    /**
     * Get the application instance
     *
     * @return App
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }
}
