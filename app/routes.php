<?php

use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Controllers\HomeController;
use App\Controllers\DashboardController;
use App\Controllers\ServicesController;
use App\Controllers\ProjectsController;
use App\Controllers\ProfileController;
use App\Controllers\AuthController;

return function (App $app) {
    // Home routes
    $app->get('/', HomeController::class . ':index');
    $app->get('/about', HomeController::class . ':about');
    $app->get('/contact', HomeController::class . ':contact');
    $app->post('/contact', HomeController::class . ':handleContact');
    
    // Services routes
    $app->get('/services', ServicesController::class . ':index');
    $app->get('/services/{id}', ServicesController::class . ':show');
    
    // Projects routes
    $app->get('/projects', ProjectsController::class . ':index');
    $app->get('/projects/{id}', ProjectsController::class . ':show');
    
    // Authentication routes
    $app->get('/login', AuthController::class . ':loginForm');
    $app->post('/login', AuthController::class . ':login');
    $app->get('/register', AuthController::class . ':registerForm');
    $app->post('/register', AuthController::class . ':register');
    $app->get('/logout', AuthController::class . ':logout');
    
    // User Profile routes
    $app->get('/profile', ProfileController::class . ':index');
    $app->get('/profile/edit', ProfileController::class . ':edit');
    $app->post('/profile/update', ProfileController::class . ':update');
    
    // Dashboard routes
    $app->get('/dashboard', DashboardController::class . ':index');
    
    // API routes
    $app->group('/api', function ($group) {
        $group->get('/users', 'App\Controllers\Api\UserController:index');
        $group->get('/users/{id}', 'App\Controllers\Api\UserController:show');
        $group->post('/users', 'App\Controllers\Api\UserController:store');
        $group->put('/users/{id}', 'App\Controllers\Api\UserController:update');
        $group->delete('/users/{id}', 'App\Controllers\Api\UserController:delete');
    });
};
