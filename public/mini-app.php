<?php
/**
 * Standalone Mini-Application
 * 
 * This is a completely standalone mini-application that demonstrates Twig working correctly
 * without relying on the main application bootstrap or database connections.
 */

// Include the Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Twig\Loader\FilesystemLoader;

// Set up paths
$basePath = dirname(__DIR__);
$viewPath = $basePath . '/resources/views';
$cachePath = $basePath . '/storage/cache/views';

// Ensure cache directory exists
if (!file_exists($cachePath)) {
    mkdir($cachePath, 0777, true);
}

// Create the Slim app first
$app = AppFactory::create();

// Create Twig loader and environment
$loader = new FilesystemLoader($viewPath);
$twig = new Twig($loader, [
    'cache' => false,
    'debug' => true,
    'auto_reload' => true,
]);

// Add Twig middleware
$app->add(TwigMiddleware::create($app, $twig));

// Define a route for the root path
$app->get('/', function (Request $request, Response $response) use ($twig) {
    return $twig->render($response, 'test.twig', [
        'name' => 'Mini-App User',
        'time' => date('Y-m-d H:i:s')
    ]);
});

// Define a route for testing Twig templates
$app->get('/mini-test', function (Request $request, Response $response) use ($twig) {
    return $twig->render($response, 'test.twig', [
        'name' => 'Mini-App User',
        'time' => date('Y-m-d H:i:s')
    ]);
});

// Define a simple JSON route
$app->get('/mini-api', function (Request $request, Response $response) {
    $data = [
        'status' => 'success',
        'message' => 'Mini-app API is working',
        'time' => date('Y-m-d H:i:s')
    ];
    
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json');
});

// Add error handling
$app->addErrorMiddleware(true, true, true);

// Run the app
$app->run();
