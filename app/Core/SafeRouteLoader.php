<?php
/**
 * SafeRouteLoader Class
 * 
 * This class provides a safe way to load routes, preventing duplicate registrations
 * by integrating with the RouteRegistry system.
 */

namespace App\Core;

use Slim\App;
use Slim\Routing\RouteCollectorProxy;

class SafeRouteLoader
{
    /**
     * @var App Slim application instance
     */
    private $app;
    
    /**
     * Constructor
     * 
     * @param App $app Slim application instance
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }
    
    /**
     * Register a GET route if not already registered
     * 
     * @param string $pattern Route pattern
     * @param callable|string $handler Route handler
     * @param string|null $name Route name
     * @return RouteCollectorProxy|null Route instance or null if already registered
     */
    public function get($pattern, $handler, $name = null)
    {
        if (RouteRegistry::register('GET', $pattern)) {
            $route = $this->app->get($pattern, $handler);
            if ($name) {
                $route->setName($name);
            }
            return $route;
        }
        return null;
    }
    
    /**
     * Register a POST route if not already registered
     * 
     * @param string $pattern Route pattern
     * @param callable|string $handler Route handler
     * @param string|null $name Route name
     * @return RouteCollectorProxy|null Route instance or null if already registered
     */
    public function post($pattern, $handler, $name = null)
    {
        if (RouteRegistry::register('POST', $pattern)) {
            $route = $this->app->post($pattern, $handler);
            if ($name) {
                $route->setName($name);
            }
            return $route;
        }
        return null;
    }
    
    /**
     * Register a PUT route if not already registered
     * 
     * @param string $pattern Route pattern
     * @param callable|string $handler Route handler
     * @param string|null $name Route name
     * @return RouteCollectorProxy|null Route instance or null if already registered
     */
    public function put($pattern, $handler, $name = null)
    {
        if (RouteRegistry::register('PUT', $pattern)) {
            $route = $this->app->put($pattern, $handler);
            if ($name) {
                $route->setName($name);
            }
            return $route;
        }
        return null;
    }
    
    /**
     * Register a DELETE route if not already registered
     * 
     * @param string $pattern Route pattern
     * @param callable|string $handler Route handler
     * @param string|null $name Route name
     * @return RouteCollectorProxy|null Route instance or null if already registered
     */
    public function delete($pattern, $handler, $name = null)
    {
        if (RouteRegistry::register('DELETE', $pattern)) {
            $route = $this->app->delete($pattern, $handler);
            if ($name) {
                $route->setName($name);
            }
            return $route;
        }
        return null;
    }
    
    /**
     * Register a PATCH route if not already registered
     * 
     * @param string $pattern Route pattern
     * @param callable|string $handler Route handler
     * @param string|null $name Route name
     * @return RouteCollectorProxy|null Route instance or null if already registered
     */
    public function patch($pattern, $handler, $name = null)
    {
        if (RouteRegistry::register('PATCH', $pattern)) {
            $route = $this->app->patch($pattern, $handler);
            if ($name) {
                $route->setName($name);
            }
            return $route;
        }
        return null;
    }
    
    /**
     * Register a route group if not already registered
     * 
     * @param string $prefix Group prefix
     * @param callable $callback Group callback
     * @return RouteCollectorProxy|null Route group or null if already registered
     */
    public function group($prefix, $callback)
    {
        // Groups don't need to be registered in RouteRegistry
        // Individual routes within the group will be registered
        return $this->app->group($prefix, $callback);
    }
    
    /**
     * Register a route with any method if not already registered
     * 
     * @param array|string $methods HTTP method(s)
     * @param string $pattern Route pattern
     * @param callable|string $handler Route handler
     * @param string|null $name Route name
     * @return RouteCollectorProxy|null Route instance or null if already registered
     */
    public function map($methods, $pattern, $handler, $name = null)
    {
        // Convert string method to array
        if (!is_array($methods)) {
            $methods = [$methods];
        }
        
        // Check if any of the methods are already registered
        $canRegister = true;
        foreach ($methods as $method) {
            if (!RouteRegistry::register($method, $pattern)) {
                $canRegister = false;
                break;
            }
        }
        
        if ($canRegister) {
            $route = $this->app->map($methods, $pattern, $handler);
            if ($name) {
                $route->setName($name);
            }
            return $route;
        }
        
        return null;
    }
}
