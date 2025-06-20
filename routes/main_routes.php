<?php
/**
 * Main Routes File
 * This file consolidates all routes for the application to prevent conflicts
 */

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

// Home routes
$app->get('/', function (Request $request, Response $response) use ($container) {
    return $container->get('renderer')->render($response, 'dashboard/index.php', [
        'appName' => 'Gideons Technology',
        'currentYear' => date('Y')
    ]);
});

$app->get('/about', function (Request $request, Response $response) use ($container) {
    return $container->get('renderer')->render($response, 'about.php', [
        'appName' => 'Gideons Technology',
        'currentYear' => date('Y')
    ]);
});

$app->get('/contact', function (Request $request, Response $response) use ($container) {
    return $container->get('renderer')->render($response, 'contact.php', [
        'appName' => 'Gideons Technology',
        'currentYear' => date('Y')
    ]);
});

// Auth routes
$app->get('/login', function (Request $request, Response $response) use ($container) {
    return $container->get('renderer')->render($response, 'auth/login.php', [
        'appName' => 'Gideons Technology'
    ]);
});

$app->post('/login', function (Request $request, Response $response) {
    // Authentication logic here
    return $response->withHeader('Location', '/')->withStatus(302);
});

$app->get('/register', function (Request $request, Response $response) use ($container) {
    return $container->get('renderer')->render($response, 'auth/register.php', [
        'appName' => 'Gideons Technology'
    ]);
});

$app->post('/register', function (Request $request, Response $response) {
    // Registration logic here
    return $response->withHeader('Location', '/login')->withStatus(302);
});

$app->get('/logout', function (Request $request, Response $response) {
    // Logout logic - destroy session
    session_start();
    session_destroy();
    return $response->withHeader('Location', '/login')->withStatus(302);
});

// Dashboard routes
$app->get('/dashboard', function (Request $request, Response $response) use ($container) {
    return $container->get('renderer')->render($response, 'dashboard/index.php', [
        'appName' => 'Gideons Technology',
        'currentYear' => date('Y')
    ]);
});

// User Dashboard
$app->get('/user/dashboard', function (Request $request, Response $response) use ($container) {
    return $container->get('renderer')->render($response, 'user/dashboard.php', [
        'appName' => 'Gideons Technology',
        'currentYear' => date('Y')
    ]);
});

// Admin routes
$app->get('/admin/dashboard', function (Request $request, Response $response) use ($container) {
    return $container->get('renderer')->render($response, 'admin/dashboard.php', [
        'appName' => 'Gideons Technology',
        'currentYear' => date('Y')
    ]);
});

$app->get('/admin/users', function (Request $request, Response $response) use ($container) {
    return $container->get('App\Http\Controllers\AdminController')->users($request, $response);
});

$app->get('/admin/products', function (Request $request, Response $response) use ($container) {
    return $container->get('App\Http\Controllers\AdminController')->products($request, $response);
});

$app->get('/admin/orders', function (Request $request, Response $response) use ($container) {
    return $container->get('App\Http\Controllers\AdminController')->orders($request, $response);
});

$app->get('/admin/settings', function (Request $request, Response $response) use ($container) {
    return $container->get('App\Http\Controllers\AdminController')->settings($request, $response);
});

// Gtech Platform routes
$app->get('/gtech', function (Request $request, Response $response) use ($container) {
    return $container->get('renderer')->render($response, 'gtech/index.php', [
        'appName' => 'Gideons Technology',
        'currentYear' => date('Y')
    ]);
});

$app->get('/gtech/services', function (Request $request, Response $response) use ($container) {
    return $container->get('App\Http\Controllers\GtechController')->services($request, $response);
});

$app->get('/gtech/products', function (Request $request, Response $response) use ($container) {
    return $container->get('App\Http\Controllers\GtechController')->products($request, $response);
});

// Profile routes
$app->get('/profile', function (Request $request, Response $response) use ($container) {
    return $container->get('App\Http\Controllers\ProfileController')->index($request, $response);
});

$app->get('/profile/edit', function (Request $request, Response $response) use ($container) {
    return $container->get('App\Http\Controllers\ProfileController')->edit($request, $response);
});

$app->post('/profile/update', function (Request $request, Response $response) use ($container) {
    return $container->get('App\Http\Controllers\ProfileController')->update($request, $response);
});

// Services routes
$app->get('/services', function (Request $request, Response $response) use ($container) {
    return $container->get('renderer')->render($response, 'services/index.php', [
        'appName' => 'Gideons Technology',
        'currentYear' => date('Y')
    ]);
});

$app->get('/services/web-development', function (Request $request, Response $response) use ($container) {
    return $container->get('renderer')->render($response, 'services/web-development.php', [
        'appName' => 'Gideons Technology',
        'currentYear' => date('Y')
    ]);
});

$app->get('/services/repair', function (Request $request, Response $response) use ($container) {
    return $container->get('renderer')->render($response, 'services/repair.php', [
        'appName' => 'Gideons Technology',
        'currentYear' => date('Y')
    ]);
});

$app->get('/services/fintech', function (Request $request, Response $response) use ($container) {
    return $container->get('renderer')->render($response, 'services/fintech.php', [
        'appName' => 'Gideons Technology',
        'currentYear' => date('Y')
    ]);
});

// Web Development Templates routes
$app->get('/web-dev/templates', function (Request $request, Response $response) use ($container) {
    return $container->get('App\Http\Controllers\TemplateController')->index($request, $response);
});

$app->get('/web-dev/templates/category/{category}', function (Request $request, Response $response, $args) use ($container) {
    return $container->get('App\Http\Controllers\TemplateController')->index($request, $response, $args);
});

$app->get('/web-dev/template/{id}', function (Request $request, Response $response, $args) use ($container) {
    return $container->get('App\Http\Controllers\TemplateController')->show($request, $response, $args);
});

// API routes
$app->group('/api', function (RouteCollectorProxy $group) use ($container) {
    // Users API
    $group->get('/users', function (Request $request, Response $response) use ($container) {
        return $container->get('App\Http\Controllers\ApiController')->getAllUsers($request, $response);
    });
    
    $group->get('/users/{id}', function (Request $request, Response $response, $args) use ($container) {
        return $container->get('App\Http\Controllers\ApiController')->getUser($request, $response, $args);
    });
    
    // Products API
    $group->get('/products', function (Request $request, Response $response) use ($container) {
        return $container->get('App\Http\Controllers\ApiController')->getAllProducts($request, $response);
    });
    
    // Templates API
    $group->get('/templates', function (Request $request, Response $response) use ($container) {
        return $container->get('App\Http\Controllers\ApiController')->getTemplates($request, $response);
    });
    
    // Orders API
    $group->get('/orders', function (Request $request, Response $response) use ($container) {
        return $container->get('App\Http\Controllers\ApiController')->getOrders($request, $response);
    });
});