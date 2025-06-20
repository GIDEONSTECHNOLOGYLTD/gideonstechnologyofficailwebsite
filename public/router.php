<?php
/**
 * Gideon's Technology - Router Script
 * 
 * This file handles fallback routing for the application when a route
 * is not handled by the Slim framework. It's important to check if
 * a request has already been handled by Slim before processing it here.
 */

// Set a flag to track if the request has been handled by Slim
$handled_by_slim = false;

// Include the bootstrap file
require_once __DIR__ . '/../app/bootstrap.php';

// Get the request URI
$uri = $_SERVER['REQUEST_URI'];

// Remove query string
$uri = explode('?', $uri)[0];

// Debug info
error_log("Processing request for URI: {$uri}");

// Check if this request should be handled by this router
// If the request has already been handled by Slim, we should exit
if (isset($GLOBALS['ROUTE_HANDLED_BY_SLIM']) && $GLOBALS['ROUTE_HANDLED_BY_SLIM'] === true) {
    error_log("Request for {$uri} already handled by Slim framework");
    return;
}

// Handle root URL
if ($uri === '/' || $uri === '') {
    // Include the home page template
    include_once __DIR__ . '/../templates/home/index.php';
    exit;
}

// Handle static files (CSS, JS, images, etc.)
$static_file_extensions = ['css', 'js', 'jpg', 'jpeg', 'png', 'gif', 'ico', 'svg', 'woff', 'woff2', 'ttf', 'eot', 'map', 'txt'];
$path_parts = pathinfo($uri);

// Fix placeholder image URLs in HTML output
ob_start(function($buffer) {
    // Replace placeholder URLs with local images
    $buffer = str_replace('https://via.placeholder.com/800x600?text=Technology+Solutions', '/images/hero-image.jpg', $buffer);
    $buffer = str_replace('https://via.placeholder.com/600x400?text=Web+Development', '/images/web-dev.jpg', $buffer);
    $buffer = str_replace('https://via.placeholder.com/600x400?text=Fintech+Solutions', '/images/fintech.jpg', $buffer);
    $buffer = str_replace('https://via.placeholder.com/600x400?text=Repair+Services', '/images/repair.jpg', $buffer);
    return $buffer;
});

if (isset($path_parts['extension']) && in_array(strtolower($path_parts['extension']), $static_file_extensions)) {
    $file_path = __DIR__ . $uri;
    
    // Debug info
    error_log("Looking for static file: {$file_path}");
    
    if (file_exists($file_path)) {
        // Set the appropriate content type
        switch (strtolower($path_parts['extension'])) {
            case 'css':
                header('Content-Type: text/css');
                break;
            case 'js':
                header('Content-Type: application/javascript');
                break;
            case 'jpg':
            case 'jpeg':
                header('Content-Type: image/jpeg');
                break;
            case 'png':
                header('Content-Type: image/png');
                break;
            case 'gif':
                header('Content-Type: image/gif');
                break;
            case 'svg':
                header('Content-Type: image/svg+xml');
                break;
            case 'woff':
                header('Content-Type: font/woff');
                break;
            case 'woff2':
                header('Content-Type: font/woff2');
                break;
            case 'ttf':
                header('Content-Type: font/ttf');
                break;
            case 'eot':
                header('Content-Type: application/vnd.ms-fontobject');
                break;
            default:
                header('Content-Type: text/plain');
        }
        
        // Output the file contents and exit
        readfile($file_path);
        exit;
    }
}

// Get the request URI
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = trim($path, '/');
$path = preg_replace('/\\.php$/', '', $path);

// Handle static assets
if (preg_match('/^assets\//', $path)) {
    $file = __DIR__ . '/' . $path;
    if (file_exists($file)) {
        // Get file extension
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        
        // Set content type
        $content_types = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'ico' => 'image/x-icon',
            'svg' => 'image/svg+xml',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf'
        ];
        
        if (isset($content_types[$ext])) {
            header('Content-Type: ' . $content_types[$ext]);
            readfile($file);
            exit;
        }
    } elseif (preg_match('/^assets\/templates\//', $path)) {
        // Fallback placeholder for missing template images
        header('Location: https://via.placeholder.com/800x600');
        exit;
    }
    http_response_code(404);
    exit;
}

// Default route
if (empty($path)) {
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gideon's Technology - Innovative Tech Solutions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <img src="/assets/img/logo.png" alt="Gideon's Technology" height="40">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="/">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown">Services</a>
                        <ul class="dropdown-menu" aria-labelledby="servicesDropdown">
                            <li><a class="dropdown-item" href="/services/web-dev">Web Development</a></li>
                            <li><a class="dropdown-item" href="/services/fintech">Fintech Solutions</a></li>
                            <li><a class="dropdown-item" href="/services/general-tech">General Tech</a></li>
                            <li><a class="dropdown-item" href="/services/repair">Repair Services</a></li>
                            <li><a class="dropdown-item" href="/services/videographics">Video & Graphics</a></li>
                        </ul>
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

    <!-- Hero Section -->
    <section class="hero-section py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold">Welcome to Gideon's Technology</h1>
                    <p class="lead">Innovative solutions for your business and personal technology needs.</p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        <a href="/services" class="btn btn-primary btn-lg px-4 me-md-2">Our Services</a>
                        <a href="/contact" class="btn btn-outline-primary btn-lg px-4">Contact Us</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="/images/hero-image.jpg" alt="Technology Solutions" class="img-fluid rounded" onerror="this.src='https://via.placeholder.com/800x600?text=Technology+Solutions'">
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Our Services</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-img-top overflow-hidden" style="height: 200px;">
                            <img src="/images/web-dev.jpg" class="img-fluid" alt="Web Development" onerror="this.src='https://via.placeholder.com/600x400?text=Web+Development'">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Web Development</h5>
                            <p class="card-text">Custom websites and web applications tailored to your business needs.</p>
                            <a href="/services/web-dev" class="btn btn-primary">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <img src="/images/fintech.jpg" class="card-img-top" alt="Fintech Solutions" onerror="this.src='https://via.placeholder.com/600x400?text=Fintech+Solutions'">
                        <div class="card-body">
                            <h5 class="card-title">Fintech Solutions</h5>
                            <p class="card-text">Innovative financial technology solutions for modern businesses.</p>
                            <a href="/services/fintech" class="btn btn-primary">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <img src="/images/repair.jpg" class="card-img-top" alt="Repair Services" onerror="this.src='https://via.placeholder.com/600x400?text=Repair+Services'">
                        <div class="card-body">
                            <h5 class="card-title">Repair Services</h5>
                            <p class="card-text">Professional repair services for computers, phones, and other devices.</p>
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
    <script src="/assets/js/main.js"></script>
</body>
</html>
HTML;
    echo $html;
    exit;
}

// Auth routes - direct handlers to avoid routing conflicts
if ($path === 'login' || $path === 'login.php') {
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3>Login</h3>
                    </div>
                    <div class="card-body">
                        <form action="/login" method="post">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <p>Don't have an account? <a href="/register">Register</a></p>
                        <p><a href="/forgot-password">Forgot Password?</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    echo $html;
    exit;
}

if ($path === 'register' || $path === 'register.php') {
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3>Register</h3>
                    </div>
                    <div class="card-body">
                        <form action="/register" method="post">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="password_confirm" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Register</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <p>Already have an account? <a href="/login">Login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    echo $html;
    exit;
}

if ($path === 'forgot-password' || $path === 'forgot-password.php') {
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3>Forgot Password</h3>
                    </div>
                    <div class="card-body">
                        <p class="mb-4">Enter your email address and we'll send you a link to reset your password.</p>
                        <form action="/forgot-password" method="post">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Send Reset Link</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <p>Remember your password? <a href="/login">Login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    echo $html;
    exit;
}

if ($path === 'about' || $path === 'about.php') {
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <img src="/assets/img/logo.png" alt="Gideon's Technology" height="40">
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
                        <a class="nav-link" href="/services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/about">About</a>
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

    <!-- About Content -->
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h1 class="text-center mb-5">About Gideon's Technology</h1>
                
                <div class="mb-5">
                    <h2>Our Story</h2>
                    <p class="lead">Founded in 2020, Gideon's Technology has quickly established itself as a leader in innovative technology solutions.</p>
                    <p>What started as a small repair shop has grown into a comprehensive technology company offering a wide range of services from web development to fintech solutions and hardware repair. Our journey has been defined by our commitment to quality, innovation, and customer satisfaction.</p>
                </div>
                
                <div class="mb-5">
                    <h2>Our Mission</h2>
                    <p>At Gideon's Technology, our mission is to provide accessible, cutting-edge technology solutions that empower businesses and individuals to achieve their goals. We believe that technology should be a tool for progress, not a barrier to it.</p>
                </div>
                
                <div class="mb-5">
                    <h2>Our Team</h2>
                    <p>Our team consists of passionate technology experts with diverse backgrounds and specialties. From software developers to hardware specialists, our professionals bring years of experience and a commitment to excellence to every project.</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-4 text-center mb-4">
                            <img src="https://via.placeholder.com/150" class="rounded-circle mb-3" alt="Team Member">
                            <h5>John Smith</h5>
                            <p class="text-muted">Founder & CEO</p>
                        </div>
                        <div class="col-md-4 text-center mb-4">
                            <img src="https://via.placeholder.com/150" class="rounded-circle mb-3" alt="Team Member">
                            <h5>Sarah Johnson</h5>
                            <p class="text-muted">CTO</p>
                        </div>
                        <div class="col-md-4 text-center mb-4">
                            <img src="https://via.placeholder.com/150" class="rounded-circle mb-3" alt="Team Member">
                            <h5>Michael Brown</h5>
                            <p class="text-muted">Lead Developer</p>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h2>Our Values</h2>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Innovation:</strong> We constantly seek new and better ways to solve problems.</li>
                        <li class="list-group-item"><strong>Quality:</strong> We never compromise on the quality of our work.</li>
                        <li class="list-group-item"><strong>Integrity:</strong> We operate with honesty and transparency in all our dealings.</li>
                        <li class="list-group-item"><strong>Customer Focus:</strong> Our clients' needs are at the center of everything we do.</li>
                        <li class="list-group-item"><strong>Continuous Learning:</strong> We are committed to staying at the forefront of technological advancements.</li>
                    </ul>
                </div>
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
HTML;
    echo $html;
    exit;
}

if ($path === 'contact' || $path === 'contact.php') {
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <img src="/assets/img/logo.png" alt="Gideon's Technology" height="40">
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
                        <a class="nav-link" href="/services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/contact">Contact</a>
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

    <!-- Contact Content -->
    <div class="container py-5">
        <h1 class="text-center mb-5">Contact Us</h1>
        
        <div class="row">
            <div class="col-md-6 mb-4 mb-md-0">
                <h2>Get In Touch</h2>
                <p class="lead">We'd love to hear from you. Fill out the form and we'll get back to you as soon as possible.</p>
                
                <form action="/contact" method="post" class="mt-4">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
            
            <div class="col-md-6">
                <h2>Contact Information</h2>
                <p class="lead">Here's how you can reach us directly.</p>
                
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">Address</h5>
                        <p class="card-text">123 Tech Street<br>Silicon Valley, CA 94043<br>United States</p>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title">Phone</h5>
                        <p class="card-text">+1 (234) 567-890</p>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title">Email</h5>
                        <p class="card-text"><a href="mailto:info@gideonstech.com">info@gideonstech.com</a></p>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title">Business Hours</h5>
                        <p class="card-text">
                            Monday - Friday: 9:00 AM - 6:00 PM<br>
                            Saturday: 10:00 AM - 4:00 PM<br>
                            Sunday: Closed
                        </p>
                    </div>
                </div>
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
HTML;
    echo $html;
    exit;
}

if ($path === 'reset-password' || $path === 'reset-password.php') {
    $token = $_GET['token'] ?? '';
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3>Reset Password</h3>
                    </div>
                    <div class="card-body">
                        <form action="/reset-password" method="post">
                            <input type="hidden" name="token" value="$token">
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="password_confirm" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Reset Password</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <p>Remember your password? <a href="/login">Login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    echo $html;
    exit;
}

if ($path === 'auth/login') {
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3>Login</h3>
                    </div>
                    <div class="card-body">
                        <form action="/auth/login" method="post">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <p>Don't have an account? <a href="/auth/register">Register</a></p>
                        <p><a href="/auth/forgot-password">Forgot Password?</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    echo $html;
    exit;
}

if ($path === 'auth/register') {
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3>Register</h3>
                    </div>
                    <div class="card-body">
                        <form action="/auth/register" method="post">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="password_confirm" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Register</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <p>Already have an account? <a href="/auth/login">Login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    echo $html;
    exit;
}

if ($path === 'user/profile') {
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h2>User Profile</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center mb-4">
                            <img src="https://via.placeholder.com/150" class="img-fluid rounded-circle" alt="Profile Picture">
                            <h4 class="mt-2">John Doe</h4>
                            <p class="text-muted">Member since: January 2025</p>
                        </div>
                        <div class="list-group">
                            <a href="/user/profile" class="list-group-item list-group-item-action active">Profile</a>
                            <a href="/user/orders" class="list-group-item list-group-item-action">Orders</a>
                            <a href="/user/payments" class="list-group-item list-group-item-action">Payments</a>
                            <a href="/user/settings" class="list-group-item list-group-item-action">Settings</a>
                            <a href="/auth/logout" class="list-group-item list-group-item-action text-danger">Logout</a>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h3>Personal Information</h3>
                        <form>
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" value="John Doe">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" value="john.doe@example.com">
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" value="+1 (555) 123-4567">
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" rows="3">123 Main St, Anytown, CA 12345</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    echo $html;
    exit;
}

if ($path === 'services') {
    $html = <<<HTML
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
HTML;
    echo $html;
    exit;
}

if ($path === 'admin/products') {
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Products - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 bg-dark text-white p-0" style="min-height: 100vh;">
                <div class="p-3 text-center">
                    <h4>Admin Panel</h4>
                </div>
                <div class="list-group list-group-flush">
                    <a href="/admin" class="list-group-item list-group-item-action bg-dark text-white">Dashboard</a>
                    <a href="/admin/users" class="list-group-item list-group-item-action bg-dark text-white">Users</a>
                    <a href="/admin/products" class="list-group-item list-group-item-action bg-dark text-white active">Products</a>
                    <a href="/admin/orders" class="list-group-item list-group-item-action bg-dark text-white">Orders</a>
                    <a href="/admin/services" class="list-group-item list-group-item-action bg-dark text-white">Services</a>
                    <a href="/admin/settings" class="list-group-item list-group-item-action bg-dark text-white">Settings</a>
                    <a href="/" class="list-group-item list-group-item-action bg-dark text-white">Back to Site</a>
                </div>
            </div>
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Product Management</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">Add New Product</button>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0">Product Inventory</h5>
                            </div>
                            <div class="col-auto">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search products...">
                                    <button class="btn btn-outline-secondary" type="button">Search</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Product Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td><img src="https://via.placeholder.com/50" alt="Product" class="img-thumbnail"></td>
                                        <td>Dell XPS 13 Laptop</td>
                                        <td>Computers & Laptops</td>
                                        <td>$1,299.99</td>
                                        <td>15</td>
                                        <td><span class="badge bg-success">In Stock</span></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary">Edit</button>
                                                <button class="btn btn-outline-danger">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td><img src="https://via.placeholder.com/50" alt="Product" class="img-thumbnail"></td>
                                        <td>iPhone 13 Pro</td>
                                        <td>Smartphones & Tablets</td>
                                        <td>$999.99</td>
                                        <td>8</td>
                                        <td><span class="badge bg-success">In Stock</span></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary">Edit</button>
                                                <button class="btn btn-outline-danger">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td><img src="https://via.placeholder.com/50" alt="Product" class="img-thumbnail"></td>
                                        <td>Sony WH-1000XM4 Headphones</td>
                                        <td>Audio & Headphones</td>
                                        <td>$349.99</td>
                                        <td>22</td>
                                        <td><span class="badge bg-success">In Stock</span></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary">Edit</button>
                                                <button class="btn btn-outline-danger">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td><img src="https://via.placeholder.com/50" alt="Product" class="img-thumbnail"></td>
                                        <td>PlayStation 5</td>
                                        <td>Gaming</td>
                                        <td>$499.99</td>
                                        <td>0</td>
                                        <td><span class="badge bg-danger">Out of Stock</span></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary">Edit</button>
                                                <button class="btn btn-outline-danger">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td><img src="https://via.placeholder.com/50" alt="Product" class="img-thumbnail"></td>
                                        <td>Samsung 65" QLED TV</td>
                                        <td>TVs & Home Theater</td>
                                        <td>$1,199.99</td>
                                        <td>4</td>
                                        <td><span class="badge bg-warning text-dark">Low Stock</span></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary">Edit</button>
                                                <button class="btn btn-outline-danger">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <nav>
                            <ul class="pagination justify-content-center mb-0">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="productName" class="form-label">Product Name</label>
                                    <input type="text" class="form-control" id="productName" required>
                                </div>
                                <div class="mb-3">
                                    <label for="productCategory" class="form-label">Category</label>
                                    <select class="form-select" id="productCategory">
                                        <option value="computers">Computers & Laptops</option>
                                        <option value="smartphones">Smartphones & Tablets</option>
                                        <option value="audio">Audio & Headphones</option>
                                        <option value="gaming">Gaming</option>
                                        <option value="tv">TVs & Home Theater</option>
                                        <option value="cameras">Cameras & Photography</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="productPrice" class="form-label">Price ($)</label>
                                    <input type="number" step="0.01" class="form-control" id="productPrice" required>
                                </div>
                                <div class="mb-3">
                                    <label for="productStock" class="form-label">Stock Quantity</label>
                                    <input type="number" class="form-control" id="productStock" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="productImage" class="form-label">Product Image</label>
                                    <input type="file" class="form-control" id="productImage">
                                </div>
                                <div class="mb-3">
                                    <label for="productDescription" class="form-label">Description</label>
                                    <textarea class="form-control" id="productDescription" rows="5"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="productStatus" class="form-label">Status</label>
                                    <select class="form-select" id="productStatus">
                                        <option value="instock" selected>In Stock</option>
                                        <option value="lowstock">Low Stock</option>
                                        <option value="outofstock">Out of Stock</option>
                                        <option value="discontinued">Discontinued</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save Product</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;
    echo $html;
    exit;
}

if (preg_match('#^gstore/product/([0-9]+)$#', $path, $matches)) {
    $product_id = $matches[1];
    $product_name = '';
    $product_price = '';
    $product_description = '';
    $product_image = '';
    $product_stock = '';
    
    // Mock product data based on ID
    switch($product_id) {
        case '1':
            $product_name = 'Dell XPS 13';
            $product_price = '$1,299.99';
            $product_description = 'The Dell XPS 13 is a premium ultrabook featuring a stunning 13.4" FHD+ display, powerful Intel Core i7 processor, 16GB RAM, and a fast 512GB SSD. Perfect for professionals and students who need a reliable, high-performance laptop.';
            $product_image = '/images/dell-xps.jpg';
            $product_stock = 'In Stock';
            break;
        case '2':
            $product_name = 'MacBook Pro 14"';
            $product_price = '$1,999.99';
            $product_description = 'The MacBook Pro 14" features a stunning Liquid Retina XDR display, powerful M1 Pro chip, 16GB unified memory, and 512GB SSD storage. Perfect for creative professionals and developers.';
            $product_image = '/images/macbook-pro.jpg';
            $product_stock = 'In Stock';
            break;
        case '3':
            $product_name = 'HP Spectre x360';
            $product_price = '$1,399.99';
            $product_description = 'The HP Spectre x360 is a versatile 2-in-1 laptop with a gorgeous 14" 4K OLED display, Intel Core i7 processor, 16GB RAM, and a spacious 1TB SSD. Perfect for creative professionals and multitaskers.';
            $product_image = '/images/hp-spectre.jpg';
            $product_stock = 'In Stock';
            break;
        case '4':
            $product_name = 'Website Template - Business Pro';
            $product_price = '$99.99';
            $product_description = 'A professional website template designed for businesses. Includes multiple page layouts, responsive design, contact forms, and integration with popular CMS platforms.';
            $product_image = '/images/template-business.jpg';
            $product_stock = 'Digital Download';
            break;
        case '5':
            $product_name = 'E-commerce Template - ShopMaster';
            $product_price = '$149.99';
            $product_description = 'A complete e-commerce website template with product listings, shopping cart, checkout process, and admin dashboard. Ready to integrate with popular payment gateways.';
            $product_image = '/images/template-ecommerce.jpg';
            $product_stock = 'Digital Download';
            break;
        case '6':
            $product_name = 'Portfolio Template - Creative Pro';
            $product_price = '$79.99';
            $product_description = 'A stunning portfolio template for creative professionals. Showcase your work with beautiful galleries, project pages, and a customizable homepage.';
            $product_image = '/images/template-portfolio.jpg';
            $product_stock = 'Digital Download';
            break;
        default:
            $product_name = 'Product Not Found';
            $product_price = 'N/A';
            $product_description = 'The requested product could not be found.';
            $product_image = '/images/product-not-found.jpg';
            $product_stock = 'Unavailable';
    }
    
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$product_name} - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .product-image {
            max-height: 400px;
            object-fit: contain;
        }
        .related-product {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .related-product:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Gideon's Technology</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/gstore">GStore</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact">Contact</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="/gstore/cart" class="btn btn-outline-light me-2">
                        <i class="fas fa-shopping-cart"></i> Cart (3)
                    </a>
                    <a href="/login" class="btn btn-outline-light me-2">Login</a>
                    <a href="/register" class="btn btn-primary">Register</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="/gstore" class="text-decoration-none">GStore</a></li>
                <li class="breadcrumb-item active" aria-current="page">{$product_name}</li>
            </ol>
        </nav>
        
        <div class="row mb-5">
            <div class="col-md-6">
                <div class="card">
                    <img src="{$product_image}" class="card-img-top product-image" alt="{$product_name}" onerror="this.src='https://via.placeholder.com/600x400?text={$product_name}'">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-3">
                                <img src="{$product_image}" class="img-thumbnail" alt="{$product_name}" onerror="this.src='https://via.placeholder.com/150x100?text={$product_name}'">
                            </div>
                            <div class="col-3">
                                <img src="{$product_image}" class="img-thumbnail" alt="{$product_name}" onerror="this.src='https://via.placeholder.com/150x100?text={$product_name}'">
                            </div>
                            <div class="col-3">
                                <img src="{$product_image}" class="img-thumbnail" alt="{$product_name}" onerror="this.src='https://via.placeholder.com/150x100?text={$product_name}'">
                            </div>
                            <div class="col-3">
                                <img src="{$product_image}" class="img-thumbnail" alt="{$product_name}" onerror="this.src='https://via.placeholder.com/150x100?text={$product_name}'">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <h1 class="mb-3">{$product_name}</h1>
                <div class="d-flex align-items-center mb-3">
                    <div class="text-warning me-2">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <span>4.5 (24 reviews)</span>
                </div>
                <h2 class="text-primary mb-4">{$product_price}</h2>
                <p class="mb-4">{$product_description}</p>
                
                <div class="d-flex align-items-center mb-4">
                    <span class="me-3">Availability:</span>
                    <span class="badge bg-success">{$product_stock}</span>
                </div>
                
                <div class="d-flex align-items-center mb-4">
                    <span class="me-3">Quantity:</span>
                    <div class="input-group" style="width: 130px;">
                        <button class="btn btn-outline-secondary" type="button">-</button>
                        <input type="text" class="form-control text-center" value="1">
                        <button class="btn btn-outline-secondary" type="button">+</button>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex">
                    <button class="btn btn-primary btn-lg">
                        <i class="fas fa-shopping-cart me-2"></i> Add to Cart
                    </button>
                    <button class="btn btn-success btn-lg">
                        <i class="fas fa-bolt me-2"></i> Buy Now
                    </button>
                </div>
            </div>
        </div>
        
        <div class="row mb-5">
            <div class="col-12">
                <ul class="nav nav-tabs" id="productTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab" aria-controls="description" aria-selected="true">Description</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="specifications-tab" data-bs-toggle="tab" data-bs-target="#specifications" type="button" role="tab" aria-controls="specifications" aria-selected="false">Specifications</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">Reviews</button>
                    </li>
                </ul>
                <div class="tab-content p-4 border border-top-0 rounded-bottom" id="productTabsContent">
                    <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                        <h4>Product Description</h4>
                        <p>{$product_description}</p>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla facilisi. Sed euismod, nisl eget ultricies ultricies, nunc nisl aliquam nunc, vitae aliquam nisl nunc vitae nisl. Sed euismod, nisl eget ultricies ultricies, nunc nisl aliquam nunc, vitae aliquam nisl nunc vitae nisl.</p>
                        <p>Sed euismod, nisl eget ultricies ultricies, nunc nisl aliquam nunc, vitae aliquam nisl nunc vitae nisl. Sed euismod, nisl eget ultricies ultricies, nunc nisl aliquam nunc, vitae aliquam nisl nunc vitae nisl.</p>
                    </div>
                    <div class="tab-pane fade" id="specifications" role="tabpanel" aria-labelledby="specifications-tab">
                        <h4>Technical Specifications</h4>
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th scope="row" style="width: 30%;">Model</th>
                                    <td>{$product_name}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Manufacturer</th>
                                    <td>Gideon's Technology</td>
                                </tr>
                                <tr>
                                    <th scope="row">Release Year</th>
                                    <td>2023</td>
                                </tr>
                                <tr>
                                    <th scope="row">Warranty</th>
                                    <td>1 Year Limited Warranty</td>
                                </tr>
                                <tr>
                                    <th scope="row">Support</th>
                                    <td>24/7 Customer Support</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                        <h4>Customer Reviews</h4>
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <img src="https://via.placeholder.com/50" class="rounded-circle me-3" alt="User">
                                <div>
                                    <h5 class="mb-0">John Doe</h5>
                                    <div class="text-warning">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <small class="text-muted">Posted on May 1, 2023</small>
                                </div>
                            </div>
                            <p>Excellent product! Exceeded my expectations in every way. The quality is outstanding and it performs exactly as advertised.</p>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <img src="https://via.placeholder.com/50" class="rounded-circle me-3" alt="User">
                                <div>
                                    <h5 class="mb-0">Jane Smith</h5>
                                    <div class="text-warning">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                    <small class="text-muted">Posted on April 15, 2023</small>
                                </div>
                            </div>
                            <p>Very good product overall. The only minor issue I had was with the setup process, which was a bit complicated. Once set up though, it works perfectly.</p>
                        </div>
                        <hr>
                        <h5>Write a Review</h5>
                        <form>
                            <div class="mb-3">
                                <label for="reviewRating" class="form-label">Rating</label>
                                <select class="form-select" id="reviewRating">
                                    <option value="5">5 Stars - Excellent</option>
                                    <option value="4">4 Stars - Very Good</option>
                                    <option value="3">3 Stars - Good</option>
                                    <option value="2">2 Stars - Fair</option>
                                    <option value="1">1 Star - Poor</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="reviewTitle" class="form-label">Review Title</label>
                                <input type="text" class="form-control" id="reviewTitle" placeholder="Summarize your experience">
                            </div>
                            <div class="mb-3">
                                <label for="reviewContent" class="form-label">Review</label>
                                <textarea class="form-control" id="reviewContent" rows="4" placeholder="Share your experience with this product"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Review</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <h3 class="mb-4">Related Products</h3>
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card related-product h-100">
                    <img src="/images/macbook-pro.jpg" class="card-img-top" alt="MacBook Pro" onerror="this.src='https://via.placeholder.com/300x200?text=MacBook+Pro'">
                    <div class="card-body">
                        <h5 class="card-title">MacBook Pro 14"</h5>
                        <p class="card-text text-primary fw-bold">$1,999.99</p>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <a href="/gstore/product/2" class="btn btn-sm btn-outline-primary w-100">View Details</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card related-product h-100">
                    <img src="/images/hp-spectre.jpg" class="card-img-top" alt="HP Spectre x360" onerror="this.src='https://via.placeholder.com/300x200?text=HP+Spectre+x360'">
                    <div class="card-body">
                        <h5 class="card-title">HP Spectre x360</h5>
                        <p class="card-text text-primary fw-bold">$1,399.99</p>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <a href="/gstore/product/3" class="btn btn-sm btn-outline-primary w-100">View Details</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card related-product h-100">
                    <img src="/images/template-business.jpg" class="card-img-top" alt="Website Template - Business Pro" onerror="this.src='https://via.placeholder.com/300x200?text=Website+Template'">
                    <div class="card-body">
                        <h5 class="card-title">Website Template - Business Pro</h5>
                        <p class="card-text text-primary fw-bold">$99.99</p>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <a href="/gstore/product/4" class="btn btn-sm btn-outline-primary w-100">View Details</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card related-product h-100">
                    <img src="/images/template-ecommerce.jpg" class="card-img-top" alt="E-commerce Template - ShopMaster" onerror="this.src='https://via.placeholder.com/300x200?text=E-commerce+Template'">
                    <div class="card-body">
                        <h5 class="card-title">E-commerce Template - ShopMaster</h5>
                        <p class="card-text text-primary fw-bold">$149.99</p>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <a href="/gstore/product/5" class="btn btn-sm btn-outline-primary w-100">View Details</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h5>Gideon's Technology</h5>
                    <p>Your trusted partner for all technology needs since 2010.</p>
                </div>
                <div class="col-md-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="/" class="text-white">Home</a></li>
                        <li><a href="/services" class="text-white">Services</a></li>
                        <li><a href="/gstore" class="text-white">GStore</a></li>
                        <li><a href="/about" class="text-white">About Us</a></li>
                        <li><a href="/contact" class="text-white">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Contact Us</h5>
                    <address>
                        123 Tech Street<br>
                        San Francisco, CA 94107<br>
                        <i class="fas fa-phone"></i> (123) 456-7890<br>
                        <i class="fas fa-envelope"></i> info@gideonstech.com
                    </address>
                </div>
                <div class="col-md-3">
                    <h5>Follow Us</h5>
                    <div class="d-flex">
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                    <div class="mt-3">
                        <h6>Subscribe to our newsletter</h6>
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Email Address">
                            <button class="btn btn-primary" type="button">Subscribe</button>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p class="mb-0">&copy; 2023 Gideon's Technology. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;
    echo $html;
    exit;
}

if ($path === 'gstore/categories/templates') {
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Templates - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .product-image {
            height: 200px;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Gideon's Technology</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/gstore">GStore</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact">Contact</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="/gstore/cart" class="btn btn-outline-light me-2">
                        <i class="fas fa-shopping-cart"></i> Cart (3)
                    </a>
                    <a href="/login" class="btn btn-outline-light me-2">Login</a>
                    <a href="/register" class="btn btn-primary">Register</a>
                </div>
            </div>
        </div>
    </nav>
HTML;
    echo $html;
    exit;
}

if ($path === 'gstore/categories/accessories') {
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accessories - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .product-image {
            height: 200px;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Gideon's Technology</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/gstore">GStore</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact">Contact</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="/gstore/cart" class="btn btn-outline-light me-2">
                        <i class="fas fa-shopping-cart"></i> Cart (3)
                    </a>
                    <a href="/login" class="btn btn-outline-light me-2">Login</a>
                    <a href="/register" class="btn btn-primary">Register</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="/gstore" class="text-decoration-none">GStore</a></li>
                <li class="breadcrumb-item"><a href="/gstore/categories" class="text-decoration-none">Categories</a></li>
                <li class="breadcrumb-item active" aria-current="page">Accessories</li>
            </ol>
        </nav>
        
        <h1 class="mb-4">Accessories</h1>
        
        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Filters</h5>
                    </div>
                    <div class="card-body">
                        <h6>Category</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="catHeadphones" checked>
                            <label class="form-check-label" for="catHeadphones">
                                Headphones & Audio
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="catStorage" checked>
                            <label class="form-check-label" for="catStorage">
                                Storage & Drives
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="catPeripherals" checked>
                            <label class="form-check-label" for="catPeripherals">
                                Peripherals
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="catCases" checked>
                            <label class="form-check-label" for="catCases">
                                Cases & Protection
                            </label>
                        </div>
                        
                        <hr>
                        
                        <h6>Price Range</h6>
                        <div class="mb-3">
                            <label for="priceRange" class="form-label">$0 - $500</label>
                            <input type="range" class="form-range" min="0" max="500" id="priceRange">
                        </div>
                        
                        <hr>
                        
                        <h6>Brand</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="brandApple" checked>
                            <label class="form-check-label" for="brandApple">
                                Apple
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="brandSamsung" checked>
                            <label class="form-check-label" for="brandSamsung">
                                Samsung
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="brandSony" checked>
                            <label class="form-check-label" for="brandSony">
                                Sony
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="brandLogitech" checked>
                            <label class="form-check-label" for="brandLogitech">
                                Logitech
                            </label>
                        </div>
                        
                        <hr>
                        
                        <button class="btn btn-primary w-100">Apply Filters</button>
                    </div>
                </div>
            </div>
            
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <span>Showing 12 of 36 products</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <label for="sortOptions" class="me-2">Sort by:</label>
                        <select class="form-select" id="sortOptions">
                            <option selected>Popularity</option>
                            <option>Price: Low to High</option>
                            <option>Price: High to Low</option>
                            <option>Newest First</option>
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Product 1 -->
                    <div class="col-md-4 mb-4">
                        <div class="card product-card h-100">
                            <img src="/images/airpods-pro.jpg" class="card-img-top product-image" alt="AirPods Pro" onerror="this.src='https://via.placeholder.com/300x200?text=AirPods+Pro'">
                            <div class="card-body">
                                <h5 class="card-title">AirPods Pro</h5>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-primary fw-bold">$249.99</span>
                                    <div class="text-warning">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                </div>
                                <p class="card-text">Active Noise Cancellation, Transparency mode, Spatial Audio, sweat and water resistant.</p>
                            </div>
                            <div class="card-footer bg-white border-top-0 d-flex justify-content-between">
                                <a href="/gstore/product/13" class="btn btn-sm btn-outline-primary">View Details</a>
                                <button class="btn btn-sm btn-primary">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product 2 -->
                    <div class="col-md-4 mb-4">
                        <div class="card product-card h-100">
                            <img src="/images/samsung-buds.jpg" class="card-img-top product-image" alt="Samsung Galaxy Buds Pro" onerror="this.src='https://via.placeholder.com/300x200?text=Samsung+Galaxy+Buds+Pro'">
                            <div class="card-body">
                                <h5 class="card-title">Samsung Galaxy Buds Pro</h5>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-primary fw-bold">$199.99</span>
                                    <div class="text-warning">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                </div>
                                <p class="card-text">Intelligent ANC, 360 Audio, IPX7 water resistance, seamless device switching.</p>
                            </div>
                            <div class="card-footer bg-white border-top-0 d-flex justify-content-between">
                                <a href="/gstore/product/14" class="btn btn-sm btn-outline-primary">View Details</a>
                                <button class="btn btn-sm btn-primary">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product 3 -->
                    <div class="col-md-4 mb-4">
                        <div class="card product-card h-100">
                            <img src="/images/sony-headphones.jpg" class="card-img-top product-image" alt="Sony WH-1000XM4" onerror="this.src='https://via.placeholder.com/300x200?text=Sony+WH-1000XM4'">
                            <div class="card-body">
                                <h5 class="card-title">Sony WH-1000XM4</h5>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-primary fw-bold">$349.99</span>
                                    <div class="text-warning">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                                <p class="card-text">Industry-leading noise cancellation, 30-hour battery life, speak-to-chat technology.</p>
                            </div>
                            <div class="card-footer bg-white border-top-0 d-flex justify-content-between">
                                <a href="/gstore/product/15" class="btn btn-sm btn-outline-primary">View Details</a>
                                <button class="btn btn-sm btn-primary">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product 4 -->
                    <div class="col-md-4 mb-4">
                        <div class="card product-card h-100">
                            <img src="/images/samsung-ssd.jpg" class="card-img-top product-image" alt="Samsung 970 EVO Plus 1TB SSD" onerror="this.src='https://via.placeholder.com/300x200?text=Samsung+970+EVO+Plus+1TB+SSD'">
                            <div class="card-body">
                                <h5 class="card-title">Samsung 970 EVO Plus 1TB SSD</h5>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-primary fw-bold">$129.99</span>
                                    <div class="text-warning">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                                <p class="card-text">NVMe M.2 SSD, sequential read/write speeds up to 3,500/3,300 MB/s, 5-year warranty.</p>
                            </div>
                            <div class="card-footer bg-white border-top-0 d-flex justify-content-between">
                                <a href="/gstore/product/16" class="btn btn-sm btn-outline-primary">View Details</a>
                                <button class="btn btn-sm btn-primary">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product 5 -->
                    <div class="col-md-4 mb-4">
                        <div class="card product-card h-100">
                            <img src="/images/logitech-mouse.jpg" class="card-img-top product-image" alt="Logitech MX Master 3" onerror="this.src='https://via.placeholder.com/300x200?text=Logitech+MX+Master+3'">
                            <div class="card-body">
                                <h5 class="card-title">Logitech MX Master 3</h5>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-primary fw-bold">$99.99</span>
                                    <div class="text-warning">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                </div>
                                <p class="card-text">Advanced wireless mouse with electromagnetic scroll wheel, customizable buttons, and app-specific profiles.</p>
                            </div>
                            <div class="card-footer bg-white border-top-0 d-flex justify-content-between">
                                <a href="/gstore/product/17" class="btn btn-sm btn-outline-primary">View Details</a>
                                <button class="btn btn-sm btn-primary">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product 6 -->
                    <div class="col-md-4 mb-4">
                        <div class="card product-card h-100">
                            <img src="/images/iphone-case.jpg" class="card-img-top product-image" alt="iPhone 14 Pro Case" onerror="this.src='https://via.placeholder.com/300x200?text=iPhone+14+Pro+Case'">
                            <div class="card-body">
                                <h5 class="card-title">iPhone 14 Pro Case</h5>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-primary fw-bold">$49.99</span>
                                    <div class="text-warning">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                </div>
                                <p class="card-text">Premium leather case with MagSafe compatibility, microfiber lining, and raised edges for screen protection.</p>
                            </div>
                            <div class="card-footer bg-white border-top-0 d-flex justify-content-between">
                                <a href="/gstore/product/18" class="btn btn-sm btn-outline-primary">View Details</a>
                                <button class="btn btn-sm btn-primary">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pagination -->
                <nav aria-label="Product pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h5>Gideon's Technology</h5>
                    <p>Your trusted partner for all technology needs since 2010.</p>
                </div>
                <div class="col-md-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="/" class="text-white">Home</a></li>
                        <li><a href="/services" class="text-white">Services</a></li>
                        <li><a href="/gstore" class="text-white">GStore</a></li>
                        <li><a href="/about" class="text-white">About Us</a></li>
                        <li><a href="/contact" class="text-white">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Contact Us</h5>
                    <address>
                        123 Tech Street<br>
                        San Francisco, CA 94107<br>
                        <i class="fas fa-phone"></i> (123) 456-7890<br>
                        <i class="fas fa-envelope"></i> info@gideonstech.com
                    </address>
                </div>
                <div class="col-md-3">
                    <h5>Follow Us</h5>
                    <div class="d-flex">
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                    <div class="mt-3">
                        <h6>Subscribe to our newsletter</h6>
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Email Address">
                            <button class="btn btn-primary" type="button">Subscribe</button>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p class="mb-0">&copy; 2023 Gideon's Technology. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;
    echo $html;
    exit;
}

if ($path === 'gstore/categories/phones') {
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phones & Tablets - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .product-image {
            height: 200px;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Gideon's Technology</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/gstore">GStore</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact">Contact</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="/gstore/cart" class="btn btn-outline-light me-2">
                        <i class="fas fa-shopping-cart"></i> Cart (3)
                    </a>
                    <a href="/login" class="btn btn-outline-light me-2">Login</a>
                    <a href="/register" class="btn btn-primary">Register</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="/gstore" class="text-decoration-none">GStore</a></li>
                <li class="breadcrumb-item"><a href="/gstore/categories" class="text-decoration-none">Categories</a></li>
                <li class="breadcrumb-item active" aria-current="page">Phones & Tablets</li>
            </ol>
        </nav>
        
        <h1 class="mb-4">Phones & Tablets</h1>
        
        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Filters</h5>
                    </div>
                    <div class="card-body">
                        <h6>Brand</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="brandApple" checked>
                            <label class="form-check-label" for="brandApple">
                                Apple
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="brandSamsung" checked>
                            <label class="form-check-label" for="brandSamsung">
                                Samsung
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="brandGoogle" checked>
                            <label class="form-check-label" for="brandGoogle">
                                Google
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="brandXiaomi" checked>
                            <label class="form-check-label" for="brandXiaomi">
                                Xiaomi
                            </label>
                        </div>
                        
                        <hr>
                        
                        <h6>Price Range</h6>
                        <div class="mb-3">
                            <label for="priceRange" class="form-label">$0 - $2000</label>
                            <input type="range" class="form-range" min="0" max="2000" id="priceRange">
                        </div>
                        
                        <hr>
                        
                        <h6>Device Type</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="typeSmartphone" checked>
                            <label class="form-check-label" for="typeSmartphone">
                                Smartphone
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="typeTablet" checked>
                            <label class="form-check-label" for="typeTablet">
                                Tablet
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="typeFoldable" checked>
                            <label class="form-check-label" for="typeFoldable">
                                Foldable
                            </label>
                        </div>
                        
                        <hr>
                        
                        <button class="btn btn-primary w-100">Apply Filters</button>
                    </div>
                </div>
            </div>
            
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <span>Showing 12 of 24 products</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <label for="sortOptions" class="me-2">Sort by:</label>
                        <select class="form-select" id="sortOptions">
                            <option selected>Popularity</option>
                            <option>Price: Low to High</option>
                            <option>Price: High to Low</option>
                            <option>Newest First</option>
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Product 1 -->
                    <div class="col-md-4 mb-4">
                        <div class="card product-card h-100">
                            <img src="/images/iphone-14.jpg" class="card-img-top product-image" alt="iPhone 14 Pro" onerror="this.src='https://via.placeholder.com/300x200?text=iPhone+14+Pro'">
                            <div class="card-body">
                                <h5 class="card-title">iPhone 14 Pro</h5>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-primary fw-bold">$999.99</span>
                                    <div class="text-warning">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                </div>
                                <p class="card-text">A16 Bionic chip, 48MP camera, Dynamic Island, Always-On display.</p>
                            </div>
                            <div class="card-footer bg-white border-top-0 d-flex justify-content-between">
                                <a href="/gstore/product/7" class="btn btn-sm btn-outline-primary">View Details</a>
                                <button class="btn btn-sm btn-primary">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product 2 -->
                    <div class="col-md-4 mb-4">
                        <div class="card product-card h-100">
                            <img src="/images/samsung-s23.jpg" class="card-img-top product-image" alt="Samsung Galaxy S23 Ultra" onerror="this.src='https://via.placeholder.com/300x200?text=Samsung+Galaxy+S23+Ultra'">
                            <div class="card-body">
                                <h5 class="card-title">Samsung Galaxy S23 Ultra</h5>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-primary fw-bold">$1,199.99</span>
                                    <div class="text-warning">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                                <p class="card-text">Snapdragon 8 Gen 2, 200MP camera, S Pen included, 5000mAh battery.</p>
                            </div>
                            <div class="card-footer bg-white border-top-0 d-flex justify-content-between">
                                <a href="/gstore/product/8" class="btn btn-sm btn-outline-primary">View Details</a>
                                <button class="btn btn-sm btn-primary">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product 3 -->
                    <div class="col-md-4 mb-4">
                        <div class="card product-card h-100">
                            <img src="/images/pixel-7.jpg" class="card-img-top product-image" alt="Google Pixel 7 Pro" onerror="this.src='https://via.placeholder.com/300x200?text=Google+Pixel+7+Pro'">
                            <div class="card-body">
                                <h5 class="card-title">Google Pixel 7 Pro</h5>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-primary fw-bold">$899.99</span>
                                    <div class="text-warning">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                </div>
                                <p class="card-text">Google Tensor G2 chip, 50MP camera, Pure Android experience.</p>
                            </div>
                            <div class="card-footer bg-white border-top-0 d-flex justify-content-between">
                                <a href="/gstore/product/9" class="btn btn-sm btn-outline-primary">View Details</a>
                                <button class="btn btn-sm btn-primary">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product 4 -->
                    <div class="col-md-4 mb-4">
                        <div class="card product-card h-100">
                            <img src="/images/ipad-pro.jpg" class="card-img-top product-image" alt="iPad Pro 12.9"" onerror="this.src='https://via.placeholder.com/300x200?text=iPad+Pro+12.9'">
                            <div class="card-body">
                                <h5 class="card-title">iPad Pro 12.9"</h5>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-primary fw-bold">$1,099.99</span>
                                    <div class="text-warning">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                                <p class="card-text">M2 chip, Liquid Retina XDR display, Thunderbolt port, Apple Pencil support.</p>
                            </div>
                            <div class="card-footer bg-white border-top-0 d-flex justify-content-between">
                                <a href="/gstore/product/10" class="btn btn-sm btn-outline-primary">View Details</a>
                                <button class="btn btn-sm btn-primary">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product 5 -->
                    <div class="col-md-4 mb-4">
                        <div class="card product-card h-100">
                            <img src="/images/galaxy-tab.jpg" class="card-img-top product-image" alt="Samsung Galaxy Tab S8 Ultra" onerror="this.src='https://via.placeholder.com/300x200?text=Samsung+Galaxy+Tab+S8+Ultra'">
                            <div class="card-body">
                                <h5 class="card-title">Samsung Galaxy Tab S8 Ultra</h5>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-primary fw-bold">$899.99</span>
                                    <div class="text-warning">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                </div>
                                <p class="card-text">14.6" AMOLED display, Snapdragon 8 Gen 1, S Pen included, DeX mode.</p>
                            </div>
                            <div class="card-footer bg-white border-top-0 d-flex justify-content-between">
                                <a href="/gstore/product/11" class="btn btn-sm btn-outline-primary">View Details</a>
                                <button class="btn btn-sm btn-primary">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product 6 -->
                    <div class="col-md-4 mb-4">
                        <div class="card product-card h-100">
                            <img src="/images/galaxy-fold.jpg" class="card-img-top product-image" alt="Samsung Galaxy Z Fold 4" onerror="this.src='https://via.placeholder.com/300x200?text=Samsung+Galaxy+Z+Fold+4'">
                            <div class="card-body">
                                <h5 class="card-title">Samsung Galaxy Z Fold 4</h5>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-primary fw-bold">$1,799.99</span>
                                    <div class="text-warning">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                </div>
                                <p class="card-text">7.6" foldable display, Snapdragon 8+ Gen 1, S Pen support, improved durability.</p>
                            </div>
                            <div class="card-footer bg-white border-top-0 d-flex justify-content-between">
                                <a href="/gstore/product/12" class="btn btn-sm btn-outline-primary">View Details</a>
                                <button class="btn btn-sm btn-primary">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pagination -->
                <nav aria-label="Product pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h5>Gideon's Technology</h5>
                    <p>Your trusted partner for all technology needs since 2010.</p>
                </div>
                <div class="col-md-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="/" class="text-white">Home</a></li>
                        <li><a href="/services" class="text-white">Services</a></li>
                        <li><a href="/gstore" class="text-white">GStore</a></li>
                        <li><a href="/about" class="text-white">About Us</a></li>
                        <li><a href="/contact" class="text-white">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Contact Us</h5>
                    <address>
                        123 Tech Street<br>
                        San Francisco, CA 94107<br>
                        <i class="fas fa-phone"></i> (123) 456-7890<br>
                        <i class="fas fa-envelope"></i> info@gideonstech.com
                    </address>
                </div>
                <div class="col-md-3">
                    <h5>Follow Us</h5>
                    <div class="d-flex">
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                    <div class="mt-3">
                        <h6>Subscribe to our newsletter</h6>
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Email Address">
                            <button class="btn btn-primary" type="button">Subscribe</button>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p class="mb-0">&copy; 2023 Gideon's Technology. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;
    echo $html;
    exit;
}

if ($path === 'gstore/categories/computers') {
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Computers & Laptops - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .breadcrumb-item a {
            text-decoration: none;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Gideon's Technology</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/gstore">GStore</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact">Contact</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="/gstore/cart" class="btn btn-outline-light me-2">
                        <i class="fas fa-shopping-cart"></i> Cart (3)
                    </a>
                    <a href="/login" class="btn btn-outline-light me-2">Login</a>
                    <a href="/register" class="btn btn-primary">Register</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/gstore">GStore</a></li>
                <li class="breadcrumb-item"><a href="/gstore/categories">Categories</a></li>
                <li class="breadcrumb-item active" aria-current="page">Computers & Laptops</li>
            </ol>
        </nav>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Computers & Laptops</h1>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    Sort By
                </button>
                <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                    <li><a class="dropdown-item" href="#">Newest</a></li>
                    <li><a class="dropdown-item" href="#">Price: Low to High</a></li>
                    <li><a class="dropdown-item" href="#">Price: High to Low</a></li>
                    <li><a class="dropdown-item" href="#">Best Selling</a></li>
                </ul>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Filter Products</h5>
                    </div>
                    <div class="card-body">
                        <h6>Price Range</h6>
                        <div class="mb-3">
                            <input type="range" class="form-range" min="0" max="3000" step="100" id="priceRange">
                            <div class="d-flex justify-content-between">
                                <span>$0</span>
                                <span>$3000+</span>
                            </div>
                        </div>
                        
                        <h6>Brand</h6>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="brandDell">
                                <label class="form-check-label" for="brandDell">Dell</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="brandHP">
                                <label class="form-check-label" for="brandHP">HP</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="brandApple">
                                <label class="form-check-label" for="brandApple">Apple</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="brandLenovo">
                                <label class="form-check-label" for="brandLenovo">Lenovo</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="brandAsus">
                                <label class="form-check-label" for="brandAsus">Asus</label>
                            </div>
                        </div>
                        
                        <h6>Type</h6>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="typeLaptop">
                                <label class="form-check-label" for="typeLaptop">Laptop</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="typeDesktop">
                                <label class="form-check-label" for="typeDesktop">Desktop</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="typeGaming">
                                <label class="form-check-label" for="typeGaming">Gaming</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="typeWorkstation">
                                <label class="form-check-label" for="typeWorkstation">Workstation</label>
                            </div>
                        </div>
                        
                        <button class="btn btn-primary w-100">Apply Filters</button>
                    </div>
                </div>
            </div>
            
            <div class="col-md-9">
                <div class="row g-4">
                    <!-- Product 1 -->
                    <div class="col-md-4">
                        <div class="card product-card h-100">
                            <img src="/images/dell-xps.jpg" class="card-img-top" alt="Dell XPS 13" onerror="this.src='https://via.placeholder.com/300x200?text=Dell+XPS+13'">
                            <div class="card-body">
                                <h5 class="card-title">Dell XPS 13</h5>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-primary fw-bold">$1,299.99</span>
                                    <span class="badge bg-success">In Stock</span>
                                </div>
                                <p class="card-text">13.4" FHD+ Display, Intel Core i7, 16GB RAM, 512GB SSD</p>
                            </div>
                            <div class="card-footer bg-white border-top-0">
                                <div class="d-grid gap-2">
                                    <a href="/gstore/product/1" class="btn btn-outline-primary">View Details</a>
                                    <button class="btn btn-primary">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product 2 -->
                    <div class="col-md-4">
                        <div class="card product-card h-100">
                            <img src="/images/macbook-pro.jpg" class="card-img-top" alt="MacBook Pro" onerror="this.src='https://via.placeholder.com/300x200?text=MacBook+Pro'">
                            <div class="card-body">
                                <h5 class="card-title">MacBook Pro 14"</h5>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-primary fw-bold">$1,999.99</span>
                                    <span class="badge bg-success">In Stock</span>
                                </div>
                                <p class="card-text">14" Liquid Retina XDR Display, M1 Pro, 16GB RAM, 512GB SSD</p>
                            </div>
                            <div class="card-footer bg-white border-top-0">
                                <div class="d-grid gap-2">
                                    <a href="/gstore/product/2" class="btn btn-outline-primary">View Details</a>
                                    <button class="btn btn-primary">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product 3 -->
                    <div class="col-md-4">
                        <div class="card product-card h-100">
                            <img src="/images/hp-spectre.jpg" class="card-img-top" alt="HP Spectre x360" onerror="this.src='https://via.placeholder.com/300x200?text=HP+Spectre+x360'">
                            <div class="card-body">
                                <h5 class="card-title">HP Spectre x360</h5>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-primary fw-bold">$1,399.99</span>
                                    <span class="badge bg-success">In Stock</span>
                                </div>
                                <p class="card-text">14" 4K OLED Display, Intel Core i7, 16GB RAM, 1TB SSD</p>
                            </div>
                            <div class="card-footer bg-white border-top-0">
                                <div class="d-grid gap-2">
                                    <a href="/gstore/product/3" class="btn btn-outline-primary">View Details</a>
                                    <button class="btn btn-primary">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product 4 -->
                    <div class="col-md-4">
                        <div class="card product-card h-100">
                            <img src="/images/lenovo-thinkpad.jpg" class="card-img-top" alt="Lenovo ThinkPad X1" onerror="this.src='https://via.placeholder.com/300x200?text=Lenovo+ThinkPad+X1'">
                            <div class="card-body">
                                <h5 class="card-title">Lenovo ThinkPad X1</h5>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-primary fw-bold">$1,499.99</span>
                                    <span class="badge bg-success">In Stock</span>
                                </div>
                                <p class="card-text">14" FHD Display, Intel Core i7, 16GB RAM, 512GB SSD</p>
                            </div>
                            <div class="card-footer bg-white border-top-0">
                                <div class="d-grid gap-2">
                                    <a href="/gstore/product/4" class="btn btn-outline-primary">View Details</a>
                                    <button class="btn btn-primary">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product 5 -->
                    <div class="col-md-4">
                        <div class="card product-card h-100">
                            <img src="/images/asus-rog.jpg" class="card-img-top" alt="ASUS ROG Zephyrus" onerror="this.src='https://via.placeholder.com/300x200?text=ASUS+ROG+Zephyrus'">
                            <div class="card-body">
                                <h5 class="card-title">ASUS ROG Zephyrus</h5>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-primary fw-bold">$1,799.99</span>
                                    <span class="badge bg-warning text-dark">Low Stock</span>
                                </div>
                                <p class="card-text">15.6" QHD Display, AMD Ryzen 9, 32GB RAM, 1TB SSD, RTX 3080</p>
                            </div>
                            <div class="card-footer bg-white border-top-0">
                                <div class="d-grid gap-2">
                                    <a href="/gstore/product/5" class="btn btn-outline-primary">View Details</a>
                                    <button class="btn btn-primary">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product 6 -->
                    <div class="col-md-4">
                        <div class="card product-card h-100">
                            <img src="/images/alienware-aurora.jpg" class="card-img-top" alt="Alienware Aurora R13" onerror="this.src='https://via.placeholder.com/300x200?text=Alienware+Aurora+R13'">
                            <div class="card-body">
                                <h5 class="card-title">Alienware Aurora R13</h5>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-primary fw-bold">$2,499.99</span>
                                    <span class="badge bg-success">In Stock</span>
                                </div>
                                <p class="card-text">Intel Core i9, 32GB RAM, 1TB SSD + 2TB HDD, RTX 3090, Liquid Cooling</p>
                            </div>
                            <div class="card-footer bg-white border-top-0">
                                <div class="d-grid gap-2">
                                    <a href="/gstore/product/6" class="btn btn-outline-primary">View Details</a>
                                    <button class="btn btn-primary">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pagination -->
                <nav class="mt-4" aria-label="Product pagination">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h5>Gideon's Technology</h5>
                    <p>Your trusted partner for all technology needs since 2010.</p>
                </div>
                <div class="col-md-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="/" class="text-white">Home</a></li>
                        <li><a href="/services" class="text-white">Services</a></li>
                        <li><a href="/gstore" class="text-white">GStore</a></li>
                        <li><a href="/about" class="text-white">About Us</a></li>
                        <li><a href="/contact" class="text-white">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Contact Us</h5>
                    <address>
                        123 Tech Street<br>
                        San Francisco, CA 94107<br>
                        <i class="fas fa-phone"></i> (123) 456-7890<br>
                        <i class="fas fa-envelope"></i> info@gideonstech.com
                    </address>
                </div>
                <div class="col-md-3">
                    <h5>Follow Us</h5>
                    <div class="d-flex">
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                    <div class="mt-3">
                        <h6>Subscribe to our newsletter</h6>
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Email Address">
                            <button class="btn btn-primary" type="button">Subscribe</button>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p class="mb-0">&copy; 2023 Gideon's Technology. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;
    echo $html;
    exit;
}

if ($path === 'gstore/categories') {
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Categories - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .category-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .category-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #0d6efd;
        }
        .breadcrumb-item a {
            text-decoration: none;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Gideon's Technology</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/gstore">GStore</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact">Contact</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="/gstore/cart" class="btn btn-outline-light me-2">
                        <i class="fas fa-shopping-cart"></i> Cart (3)
                    </a>
                    <a href="/login" class="btn btn-outline-light me-2">Login</a>
                    <a href="/register" class="btn btn-primary">Register</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/gstore">GStore</a></li>
                <li class="breadcrumb-item active" aria-current="page">Categories</li>
            </ol>
        </nav>
        
        <h1 class="mb-4">Product Categories</h1>
        
        <div class="row mb-4">
            <div class="col-md-8">
                <p class="lead">Browse our wide range of technology products by category. We offer competitive prices and expert advice on all our products.</p>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search categories...">
                    <button class="btn btn-primary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="row g-4">
            <!-- Category 1 -->
            <div class="col-md-4 col-sm-6">
                <div class="card category-card text-center">
                    <div class="card-body">
                        <div class="category-icon">
                            <i class="fas fa-laptop"></i>
                        </div>
                        <h3 class="card-title">Computers & Laptops</h3>
                        <p class="card-text">Desktops, laptops, and accessories for work and play.</p>
                        <p><span class="badge bg-primary">24 Products</span></p>
                        <a href="/gstore/categories/computers" class="btn btn-outline-primary">Browse Products</a>
                    </div>
                </div>
            </div>
            
            <!-- Category 2 -->
            <div class="col-md-4 col-sm-6">
                <div class="card category-card text-center">
                    <div class="card-body">
                        <div class="category-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3 class="card-title">Smartphones & Tablets</h3>
                        <p class="card-text">The latest mobile devices and accessories.</p>
                        <p><span class="badge bg-primary">36 Products</span></p>
                        <a href="/gstore/categories/mobile" class="btn btn-outline-primary">Browse Products</a>
                    </div>
                </div>
            </div>
            
            <!-- Category 3 -->
            <div class="col-md-4 col-sm-6">
                <div class="card category-card text-center">
                    <div class="card-body">
                        <div class="category-icon">
                            <i class="fas fa-gamepad"></i>
                        </div>
                        <h3 class="card-title">Gaming</h3>
                        <p class="card-text">Consoles, games, and gaming accessories.</p>
                        <p><span class="badge bg-primary">18 Products</span></p>
                        <a href="/gstore/categories/gaming" class="btn btn-outline-primary">Browse Products</a>
                    </div>
                </div>
            </div>
            
            <!-- Category 4 -->
            <div class="col-md-4 col-sm-6">
                <div class="card category-card text-center">
                    <div class="card-body">
                        <div class="category-icon">
                            <i class="fas fa-headphones"></i>
                        </div>
                        <h3 class="card-title">Audio & Headphones</h3>
                        <p class="card-text">Speakers, headphones, and audio equipment.</p>
                        <p><span class="badge bg-primary">29 Products</span></p>
                        <a href="/gstore/categories/audio" class="btn btn-outline-primary">Browse Products</a>
                    </div>
                </div>
            </div>
            
            <!-- Category 5 -->
            <div class="col-md-4 col-sm-6">
                <div class="card category-card text-center">
                    <div class="card-body">
                        <div class="category-icon">
                            <i class="fas fa-tv"></i>
                        </div>
                        <h3 class="card-title">TVs & Home Theater</h3>
                        <p class="card-text">Televisions, projectors, and home theater systems.</p>
                        <p><span class="badge bg-primary">15 Products</span></p>
                        <a href="/gstore/categories/tv" class="btn btn-outline-primary">Browse Products</a>
                    </div>
                </div>
            </div>
            
            <!-- Category 6 -->
            <div class="col-md-4 col-sm-6">
                <div class="card category-card text-center">
                    <div class="card-body">
                        <div class="category-icon">
                            <i class="fas fa-camera"></i>
                        </div>
                        <h3 class="card-title">Cameras & Photography</h3>
                        <p class="card-text">Digital cameras, lenses, and photography equipment.</p>
                        <p><span class="badge bg-primary">22 Products</span></p>
                        <a href="/gstore/categories/cameras" class="btn btn-outline-primary">Browse Products</a>
                    </div>
                </div>
            </div>
            
            <!-- Category 7 -->
            <div class="col-md-4 col-sm-6">
                <div class="card category-card text-center">
                    <div class="card-body">
                        <div class="category-icon">
                            <i class="fas fa-network-wired"></i>
                        </div>
                        <h3 class="card-title">Networking</h3>
                        <p class="card-text">Routers, switches, and networking equipment.</p>
                        <p><span class="badge bg-primary">17 Products</span></p>
                        <a href="/gstore/categories/networking" class="btn btn-outline-primary">Browse Products</a>
                    </div>
                </div>
            </div>
            
            <!-- Category 8 -->
            <div class="col-md-4 col-sm-6">
                <div class="card category-card text-center">
                    <div class="card-body">
                        <div class="category-icon">
                            <i class="fas fa-print"></i>
                        </div>
                        <h3 class="card-title">Printers & Scanners</h3>
                        <p class="card-text">Printers, scanners, and office equipment.</p>
                        <p><span class="badge bg-primary">14 Products</span></p>
                        <a href="/gstore/categories/printers" class="btn btn-outline-primary">Browse Products</a>
                    </div>
                </div>
            </div>
            
            <!-- Category 9 -->
            <div class="col-md-4 col-sm-6">
                <div class="card category-card text-center">
                    <div class="card-body">
                        <div class="category-icon">
                            <i class="fas fa-hdd"></i>
                        </div>
                        <h3 class="card-title">Storage & Drives</h3>
                        <p class="card-text">Hard drives, SSDs, and storage solutions.</p>
                        <p><span class="badge bg-primary">21 Products</span></p>
                        <a href="/gstore/categories/storage" class="btn btn-outline-primary">Browse Products</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h5>Gideon's Technology</h5>
                    <p>Your trusted partner for all technology needs since 2010.</p>
                </div>
                <div class="col-md-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="/" class="text-white">Home</a></li>
                        <li><a href="/services" class="text-white">Services</a></li>
                        <li><a href="/gstore" class="text-white">GStore</a></li>
                        <li><a href="/about" class="text-white">About Us</a></li>
                        <li><a href="/contact" class="text-white">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Contact Us</h5>
                    <address>
                        123 Tech Street<br>
                        San Francisco, CA 94107<br>
                        <i class="fas fa-phone"></i> (123) 456-7890<br>
                        <i class="fas fa-envelope"></i> info@gideonstech.com
                    </address>
                </div>
                <div class="col-md-3">
                    <h5>Follow Us</h5>
                    <div class="d-flex">
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                    <div class="mt-3">
                        <h6>Subscribe to our newsletter</h6>
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Email Address">
                            <button class="btn btn-primary" type="button">Subscribe</button>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p class="mb-0">&copy; 2023 Gideon's Technology. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;
    echo $html;
    exit;
}

if ($path === 'admin/services') {
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Services - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 bg-dark text-white p-0" style="min-height: 100vh;">
                <div class="p-3 text-center">
                    <h4>Admin Panel</h4>
                </div>
                <div class="list-group list-group-flush">
                    <a href="/admin" class="list-group-item list-group-item-action bg-dark text-white">Dashboard</a>
                    <a href="/admin/users" class="list-group-item list-group-item-action bg-dark text-white">Users</a>
                    <a href="/admin/products" class="list-group-item list-group-item-action bg-dark text-white">Products</a>
                    <a href="/admin/orders" class="list-group-item list-group-item-action bg-dark text-white">Orders</a>
                    <a href="/admin/services" class="list-group-item list-group-item-action bg-dark text-white active">Services</a>
                    <a href="/admin/settings" class="list-group-item list-group-item-action bg-dark text-white">Settings</a>
                    <a href="/" class="list-group-item list-group-item-action bg-dark text-white">Back to Site</a>
                </div>
            </div>
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Service Management</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal">Add New Service</button>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Total Services</h5>
                                <h2 class="display-4 fw-bold">12</h2>
                                <p class="text-muted">Active services in your catalog</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Bookings This Month</h5>
                                <h2 class="display-4 fw-bold">28</h2>
                                <p class="text-success">↑ 12% from last month</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Revenue</h5>
                                <h2 class="display-4 fw-bold">$4.2k</h2>
                                <p class="text-success">↑ 8% from last month</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Customer Satisfaction</h5>
                                <h2 class="display-4 fw-bold">4.8</h2>
                                <p class="text-muted">Average rating out of 5</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-white">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0">Services List</h5>
                            </div>
                            <div class="col-auto">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search services...">
                                    <button class="btn btn-outline-secondary" type="button">Search</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Service Name</th>
                                        <th>Category</th>
                                        <th>Duration</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Computer Repair</td>
                                        <td>Repair</td>
                                        <td>2 hours</td>
                                        <td>$99.99</td>
                                        <td><span class="badge bg-success">Active</span></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary">Edit</button>
                                                <button class="btn btn-outline-danger">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Network Setup</td>
                                        <td>Installation</td>
                                        <td>3 hours</td>
                                        <td>$149.99</td>
                                        <td><span class="badge bg-success">Active</span></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary">Edit</button>
                                                <button class="btn btn-outline-danger">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Data Recovery</td>
                                        <td>Recovery</td>
                                        <td>4 hours</td>
                                        <td>$199.99</td>
                                        <td><span class="badge bg-success">Active</span></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary">Edit</button>
                                                <button class="btn btn-outline-danger">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>Virus Removal</td>
                                        <td>Security</td>
                                        <td>2 hours</td>
                                        <td>$89.99</td>
                                        <td><span class="badge bg-success">Active</span></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary">Edit</button>
                                                <button class="btn btn-outline-danger">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td>Website Development</td>
                                        <td>Development</td>
                                        <td>Custom</td>
                                        <td>$999.99</td>
                                        <td><span class="badge bg-warning text-dark">Limited</span></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary">Edit</button>
                                                <button class="btn btn-outline-danger">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>6</td>
                                        <td>IT Consultation</td>
                                        <td>Consultation</td>
                                        <td>1 hour</td>
                                        <td>$79.99</td>
                                        <td><span class="badge bg-danger">Inactive</span></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary">Edit</button>
                                                <button class="btn btn-outline-danger">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add Service Modal -->
    <div class="modal fade" id="addServiceModal" tabindex="-1" aria-labelledby="addServiceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addServiceModalLabel">Add New Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="serviceName" class="form-label">Service Name</label>
                            <input type="text" class="form-control" id="serviceName" required>
                        </div>
                        <div class="mb-3">
                            <label for="serviceCategory" class="form-label">Category</label>
                            <select class="form-select" id="serviceCategory">
                                <option value="repair">Repair</option>
                                <option value="installation">Installation</option>
                                <option value="recovery">Recovery</option>
                                <option value="security">Security</option>
                                <option value="development">Development</option>
                                <option value="consultation">Consultation</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="serviceDuration" class="form-label">Duration</label>
                            <input type="text" class="form-control" id="serviceDuration" placeholder="e.g. 2 hours, Custom">
                        </div>
                        <div class="mb-3">
                            <label for="servicePrice" class="form-label">Price ($)</label>
                            <input type="number" step="0.01" class="form-control" id="servicePrice" required>
                        </div>
                        <div class="mb-3">
                            <label for="serviceDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="serviceDescription" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="serviceStatus" class="form-label">Status</label>
                            <select class="form-select" id="serviceStatus">
                                <option value="active" selected>Active</option>
                                <option value="limited">Limited Availability</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save Service</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;
    echo $html;
    exit;
}

if ($path === 'admin/orders') {
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 bg-dark text-white p-0" style="min-height: 100vh;">
                <div class="p-3 text-center">
                    <h4>Admin Panel</h4>
                </div>
                <div class="list-group list-group-flush">
                    <a href="/admin" class="list-group-item list-group-item-action bg-dark text-white">Dashboard</a>
                    <a href="/admin/users" class="list-group-item list-group-item-action bg-dark text-white">Users</a>
                    <a href="/admin/products" class="list-group-item list-group-item-action bg-dark text-white">Products</a>
                    <a href="/admin/orders" class="list-group-item list-group-item-action bg-dark text-white active">Orders</a>
                    <a href="/admin/services" class="list-group-item list-group-item-action bg-dark text-white">Services</a>
                    <a href="/admin/settings" class="list-group-item list-group-item-action bg-dark text-white">Settings</a>
                    <a href="/" class="list-group-item list-group-item-action bg-dark text-white">Back to Site</a>
                </div>
            </div>
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Order Management</h2>
                    <div>
                        <button class="btn btn-outline-primary me-2">Export Orders</button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterOrdersModal">Filter Orders</button>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0">Recent Orders</h5>
                            </div>
                            <div class="col-auto">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search orders...">
                                    <button class="btn btn-outline-secondary" type="button">Search</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Date</th>
                                        <th>Products</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>#ORD-2023-1001</td>
                                        <td>John Doe</td>
                                        <td>May 5, 2023</td>
                                        <td>3 items</td>
                                        <td>$249.99</td>
                                        <td><span class="badge bg-success">Completed</span></td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#">View Details</a></li>
                                                    <li><a class="dropdown-item" href="#">Print Invoice</a></li>
                                                    <li><a class="dropdown-item" href="#">Mark as Shipped</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="#">Cancel Order</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#ORD-2023-1002</td>
                                        <td>Jane Smith</td>
                                        <td>May 4, 2023</td>
                                        <td>1 item</td>
                                        <td>$129.99</td>
                                        <td><span class="badge bg-warning text-dark">Processing</span></td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#">View Details</a></li>
                                                    <li><a class="dropdown-item" href="#">Print Invoice</a></li>
                                                    <li><a class="dropdown-item" href="#">Mark as Shipped</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="#">Cancel Order</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#ORD-2023-1003</td>
                                        <td>Robert Johnson</td>
                                        <td>May 3, 2023</td>
                                        <td>2 items</td>
                                        <td>$199.98</td>
                                        <td><span class="badge bg-info">Shipped</span></td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#">View Details</a></li>
                                                    <li><a class="dropdown-item" href="#">Print Invoice</a></li>
                                                    <li><a class="dropdown-item" href="#">Track Shipment</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="#">Cancel Order</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#ORD-2023-1004</td>
                                        <td>Sarah Williams</td>
                                        <td>May 2, 2023</td>
                                        <td>5 items</td>
                                        <td>$599.95</td>
                                        <td><span class="badge bg-danger">Cancelled</span></td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#">View Details</a></li>
                                                    <li><a class="dropdown-item" href="#">Restore Order</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#ORD-2023-1005</td>
                                        <td>Michael Brown</td>
                                        <td>May 1, 2023</td>
                                        <td>1 item</td>
                                        <td>$1,299.99</td>
                                        <td><span class="badge bg-primary">Delivered</span></td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#">View Details</a></li>
                                                    <li><a class="dropdown-item" href="#">Print Invoice</a></li>
                                                    <li><a class="dropdown-item" href="#">Process Return</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <nav>
                            <ul class="pagination justify-content-center mb-0">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filter Orders Modal -->
    <div class="modal fade" id="filterOrdersModal" tabindex="-1" aria-labelledby="filterOrdersModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterOrdersModalLabel">Filter Orders</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="dateRange" class="form-label">Date Range</label>
                            <select class="form-select" id="dateRange">
                                <option value="today">Today</option>
                                <option value="yesterday">Yesterday</option>
                                <option value="last7days">Last 7 Days</option>
                                <option value="last30days" selected>Last 30 Days</option>
                                <option value="thisMonth">This Month</option>
                                <option value="lastMonth">Last Month</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="orderStatus" class="form-label">Order Status</label>
                            <select class="form-select" id="orderStatus">
                                <option value="all" selected>All Statuses</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="minAmount" class="form-label">Minimum Amount</label>
                            <input type="number" class="form-control" id="minAmount" placeholder="0.00">
                        </div>
                        <div class="mb-3">
                            <label for="maxAmount" class="form-label">Maximum Amount</label>
                            <input type="number" class="form-control" id="maxAmount" placeholder="Any">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Apply Filters</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;
    echo $html;
    exit;
}

if ($path === 'admin/users') {
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Users - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 bg-dark text-white p-0" style="min-height: 100vh;">
                <div class="p-3 text-center">
                    <h4>Admin Panel</h4>
                </div>
                <div class="list-group list-group-flush">
                    <a href="/admin" class="list-group-item list-group-item-action bg-dark text-white">Dashboard</a>
                    <a href="/admin/users" class="list-group-item list-group-item-action bg-dark text-white active">Users</a>
                    <a href="/admin/products" class="list-group-item list-group-item-action bg-dark text-white">Products</a>
                    <a href="/admin/orders" class="list-group-item list-group-item-action bg-dark text-white">Orders</a>
                    <a href="/admin/services" class="list-group-item list-group-item-action bg-dark text-white">Services</a>
                    <a href="/admin/settings" class="list-group-item list-group-item-action bg-dark text-white">Settings</a>
                    <a href="/" class="list-group-item list-group-item-action bg-dark text-white">Back to Site</a>
                </div>
            </div>
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Manage Users</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">Add New User</button>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Registered</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>John Doe</td>
                                        <td>john@example.com</td>
                                        <td><span class="badge bg-primary">Admin</span></td>
                                        <td><span class="badge bg-success">Active</span></td>
                                        <td>2023-01-15</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary">Edit</button>
                                                <button class="btn btn-outline-danger">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Jane Smith</td>
                                        <td>jane@example.com</td>
                                        <td><span class="badge bg-secondary">Customer</span></td>
                                        <td><span class="badge bg-success">Active</span></td>
                                        <td>2023-02-20</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary">Edit</button>
                                                <button class="btn btn-outline-danger">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Robert Johnson</td>
                                        <td>robert@example.com</td>
                                        <td><span class="badge bg-secondary">Customer</span></td>
                                        <td><span class="badge bg-warning text-dark">Pending</span></td>
                                        <td>2023-03-05</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary">Edit</button>
                                                <button class="btn btn-outline-danger">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>Sarah Williams</td>
                                        <td>sarah@example.com</td>
                                        <td><span class="badge bg-info">Editor</span></td>
                                        <td><span class="badge bg-success">Active</span></td>
                                        <td>2023-03-15</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary">Edit</button>
                                                <button class="btn btn-outline-danger">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td>Michael Brown</td>
                                        <td>michael@example.com</td>
                                        <td><span class="badge bg-secondary">Customer</span></td>
                                        <td><span class="badge bg-danger">Inactive</span></td>
                                        <td>2023-04-10</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary">Edit</button>
                                                <button class="btn btn-outline-danger">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role">
                                <option value="customer">Customer</option>
                                <option value="editor">Editor</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status">
                                <option value="active">Active</option>
                                <option value="pending">Pending</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save User</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;
    echo $html;
    exit;
}

if ($path === 'admin') {
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 bg-dark text-white p-0" style="min-height: 100vh;">
                <div class="p-3 text-center">
                    <h4>Admin Panel</h4>
                </div>
                <div class="list-group list-group-flush">
                    <a href="/admin" class="list-group-item list-group-item-action bg-dark text-white">Dashboard</a>
                    <a href="/admin/users" class="list-group-item list-group-item-action bg-dark text-white">Users</a>
                    <a href="/admin/products" class="list-group-item list-group-item-action bg-dark text-white">Products</a>
                    <a href="/admin/orders" class="list-group-item list-group-item-action bg-dark text-white">Orders</a>
                    <a href="/admin/services" class="list-group-item list-group-item-action bg-dark text-white">Services</a>
                    <a href="/admin/settings" class="list-group-item list-group-item-action bg-dark text-white">Settings</a>
                    <a href="/" class="list-group-item list-group-item-action bg-dark text-white">Back to Site</a>
                </div>
            </div>
            <div class="col-md-10 p-4">
                <h2>Dashboard</h2>
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Users</h5>
                                <h2>1,250</h2>
                                <p>Total registered users</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Orders</h5>
                                <h2>843</h2>
                                <p>Total orders processed</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body">
                                <h5 class="card-title">Products</h5>
                                <h2>156</h2>
                                <p>Products in inventory</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Revenue</h5>
                                <h2>$52,489</h2>
                                <p>Total revenue this month</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    echo $html;
    exit;
}

// GStore route - direct handler to avoid Slim routing conflicts
if ($path === 'gstore') {
    // Use the standalone GStore handler to completely bypass Slim routing
    require __DIR__ . '/direct-gstore.php';
    exit;
}

// Handle GStore sub-routes through the main application
if (preg_match('/^gstore\//', $path)) {
    // For sub-routes, use the main application but ensure we don't register the main route again
    define('GSTORE_ROUTE_REGISTERED', true);
    require __DIR__ . '/index.php';
    exit;
}

// Define routes
$routes = [
    'dashboard' => 'dashboard/index.php',
    'templates' => 'templates/index.php',
    'services' => 'services/index.php',
    'templates/purchase' => 'templates/purchase.php',
    'services/web-dev' => 'services/web-dev/index.php',
    'services/fintech' => 'services/fintech/index.php',
    'services/general-tech' => 'services/general-tech/index.php',
    'services/repair' => 'services/repair/index.php',
    'services/videographics' => 'services/graphics/index.php',
    'services/videographics/editor' => 'services/videographics/editor.php',
    'services/gstore' => 'services/gstore/index.php',
    'services/gstore/cart' => 'services/gstore/cart.php',
    'services/gstore/cart/add' => 'services/gstore/cart_add.php',
    'services/gstore/checkout' => 'services/gstore/checkout.php',
    'services/gstore/checkout/process' => 'services/gstore/checkout_process.php',
    'services/gstore/invoice' => 'services/gstore/invoice.php',
    'admin/products' => 'admin/products/index.php',
    'admin/products/create' => 'admin/products/create.php',
    'login' => 'login.php',
    'register' => 'register.php',
    'account' => 'account.php',
    'contact' => 'contact.php',
    'about' => 'about.php',
    'logout' => 'logout.php'
];

// Check if route exists
if (isset($routes[$path])) {
    $file = __DIR__ . '/' . $routes[$path];
    if (file_exists($file)) {
        require $file;
        exit;
    }
}

// Template detail page
if (preg_match('/^templates\/(\d+)$/', $path, $matches)) {
    $_GET['id'] = $matches[1];
    require __DIR__ . '/templates/template.php';
    exit;
}

// Only handle GStore category pages if not already handled by Slim
if (!isset($_SERVER['REDIRECT_STATUS']) && preg_match('/^gstore\/categories\/([a-zA-Z0-9_-]+)$/', $path, $matches)) {
    $category = $matches[1];
    $categoryTitle = ucfirst($category);
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products in {$categoryTitle} - Gideon's Technology</title>
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
                        <a class="nav-link active" href="/gstore">Store</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/services">Services</a>
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

    <div class="container py-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="/gstore" class="text-decoration-none">Store</a></li>
                <li class="breadcrumb-item active" aria-current="page">{$categoryTitle}</li>
            </ol>
        </nav>

        <h1 class="mb-4">Products in {$categoryTitle}</h1>
        <p class="lead mb-5">Browse our selection of high-quality {$category} products.</p>

        <div class="row">
            <!-- Product cards would go here -->
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="/images/placeholder.jpg" class="card-img-top" alt="Product 1">
                    <div class="card-body">
                        <h5 class="card-title">Product 1</h5>
                        <p class="card-text">This is a sample product in the {$category} category.</p>
                        <p class="card-text text-primary fw-bold">$99.99</p>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="/gstore/product/1" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="/images/placeholder.jpg" class="card-img-top" alt="Product 2">
                    <div class="card-body">
                        <h5 class="card-title">Product 2</h5>
                        <p class="card-text">This is another sample product in the {$category} category.</p>
                        <p class="card-text text-primary fw-bold">$149.99</p>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="/gstore/product/2" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="/images/placeholder.jpg" class="card-img-top" alt="Product 3">
                    <div class="card-body">
                        <h5 class="card-title">Product 3</h5>
                        <p class="card-text">A third sample product in the {$category} category.</p>
                        <p class="card-text text-primary fw-bold">$199.99</p>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="/gstore/product/3" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                        <li><a href="/gstore" class="text-white">Store</a></li>
                        <li><a href="/services" class="text-white">Services</a></li>
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
    exit;
}

// Only handle GStore product pages if not already handled by Slim
if (!isset($_SERVER['REDIRECT_STATUS']) && preg_match('/^gstore\/product\/(\d+)$/', $path, $matches)) {
    $productId = $matches[1];
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product #{$productId} - Gideon's Technology</title>
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
                        <a class="nav-link active" href="/gstore">Store</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/services">Services</a>
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

    <div class="container py-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="/gstore" class="text-decoration-none">Store</a></li>
                <li class="breadcrumb-item active" aria-current="page">Product #{$productId}</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-6">
                <img src="/images/placeholder.jpg" class="img-fluid rounded" alt="Product #{$productId}">
            </div>
            <div class="col-md-6">
                <h1 class="mb-4">Product #{$productId}</h1>
                <p class="lead">This is a detailed description of product #{$productId}. It includes all the features and specifications that a customer would want to know before making a purchase.</p>
                <div class="d-flex align-items-center mb-4">
                    <div class="me-2">
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-half text-warning"></i>
                    </div>
                    <span>(4.5/5 - 24 reviews)</span>
                </div>
                <h2 class="text-primary mb-4">$199.99</h2>
                <div class="mb-4">
                    <h5>Key Features:</h5>
                    <ul>
                        <li>Feature 1: High-quality materials</li>
                        <li>Feature 2: Advanced technology</li>
                        <li>Feature 3: User-friendly interface</li>
                        <li>Feature 4: Long-lasting battery</li>
                        <li>Feature 5: Compact and portable design</li>
                    </ul>
                </div>
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <label for="quantity" class="me-3">Quantity:</label>
                        <input type="number" id="quantity" class="form-control" value="1" min="1" style="width: 70px;">
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-lg">Add to Cart</button>
                    <button class="btn btn-outline-secondary btn-lg">Add to Wishlist</button>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12">
                <ul class="nav nav-tabs" id="productTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab" aria-controls="description" aria-selected="true">Description</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="specifications-tab" data-bs-toggle="tab" data-bs-target="#specifications" type="button" role="tab" aria-controls="specifications" aria-selected="false">Specifications</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">Reviews</button>
                    </li>
                </ul>
                <div class="tab-content p-4 border border-top-0 rounded-bottom" id="productTabsContent">
                    <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                        <h4>Product Description</h4>
                        <p>This is a detailed description of Product #{$productId}. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla facilisi. Sed euismod, nisl eget ultricies ultricies, nunc nisl aliquam nunc, vitae aliquam nisl nunc vitae nisl.</p>
                        <p>Sed euismod, nisl eget ultricies ultricies, nunc nisl aliquam nunc, vitae aliquam nisl nunc vitae nisl. Nulla facilisi. Sed euismod, nisl eget ultricies ultricies, nunc nisl aliquam nunc, vitae aliquam nisl nunc vitae nisl.</p>
                    </div>
                    <div class="tab-pane fade" id="specifications" role="tabpanel" aria-labelledby="specifications-tab">
                        <h4>Technical Specifications</h4>
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th scope="row">Dimensions</th>
                                    <td>10 x 5 x 2 inches</td>
                                </tr>
                                <tr>
                                    <th scope="row">Weight</th>
                                    <td>1.5 lbs</td>
                                </tr>
                                <tr>
                                    <th scope="row">Material</th>
                                    <td>Aluminum and plastic</td>
                                </tr>
                                <tr>
                                    <th scope="row">Battery Life</th>
                                    <td>Up to 10 hours</td>
                                </tr>
                                <tr>
                                    <th scope="row">Warranty</th>
                                    <td>1 year limited warranty</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                        <h4>Customer Reviews</h4>
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <img src="/images/placeholder.jpg" class="rounded-circle me-3" alt="User" style="width: 50px; height: 50px;">
                                <div>
                                    <h5 class="mb-0">John Doe</h5>
                                    <div class="text-warning">
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                    </div>
                                </div>
                            </div>
                            <p>Great product! Exactly what I was looking for. The quality is excellent and it works perfectly.</p>
                        </div>
                        <hr>
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <img src="/images/placeholder.jpg" class="rounded-circle me-3" alt="User" style="width: 50px; height: 50px;">
                                <div>
                                    <h5 class="mb-0">Jane Smith</h5>
                                    <div class="text-warning">
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star"></i>
                                    </div>
                                </div>
                            </div>
                            <p>Very satisfied with my purchase. The only reason I'm giving it 4 stars instead of 5 is that the battery life could be better.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                        <li><a href="/gstore" class="text-white">Store</a></li>
                        <li><a href="/services" class="text-white">Services</a></li>
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
    exit;
}

// Preview redirect
if (preg_match('/^templates\/preview\/(\d+)$/', $path, $matches)) {
    $stmt = $pdo->prepare('SELECT preview_url FROM templates WHERE id = ?');
    $stmt->execute([$matches[1]]);
    if ($template = $stmt->fetch()) {
        header('Location: ' . SITE_URL . '/' . ltrim($template->preview_url, '/'));
        exit;
    }
    http_response_code(404);
    exit;
}

// Service pages
if (preg_match('/^services\/(web-dev|fintech|repair|general-tech|videographics)$/', $path, $matches)) {
    $service_type = $matches[1];
    $file_path = __DIR__ . "/services/{$service_type}/index.php";
    
    if (file_exists($file_path)) {
        require $file_path;
        exit;
    }
}

// Service detail pages
if (preg_match('/^services\/(web-dev|fintech)\/(\d+)$/', $path, $matches)) {
    $_GET['type'] = $matches[1];
    $_GET['id'] = $matches[2];
    require __DIR__ . "/services/{$matches[1]}/service.php";
    exit;
}

// Gstore invoice display
if (preg_match('/^services\/gstore\/invoice(?:\/)?(\d+)?$/', $path, $matches)) {
    $_GET['order_id'] = $matches[1] ?? null;
    require __DIR__ . '/services/gstore/invoice.php';
    exit;
}

// Dynamic gstore product detail (exclude above)
if (preg_match('/^services\/gstore\/([^\/]+)$/', $path, $matches)) {
    $_GET['slug'] = $matches[1];
    require __DIR__ . '/services/gstore/product.php';
    exit;
}

// Dynamic admin product edit
if (preg_match('/^admin\/products\/edit\/(\d+)$/', $path, $matches)) {
    $_GET['id'] = $matches[1];
    require __DIR__ . '/admin/products/edit.php';
    exit;
}

// Dynamic admin product delete
if (preg_match('/^admin\/products\/delete\/(\d+)$/', $path, $matches)) {
    $_GET['id'] = $matches[1];
    require __DIR__ . '/admin/products/delete.php';
    exit;
}

// Download purchased template
if (preg_match('/^templates\/download\/(\d+)$/', $path, $matches)) {
    $_GET['purchase_id'] = $matches[1];
    require __DIR__ . '/templates/download.php';
    exit;
}

// Invoice for purchased template
if (preg_match('/^templates\/invoice\/(\d+)$/', $path, $matches)) {
    $_GET['purchase_id'] = $matches[1];
    require __DIR__ . '/templates/invoice.php';
    exit;
}

// 404 Not Found
http_response_code(404);

// Create a user-friendly 404 page
$html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <img src="/assets/img/logo.png" alt="Gideon's Technology" height="40">
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
                        <a class="nav-link" href="/services">Services</a>
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

    <!-- 404 Content -->
    <div class="container py-5 text-center">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1 class="display-1 text-danger">404</h1>
                <h2 class="mb-4">Page Not Found</h2>
                <p class="lead mb-5">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
                <a href="/" class="btn btn-primary btn-lg">Go to Homepage</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5 fixed-bottom">
        <div class="container">
            <div class="text-center">
                <p>&copy; 2025 Gideon's Technology. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;

echo $html;
exit;
