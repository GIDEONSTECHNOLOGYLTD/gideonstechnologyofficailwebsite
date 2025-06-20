<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gideons Technology</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="/assets/js/main.js" defer></script>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="/" class="navbar-brand">Gideons Technology</a>
            <button class="navbar-toggler">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="navbar-collapse">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="/" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item">
                        <a href="/services" class="nav-link">Services</a>
                    </li>
                    <li class="nav-item">
                        <a href="/contact" class="nav-link">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <section class="hero">
            <div class="container">
                <h1>Welcome to Gideons Technology</h1>
                <p>Your trusted partner in technology solutions</p>
            </div>
        </section>

        <section class="services-area">
            <div class="container">
                <h2 class="section-title">Our Services</h2>
                <div class="services-grid">
                    <div class="service-item">
                        <h3>Web Development</h3>
                        <p>Custom websites and web applications</p>
                    </div>
                    <div class="service-item">
                        <h3>Hardware Repair</h3>
                        <p>Expert computer and device repair</p>
                    </div>
                    <div class="service-item">
                        <h3>Software Solutions</h3>
                        <p>Custom software development</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Gideons Technology. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
