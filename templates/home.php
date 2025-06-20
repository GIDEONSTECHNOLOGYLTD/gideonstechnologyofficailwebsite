<?php
/**
 * Home page template for Gideons Technology
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
    <link rel="stylesheet" href="/assets/css/styles.css">
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
                        <a class="nav-link active" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gtech">Services</a>
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
    <header class="py-5 bg-primary text-white text-center">
        <div class="container">
            <h1 class="display-4 fw-bold">Welcome to <?= $appName ?? 'Gideons Technology' ?></h1>
            <p class="lead mb-4">Innovative technology solutions for modern businesses</p>
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                <a href="/gtech" class="btn btn-outline-light btn-lg px-4 me-sm-3">Our Services</a>
                <a href="/contact" class="btn btn-light btn-lg px-4">Contact Us</a>
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-code-square text-primary display-4 mb-3"></i>
                            <h3 class="card-title">Web Development</h3>
                            <p class="card-text">Custom websites and web applications tailored to your business needs.</p>
                            <a href="/services/web-development" class="btn btn-outline-primary">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-tools text-primary display-4 mb-3"></i>
                            <h3 class="card-title">Tech Repair</h3>
                            <p class="card-text">Professional repair services for all your technology devices.</p>
                            <a href="/services/repair" class="btn btn-outline-primary">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-cash-coin text-primary display-4 mb-3"></i>
                            <h3 class="card-title">Fintech Solutions</h3>
                            <p class="card-text">Modern financial technology services to streamline your operations.</p>
                            <a href="/services/fintech" class="btn btn-outline-primary">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-5 bg-light">
        <div class="container text-center">
            <h2 class="mb-4">Ready to upgrade your technology?</h2>
            <p class="lead mb-4">Browse our online store for quality tech products at competitive prices.</p>
            <a href="/gstore" class="btn btn-primary btn-lg">Shop Now</a>
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
    <?php if (isset($consoleLog) && !empty($consoleLog)): ?>
        <?= $consoleLog ?>
    <?php endif; ?>
</body>
</html>