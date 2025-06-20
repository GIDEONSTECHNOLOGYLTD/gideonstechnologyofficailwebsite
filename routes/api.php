<?php
/**
 * API Routes for Slim Framework
 * This file defines all API endpoints for the application
 * Updated to use SafeRouteLoader to prevent duplicate route registrations
 */

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Controllers\ApiController;
use App\Core\RouteRegistry;
use App\Core\SafeRouteLoader;

// Define a global flag to prevent duplicate loading
if (!defined('API_ROUTES_REGISTERED')) {
    define('API_ROUTES_REGISTERED', true);
    
    return function (App $app) {
        // Create a SafeRouteLoader instance for the API group
        $safeRouter = new SafeRouteLoader($app);
        
        // Group all API routes
        $app->group('/api', function (RouteCollectorProxy $group) use ($safeRouter) {
            // Templates endpoint - using RouteRegistry to prevent duplicates
            if (!RouteRegistry::isRegistered('GET', '/api/templates')) {
                RouteRegistry::register('GET', '/api/templates');
                $group->get('/templates', [ApiController::class, 'getTemplates']);
                error_log('Registered route: GET /api/templates');
            }
            
            // Orders endpoint - using RouteRegistry to prevent duplicates
            if (!RouteRegistry::isRegistered('GET', '/api/orders')) {
                RouteRegistry::register('GET', '/api/orders');
                $group->get('/orders', [ApiController::class, 'getOrders']);
                error_log('Registered route: GET /api/orders');
            }
            
            // Users endpoints - using RouteRegistry to prevent duplicates
            if (!RouteRegistry::isRegistered('GET', '/api/users')) {
                RouteRegistry::register('GET', '/api/users');
                $group->get('/users', [ApiController::class, 'getAllUsers']);
                error_log('Registered route: GET /api/users');
            }
            
            if (!RouteRegistry::isRegistered('GET', '/api/users/{id}')) {
                RouteRegistry::register('GET', '/api/users/{id}');
                $group->get('/users/{id}', [ApiController::class, 'getUserById']);
                error_log('Registered route: GET /api/users/{id}');
            }
            
            if (!RouteRegistry::isRegistered('POST', '/api/users')) {
                RouteRegistry::register('POST', '/api/users');
                $group->post('/users', [ApiController::class, 'createUser']);
                error_log('Registered route: POST /api/users');
            }
            
            if (!RouteRegistry::isRegistered('PUT', '/api/users/{id}')) {
                RouteRegistry::register('PUT', '/api/users/{id}');
                $group->put('/users/{id}', [ApiController::class, 'updateUser']);
                error_log('Registered route: PUT /api/users/{id}');
            }
            
            if (!RouteRegistry::isRegistered('DELETE', '/api/users/{id}')) {
                RouteRegistry::register('DELETE', '/api/users/{id}');
                $group->delete('/users/{id}', [ApiController::class, 'deleteUser']);
                error_log('Registered route: DELETE /api/users/{id}');
            }
        });
    };
} else {
    // If already registered, return an empty function
    return function (App $app) {
        // Routes already registered
        error_log('API routes already registered, skipping...');
    };
}