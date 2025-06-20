<?php

use App\Controllers\TwigTestController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (\Slim\App $app) {
    $container = $app->getContainer();
    
    // Define a route for testing Twig templates without database access
    $app->get('/twig-test', function (Request $request, Response $response) use ($container) {
        // Create a simple Twig environment for testing without database dependencies
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../resources/views');
        $twig = new \Slim\Views\Twig($loader, ['cache' => false, 'debug' => true]);
        
        // Create controller instance with the Twig view
        $controller = new TwigTestController($twig);
        return $controller->index($request, $response);
    });
};
