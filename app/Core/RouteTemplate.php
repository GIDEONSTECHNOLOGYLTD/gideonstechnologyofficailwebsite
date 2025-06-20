<?php
/**
 * Route Template
 * 
 * This file serves as a template for all route files in the application.
 * It demonstrates the standardized approach to route registration using
 * the RouteManager and RouteRegistry pattern.
 */

use App\Core\RouteManager;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

// Define a global flag to prevent duplicate loading
// Use a unique constant name for each route file
if (!defined('TEMPLATE_ROUTES_REGISTERED')) {
    define('TEMPLATE_ROUTES_REGISTERED', true);
    
    return function (App $app, ?ContainerInterface $container = null) {
        // Create a RouteManager instance
        $routeManager = new RouteManager($app, $container);
        
        // Register routes using RouteManager
        // Example of a simple route with a closure
        $routeManager->get('/example', function (Request $request, Response $response) {
            $response->getBody()->write('<h1>Example Route</h1>');
            return $response;
        }, 'example.route');
        
        // Example of a route with a controller
        $routeManager->get('/example/controller', 
            ['App\\Controllers\\ExampleController', 'index'], 
            'example.controller', 
            'default' // Fallback type if controller not available
        );
        
        // Example of a route group
        $routeManager->group('/group', function (RouteCollectorProxy $group) use ($routeManager) {
            // Routes within the group
            $routeManager->get('/example', function (Request $request, Response $response) {
                $response->getBody()->write('<h1>Group Example Route</h1>');
                return $response;
            }, 'group.example');
        });
    };
} else {
    // If already registered, return an empty function
    return function (App $app, ?ContainerInterface $container = null) {
        // Routes already registered
        error_log('Template routes already registered, skipping...');
    };
}
