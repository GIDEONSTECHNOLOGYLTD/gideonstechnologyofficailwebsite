<?php
/**
 * Services Routes - Consolidated with GTech Platform
 * 
 * This file now redirects general services to the GTech platform services.
 * This consolidation ensures a single source of truth for all services.
 */

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Core\RouteRegistry;

return function (App $app) {
    // Get container
    $container = $app->getContainer();
    
    // Check if GTech services routes are already registered
    // This prevents duplicate route registration errors
    if (defined('GTECH_SERVICES_ROUTES_REGISTERED')) {
        // GTech services routes are already registered, so we'll avoid registering duplicates
        // Just log this information
        error_log('GTech services routes already registered, avoiding duplicates in services.php');
    }
    
    // IMPORTANT: All services are now consolidated under the GTech platform
    // The standalone /services route now redirects to the GTech services with a banner
    // explaining the consolidation
    
    // Main services route - redirect to GTech services
    if (!RouteRegistry::isRegistered('GET', '/services')) {
        RouteRegistry::register('GET', '/services');
        $app->get('/services', function (Request $request, Response $response) {
            // Implement a permanent redirect (301) to the GTech services page
            return $response
                ->withHeader('Location', '/gtech/services')
                ->withStatus(301);
        });
    }
    
    // Handle specific service routes and redirect them to GTech equivalents
    $serviceTypes = ['web-dev', 'repair', 'fintech'];
    
    foreach ($serviceTypes as $serviceType) {
        $route = "/services/{$serviceType}";
        if (!RouteRegistry::isRegistered('GET', $route)) {
            RouteRegistry::register('GET', $route);
            $app->get($route, function (Request $request, Response $response) use ($serviceType) {
                // Redirect to the equivalent GTech service page
                return $response
                    ->withHeader('Location', "/gtech/services/{$serviceType}")
                    ->withStatus(301);
            });
        }
    }
    
    // Individual service detail routes - avoid using catch-all to prevent conflicts
    // Instead, register specific ID routes for services
    if (!RouteRegistry::isRegistered('GET', '/services/details/{id}')) {
        RouteRegistry::register('GET', '/services/details/{id}');
        $app->get('/services/details/{id}', function (Request $request, Response $response, $args) {
            $id = $args['id'] ?? '';
            return $response
                ->withHeader('Location', "/gtech/services/details/{$id}")
                ->withStatus(301);
        });
    }

    // Admin service routes
    if (!RouteRegistry::isRegistered('GROUP', '/admin/services')) {
        RouteRegistry::register('GROUP', '/admin/services');
        $app->group('/admin/services', function (RouteCollectorProxy $group) {
            // List all services
            $group->get('', 'App\Controllers\AdminController:getAllServices');
            
            // Create new service
            $group->get('/create', 'App\Controllers\AdminController:createService');
            $group->post('/store', 'App\Controllers\AdminController:storeService');
            
            // Edit service
            $group->get('/{id}/edit', 'App\Controllers\AdminController:editService');
            $group->post('/{id}/update', 'App\Controllers\AdminController:updateService');
            
            // Delete service
            $group->post('/{id}/delete', 'App\Controllers\AdminController:deleteService');
        })->add(new \App\Middleware\AdminMiddleware());
    }
    
    // Register individual service routes
    if (!RouteRegistry::isRegistered('GET', '/services/{id}')) {
        RouteRegistry::register('GET', '/services/{id}');
        $app->get('/services/{id}', function (Request $request, Response $response, array $args) {
            $id = $args['id'];
            $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Details - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Service #{$id} Details</h1>
        <p>This page shows details for a specific service.</p>
        <a href="/services" class="btn btn-primary">Back to Services</a>
    </div>
</body>
</html>
HTML;
            
            $response->getBody()->write($html);
            return $response->withHeader('Content-Type', 'text/html');
        });
    }

    return $app;
};