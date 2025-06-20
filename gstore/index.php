<?php
/**
 * Store homepage for Gideons Technology
 */

// Set variables for template
$appName = 'Gideons Technology';
$currentYear = date('Y');
$pageTitle = 'Store';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $appName ?> - <?= $pageTitle ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php"><?= $appName ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Services</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../templates/services/web-development/applications.php">Web Applications</a></li>
                            <li><a class="dropdown-item" href="../templates/services/web-development/ecommerce.php">E-commerce</a></li>
                            <li><a class="dropdown-item" href="../templates/services/web-development/design.php">Web Design</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Store</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../contact.php">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="bg-primary text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold">Gideons Technology Store</h1>
                    <p class="lead">Premium tech products and digital solutions for your business</p>
                    <button class="btn btn-light btn-lg mt-3">Shop Now</button>
                </div>
                <div class="col-lg-4">
                    <img src="https://via.placeholder.com/500x300?text=Store" alt="Gideons Technology Store" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </header>

    <!-- Featured Products -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Featured Products</h2>
            
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <div class="col">
                    <div class="card h-100">
                        <img src="https://via.placeholder.com/300x200?text=Product+1" class="card-img-top" alt="Product 1">
                        <div class="card-body">
                            <h5 class="card-title">Premium Website Template</h5>
                            <p class="card-text">Responsive, modern design with full customization options.</p>
                            <p class="fw-bold text-primary fs-4">$49.99</p>
                            <button class="btn btn-primary">Add to Cart</button>
                        </div>
                    </div>
                </div>
                
                <div class="col">
                    <div class="card h-100">
                        <img src="https://via.placeholder.com/300x200?text=Product+2" class="card-img-top" alt="Product 2">
                        <div class="card-body">
                            <h5 class="card-title">SEO Starter Package</h5>
                            <p class="card-text">Complete SEO analysis and optimization for your website.</p>
                            <p class="fw-bold text-primary fs-4">$199.99</p>
                            <button class="btn btn-primary">Add to Cart</button>
                        </div>
                    </div>
                </div>
                
                <div class="col">
                    <div class="card h-100">
                        <img src="https://via.placeholder.com/300x200?text=Product+3" class="card-img-top" alt="Product 3">
                        <div class="card-body">
                            <h5 class="card-title">E-commerce Plugin Bundle</h5>
                            <p class="card-text">Essential plugins for WooCommerce and Shopify stores.</p>
                            <p class="fw-bold text-primary fs-4">$79.99</p>
                            <button class="btn btn-primary">Add to Cart</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Categories -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Product Categories</h2>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="bi bi-laptop fs-1 text-primary mb-3"></i>
                            <h3 class="card-title h4">Website Templates</h3>
                            <p class="card-text">Professional templates for various industries and purposes.</p>
                            <a href="#" class="btn btn-outline-primary">Browse Templates</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="bi bi-plugin fs-1 text-primary mb-3"></i>
                            <h3 class="card-title h4">Plugins & Extensions</h3>
                            <p class="card-text">Enhance your website functionality with our premium plugins.</p>
                            <a href="#" class="btn btn-outline-primary">View Plugins</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="bi bi-graph-up fs-1 text-primary mb-3"></i>
                            <h3 class="card-title h4">Digital Marketing</h3>
                            <p class="card-text">SEO packages, social media kits, and marketing tools.</p>
                            <a href="#" class="btn btn-outline-primary">Explore Marketing</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Best Sellers -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Best Sellers</h2>
            
            <div class="row row-cols-2 row-cols-md-4 g-4">
                <div class="col">
                    <div class="card h-100">
                        <div class="position-absolute top-0 end-0 p-2">
                            <span class="badge bg-danger">Hot</span>
                        </div>
                        <img src="https://via.placeholder.com/300x200?text=Best+Seller+1" class="card-img-top" alt="Best Seller 1">
                        <div class="card-body">
                            <h5 class="card-title">Business Pro Theme</h5>
                            <p class="fw-bold text-primary">$69.99</p>
                            <button class="btn btn-sm btn-primary">Add to Cart</button>
                        </div>
                    </div>
                </div>
                
                <div class="col">
                    <div class="card h-100">
                        <div class="position-absolute top-0 end-0 p-2">
                            <span class="badge bg-danger">Hot</span>
                        </div>
                        <img src="https://via.placeholder.com/300x200?text=Best+Seller+2" class="card-img-top" alt="Best Seller 2">
                        <div class="card-body">
                            <h5 class="card-title">Analytics Dashboard</h5>
                            <p class="fw-bold text-primary">$129.99</p>
                            <button class="btn btn-sm btn-primary">Add to Cart</button>
                        </div>
                    </div>
                </div>
                
                <div class="col">
                    <div class="card h-100">
                        <img src="https://via.placeholder.com/300x200?text=Best+Seller+3" class="card-img-top" alt="Best Seller 3">
                        <div class="card-body">
                            <h5 class="card-title">Security Suite</h5>
                            <p class="fw-bold text-primary">$89.99</p>
                            <button class="btn btn-sm btn-primary">Add to Cart</button>
                        </div>
                    </div>
                </div>
                
                <div class="col">
                    <div class="card h-100">
                        <img src="https://via.placeholder.com/300x200?text=Best+Seller+4" class="card-img-top" alt="Best Seller 4">
                        <div class="card-body">
                            <h5 class="card-title">Contact Form Pro</h5>
                            <p class="fw-bold text-primary">$39.99</p>
                            <button class="btn btn-sm btn-primary">Add to Cart</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Customer Reviews</h2>
            
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <div class="card border-0 bg-transparent">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <i class="bi bi-star-fill text-warning"></i>
                                        </div>
                                        <p class="lead fst-italic mb-4">"The Business Pro Theme was exactly what my company needed. Installation was simple and the support team was incredibly helpful."</p>
                                        <div class="d-flex justify-content-center">
                                            <div class="text-center">
                                                <h5 class="fw-bold mb-0">David Wilson</h5>
                                                <p class="text-muted">CEO, Wilson Enterprises</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="carousel-item">
                                <div class="card border-0 bg-transparent">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <i class="bi bi-star-fill text-warning"></i>
                                        </div>
                                        <p class="lead fst-italic mb-4">"I've purchased multiple plugins from Gideons Technology. Each one has been high-quality and their customer service is outstanding."</p>
                                        <div class="d-flex justify-content-center">
                                            <div class="text-center">
                                                <h5 class="fw-bold mb-0">Jennifer Adams</h5>
                                                <p class="text-muted">Web Developer</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="carousel-item">
                                <div class="card border-0 bg-transparent">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <i class="bi bi-star-half text-warning"></i>
                                        </div>
                                        <p class="lead fst-italic mb-4">"The SEO Starter Package helped us increase our organic traffic by 200% in just three months. Definitely worth the investment!"</p>
                                        <div class="d-flex justify-content-center">
                                            <div class="text-center">
                                                <h5 class="fw-bold mb-0">Robert Chen</h5>
                                                <p class="text-muted">Marketing Director, Chen Industries</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="py-5 bg-primary text-white">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="mb-4">Subscribe to Our Newsletter</h2>
                    <p class="lead mb-4">Get updates about new products, special offers, and helpful tips.</p>
                    <form class="row g-3 justify-content-center">
                        <div class="col-md-8">
                            <input type="email" class="form-control form-control-lg" placeholder="Your email address">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-light btn-lg">Subscribe</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-4 mb-md-0">
                    <h5>Gideons Technology</h5>
                    <p>Premium digital products and solutions for your business needs.</p>
                </div>
                <div class="col-md-3 mb-4 mb-md-0">
                    <h5>Shop</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white text-decoration-none">Templates</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Plugins</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Marketing</a></li>
                        <li><a href="#" class="text-white text-decoration-none">New Arrivals</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4 mb-md-0">
                    <h5>Support</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white text-decoration-none">Help Center</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Documentation</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Contact Us</a></li>
                        <li><a href="#" class="text-white text-decoration-none">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Contact</h5>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-envelope me-2"></i> info@gideonstech.com</li>
                        <li><i class="bi bi-phone me-2"></i> (123) 456-7890</li>
                        <li class="mt-3">
                            <a href="#" class="text-white me-2"><i class="bi bi-facebook fs-5"></i></a>
                            <a href="#" class="text-white me-2"><i class="bi bi-twitter fs-5"></i></a>
                            <a href="#" class="text-white me-2"><i class="bi bi-instagram fs-5"></i></a>
                            <a href="#" class="text-white"><i class="bi bi-linkedin fs-5"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?= $currentYear ?> <?= $appName ?>. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-white me-3">Privacy Policy</a>
                    <a href="#" class="text-white me-3">Terms of Service</a>
                    <a href="#" class="text-white">Refund Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>