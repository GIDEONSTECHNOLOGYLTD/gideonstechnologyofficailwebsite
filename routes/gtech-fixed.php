<?php
/**
 * Gideon's Technology GTech Routes
 * 
 * This file contains all routes related to GTech services platform
 * Uses RouteRegistry to prevent duplicate route registrations
 */

use App\Controllers\GeneralTechController;
use App\Controllers\RepairServicesController;
use App\Controllers\HardwareRepairController;
use App\Core\RouteRegistry;
use App\Utilities\Logger;

// Define a global flag to prevent duplicate loading
if (!defined('GTECH_ROUTES_REGISTERED')) {
    define('GTECH_ROUTES_REGISTERED', true);
    
    return function ($app, $container) {
        // GTech home page
        if (!RouteRegistry::isRegistered('GET', '/gtech')) {
            RouteRegistry::register('GET', '/gtech');
            $app->get('/gtech', function ($request, $response) use ($app, $container) {
                $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GTech Services - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 2rem;
            padding-bottom: 2rem;
        }
        .service-card {
            margin-bottom: 1.5rem;
            transition: transform 0.3s;
        }
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="mb-5 text-center">
            <h1>GTech Services</h1>
            <p class="lead">Professional technology services for businesses and individuals</p>
        </header>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card service-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Web Development</h5>
                        <p class="card-text">Custom websites, web applications, and e-commerce solutions.</p>
                        <a href="/gtech/services/web-development" class="btn btn-primary">Learn More</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card service-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Hardware Repair</h5>
                        <p class="card-text">Computer, laptop, and mobile device repair services.</p>
                        <a href="/gtech/services/repair" class="btn btn-primary">Learn More</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card service-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Fintech Solutions</h5>
                        <p class="card-text">Financial technology solutions for businesses.</p>
                        <a href="/gtech/services/fintech" class="btn btn-primary">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="/" class="btn btn-outline-secondary">Back to Homepage</a>
        </div>
    </div>
    
    <footer class="text-center mt-5">
        <p>&copy; " . date('Y') . " Gideon's Technology Ltd. All rights reserved.</p>
    </footer>
</body>
</html>
HTML;

                $response->getBody()->write($html);
                return $response->withHeader('Content-Type', 'text/html');
            });
        }

        // GTech services routes
        if (!RouteRegistry::isRegistered('GET', '/gtech/services')) {
            RouteRegistry::register('GET', '/gtech/services');
            $app->group('/gtech/services', function ($group) use ($app, $container) {
                
                // Services overview
                $group->get('', function ($request, $response) use ($app, $container) {
                    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GTech Services Overview - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 2rem;
            padding-bottom: 2rem;
        }
        .service-card {
            margin-bottom: 1.5rem;
            transition: transform 0.3s;
        }
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="mb-5 text-center">
            <h1>GTech Services Overview</h1>
            <p class="lead">Explore our comprehensive technology service offerings</p>
        </header>
        
        <div class="row">
            <div class="col-md-12">
                <h2>Our Services</h2>
                <p>At Gideon's Technology, we offer a wide range of professional technology services for businesses and individuals. Our team of experts is dedicated to providing high-quality solutions tailored to your specific needs.</p>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="/gtech" class="btn btn-outline-secondary">Back to GTech Home</a>
            <a href="/" class="btn btn-outline-secondary ml-2">Main Homepage</a>
        </div>
    </div>
    
    <footer class="text-center mt-5">
        <p>&copy; " . date('Y') . " Gideon's Technology Ltd. All rights reserved.</p>
    </footer>
</body>
</html>
HTML;

                    $response->getBody()->write($html);
                    return $response->withHeader('Content-Type', 'text/html');
                });
                
                // Simple route handlers for service pages
                $group->get('/repair', function ($request, $response) {
                    $response->getBody()->write("<h1>Repair Services</h1><p>Our repair services page.</p>");
                    return $response;
                });
                
                $group->get('/web-development', function ($request, $response) {
                    $response->getBody()->write("<h1>Web Development</h1><p>Our web development services page.</p>");
                    return $response;
                });
                
                $group->get('/fintech', function ($request, $response) {
                    $response->getBody()->write("<h1>Fintech Solutions</h1><p>Our fintech solutions page.</p>");
                    return $response;
                });
            });
        }
        
        return $app;
    };
} else {
    // If already registered, return an empty function
    return function ($app, $container) {
        // Routes already registered
        Logger::info("GTech routes already registered, skipping...");
        return $app;
    };
}
