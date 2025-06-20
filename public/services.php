<?php
// This file will be included by the router.php file
// It contains the services page content
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <img src="/images/logo.svg" alt="Gideon's Technology" height="40">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gstore">GStore</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a class="btn btn-primary" href="/login">Login</a>
                    <a class="btn btn-outline-light ms-2" href="/register">Register</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Services Content -->
    <div class="container py-5">
        <h1 class="text-center mb-5">Our Services</h1>
        
        <div class="row mb-5">
            <div class="col-md-6">
                <img src="/images/web-dev.jpg" class="img-fluid rounded" alt="Web Development" onerror="this.src='https://via.placeholder.com/600x400?text=Web+Development'">
            </div>
            <div class="col-md-6">
                <h2>Web Development</h2>
                <p class="lead">Custom websites and web applications tailored to your business needs.</p>
                <p>Our web development services include:</p>
                <ul>
                    <li>Responsive website design</li>
                    <li>E-commerce solutions</li>
                    <li>Content management systems</li>
                    <li>Custom web applications</li>
                    <li>API development and integration</li>
                </ul>
                <a href="/services/web-dev" class="btn btn-primary">Learn More</a>
            </div>
        </div>
        
        <div class="row mb-5">
            <div class="col-md-6 order-md-2">
                <img src="/images/fintech.jpg" class="img-fluid rounded" alt="Fintech Solutions" onerror="this.src='https://via.placeholder.com/600x400?text=Fintech+Solutions'">
            </div>
            <div class="col-md-6 order-md-1">
                <h2>Fintech Solutions</h2>
                <p class="lead">Innovative financial technology solutions for modern businesses.</p>
                <p>Our fintech services include:</p>
                <ul>
                    <li>Payment processing systems</li>
                    <li>Digital banking solutions</li>
                    <li>Blockchain implementation</li>
                    <li>Financial analytics tools</li>
                    <li>Security and compliance</li>
                </ul>
                <a href="/services/fintech" class="btn btn-primary">Learn More</a>
            </div>
        </div>
        
        <div class="row mb-5">
            <div class="col-md-6">
                <img src="/images/general-tech.jpg" class="img-fluid rounded" alt="General Tech" onerror="this.src='https://via.placeholder.com/600x400?text=General+Tech'">
            </div>
            <div class="col-md-6">
                <h2>General Tech</h2>
                <p class="lead">Comprehensive technology solutions for businesses of all sizes.</p>
                <p>Our general tech services include:</p>
                <ul>
                    <li>IT consulting and strategy</li>
                    <li>Cloud computing solutions</li>
                    <li>Cybersecurity services</li>
                    <li>Software development</li>
                    <li>Technical support and maintenance</li>
                </ul>
                <a href="/services/general-tech" class="btn btn-primary">Learn More</a>
            </div>
        </div>
        
        <div class="row mb-5">
            <div class="col-md-6 order-md-2">
                <img src="/images/repair.jpg" class="img-fluid rounded" alt="Repair Services" onerror="this.src='https://via.placeholder.com/600x400?text=Repair+Services'">
            </div>
            <div class="col-md-6 order-md-1">
                <h2>Repair Services</h2>
                <p class="lead">Professional repair services for computers, phones, and other devices.</p>
                <p>Our repair services include:</p>
                <ul>
                    <li>Computer and laptop repairs</li>
                    <li>Smartphone and tablet repairs</li>
                    <li>Data recovery</li>
                    <li>Hardware upgrades</li>
                    <li>Network troubleshooting</li>
                </ul>
                <a href="/services/repair" class="btn btn-primary">Learn More</a>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <img src="/images/video-graphics.jpg" class="img-fluid rounded" alt="Video & Graphics" onerror="this.src='https://via.placeholder.com/600x400?text=Video+and+Graphics'">
            </div>
            <div class="col-md-6">
                <h2>Video & Graphics</h2>
                <p class="lead">Professional video production and graphic design services.</p>
                <p>Our video and graphics services include:</p>
                <ul>
                    <li>Corporate video production</li>
                    <li>Motion graphics and animation</li>
                    <li>Logo and brand identity design</li>
                    <li>Marketing materials and print design</li>
                    <li>UI/UX design for web and mobile</li>
                </ul>
                <a href="/services/video-graphics" class="btn btn-primary">Learn More</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Gideon's Technology</h5>
                    <p>Innovative solutions for all your technology needs.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="/about" class="text-white">About Us</a></li>
                        <li><a href="/services" class="text-white">Services</a></li>
                        <li><a href="/contact" class="text-white">Contact</a></li>
                        <li><a href="/privacy" class="text-white">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Us</h5>
                    <address>
                        123 Tech Street<br>
                        Silicon Valley, CA 94043<br>
                        <a href="mailto:info@gideonstech.com" class="text-white">info@gideonstech.com</a><br>
                        <a href="tel:+1234567890" class="text-white">+1 (234) 567-890</a>
                    </address>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; 2025 Gideon's Technology. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
