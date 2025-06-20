<?php

/**
 * Authentication Routes
 * 
 * This file defines all routes for user authentication (login, register, logout).
 * Uses RouteRegistry to prevent duplicate route registrations
 */

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\AuthController;
use App\Http\Controllers\GtechController;
use App\Core\RouteRegistry;
use App\Core\RouteManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

// Define a global flag to prevent duplicate loading
if (!defined('AUTH_ROUTES_REGISTERED')) {
    define('AUTH_ROUTES_REGISTERED', true);
    
    return function (App $app, ?ContainerInterface $container = null) {
        // Create a RouteManager instance
        $routeManager = new RouteManager($app, $container);
        
        // Non-grouped routes - using RouteManager
        $routeManager->get('/contact', ['App\Http\Controllers\GtechController', 'contact'], 'contact', 'default');
        
        // Access denied route
        if (!RouteRegistry::isRegistered('GET', '/auth/access-denied')) {
            RouteRegistry::register('GET', '/auth/access-denied');
            $app->get('/auth/access-denied', function (Request $request, Response $response) use ($container) {
                return $container->get('renderer')->render($response->withStatus(403), 'errors/access-denied.php', [
                    'title' => 'Access Denied',
                    'message' => 'You do not have permission to access this page. Please log in with an administrator account.',
                    'appName' => $container->get('settings')['appName'] ?? 'Gideon\'s Technology'
                ]);
            })->setName('auth.access-denied');
        }
        
        // Auth routes - grouped under /auth prefix using RouteManager
        $routeManager->group('/auth', function ($group) use ($routeManager, $app) {
            // Login routes - support both /auth/login and /login for compatibility
            $routeManager->get('/login', ['App\Controllers\AuthController', 'loginForm'], 'auth.login', 'html');
            
            // Also register the root /login route to ensure all links work
            if (!RouteRegistry::isRegistered('GET', '/login')) {
                RouteRegistry::register('GET', '/login');
                $app->get('/login', ['App\Controllers\AuthController', 'loginForm'])->setName('login');
            }
            
            // Login submission routes - support both paths
            $routeManager->post('/login', ['App\Controllers\AuthController', 'login'], 'auth.login.submit', 'default');
            
            if (!RouteRegistry::isRegistered('POST', '/login')) {
                RouteRegistry::register('POST', '/login');
                $app->post('/login', ['App\Controllers\AuthController', 'login']);
            }
            
            // Register form route
            $routeManager->get('/register', ['App\Controllers\AuthController', 'registerForm'], 'auth.register', 'html');
            
            // Also register the root /register route to ensure all links work
            if (!RouteRegistry::isRegistered('GET', '/register')) {
                RouteRegistry::register('GET', '/register');
                $app->get('/register', ['App\Controllers\AuthController', 'registerForm'])->setName('register');
            }
            
            // Register submission route
            $routeManager->post('/register', ['App\Controllers\AuthController', 'register'], 'auth.register.submit', 'default');
            
            // Also register the root /register POST route to ensure all form submissions work
            if (!RouteRegistry::isRegistered('POST', '/register')) {
                RouteRegistry::register('POST', '/register');
                $app->post('/register', ['App\Controllers\AuthController', 'register']);
            }
            
            // Logout route
            $routeManager->get('/logout', ['App\Controllers\AuthController', 'logout'], 'auth.logout', 'default');
            
            // Forgot password routes
            $routeManager->get('/forgot-password', ['App\Controllers\AuthController', 'forgotPasswordForm'], 'auth.forgot', 'html');
            $routeManager->post('/forgot-password', ['App\Controllers\AuthController', 'forgotPassword'], 'auth.forgot.submit', 'default');
            
            // Reset password routes
            $routeManager->get('/reset-password/{token}', ['App\Controllers\AuthController', 'resetPasswordForm'], 'auth.reset', 'html');
            $routeManager->post('/reset-password', ['App\Controllers\AuthController', 'resetPassword'], 'auth.reset.submit', 'default');
        });
    };
} else {
    // If already registered, return an empty function
    return function (App $app, ?ContainerInterface $container = null) {
        // Routes already registered
        error_log('Auth routes already registered, skipping...');
    };
}
