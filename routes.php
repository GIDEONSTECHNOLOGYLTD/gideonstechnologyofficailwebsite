<?php
/**
 * Application Routes
 * 
 * This file contains all the routes for the Slim application
 */

// Main public routes - Root route commented out to prevent duplicate with app/routes/web.php
// $app->get('/', 'App\Controllers\HomeController:index');
$app->get('/about', 'App\Controllers\HomeController:about');
$app->get('/contact', 'App\Controllers\HomeController:contact');
$app->post('/contact', 'App\Controllers\HomeController:handleContact');

// Blog routes
$app->get('/blog', 'App\Controllers\BlogController:index');
$app->get('/blog/category/{category}', 'App\Controllers\BlogController:category');
$app->get('/blog/{slug}', 'App\Controllers\BlogController:show');

// Services routes - specific routes must come BEFORE variable routes
$app->get('/services', 'App\Controllers\ServicesController:index');
$app->get('/services/web-development', 'App\Controllers\ServicesController:webDevelopment');
$app->get('/services/mobile-development', 'App\Controllers\ServicesController:mobileDevelopment');
// Generic service route should be LAST
$app->get('/services/{id}', 'App\Controllers\ServicesController:show');

// Projects routes
$app->get('/projects', 'App\Controllers\ProjectsController:index');
$app->get('/projects/{id}', 'App\Controllers\ProjectsController:show');

// Auth routes - DISABLED to prevent duplicate route registrations
// These routes are now defined in /routes/auth.php
// $app->get('/login', 'App\Controllers\AuthController:loginForm');
// $app->post('/login', 'App\Controllers\AuthController:login');
// $app->get('/register', 'App\Controllers\AuthController:registerForm');
// $app->post('/register', 'App\Controllers\AuthController:register');
// $app->get('/logout', 'App\Controllers\AuthController:logout');
// $app->get('/forgot-password', 'App\Controllers\AuthController:forgotPasswordForm');
// $app->post('/forgot-password', 'App\Controllers\AuthController:forgotPassword');
// $app->get('/reset-password/{token}', 'App\Controllers\AuthController:resetPasswordForm');
// $app->post('/reset-password', 'App\Controllers\AuthController:resetPassword');

// User area routes (protected)
$app->group('/dashboard', function($app) {
    $app->get('', 'App\Controllers\DashboardController:index');
    $app->get('/profile', 'App\Controllers\DashboardController:profile');
    $app->post('/profile', 'App\Controllers\DashboardController:updateProfile');
    $app->get('/orders', 'App\Controllers\DashboardController:orders');
    $app->get('/services', 'App\Controllers\DashboardController:services');
})->add('App\Middleware\AuthMiddleware');

// Order routes - Commented out to prevent duplicate route definitions
// These routes are now defined in app/routes/web.php and other route files
// $app->get('/orders', 'App\Controllers\OrderController:index');
// $app->get('/orders/{id}', 'App\Controllers\OrderController:show');
// $app->post('/orders', 'App\Controllers\OrderController:create');

// Profile routes
$app->get('/profile', 'App\Controllers\ProfileController:index');
$app->get('/profile/edit', 'App\Controllers\ProfileController:edit');
$app->post('/profile/update', 'App\Controllers\ProfileController:update');

// Web Templates routes
$app->get('/web-dev/templates', 'App\Controllers\TemplateController:index');
$app->get('/web-dev/templates/purchased', 'App\Controllers\TemplateController:purchased');
$app->get('/web-dev/template/{id}', 'App\Controllers\TemplateController:show');
$app->get('/web-dev/template/{id}/download', 'App\Controllers\TemplateController:download');

// API Routes
$app->group('/api', function($app) {
    // API Info
    $app->get('', 'App\Controllers\Api\ApiController:index');
    $app->get('/health', 'App\Controllers\Api\ApiController:healthCheck');
    
    // Users endpoints
    $app->get('/users', 'App\Controllers\Api\ApiController:getAllUsers');
    $app->get('/users/{id}', 'App\Controllers\Api\ApiController:getUser');
    
    // Auth endpoints
    $app->post('/auth/login', 'App\Controllers\Api\AuthController:login');
    $app->post('/auth/register', 'App\Controllers\Api\AuthController:register');
    $app->get('/auth/logout', 'App\Controllers\Api\AuthController:logout');
    
    // Templates endpoint
    $app->get('/templates', 'App\Controllers\Api\ApiController:getTemplates');
    
    // Orders endpoint
    // Commented out to prevent duplicate route definitions - now defined in routes/api.php
    // $app->get('/orders', 'App\Controllers\Api\ApiController:getOrders');
    // $app->post('/orders', 'App\Controllers\Api\ApiController:createOrder');
});

// Checkout routes
$app->get('/checkout', 'App\Controllers\CheckoutController:index');
$app->post('/checkout', 'App\Controllers\CheckoutController:process');
$app->get('/checkout/success', 'App\Controllers\CheckoutController:success');
$app->get('/checkout/cancel', 'App\Controllers\CheckoutController:cancel');

// Admin routes (protected by admin middleware)
$app->group('/admin', function($app) {
    $app->get('/dashboard', 'App\Controllers\AdminController:dashboard');
    $app->get('/users', 'App\Controllers\AdminController:users');
    // Commented out to prevent duplicate route definitions - now defined in routes/admin.php
    // $app->get('/orders', 'App\Controllers\AdminController:orders');
    $app->get('/services', 'App\Controllers\AdminController:services');
    $app->get('/settings', 'App\Controllers\AdminController:settings');
    
    // Group reports routes
    $app->group('/reports', function($app) {
        $app->get('/sales', 'App\Controllers\Admin\ReportController:sales');
        $app->get('/users', 'App\Controllers\Admin\ReportController:users');
        $app->get('/products', 'App\Controllers\Admin\ReportController:products');
    });
})->add('App\Middleware\AdminMiddleware');

// Gtech platform routes
$app->group('/gtech', function($app) {
    $app->get('', 'App\Controllers\GtechController:dashboard');
    $app->get('/services', 'App\Controllers\GtechController:services');
    $app->get('/products', 'App\Controllers\GtechController:products');
});