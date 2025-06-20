<?php

use Psr\Http\Message\ResponseInterface as Response;
use Slim\App;

// Define routes for the application
return function (App $app) {
    // Home routes
    $app->get('/', 'App\Controllers\PageController:home');
    $app->get('/about', 'App\Controllers\PageController:about');
    $app->get('/contact', 'App\Controllers\PageController:contact');
    $app->get('/services', 'App\Controllers\ServicesController:index');
    
    // Auth routes
    $app->get('/login', 'App\Controllers\AuthController:loginForm');
    $app->post('/login', 'App\Controllers\AuthController:login');
    $app->get('/register', 'App\Controllers\AuthController:registerForm');
    $app->post('/register', 'App\Controllers\AuthController:register');
    $app->get('/logout', 'App\Controllers\AuthController:logout');
    
    // Dashboard routes
    $app->get('/dashboard', 'App\Controllers\DashboardController:index');
    
    // API routes
    $app->group('/api', function ($group) {
        // User API endpoints
        $group->group('/users', function ($group) {
            $group->get('', 'App\Controllers\Api\ApiController:getAllUsers');
            $group->get('/{id}', 'App\Controllers\Api\ApiController:getUserById');
            $group->post('', 'App\Controllers\Api\ApiController:createUser');
            $group->put('/{id}', 'App\Controllers\Api\ApiController:updateUser');
            $group->delete('/{id}', 'App\Controllers\Api\ApiController:deleteUser');
        });
        
        // Product API endpoints
        $group->group('/products', function ($group) {
            $group->get('', 'App\Controllers\Api\ApiController:getAllProducts');
            $group->get('/{id}', 'App\Controllers\Api\ApiController:getProductById');
            $group->post('', 'App\Controllers\Api\ApiController:createProduct');
            $group->put('/{id}', 'App\Controllers\Api\ApiController:updateProduct');
            $group->delete('/{id}', 'App\Controllers\Api\ApiController:deleteProduct');
        });
        
        // Services API endpoints
        $group->group('/services', function ($group) {
            $group->get('', 'App\Controllers\Api\ServiceController:getAll');
            $group->get('/{id}', 'App\Controllers\Api\ServiceController:getById');
        });
    });
};