<?php
/**
 * API Routes for Gideons Technology application
 * 
 * This file contains all API endpoints with proper versioning.
 * All responses follow a consistent JSON format.
 */

use Slim\App;
use Slim\Routing\RouteCollectorProxy;

// Controllers
use App\Controllers\Api\V1\AuthController;
use App\Controllers\Api\V1\UserController;
use App\Controllers\Api\V1\ProductController;
use App\Controllers\Api\V1\OrderController;
use App\Controllers\Api\V1\ServiceController;
use App\Controllers\Api\V1\TemplateController;

// Middleware
use App\Middleware\ApiAuthMiddleware;
use App\Middleware\ApiRateLimitMiddleware;

return function (App $app) {
    // API Routes with versioning
    $app->group('/api', function (RouteCollectorProxy $group) {
        // API Info
        $group->get('', function ($request, $response) {
            $data = [
                'name' => 'Gideons Technology API',
                'version' => '1.0',
                'status' => 'active',
                'documentation' => '/api/docs'
            ];
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $data
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
        });
        
        // API Documentation
        $group->get('/docs', function ($request, $response) {
            // Redirect to API documentation page
            return $response->withHeader('Location', '/documentation/api')->withStatus(302);
        });
        
        // Health Check
        $group->get('/health', function ($request, $response) {
            $data = [
                'status' => 'healthy',
                'timestamp' => time(),
                'environment' => getenv('APP_ENV') ?: 'production'
            ];
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $data
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
        });
        
        // Version 1 API Routes
        $group->group('/v1', function (RouteCollectorProxy $group) {
            // Authentication endpoints - no auth middleware
            $group->post('/auth/login', [AuthController::class, 'login']);
            $group->post('/auth/register', [AuthController::class, 'register']);
            $group->post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
            $group->post('/auth/reset-password', [AuthController::class, 'resetPassword']);
            
            // Protected routes - require authentication
            $group->group('', function (RouteCollectorProxy $group) {
                // Auth endpoint that requires authentication
                $group->get('/auth/me', [AuthController::class, 'me']);
                $group->post('/auth/logout', [AuthController::class, 'logout']);
                $group->put('/auth/change-password', [AuthController::class, 'changePassword']);
                
                // User endpoints
                $group->get('/users', [UserController::class, 'index']);
                $group->get('/users/{id}', [UserController::class, 'show']);
                $group->put('/users/{id}', [UserController::class, 'update']);
                $group->delete('/users/{id}', [UserController::class, 'delete']);
                
                // Product endpoints
                $group->get('/products', [ProductController::class, 'index']);
                $group->get('/products/search', [ProductController::class, 'search']);
                $group->get('/products/{id}', [ProductController::class, 'show']);
                $group->post('/products', [ProductController::class, 'store']);
                $group->put('/products/{id}', [ProductController::class, 'update']);
                $group->delete('/products/{id}', [ProductController::class, 'delete']);
                
                // Order endpoints
                $group->get('/orders', [OrderController::class, 'index']);
                $group->get('/orders/{id}', [OrderController::class, 'show']);
                $group->post('/orders', [OrderController::class, 'store']);
                $group->put('/orders/{id}', [OrderController::class, 'update']);
                $group->put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
                $group->delete('/orders/{id}', [OrderController::class, 'delete']);
                
                // Service endpoints
                $group->get('/services', [ServiceController::class, 'index']);
                $group->get('/services/{id}', [ServiceController::class, 'show']);
                
                // Template endpoints
                $group->get('/templates', [TemplateController::class, 'index']);
                $group->get('/templates/{id}', [TemplateController::class, 'show']);
            })->add(new ApiAuthMiddleware());
        })->add(new ApiRateLimitMiddleware());
    });
    
    // Legacy API routes - redirect to versioned endpoints
    // These routes ensure backward compatibility while encouraging migration to versioned API
    $app->get('/web-dev/templates', function ($request, $response) {
        return $response->withHeader('Location', '/api/v1/templates')->withStatus(301);
    });
    
    $app->get('/orders', function ($request, $response) {
        return $response->withHeader('Location', '/api/v1/orders')->withStatus(301);
    });
    
    $app->post('/orders', function ($request, $response) {
        return $response->withHeader('Location', '/api/v1/orders')->withStatus(301);
    });
};