<?php
/**
 * Web Development Services template for Gideons Technology
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $appName ?> - Web Development Services</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/"><?= $appName ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/gtech">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gstore">Store</a>
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

    <!-- Hero Section -->
    <header class="bg-primary text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold">Web Development Solutions</h1>
                    <p class="lead">Custom websites and web applications built to help your business thrive in the digital world.</p>
                    <a href="/contact" class="btn btn-light btn-lg mt-3">Get a Free Quote</a>
                </div>
                <div class="col-lg-4">
                    <img src="https://via.placeholder.com/500x300?text=Web+Development" alt="Web Development" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </header>

    <!-- Services Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Our Web Development Services</h2>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <i class="bi bi-laptop fs-1 text-primary"></i>
                            </div>
                            <h5 class="card-title text-center">Website Design</h5>
                            <p class="card-text">Modern, responsive website designs that look great on all devices and provide an exceptional user experience.</p>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Responsive layouts</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> User-centered design</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Brand consistency</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent border-0 text-center">
                            <a href="/services/web-development/design" class="btn btn-outline-primary">Learn More</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <i class="bi bi-code-square fs-1 text-primary"></i>
                            </div>
                            <h5 class="card-title text-center">Custom Web Applications</h5>
                            <p class="card-text">Tailored web applications that automate processes, improve efficiency, and solve your unique business challenges.</p>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Scalable architecture</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Secure development</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> API integrations</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent border-0 text-center">
                            <a href="/services/web-development/applications" class="btn btn-outline-primary">Learn More</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <i class="bi bi-cart-check fs-1 text-primary"></i>
                            </div>
                            <h5 class="card-title text-center">E-Commerce Solutions</h5>
                            <p class="card-text">Custom online stores built to showcase your products, provide a seamless shopping experience, and boost sales.</p>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Payment processing</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Inventory management</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Mobile-friendly checkout</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent border-0 text-center">
                            <a href="/services/web-development/ecommerce" class="btn btn-outline-primary">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Our Development Process</h2>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center mb-4">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <h3 class="mb-0">1</h3>
                        </div>
                        <h5>Discovery</h5>
                        <p>We learn about your business, goals, and project requirements.</p>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="text-center mb-4">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <h3 class="mb-0">2</h3>
                        </div>
                        <h5>Planning</h5>
                        <p>We create wireframes, mockups, and a development strategy.</p>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="text-center mb-4">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <h3 class="mb-0">3</h3>
                        </div>
                        <h5>Development</h5>
                        <p>We build your website or application following best practices.</p>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="text-center mb-4">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <h3 class="mb-0">4</h3>
                        </div>
                        <h5>Launch & Support</h5>
                        <p>We deploy your project and provide ongoing maintenance.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-primary text-white">
        <div class="container text-center">
            <h2 class="mb-4">Ready to Start Your Web Development Project?</h2>
            <p class="lead mb-4">Contact us today for a free consultation and quote.</p>
            <a href="/contact" class="btn btn-light btn-lg">Get Started</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; <?= $currentYear ?> <?= $appName ?>. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="/" class="text-white me-3">Home</a>
                    <a href="/contact" class="text-white me-3">Contact</a>
                    <a href="/privacy" class="text-white">Privacy Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>