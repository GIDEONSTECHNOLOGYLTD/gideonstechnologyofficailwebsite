<?php

use App\Controllers\SimpleTestController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// Ensure BASE_PATH is defined
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

return function (\Slim\App $app) {
    // Define simple routes that don't require database connections
    
    // JSON response route
    $app->get('/api/simple', function (Request $request, Response $response) {
        $controller = new SimpleTestController();
        return $controller->index($request, $response);
    });
    
    // Define a simple route for testing without database access
    $app->get('/simple-test-alt', function (Request $request, Response $response) {
        $response->getBody()->write("<html><body><h1>Simple Test Page (Alt)</h1><p>This is a simple test page (alternative version).</p></body></html>");
        return $response->withHeader('Content-Type', 'text/html');
    });
    
    // Add a test route for gstore to bypass the complex controller logic
    // Use RouteRegistry to prevent duplicate route registrations
    if (!\App\Core\RouteRegistry::isRegistered('GET', '/gstore-test')) {
        \App\Core\RouteRegistry::register('GET', '/gstore-test');
        $app->get('/gstore-test', function (Request $request, Response $response) {
            $response->getBody()->write("<html><body><h1>GStore Test Page</h1><p>This is a simple test page for GStore.</p></body></html>");
            return $response->withHeader('Content-Type', 'text/html');
        });
    }
    
    // HTML response route
    $app->get('/simple', function (Request $request, Response $response) {
        $controller = new SimpleTestController();
        return $controller->html($request, $response);
    });
};
