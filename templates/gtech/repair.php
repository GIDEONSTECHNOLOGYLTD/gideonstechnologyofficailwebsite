<?php
/**
 * Repair Services Template
 */

// Ensure these variables are defined
$appName = $appName ?? 'Gideon\'s Technology';
$currentYear = $currentYear ?? date('Y');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Repair Services') ?> | <?= htmlspecialchars($appName ?? 'Gideons Technology') ?></title>
    <meta name="description" content="<?= htmlspecialchars($description ?? 'Professional repair services for computers, laptops, and mobile devices') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <style>
        .service-card {
            transition: transform 0.3s;
            height: 100%;
        }
        .service-card:hover {
            transform: translateY(-5px);
        }
        .service-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #0d6efd;
        }
        .price-tag {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #0d6efd;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/"><?= htmlspecialchars($appName ?? 'Gideons Technology') ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gtech">GTech Platform</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/gtech/services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact">Contact</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard">Dashboard</a>
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

    <!-- Header -->
    <header class="bg-primary text-white text-center py-5">
        <div class="container">
            <h1 class="display-4">Professional Repair Services</h1>
            <p class="lead">Expert repairs for all your technology devices</p>
        </div>
    </header>

    <!-- Main Content -->
    <main class="py-5">
        <div class="container">
            <!-- Service Overview -->
            <section class="mb-5">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h2>Technology Repair Experts</h2>
                        <p class="lead">We fix your devices quickly and professionally</p>
                        <p>At Gideon's Technology, we understand how important your devices are to your daily life and business. Our certified technicians can diagnose and repair a wide range of issues with computers, laptops, smartphones, tablets, and other electronic devices.</p>
                        <p>We pride ourselves on quick turnaround times, quality repairs, and exceptional customer service.</p>
                        <a href="/contact" class="btn btn-primary btn-lg mt-3">Get a Free Quote</a>
                    </div>
                    <div class="col-lg-6">
                        <img src="/assets/images/repair-service.jpg" alt="Repair Service" class="img-fluid rounded shadow" onerror="this.src='https://placehold.co/600x400?text=Repair+Services'">
                    </div>
                </div>
            </section>

            <!-- Services -->
            <section class="mb-5">
                <h2 class="text-center mb-4">Our Repair Services</h2>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card service-card shadow-sm">
                            <div class="card-body text-center">
                                <div class="service-icon">
                                    <i class="bi bi-laptop"></i>
                                </div>
                                <h3>Computer Repair</h3>
                                <p>We fix hardware and software issues for desktops and laptops.</p>
                                <ul class="text-start">
                                    <li>Hardware replacement</li>
                                    <li>Software troubleshooting</li>
                                    <li>Virus removal</li>
                                    <li>Data recovery</li>
                                </ul>
                                <a href="/gtech/services/repair/computer" class="btn btn-outline-primary mt-3">Learn More</a>
                            </div>
                            <div class="price-tag">From $49</div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card service-card shadow-sm">
                            <div class="card-body text-center">
                                <div class="service-icon">
                                    <i class="bi bi-phone"></i>
                                </div>
                                <h3>Phone Repair</h3>
                                <p>Expert repairs for all smartphone brands and models.</p>
                                <ul class="text-start">
                                    <li>Screen replacement</li>
                                    <li>Battery replacement</li>
                                    <li>Water damage repair</li>
                                    <li>Camera & button fixes</li>
                                </ul>
                                <a href="/gtech/services/repair/phone" class="btn btn-outline-primary mt-3">Learn More</a>
                            </div>
                            <div class="price-tag">From $39</div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card service-card shadow-sm">
                            <div class="card-body text-center">
                                <div class="service-icon">
                                    <i class="bi bi-tablet"></i>
                                </div>
                                <h3>Tablet Repair</h3>
                                <p>Comprehensive repair services for all tablet devices.</p>
                                <ul class="text-start">
                                    <li>Screen repairs</li>
                                    <li>Battery replacement</li>
                                    <li>Charging port repair</li>
                                    <li>Software issues</li>
                                </ul>
                                <a href="/gtech/services/repair/tablet" class="btn btn-outline-primary mt-3">Learn More</a>
                            </div>
                            <div class="price-tag">From $45</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Why Choose Us -->
            <section class="mb-5 bg-light p-5 rounded">
                <h2 class="text-center mb-4">Why Choose Our Repair Services</h2>
                <div class="row">
                    <div class="col-md-3 text-center">
                        <div class="mb-3">
                            <i class="bi bi-award text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h4>Certified Technicians</h4>
                        <p>Our repair specialists are fully certified and trained</p>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="mb-3">
                            <i class="bi bi-shield-check text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h4>90-Day Warranty</h4>
                        <p>All our repairs come with a 90-day warranty</p>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="mb-3">
                            <i class="bi bi-clock text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h4>Fast Turnaround</h4>
                        <p>Most repairs completed within 24-48 hours</p>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="mb-3">
                            <i class="bi bi-cash-coin text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h4>Competitive Pricing</h4>
                        <p>Quality repairs at affordable prices</p>
                    </div>
                </div>
            </section>

            <!-- Call to Action -->
            <section class="text-center">
                <h2>Ready to Get Your Device Fixed?</h2>
                <p class="lead mb-4">Our tech experts are ready to solve your problems</p>
                <a href="/contact" class="btn btn-primary btn-lg px-5">Contact Us Now</a>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; <?= htmlspecialchars($currentYear ?? date('Y')) ?> <?= htmlspecialchars($appName ?? 'Gideons Technology') ?>. All rights reserved.</p>
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