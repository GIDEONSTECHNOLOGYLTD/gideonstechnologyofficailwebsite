<?php

use App\Controllers\TestController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (\Slim\App $app) {
    $container = $app->getContainer();
    
    // Define the test route using the controller class directly
    $app->get('/test', function (Request $request, Response $response) use ($container) {
        // Create controller instance directly with dependencies from container
        $controller = new TestController(
            $container->get('twig.view'),
            $container->get(Messages::class)
        );
        return $controller->test($request, $response);
    });
    
    // Define a simple test route that doesn't use database connections
    $app->get('/simple-test', function (Request $request, Response $response) use ($container) {
        // Create controller instance directly with dependencies from container
        $controller = new TestController(
            $container->get('twig.view'),
            $container->get(Messages::class)
        );
        return $controller->simpleTest($request, $response);
    });
};
