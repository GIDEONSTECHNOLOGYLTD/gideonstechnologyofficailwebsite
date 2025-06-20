<?php
/**
 * Direct GStore Handler
 * 
 * This is a standalone file that handles the GStore route directly
 * without relying on the Slim framework's routing system.
 */

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Output the GStore page HTML
echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GStore - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 2rem;
            padding-bottom: 2rem;
        }
        .product-card {
            margin-bottom: 1.5rem;
            transition: transform 0.3s;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="mb-5">
            <div class="d-flex justify-content-between align-items-center">
                <h1>GStore</h1>
                <div>
                    <a href="/gstore/cart" class="btn btn-outline-primary"><i class="bi bi-cart"></i> Cart</a>
                    <a href="/auth/login" class="btn btn-outline-secondary ms-2">Login</a>
                </div>
            </div>
            <p class="lead">Your one-stop shop for technology products and services</p>
        </header>
        
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Categories</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="/gstore/products/category/computers" class="btn btn-sm btn-outline-primary">Computers</a>
                            <a href="/gstore/products/category/mobile" class="btn btn-sm btn-outline-primary">Mobile Devices</a>
                            <a href="/gstore/products/category/accessories" class="btn btn-sm btn-outline-primary">Accessories</a>
                            <a href="/gstore/products/category/software" class="btn btn-sm btn-outline-primary">Software</a>
                            <a href="/gstore/products/category/services" class="btn btn-sm btn-outline-primary">Services</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <h2 class="mb-4">Featured Products</h2>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card product-card h-100">
                    <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Product Image">
                    <div class="card-body">
                        <h5 class="card-title">Premium Laptop</h5>
                        <p class="card-text">High-performance laptop with the latest specifications.</p>
                        <p class="text-primary fw-bold">$1,299.99</p>
                        <a href="/gstore/products/1" class="btn btn-primary">View Details</a>
                        <a href="/gstore/cart/add/1" class="btn btn-outline-success">Add to Cart</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card product-card h-100">
                    <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Product Image">
                    <div class="card-body">
                        <h5 class="card-title">Wireless Headphones</h5>
                        <p class="card-text">Premium wireless headphones with noise cancellation.</p>
                        <p class="text-primary fw-bold">$249.99</p>
                        <a href="/gstore/products/2" class="btn btn-primary">View Details</a>
                        <a href="/gstore/cart/add/2" class="btn btn-outline-success">Add to Cart</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card product-card h-100">
                    <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Product Image">
                    <div class="card-body">
                        <h5 class="card-title">Smart Watch</h5>
                        <p class="card-text">Feature-rich smart watch with health monitoring.</p>
                        <p class="text-primary fw-bold">$199.99</p>
                        <a href="/gstore/products/3" class="btn btn-primary">View Details</a>
                        <a href="/gstore/cart/add/3" class="btn btn-outline-success">Add to Cart</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="/gstore/products" class="btn btn-outline-primary">View All Products</a>
            <a href="/" class="btn btn-outline-secondary ms-2">Back to Homepage</a>
        </div>
    </div>
    
    <footer class="text-center mt-5">
        <p>&copy; <?= date('Y') ?> Gideon's Technology Ltd. All rights reserved.</p>
    </footer>
</body>
</html>
HTML;
