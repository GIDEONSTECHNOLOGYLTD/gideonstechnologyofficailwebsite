<?php
// Templates category page
$html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Templates - Gideon's Technology</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
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
                        <a class="nav-link" href="/services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/gstore">Store</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="/gstore" class="text-decoration-none">Store</a></li>
                <li class="breadcrumb-item active" aria-current="page">Website Templates</li>
            </ol>
        </nav>

        <h1 class="mb-4">Website Templates</h1>
        <p class="lead mb-5">Browse our collection of premium website templates for various industries and purposes.</p>

        <div class="row">
            <!-- Template cards -->
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="/images/placeholder.jpg" class="card-img-top" alt="Business Pro Template">
                    <div class="card-body">
                        <h5 class="card-title">Business Pro</h5>
                        <p class="card-text">Professional template for corporate websites with modern design.</p>
                        <p class="card-text text-primary fw-bold">$59.99</p>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="/gstore/product/31" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="/images/placeholder.jpg" class="card-img-top" alt="E-Commerce Plus Template">
                    <div class="card-body">
                        <h5 class="card-title">E-Commerce Plus</h5>
                        <p class="card-text">Complete online store template with product pages and checkout system.</p>
                        <p class="card-text text-primary fw-bold">$79.99</p>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="/gstore/product/32" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="/images/placeholder.jpg" class="card-img-top" alt="Portfolio Max Template">
                    <div class="card-body">
                        <h5 class="card-title">Portfolio Max</h5>
                        <p class="card-text">Showcase your work with this elegant portfolio template for creatives.</p>
                        <p class="card-text text-primary fw-bold">$49.99</p>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="/gstore/product/33" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="/images/placeholder.jpg" class="card-img-top" alt="Restaurant Deluxe Template">
                    <div class="card-body">
                        <h5 class="card-title">Restaurant Deluxe</h5>
                        <p class="card-text">Perfect template for restaurants with menu display and reservation system.</p>
                        <p class="card-text text-primary fw-bold">$69.99</p>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="/gstore/product/34" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="/images/placeholder.jpg" class="card-img-top" alt="Blog Master Template">
                    <div class="card-body">
                        <h5 class="card-title">Blog Master</h5>
                        <p class="card-text">Clean and responsive blog template with multiple layout options.</p>
                        <p class="card-text text-primary fw-bold">$39.99</p>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="/gstore/product/35" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="/images/placeholder.jpg" class="card-img-top" alt="Real Estate Pro Template">
                    <div class="card-body">
                        <h5 class="card-title">Real Estate Pro</h5>
                        <p class="card-text">Specialized template for real estate agencies with property listings.</p>
                        <p class="card-text text-primary fw-bold">$89.99</p>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="/gstore/product/36" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pagination -->
        <nav aria-label="Product pagination" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#">Next</a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Gideon's Technology</h5>
                    <p>Providing cutting-edge technology solutions since 2020.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="/" class="text-white">Home</a></li>
                        <li><a href="/services" class="text-white">Services</a></li>
                        <li><a href="/gstore" class="text-white">Store</a></li>
                        <li><a href="/about" class="text-white">About</a></li>
                        <li><a href="/contact" class="text-white">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Us</h5>
                    <address>
                        123 Tech Street<br>
                        San Francisco, CA 94107<br>
                        <a href="mailto:info@gideonstech.com" class="text-white">info@gideonstech.com</a><br>
                        <a href="tel:+1234567890" class="text-white">(123) 456-7890</a>
                    </address>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p class="mb-0">&copy; 2025 Gideon's Technology. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;

echo $html;
