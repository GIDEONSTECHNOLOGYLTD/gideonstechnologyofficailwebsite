<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Core\RouteRegistry;

return function (\Slim\App $app) {
    // Define a simple route for testing the gstore functionality without database access
    // Use RouteRegistry to prevent duplicate route registrations
    if (!RouteRegistry::isRegistered('GET', '/gstore-test')) {
        RouteRegistry::register('GET', '/gstore-test');
        $app->get('/gstore-test', function (Request $request, Response $response) {
            $response->getBody()->write("<html><body><h1>GStore Test Page</h1><p>This is a simple GStore test page that doesn't require database connections.</p></body></html>");
            return $response->withHeader('Content-Type', 'text/html');
        });
    }
    
    // Add a few more test routes for GStore functionality
    if (!RouteRegistry::isRegistered('GET', '/gstore-test/products')) {
        RouteRegistry::register('GET', '/gstore-test/products');
        $app->get('/gstore-test/products', function (Request $request, Response $response) {
            $response->getBody()->write("<html><body><h1>GStore Test Products</h1><p>This is a test page for GStore products.</p></body></html>");
            return $response->withHeader('Content-Type', 'text/html');
        });
    }
};
