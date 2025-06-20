<?php
// Services page
$html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services - Gideon's Technology</title>
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
                        <a class="nav-link active" href="/services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gtech">GTech</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gstore">Store</a>
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

    <!-- Hero Section -->
    <header class="bg-primary text-white text-center py-5">
        <div class="container">
            <h1 class="display-4">Our Services</h1>
            <p class="lead">Providing cutting-edge technology solutions to help your business grow</p>
        </div>
    </header>

    <!-- Services Section -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <!-- Web Development -->
                <div class="col-md-4">
                    <div class="card h-100">
                        <img src="/images/web-dev.jpg" class="card-img-top" alt="Web Development" onerror="this.src='https://via.placeholder.com/350x200?text=Web+Development'">
                        <div class="card-body">
                            <h3 class="card-title">Web Development</h3>
                            <p class="card-text">Custom web applications, e-commerce solutions, and responsive websites tailored to your business needs.</p>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Custom Website Design</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>E-commerce Solutions</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Content Management Systems</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Web Application Development</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <a href="/services/web-dev" class="btn btn-primary">Learn More</a>
                        </div>
                    </div>
                </div>

                <!-- FinTech Solutions -->
                <div class="col-md-4">
                    <div class="card h-100">
                        <img src="/images/fintech.jpg" class="card-img-top" alt="FinTech Solutions" onerror="this.src='https://via.placeholder.com/350x200?text=FinTech+Solutions'">
                        <div class="card-body">
                            <h3 class="card-title">FinTech Solutions</h3>
                            <p class="card-text">Payment processing, financial software integration, and secure transaction systems.</p>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Payment Gateway Integration</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Financial Software Development</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Secure Transaction Systems</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Banking API Integration</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <a href="/services/fintech" class="btn btn-primary">Learn More</a>
                        </div>
                    </div>
                </div>

                <!-- Tech Repair -->
                <div class="col-md-4">
                    <div class="card h-100">
                        <img src="/images/repair.jpg" class="card-img-top" alt="Tech Repair" onerror="this.src='https://via.placeholder.com/350x200?text=Tech+Repair'">
                        <div class="card-body">
                            <h3 class="card-title">Tech Repair</h3>
                            <p class="card-text">Hardware diagnostics, software troubleshooting, and system optimization services.</p>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Computer & Laptop Repair</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Data Recovery Services</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>System Optimization</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Virus & Malware Removal</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <a href="/services/repair" class="btn btn-primary">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
