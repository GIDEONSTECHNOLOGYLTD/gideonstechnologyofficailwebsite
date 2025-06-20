<?php
/**
 * RouteManager Class
 * 
 * Provides a standardized approach to route registration across the application.
 * Integrates with RouteRegistry to prevent duplicate registrations and
 * provides graceful fallbacks for missing components.
 */

namespace App\Core;

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

class RouteManager
{
    /**
     * @var App Slim application instance
     */
    private $app;
    
    /**
     * @var ContainerInterface|null Container instance
     */
    private $container;
    
    /**
     * @var array Fallback handlers for missing controllers
     */
    private $fallbackHandlers = [];
    
    /**
     * Constructor
     * 
     * @param App $app Slim application instance
     * @param ContainerInterface|null $container DI container instance
     */
    public function __construct(App $app, ?ContainerInterface $container = null)
    {
        $this->app = $app;
        $this->container = $container;
        $this->initializeFallbackHandlers();
    }
    
    /**
     * Initialize default fallback handlers
     */
    private function initializeFallbackHandlers()
    {
        // Default fallback handler
        $this->fallbackHandlers['default'] = function (Request $request, Response $response) {
            $html = '<h1>Feature Unavailable</h1><p>This feature is currently unavailable. Please try again later.</p>';
            $response->getBody()->write($html);
            return $response;
        };
        
        // HTML fallback handler - returns a nice HTML page
        $this->fallbackHandlers['html'] = function (Request $request, Response $response) {
            $route = $request->getUri()->getPath();
            $title = ucwords(str_replace(['/', '-', '_'], ' ', trim($route, '/')));
            if (empty($title)) {
                $title = 'Home';
            }
            
            $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title} - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h2>{$title}</h2>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <p>This page is currently under development.</p>
                    <p>Our team is working hard to bring you this feature soon.</p>
                </div>
                <div class="text-center mt-3">
                    <a href="/" class="btn btn-primary">Return to Homepage</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
            
            $response->getBody()->write($html);
            return $response->withHeader('Content-Type', 'text/html');
        };
        
        // Fallback for auth routes
        $this->fallbackHandlers['auth'] = function (Request $request, Response $response) {
            $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Authentication</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <p>The authentication system is currently under maintenance.</p>
                            <p>Please try again later or contact support for assistance.</p>
                        </div>
                        <div class="text-center mt-3">
                            <a href="/" class="btn btn-primary">Return to Homepage</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
            $response->getBody()->write($html);
            return $response->withHeader('Content-Type', 'text/html');
        };
        
        // Fallback for user routes
        $this->fallbackHandlers['user'] = function (Request $request, Response $response) {
            $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Account - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">User Account</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <p>The user account system is currently under maintenance.</p>
                            <p>Please try again later or contact support for assistance.</p>
                        </div>
                        <div class="text-center mt-3">
                            <a href="/" class="btn btn-primary">Return to Homepage</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
            $response->getBody()->write($html);
            return $response->withHeader('Content-Type', 'text/html');
        };
        
        // Fallback for store routes
        $this->fallbackHandlers['store'] = function (Request $request, Response $response) {
            $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GStore - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">GStore</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <p>The store is currently under maintenance.</p>
                            <p>Please try again later or contact support for assistance.</p>
                        </div>
                        <div class="text-center mt-3">
                            <a href="/" class="btn btn-primary">Return to Homepage</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
            $response->getBody()->write($html);
            return $response->withHeader('Content-Type', 'text/html');
        };
    }
    
    /**
     * Get a controller from the container or return a fallback handler
     * 
     * @param string $controllerClass Controller class name
     * @param string $method Controller method name
     * @param string $fallbackType Type of fallback to use if controller not available
     * @return callable Controller method or fallback handler
     */
    public function getControllerOrFallback($controllerClass, $method, $fallbackType = 'default')
    {
        // First try to see if the class exists directly
        if (class_exists($controllerClass)) {
            try {
                // If container is available, try to get from container
                if ($this->container && method_exists($this->container, 'get')) {
                    try {
                        $controller = $this->container->get($controllerClass);
                    } catch (\Exception $e) {
                        // If container can't resolve, create instance directly
                        $controller = new $controllerClass($this->container);
                    }
                } else {
                    // No container, create instance directly
                    $controller = new $controllerClass($this->container);
                }
                
                // Check if method exists on controller
                if (method_exists($controller, $method)) {
                    return [$controller, $method];
                }
            } catch (\Exception $e) {
                // Log the error but continue to fallback
                error_log("Controller instantiation error: {$e->getMessage()}");
            }
        } else {
            // Log that the controller class doesn't exist
            error_log("Controller class not found: {$controllerClass}");
        }
        
        // Return appropriate fallback handler based on the type
        if (isset($this->fallbackHandlers[$fallbackType])) {
            return $this->fallbackHandlers[$fallbackType];
        }
        
        // If the specified fallback type doesn't exist, use default
        return $this->fallbackHandlers['default'];
    }
    
    /**
     * Register a GET route if not already registered
     * 
     * @param string $pattern Route pattern
     * @param callable|array $handler Route handler or [controllerClass, method]
     * @param string $name Route name
     * @param string $fallbackType Type of fallback to use if controller not available
     * @return RouteCollectorProxy|null Route instance or null if already registered
     */
    public function get($pattern, $handler, $name = null, $fallbackType = 'default')
    {
        if (RouteRegistry::register('GET', $pattern)) {
            // If handler is array with controller class and method
            if (is_array($handler) && count($handler) === 2 && is_string($handler[0])) {
                $handler = $this->getControllerOrFallback($handler[0], $handler[1], $fallbackType);
            }
            
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
     * @param callable|array $handler Route handler or [controllerClass, method]
     * @param string $name Route name
     * @param string $fallbackType Type of fallback to use if controller not available
     * @return RouteCollectorProxy|null Route instance or null if already registered
     */
    public function post($pattern, $handler, $name = null, $fallbackType = 'default')
    {
        if (RouteRegistry::register('POST', $pattern)) {
            // If handler is array with controller class and method
            if (is_array($handler) && count($handler) === 2 && is_string($handler[0])) {
                $handler = $this->getControllerOrFallback($handler[0], $handler[1], $fallbackType);
            }
            
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
     * @param callable|array $handler Route handler or [controllerClass, method]
     * @param string $name Route name
     * @param string $fallbackType Type of fallback to use if controller not available
     * @return RouteCollectorProxy|null Route instance or null if already registered
     */
    public function put($pattern, $handler, $name = null, $fallbackType = 'default')
    {
        if (RouteRegistry::register('PUT', $pattern)) {
            // If handler is array with controller class and method
            if (is_array($handler) && count($handler) === 2 && is_string($handler[0])) {
                $handler = $this->getControllerOrFallback($handler[0], $handler[1], $fallbackType);
            }
            
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
     * @param callable|array $handler Route handler or [controllerClass, method]
     * @param string $name Route name
     * @param string $fallbackType Type of fallback to use if controller not available
     * @return RouteCollectorProxy|null Route instance or null if already registered
     */
    public function delete($pattern, $handler, $name = null, $fallbackType = 'default')
    {
        if (RouteRegistry::register('DELETE', $pattern)) {
            // If handler is array with controller class and method
            if (is_array($handler) && count($handler) === 2 && is_string($handler[0])) {
                $handler = $this->getControllerOrFallback($handler[0], $handler[1], $fallbackType);
            }
            
            $route = $this->app->delete($pattern, $handler);
            if ($name) {
                $route->setName($name);
            }
            return $route;
        }
        return null;
    }
    
    /**
     * Register a route group
     * 
     * @param string $prefix Group prefix
     * @param callable $callback Group callback
     * @return RouteCollectorProxy Route group
     */
    public function group($prefix, $callback)
    {
        return $this->app->group($prefix, $callback);
    }
    
    /**
     * Get the Slim application instance
     * 
     * @return App
     */
    public function getApp()
    {
        return $this->app;
    }
    
    /**
     * Get the container instance
     * 
     * @return ContainerInterface|null
     */
    public function getContainer()
    {
        return $this->container;
    }
    
    /**
     * Set a custom fallback handler
     * 
     * @param string $type Fallback type
     * @param callable $handler Fallback handler
     * @return void
     */
    public function setFallbackHandler($type, $handler)
    {
        $this->fallbackHandlers[$type] = $handler;
    }
    
    /**
     * Get a fallback handler
     * 
     * @param string $type Fallback type
     * @return callable
     */
    public function getFallbackHandler($type = 'default')
    {
        return $this->fallbackHandlers[$type] ?? $this->fallbackHandlers['default'];
    }
}
