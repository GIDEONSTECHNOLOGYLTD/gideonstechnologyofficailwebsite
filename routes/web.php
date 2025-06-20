<?php

use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$renderer = new PhpRenderer('../templates');

// Make sure templates directories exist
if (!is_dir(__DIR__ . '/../templates/user')) {
    mkdir(__DIR__ . '/../templates/user', 0755, true);
}

if (!is_dir(__DIR__ . '/../templates/admin')) {
    mkdir(__DIR__ . '/../templates/admin', 0755, true);
}

if (!is_dir(__DIR__ . '/../templates/gtech')) {
    mkdir(__DIR__ . '/../templates/gtech', 0755, true);
}

// Make sure the template files for user and admin dashboards exist
if (!file_exists(__DIR__ . '/../templates/user/dashboard.php')) {
    // Create a simple dashboard template
    file_put_contents(__DIR__ . '/../templates/user/dashboard.php', '<?php
<!DOCTYPE html>
<html>
<head>
    <title><?= $appName ?> - User Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>User Dashboard</h1>
        <p>Welcome to your personal dashboard at <?= $appName ?>.</p>
        
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Profile</div>
                    <div class="card-body">
                        <p>View and edit your profile information</p>
                        <a href="/profile" class="btn btn-primary">Go to Profile</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Orders</div>
                    <div class="card-body">
                        <p>Track and manage your orders</p>
                        <a href="/orders" class="btn btn-primary">View Orders</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Services</div>
                    <div class="card-body">
                        <p>Access Gtech platform services</p>
                        <a href="/gtech" class="btn btn-primary">Gtech Platform</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="/logout" class="btn btn-danger">Logout</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>');
}

// Create the admin dashboard template if it doesn't exist
if (!file_exists(__DIR__ . '/../templates/admin/dashboard.php')) {
    // Create a simple admin dashboard template
    file_put_contents(__DIR__ . '/../templates/admin/dashboard.php', '<?php
<!DOCTYPE html>
<html>
<head>
    <title><?= $appName ?> - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Admin Dashboard</h1>
        <p>Welcome to the administrator dashboard at <?= $appName ?>.</p>
        
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">Users</div>
                    <div class="card-body">
                        <p>Manage user accounts</p>
                        <a href="/admin/users" class="btn btn-primary">Manage Users</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">Orders</div>
                    <div class="card-body">
                        <p>View and manage all orders</p>
                        <a href="/admin/orders" class="btn btn-primary">Manage Orders</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">Services</div>
                    <div class="card-body">
                        <p>Manage platform services</p>
                        <a href="/admin/services" class="btn btn-primary">Manage Services</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">Settings</div>
                    <div class="card-body">
                        <p>Configure platform settings</p>
                        <a href="/admin/settings" class="btn btn-primary">System Settings</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="/logout" class="btn btn-danger">Logout</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>');
}

// Create the Gtech platform template if it doesn't exist
if (!file_exists(__DIR__ . '/../templates/gtech/index.php')) {
    // Create a simple Gtech platform template
    file_put_contents(__DIR__ . '/../templates/gtech/index.php', '<?php
<!DOCTYPE html>
<html>
<head>
    <title><?= $appName ?> - Gtech Platform</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Gtech Platform</h1>
        <p>Welcome to the Gtech Platform - Your gateway to all of Gideons Technology services.</p>
        
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Web Development</div>
                    <div class="card-body">
                        <p>Custom website and application development services</p>
                        <a href="/services/web-development" class="btn btn-primary">Learn More</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Fintech Solutions</div>
                    <div class="card-body">
                        <p>Financial technology services and solutions</p>
                        <a href="/services/fintech" class="btn btn-primary">Learn More</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Hardware & Repairs</div>
                    <div class="card-body">
                        <p>Computer hardware sales and repair services</p>
                        <a href="/services/repair" class="btn btn-primary">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="/user/dashboard" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>');
}

// Define routes using Slim
// Home route - ONLY ONE DEFINITION
$app->get('/', function (Request $request, Response $response) use ($container) {
    $renderer = $container->get('renderer');
    return $renderer->render($response, 'dashboard/index.php', [
        'appName' => $container->get('settings')['appName'],
        'currentYear' => $container->get('settings')['currentYear']
    ]);
});

// Auth routes are now defined in auth.php to prevent conflicts
// Login, logout, and registration routes are centralized there
// This comment is kept for documentation purposes

// Dashboard routes
if (!RouteRegistry::isRegistered('GET', '/dashboard')) {
    RouteRegistry::register('GET', '/dashboard');
    $app->get('/dashboard', [\App\Controllers\DashboardController::class, 'index'])->setName('dashboard');
}

// User Dashboard route
$app->get('/user/dashboard', function (Request $request, Response $response) use ($container) {
    $renderer = $container->get('renderer');
    return $renderer->render($response, 'user/dashboard.php', [
        'appName' => $container->get('settings')['appName'],
        'currentYear' => $container->get('settings')['currentYear']
    ]);
});

// Admin Dashboard route
$app->get('/admin/dashboard', function (Request $request, Response $response) use ($container) {
    $renderer = $container->get('renderer');
    return $renderer->render($response, 'admin/dashboard.php', [
        'appName' => $container->get('settings')['appName'],
        'currentYear' => $container->get('settings')['currentYear']
    ]);
});

// Gtech Platform route
$app->get('/gtech', function (Request $request, Response $response) use ($container) {
    $renderer = $container->get('renderer');
    return $renderer->render($response, 'gtech/index.php', [
        'appName' => $container->get('settings')['appName'],
        'currentYear' => $container->get('settings')['currentYear']
    ]);
});

// Profile Routes
$app->get('/profile', '\App\Controllers\ProfileController:index');
$app->get('/profile/edit', '\App\Controllers\ProfileController:edit');
$app->post('/profile/update', '\App\Controllers\ProfileController:update');

// Template browsing and purchasing routes
$app->get('/web-dev/templates', '\App\Controllers\TemplateController:index');
$app->get('/web-dev/templates/category/{category}', '\App\Controllers\TemplateController:byCategory');
$app->get('/web-dev/template/{id}', '\App\Controllers\TemplateController:show');
$app->post('/web-dev/template/{id}/purchase', '\App\Controllers\TemplateController:purchase');
$app->get('/web-dev/templates/purchased', '\App\Controllers\TemplateController:purchased');
$app->get('/web-dev/template/{id}/download', '\App\Controllers\TemplateController:download');

// Dashboard Template Management
$app->get('/dashboard/templates', '\App\Controllers\TemplateController:purchased');

// Orders routes
$app->get('/orders', '\App\Controllers\OrdersController:index');
$app->get('/orders/{id}', '\App\Controllers\OrdersController:show');
$app->get('/orders/{id}/invoice', function($request, $response, $args) {
    // Mock invoice endpoint for now
    return $response->withHeader('Location', '/orders/' . $args['id'])->withStatus(302);
});

// Dashboard orders shortcut
$app->get('/dashboard/orders', function($request, $response) {
    return $response->withHeader('Location', '/orders')->withStatus(302);
});

// GStore (Gtech Store) routes
$app->get('/gstore', '\App\Controllers\GStoreController:index');
$app->get('/gstore/product/{id}', '\App\Controllers\GStoreController:product');
$app->get('/gstore/category/{category}', '\App\Controllers\GStoreController:category');
$app->get('/gstore/cart', '\App\Controllers\GStoreController:cart');
$app->get('/gstore/checkout', '\App\Controllers\GStoreController:checkout');
$app->get('/gstore/wishlist', '\App\Controllers\GStoreController:wishlist');
$app->post('/gstore/cart/add', '\App\Controllers\GStoreController:addToCart');
$app->post('/gstore/cart/remove', '\App\Controllers\GStoreController:removeFromCart');
$app->post('/gstore/wishlist/add', '\App\Controllers\GStoreController:addToWishlist');
$app->post('/gstore/checkout/process', '\App\Controllers\GStoreController:processCheckout');

// Include admin routes - This needs to be after $app is initialized
require __DIR__ . '/admin.php';

$app->run();
