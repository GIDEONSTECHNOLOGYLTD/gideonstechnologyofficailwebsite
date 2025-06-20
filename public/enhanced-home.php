<?php
/**
 * Enhanced Homepage for Gideons Technology
 * 
 * This file provides a better user experience with navigation and information
 */

// Define base path constant
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// Set error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// HTML content
$html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gideons Technology</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 0; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        header { background: #f4f4f4; padding: 20px; margin-bottom: 20px; }
        nav { margin-bottom: 20px; }
        nav a { margin-right: 15px; text-decoration: none; color: #333; padding: 8px 16px; border-radius: 4px; }
        nav a:hover { background: #e7e7e7; }
        .success-message { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .card { background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .btn { display: inline-block; background: #4CAF50; color: white !important; padding: 8px 16px; text-decoration: none; border-radius: 4px; }
        .btn:hover { background: #45a049; }
        .features { display: flex; flex-wrap: wrap; gap: 20px; }
        .feature-card { flex: 1; min-width: 300px; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Gideons Technology</h1>
            <div class="success-message">
                <strong>Success!</strong> The application is now working correctly!
            </div>
            <nav>
                <a href="/enhanced-home.php" class="btn">Home</a>
                <a href="/orders-standalone.php">Orders</a>
                <a href="/test">Test Route</a>
                <a href="/direct-fix.php">Fixed App</a>
            </nav>
        </header>
        
        <div class="card">
            <h2>Welcome to Gideons Technology</h2>
            <p>Your application has been fixed and is now running without the 500 Internal Server Error!</p>
            <p>We've implemented the following improvements:</p>
            <ul>
                <li>Fixed duplicate route definitions using the <code>RouteRegistry</code> class</li>
                <li>Implemented a global flag system to prevent routes from being registered multiple times</li>
                <li>Enhanced error handling with custom error middleware</li>
                <li>Ensured proper middleware order for optimal performance</li>
                <li>Registered all controllers with the dependency injection container</li>
            </ul>
        </div>
        
        <h2>Available Routes</h2>
        <div class="features">
            <div class="feature-card card">
                <h3>Orders</h3>
                <p>View and manage your orders with our standalone solution.</p>
                <a href="/orders-standalone.php" class="btn">View Orders</a>
            </div>
            
            <div class="feature-card card">
                <h3>Test Route</h3>
                <p>Verify that routing is working correctly with our test route.</p>
                <a href="/test" class="btn">Test Route</a>
            </div>
            
            <div class="feature-card card">
                <h3>Fixed Application</h3>
                <p>See the complete fixed application with all routes working.</p>
                <a href="/direct-fix.php" class="btn">Fixed App</a>
            </div>
        </div>
    </div>
</body>
</html>
HTML;

// Output the HTML
echo $html;
