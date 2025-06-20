<?php

/**
 * Application Routes
 * Define all application routes here
 */

/**
 * Route Configurations
 */

// Define the resource method if it doesn't exist elsewhere
if (!function_exists('resource')) {
    /**
     * Define a resourceful route for a controller
     * 
     * @param string $name The resource name
     * @param string $controller The controller class
     * @param array $options Additional options
     * @return void
     */
    function resource($name, $controller, $options = []) {
        global $router;
        
        // Create standard RESTful routes
        $router->get("/$name", "$controller@index");
        $router->get("/$name/create", "$controller@create");
        $router->post("/$name", "$controller@store");
        $router->get("/$name/{id}", "$controller@show");
        $router->get("/$name/{id}/edit", "$controller@edit");
        // Using post with _method parameter instead of put/patch
        $router->post("/$name/{id}", "$controller@update");
        // Using post with _method parameter instead of delete
        $router->post("/$name/{id}/delete", "$controller@destroy");
    }
}

// Check if the function is already declared to avoid redeclaration errors
if (!function_exists('registerRoutes')) {
    /**
     * Register application routes
     * 
     * @param \App\Core\Router $router Router instance
     */
    function registerRoutes($router) {
        // Home routes
        $router->get('/', 'HomeController@index');
        $router->get('/about', 'HomeController@about');
        $router->get('/contact', 'HomeController@contact');
        
        // Auth routes
        $router->get('/login', 'AuthController@loginForm');
        $router->post('/login', 'AuthController@login');
        $router->get('/register', 'AuthController@registerForm');
        $router->post('/register', 'AuthController@register');
        $router->get('/logout', 'AuthController@logout');
        
        // Profile route
        $router->get('/profile', 'ProfileController@index');
        
        // Dashboard routes - protected by auth middleware
        $router->get('/dashboard', 'DashboardController@index');
        
        // RESTful resource routes example
        resource('/posts', 'PostController');
        
        // API routes
        $router->get('/api/users', 'Api\UserController@index');
        $router->get('/api/users/{id}', 'Api\UserController@show');
    }
}

// Let's check the content of the routes file without modifying it