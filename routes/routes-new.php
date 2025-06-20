<?php
/**
 * Main Route Loader
 * 
 * This file loads all route files in a structured way to prevent duplicates
 * Uses RouteRegistry to prevent duplicate route registrations
 */

use App\Utilities\Logger;
use App\Core\RouteRegistry;

// Define a global flag to prevent duplicate loading
if (!defined('MAIN_ROUTES_REGISTERED')) {
    define('MAIN_ROUTES_REGISTERED', true);
}

return function ($app, $container) {
    // Root route - displaying a homepage with links to all parts of the application
    if (!RouteRegistry::isRegistered('GET', '/')) {
        RouteRegistry::register('GET', '/');
        $app->get('/', function ($request, $response) use ($container) {
            $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 2rem;
            padding-bottom: 2rem;
        }
        .card {
            margin-bottom: 1.5rem;
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="mb-5 text-center">
            <h1>Gideon's Technology</h1>
            <p class="lead">Welcome to our comprehensive technology platform</p>
        </header>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">GStore</h5>
                        <p class="card-text">Our online store for technology products and services.</p>
                        <a href="/gstore" class="btn btn-primary">Visit GStore</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">GTech Services</h5>
                        <p class="card-text">Professional technology services and solutions.</p>
                        <a href="/gtech" class="btn btn-primary">Explore Services</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">User Account</h5>
                        <p class="card-text">Manage your account, orders, and preferences.</p>
                        <a href="/user/profile" class="btn btn-primary">My Account</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Authentication</h5>
                        <p class="card-text">Login or register to access all features.</p>
                        <a href="/auth/login" class="btn btn-outline-primary me-2">Login</a>
                        <a href="/auth/register" class="btn btn-outline-success">Register</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Admin Panel</h5>
                        <p class="card-text">Administrative tools and dashboard (authorized users only).</p>
                        <a href="/admin" class="btn btn-danger">Admin Panel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="text-center mt-5">
        <p>&copy; 2025 Gideon's Technology Ltd. All rights reserved.</p>
    </footer>
</body>
</html>
HTML;

            $response->getBody()->write($html);
            return $response->withHeader('Content-Type', 'text/html');
        });
    }
    
    // Test route - simple endpoint to verify the application is working
    if (!RouteRegistry::isRegistered('GET', '/test-route')) {
        RouteRegistry::register('GET', '/test-route');
        $app->get('/test-route', function ($request, $response) {
            $response->getBody()->write("<h1>Test Route</h1><p>The application is working correctly!</p>");
            return $response;
        });
    }
    
    // API test route - returns JSON response
    if (!RouteRegistry::isRegistered('GET', '/api/test')) {
        RouteRegistry::register('GET', '/api/test');
        $app->get('/api/test', function ($request, $response) {
            $data = [
                'status' => 'success',
                'message' => 'API is working correctly',
                'timestamp' => time()
            ];
            
            $payload = json_encode($data);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        });
    }
    
    // Check for duplicate routes
    if (method_exists($app, 'getRouteCollector')) {
        $routeCollector = $app->getRouteCollector();
        $routes = $routeCollector->getRoutes();
        
        $patterns = [];
        $duplicates = [];
        
        foreach ($routes as $route) {
            $pattern = $route->getPattern();
            if (isset($patterns[$pattern])) {
                $duplicates[$pattern] = ($duplicates[$pattern] ?? 1) + 1;
            } else {
                $patterns[$pattern] = 1;
            }
        }
        
        if (!empty($duplicates)) {
            Logger::warning("Duplicate routes found: " . json_encode($duplicates));
        }
    }
    
    return $app;
};
