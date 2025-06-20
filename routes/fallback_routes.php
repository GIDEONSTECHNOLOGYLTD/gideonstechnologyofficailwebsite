<?php
/**
 * Fallback Routes for Gideons Technology
 * 
 * This file provides fallback routes for essential navigation paths
 * to ensure consistent error-free navigation throughout the application.
 */

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Core\RouteRegistry;

return function (App $app, $container = null) {
    // Fallback GStore route
    if (!RouteRegistry::isRegistered('GET', '/gstore')) {
        RouteRegistry::register('GET', '/gstore');
        $app->get('/gstore', function (Request $request, Response $response) {
            $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GStore - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="/">Gideon's Technology</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gtech">GTech Platform</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/gstore">GStore</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/login">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container">
        <h1 class="mb-4">GStore</h1>
        <div class="row">
            <div class="col-md-3">
                <div class="list-group mb-4">
                    <a href="#" class="list-group-item list-group-item-action active">All Products</a>
                    <a href="#" class="list-group-item list-group-item-action">Computers</a>
                    <a href="#" class="list-group-item list-group-item-action">Mobile</a>
                    <a href="#" class="list-group-item list-group-item-action">Accessories</a>
                    <a href="#" class="list-group-item list-group-item-action">Software</a>
                    <a href="#" class="list-group-item list-group-item-action">Services</a>
                </div>
            </div>
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Product">
                            <div class="card-body">
                                <h5 class="card-title">Laptop Pro X1</h5>
                                <p class="card-text">High-performance laptop with 16GB RAM and 512GB SSD.</p>
                                <p class="text-primary fw-bold">$1,299.99</p>
                                <a href="#" class="btn btn-primary">Add to Cart</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Product">
                            <div class="card-body">
                                <h5 class="card-title">Smartphone Z10</h5>
                                <p class="card-text">Latest smartphone with 128GB storage and dual camera.</p>
                                <p class="text-primary fw-bold">$899.99</p>
                                <a href="#" class="btn btn-primary">Add to Cart</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Product">
                            <div class="card-body">
                                <h5 class="card-title">Wireless Headphones</h5>
                                <p class="card-text">Premium noise-canceling wireless headphones.</p>
                                <p class="text-primary fw-bold">$249.99</p>
                                <a href="#" class="btn btn-primary">Add to Cart</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; <?= date('Y') ?> Gideon's Technology. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="/" class="text-white me-3">Home</a>
                    <a href="/contact" class="text-white me-3">Contact</a>
                    <a href="/privacy" class="text-white">Privacy Policy</a>
                </div>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;

            $response->getBody()->write($html);
            return $response->withHeader('Content-Type', 'text/html');
        });
    }
    
    // Fallback GTech route
    if (!RouteRegistry::isRegistered('GET', '/gtech')) {
        RouteRegistry::register('GET', '/gtech');
        $app->get('/gtech', function (Request $request, Response $response) {
            $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GTech Platform - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="/">Gideon's Technology</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/gtech">GTech Platform</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gstore">GStore</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/login">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container">
        <h1 class="mb-4">GTech Platform</h1>
        <div class="row">
            <div class="col-lg-8">
                <p class="lead">Welcome to the GTech Platform - your one-stop solution for all technology services.</p>
                <p>The GTech Platform offers a comprehensive suite of technology services designed to meet the needs of businesses and individuals alike. From hardware repairs to software development, we've got you covered.</p>
                
                <div class="mt-4">
                    <h2>Our Services</h2>
                    <div class="row mt-3">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Hardware Repair</h5>
                                    <p class="card-text">Professional repair services for computers, laptops, and mobile devices.</p>
                                    <a href="#" class="btn btn-primary">Learn More</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Software Development</h5>
                                    <p class="card-text">Custom software solutions tailored to your business needs.</p>
                                    <a href="#" class="btn btn-primary">Learn More</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">IT Consulting</h5>
                                    <p class="card-text">Expert advice on technology strategy and implementation.</p>
                                    <a href="#" class="btn btn-primary">Learn More</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Cloud Services</h5>
                                    <p class="card-text">Secure and scalable cloud solutions for your business.</p>
                                    <a href="#" class="btn btn-primary">Learn More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="card-title">Request a Service</h3>
                        <form>
                            <div class="mb-3">
                                <label for="name" class="form-label">Your Name</label>
                                <input type="text" class="form-control" id="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="service" class="form-label">Service Type</label>
                                <select class="form-select" id="service" required>
                                    <option value="">Select a service</option>
                                    <option value="hardware">Hardware Repair</option>
                                    <option value="software">Software Development</option>
                                    <option value="consulting">IT Consulting</option>
                                    <option value="cloud">Cloud Services</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Submit Request</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; <?= date('Y') ?> Gideon's Technology. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="/" class="text-white me-3">Home</a>
                    <a href="/contact" class="text-white me-3">Contact</a>
                    <a href="/privacy" class="text-white">Privacy Policy</a>
                </div>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;

            $response->getBody()->write($html);
            return $response->withHeader('Content-Type', 'text/html');
        });
    }
};
