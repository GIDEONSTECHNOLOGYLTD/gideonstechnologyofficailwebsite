<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (\Slim\App $app) {
    // Define a simple route for testing the store functionality without database access
    $app->get('/simple-store', function (Request $request, Response $response) {
        $response->getBody()->write("<html><body><h1>Simple Store Page</h1><p>This is a simple store page that doesn't require database connections.</p></body></html>");
        return $response->withHeader('Content-Type', 'text/html');
    });
};
