<?php
/**
 * GTech Platform Template
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GTech Platform | <?= $appName ?? 'Gideons Technology' ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <meta name="description" content="<?= $description ?? 'Gideon\'s Technology services platform' ?>">
    <style>
        .header { padding: 2rem 0; background-color: #f8f9fa; margin-bottom: 2rem; }
        .footer { padding: 2rem 0; background-color: #f8f9fa; margin-top: 2rem; text-align: center; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/"><?= $appName ?? 'Gideons Technology' ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/user/dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/logout">Logout</a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/login">Login</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="py-5 bg-primary text-white text-center">
        <div class="container">
            <h1 class="display-4 fw-bold">GTech Platform</h1>
            <p class="lead mb-4">Your gateway to premium technology services</p>
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                <a href="#services" class="btn btn-outline-light btn-lg px-4 me-sm-3">Explore Services</a>
                <a href="/contact" class="btn btn-light btn-lg px-4">Contact Us</a>
            </div>
        </div>
    </header>

    <!-- Services Section -->
    <section id="services" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Our Technology Services</h2>
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-code-square text-primary display-4 mb-3"></i>
                            <h3 class="card-title">Web Development</h3>
                            <p class="card-text">Custom websites and web applications tailored to your business needs.</p>
                            <ul class="list-unstyled text-start mt-4 mb-4">
                                <li><i class="bi bi-check2 text-success me-2"></i> Responsive Design</li>
                                <li><i class="bi bi-check2 text-success me-2"></i> E-commerce Solutions</li>
                                <li><i class="bi bi-check2 text-success me-2"></i> Content Management</li>
                                <li><i class="bi bi-check2 text-success me-2"></i> SEO Optimization</li>
                            </ul>
                            <a href="/gtech/services/web-development" class="btn btn-primary">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-cash-coin text-primary display-4 mb-3"></i>
                            <h3 class="card-title">Fintech Solutions</h3>
                            <p class="card-text">Modern financial technology services to streamline your operations.</p>
                            <ul class="list-unstyled text-start mt-4 mb-4">
                                <li><i class="bi bi-check2 text-success me-2"></i> Digital Payments</li>
                                <li><i class="bi bi-check2 text-success me-2"></i> Blockchain Integration</li>
                                <li><i class="bi bi-check2 text-success me-2"></i> Financial APIs</li>
                                <li><i class="bi bi-check2 text-success me-2"></i> Secure Transactions</li>
                            </ul>
                            <a href="/gtech/services/fintech" class="btn btn-primary">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-tools text-primary display-4 mb-3"></i>
                            <h3 class="card-title">Tech Repair</h3>
                            <p class="card-text">Professional repair services for all your technology devices.</p>
                            <ul class="list-unstyled text-start mt-4 mb-4">
                                <li><i class="bi bi-check2 text-success me-2"></i> Computer Repair</li>
                                <li><i class="bi bi-check2 text-success me-2"></i> Phone Screen Replacement</li>
                                <li><i class="bi bi-check2 text-success me-2"></i> Data Recovery</li>
                                <li><i class="bi bi-check2 text-success me-2"></i> Network Troubleshooting</li>
                            </ul>
                            <a href="/gtech/services/repair" class="btn btn-primary">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Why Choose GTech Platform?</h2>
            <div class="row">
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="text-center">
                        <i class="bi bi-award text-primary display-4 mb-3"></i>
                        <h4>Expert Team</h4>
                        <p>Our team of certified professionals ensures top-quality service.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="text-center">
                        <i class="bi bi-graph-up-arrow text-primary display-4 mb-3"></i>
                        <h4>Cutting-Edge Technology</h4>
                        <p>We use the latest technology to deliver modern solutions.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="text-center">
                        <i class="bi bi-headset text-primary display-4 mb-3"></i>
                        <h4>24/7 Support</h4>
                        <p>Round-the-clock support to address your concerns promptly.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="text-center">
                        <i class="bi bi-piggy-bank text-primary display-4 mb-3"></i>
                        <h4>Competitive Pricing</h4>
                        <p>Quality services at affordable prices with transparent billing.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-5 bg-primary text-white text-center">
        <div class="container">
            <h2 class="mb-4">Ready to upgrade your technology?</h2>
            <p class="lead mb-4">Get started today with our expert team and cutting-edge solutions.</p>
            <a href="/contact" class="btn btn-light btn-lg">Contact Us Now</a>
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

    <!-- JavaScript Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>