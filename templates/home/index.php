<?php
/**
 * Homepage template for Gideons Technology
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $appName ?? 'Gideons Technology' ?> - Home</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('/images/hero-image.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 8rem 0;
        }
        
        .service-card {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .testimonial-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/"><?= $appName ?? 'Gideons Technology' ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="/">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Services</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/services/web-development/applications">Web Applications</a></li>
                            <li><a class="dropdown-item" href="/services/web-development/ecommerce">E-commerce</a></li>
                            <li><a class="dropdown-item" href="/services/web-development/design">Web Design</a></li>
                            <li><a class="dropdown-item" href="/services/fintech">FinTech Solutions</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gstore">Store</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/order">Orders</a>
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
    <header class="hero-section">
        <div class="container text-center">
            <h1 class="display-3 fw-bold mb-4">Welcome to Gideons Technology</h1>
            <p class="lead mb-5">Innovative technology solutions for businesses of all sizes</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="/services" class="btn btn-primary btn-lg">Our Services</a>
                <a href="/contact" class="btn btn-outline-light btn-lg">Get in Touch</a>
            </div>
        </div>
    </header>

    <!-- Services -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Our Services</h2>
                <p class="lead">Providing cutting-edge technology solutions to help your business grow</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 service-card">
                        <div class="card-body text-center p-4">
                            <div class="text-primary mb-3">
                                <i class="bi bi-code-square fs-1"></i>
                            </div>
                            <h3 class="card-title h4">Web Development</h3>
                            <p class="card-text">Custom web applications, e-commerce solutions, and responsive websites tailored to your business needs.</p>
                            <a href="/services/web-development" class="btn btn-outline-primary mt-3">Learn More</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 service-card">
                        <div class="card-body text-center p-4">
                            <div class="text-primary mb-3">
                                <i class="bi bi-currency-exchange fs-1"></i>
                            </div>
                            <h3 class="card-title h4">FinTech Solutions</h3>
                            <p class="card-text">Payment processing, financial software integration, and secure transaction systems.</p>
                            <a href="/services/fintech" class="btn btn-outline-primary mt-3">Learn More</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 service-card">
                        <div class="card-body text-center p-4">
                            <div class="text-primary mb-3">
                                <i class="bi bi-tools fs-1"></i>
                            </div>
                            <h3 class="card-title h4">Tech Repair</h3>
                            <p class="card-text">Hardware diagnostics, software troubleshooting, and system optimization services.</p>
                            <a href="/services/repair" class="btn btn-outline-primary mt-3">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- About Us -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <img src="https://via.placeholder.com/600x400?text=About+Us" class="img-fluid rounded" alt="About Gideons Technology">
                </div>
                <div class="col-lg-6">
                    <h2 class="display-5 fw-bold mb-4">About Gideons Technology</h2>
                    <p class="lead">We are a team of passionate technologists dedicated to helping businesses leverage the power of technology.</p>
                    <p>With years of industry experience, our experts develop innovative solutions that address the unique challenges of modern businesses. We combine technical expertise with creative problem-solving to deliver results that exceed expectations.</p>
                    <ul class="list-unstyled mt-4">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> 10+ years of industry experience</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> 200+ successful projects completed</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> 95% client satisfaction rate</li>
                    </ul>
                    <a href="/contact" class="btn btn-primary mt-3">Contact Us</a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Testimonials -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">What Our Clients Say</h2>
                <p class="lead">Read testimonials from businesses that have partnered with us</p>
            </div>
            
            <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="card border-0 bg-light p-4">
                                    <div class="d-flex flex-column align-items-center text-center">
                                        <img src="https://via.placeholder.com/150x150?text=J" class="testimonial-img mb-3" alt="John Smith">
                                        <div class="card-body">
                                            <h5 class="card-title">John Smith</h5>
                                            <p class="text-muted">CEO, TechStart Inc.</p>
                                            <div class="mb-3">
                                                <i class="bi bi-star-fill text-warning"></i>
                                                <i class="bi bi-star-fill text-warning"></i>
                                                <i class="bi bi-star-fill text-warning"></i>
                                                <i class="bi bi-star-fill text-warning"></i>
                                                <i class="bi bi-star-fill text-warning"></i>
                                            </div>
                                            <p class="card-text fst-italic">"Gideons Technology delivered an exceptional e-commerce platform that has increased our online sales by 45%. Their team was professional, responsive, and truly understood our business needs."</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="carousel-item">
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="card border-0 bg-light p-4">
                                    <div class="d-flex flex-column align-items-center text-center">
                                        <img src="https://via.placeholder.com/150x150?text=S" class="testimonial-img mb-3" alt="Sarah Johnson">
                                        <div class="card-body">
                                            <h5 class="card-title">Sarah Johnson</h5>
                                            <p class="text-muted">Marketing Director, Global Solutions</p>
                                            <div class="mb-3">
                                                <i class="bi bi-star-fill text-warning"></i>
                                                <i class="bi bi-star-fill text-warning"></i>
                                                <i class="bi bi-star-fill text-warning"></i>
                                                <i class="bi bi-star-fill text-warning"></i>
                                                <i class="bi bi-star-fill text-warning"></i>
                                            </div>
                                            <p class="card-text fst-italic">"Working with Gideons Technology on our payment processing system was a game-changer. Their FinTech solutions are robust, secure, and have streamlined our operations significantly."</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon bg-primary rounded-circle" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon bg-primary rounded-circle" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </section>
    
    <!-- CTA -->
    <section class="py-5 bg-primary text-white">
        <div class="container text-center">
            <h2 class="display-5 fw-bold mb-4">Ready to Transform Your Business?</h2>
            <p class="lead mb-4">Contact us today for a free consultation and discover how we can help you achieve your technology goals.</p>
            <a href="/contact" class="btn btn-light btn-lg">Get Started</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h4><?= $appName ?? 'Gideons Technology' ?></h4>
                    <p class="mb-0">Innovative technology solutions for businesses of all sizes.</p>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="/" class="text-white text-decoration-none">Home</a></li>
                        <li><a href="/services" class="text-white text-decoration-none">Services</a></li>
                        <li><a href="/gstore" class="text-white text-decoration-none">Store</a></li>
                        <li><a href="/contact" class="text-white text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Us</h5>
                    <address class="mb-0">
                        <p><i class="bi bi-geo-alt me-2"></i> 123 Tech Street, Innovation City</p>
                        <p><i class="bi bi-envelope me-2"></i> info@gideonstech.com</p>
                        <p><i class="bi bi-telephone me-2"></i> (123) 456-7890</p>
                    </address>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?= $currentYear ?? date('Y') ?> <?= $appName ?? 'Gideons Technology' ?>. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="/privacy" class="text-white me-3">Privacy Policy</a>
                    <a href="/terms" class="text-white me-3">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>