<?php
/**
 * Web Design Services template for Gideons Technology
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $appName ?? 'Gideons Technology' ?> - Web Design</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../../../index.php"><?= $appName ?? 'Gideons Technology' ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../../../index.php">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown">Services</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="applications.php">Web Applications</a></li>
                            <li><a class="dropdown-item" href="ecommerce.php">E-commerce</a></li>
                            <li><a class="dropdown-item active" href="design.php">Web Design</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../../gstore/index.php">Store</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../../contact.php">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../../login.php">Login</a>
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
                    <h1 class="display-4 fw-bold">Web Design Services</h1>
                    <p class="lead">Beautiful, functional designs that elevate your brand and engage your audience.</p>
                    <a href="/contact" class="btn btn-light btn-lg mt-3">Get a Quote</a>
                </div>
                <div class="col-lg-4">
                    <img src="https://via.placeholder.com/500x300?text=Web+Design" alt="Web Design Services" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </header>

    <!-- Services Overview -->
    <section class="py-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-md-6">
                    <h2>Design Services We Offer</h2>
                    <p class="lead">Our expert design team creates stunning visuals and intuitive user experiences that help your business stand out.</p>
                    <p>We believe great design is where form meets function - beautiful aesthetics combined with seamless user experience. Our web design services are tailored to meet your specific business objectives while delivering an exceptional digital presence.</p>
                </div>
                <div class="col-md-6">
                    <img src="https://via.placeholder.com/600x400?text=Design+Services" alt="Design Services" class="img-fluid rounded shadow">
                </div>
            </div>
            
            <div class="row row-cols-1 row-cols-md-3 g-4 mt-3">
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-palette fs-1 text-primary mb-3"></i>
                            <h3 class="card-title h4">UI/UX Design</h3>
                            <p class="card-text">User-centered design focusing on intuitive interfaces and seamless experiences across all devices.</p>
                            <ul class="list-unstyled text-start">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>User Research</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Wireframing</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Prototyping</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Usability Testing</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-laptop fs-1 text-primary mb-3"></i>
                            <h3 class="card-title h4">Responsive Web Design</h3>
                            <p class="card-text">Mobile-first designs that look and function perfectly on all devices and screen sizes.</p>
                            <ul class="list-unstyled text-start">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Mobile Optimization</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Fluid Layouts</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Cross-Browser Compatibility</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Touch-Friendly Interfaces</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-image fs-1 text-primary mb-3"></i>
                            <h3 class="card-title h4">Visual Design</h3>
                            <p class="card-text">Eye-catching visuals that reflect your brand and create memorable impressions.</p>
                            <ul class="list-unstyled text-start">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Brand Identity</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Color Theory</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Typography</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Imagery & Illustrations</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Design Process -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Our Design Process</h2>
            
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card border-0 bg-transparent">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                <h3 class="mb-0">1</h3>
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <h4>Discovery & Research</h4>
                                            <p>We start by understanding your business, audience, and objectives. This research informs all design decisions.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-0 bg-transparent">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                <h3 class="mb-0">2</h3>
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <h4>Wireframing</h4>
                                            <p>We create low-fidelity sketches and wireframes to establish the structure and layout of your site.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-0 bg-transparent">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                <h3 class="mb-0">3</h3>
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <h4>Visual Design</h4>
                                            <p>We develop the look and feel with color schemes, typography, and visual elements that align with your brand.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-0 bg-transparent">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                <h3 class="mb-0">4</h3>
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <h4>Prototyping</h4>
                                            <p>Interactive prototypes let you experience the user flow and interface before development begins.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-0 bg-transparent">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                <h3 class="mb-0">5</h3>
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <h4>Feedback & Iteration</h4>
                                            <p>We gather your feedback and make refinements to ensure the design meets your expectations.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-0 bg-transparent">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                <h3 class="mb-0">6</h3>
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <h4>Handoff to Development</h4>
                                            <p>We provide all design assets and specifications to our development team for pixel-perfect implementation.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Portfolio Samples -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Our Design Portfolio</h2>
            
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <img src="/assets/img/portfolio-template.jpg" class="card-img-top" alt="Portfolio Website" 
                             onerror="this.src='https://via.placeholder.com/500x300?text=Portfolio'; this.onerror='';">
                        <div class="card-body">
                            <h5 class="card-title">Portfolio Website</h5>
                            <p class="card-text">Modern designer portfolio with interactive elements and smooth animations.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <img src="/assets/img/ecommerce-template.jpg" class="card-img-top" alt="E-commerce Design" 
                             onerror="this.src='https://via.placeholder.com/500x300?text=E-Commerce'; this.onerror='';">
                        <div class="card-body">
                            <h5 class="card-title">E-commerce Design</h5>
                            <p class="card-text">User-friendly online store with optimized product displays and checkout process.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <img src="/assets/img/corporate-template.jpg" class="card-img-top" alt="Corporate Website" 
                             onerror="this.src='https://via.placeholder.com/500x300?text=Corporate'; this.onerror='';">
                        <div class="card-body">
                            <h5 class="card-title">Corporate Website</h5>
                            <p class="card-text">Professional business site with clear information hierarchy and call-to-actions.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <img src="/assets/img/school-template.jpg" class="card-img-top" alt="Educational Platform" 
                             onerror="this.src='https://via.placeholder.com/500x300?text=Education'; this.onerror='';">
                        <div class="card-body">
                            <h5 class="card-title">Educational Platform</h5>
                            <p class="card-text">Engaging learning environment with intuitive navigation and content organization.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="/contact" class="btn btn-primary btn-lg">Request a Custom Design</a>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">What Our Clients Say</h2>
            
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <div class="card border-0">
                                    <div class="card-body text-center">
                                        <p class="lead fst-italic mb-4">"The design work from Gideons Technology transformed our online presence. Our conversion rates have increased by 40% since launching our new website design."</p>
                                        <div class="d-flex justify-content-center">
                                            <div class="text-center">
                                                <h5 class="fw-bold mb-0">Sarah Johnson</h5>
                                                <p class="text-muted">Marketing Director, TechSolutions Inc.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="carousel-item">
                                <div class="card border-0">
                                    <div class="card-body text-center">
                                        <p class="lead fst-italic mb-4">"Working with the design team was a fantastic experience. They really understood our vision and created a website that perfectly represents our brand."</p>
                                        <div class="d-flex justify-content-center">
                                            <div class="text-center">
                                                <h5 class="fw-bold mb-0">Michael Brown</h5>
                                                <p class="text-muted">CEO, Innovate Studios</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="carousel-item">
                                <div class="card border-0">
                                    <div class="card-body text-center">
                                        <p class="lead fst-italic mb-4">"The user experience design for our app was exceptional. Our users have commented on how intuitive and enjoyable the interface is to use."</p>
                                        <div class="d-flex justify-content-center">
                                            <div class="text-center">
                                                <h5 class="fw-bold mb-0">Emily Rodriguez</h5>
                                                <p class="text-muted">Product Manager, AppWorks</p>
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

    <!-- CTA Section -->
    <section class="py-5 bg-primary text-white">
        <div class="container text-center">
            <h2 class="mb-4">Ready to Transform Your Digital Presence?</h2>
            <p class="lead mb-4">Contact us today for a free design consultation.</p>
            <a href="/contact" class="btn btn-light btn-lg px-4">Get Started</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; <?= $currentYear ?? date('Y') ?> <?= $appName ?? 'Gideons Technology' ?>. All rights reserved.</p>
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