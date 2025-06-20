<?php
/**
 * Main Route Loader
 * 
 * This file loads all route files in a structured way to prevent duplicates
 * Uses RouteRegistry to prevent duplicate route registrations
 */

use App\Utilities\Logger;
use App\Core\RouteRegistry;
use App\Core\ErrorHandler;
use Slim\App;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// Define a global flag to prevent duplicate loading
if (!defined('MAIN_ROUTES_REGISTERED')) {
    define('MAIN_ROUTES_REGISTERED', true);
}

return function (App $app, ContainerInterface $container) {
    // Direct login route registration to ensure it's accessible
    if (!RouteRegistry::isRegistered('GET', '/login')) {
        RouteRegistry::register('GET', '/login');
        $app->get('/login', [\App\Controllers\AuthController::class, 'loginForm'])->setName('login');
    }
    
    if (!RouteRegistry::isRegistered('POST', '/login')) {
        RouteRegistry::register('POST', '/login');
        $app->post('/login', [\App\Controllers\AuthController::class, 'login'])->setName('login.post');
    }
    
    // Direct register route registration
    if (!RouteRegistry::isRegistered('GET', '/register')) {
        RouteRegistry::register('GET', '/register');
        $app->get('/register', [\App\Controllers\AuthController::class, 'registerForm'])->setName('register');
    }
    
    if (!RouteRegistry::isRegistered('POST', '/register')) {
        RouteRegistry::register('POST', '/register');
        $app->post('/register', [\App\Controllers\AuthController::class, 'register'])->setName('register.post');
    }
    
    // Also register the auth prefixed routes directly
    if (!RouteRegistry::isRegistered('GET', '/auth/register')) {
        RouteRegistry::register('GET', '/auth/register');
        $app->get('/auth/register', [\App\Controllers\AuthController::class, 'registerForm'])->setName('auth.register');
    }
    
    if (!RouteRegistry::isRegistered('POST', '/auth/register')) {
        RouteRegistry::register('POST', '/auth/register');
        $app->post('/auth/register', [\App\Controllers\AuthController::class, 'register'])->setName('auth.register.post');
    }

    // Root route - displaying a homepage with links to all parts of the application
    // This is the ONLY place where the root route should be registered
    if (!RouteRegistry::isRegistered('GET', '/')) {
        RouteRegistry::register('GET', '/');
        $app->get('/', function (Request $request, Response $response) use ($container) {
            $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gideon's Technology - Innovation & Excellence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            padding-top: 0;
            padding-bottom: 2rem;
        }
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://via.placeholder.com/1920x800');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            margin-bottom: 3rem;
        }
        .feature-card {
            margin-bottom: 1.5rem;
            transition: transform 0.3s;
            height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #0d6efd;
        }
    </style>
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
                        <a class="nav-link active" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gtech">GTech Platform</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gstore">GStore</a>
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
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-3 fw-bold mb-4">Welcome to Gideon's Technology</h1>
            <p class="lead mb-5">Innovative solutions for a digital world. We provide cutting-edge technology services and products to help your business thrive.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="/services" class="btn btn-primary btn-lg">Our Services</a>
                <a href="/gstore" class="btn btn-outline-light btn-lg">Shop Now</a>
            </div>
        </div>
    </section>
    
    <!-- Features Section -->
    <section class="container mb-5">
        <h2 class="text-center mb-5">Why Choose Gideon's Technology?</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card feature-card">
                    <div class="card-body text-center">
                        <i class="bi bi-lightning-charge feature-icon"></i>
                        <h3 class="card-title">Innovative Solutions</h3>
                        <p class="card-text">We stay at the forefront of technology to provide cutting-edge solutions for your business needs.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card feature-card">
                    <div class="card-body text-center">
                        <i class="bi bi-shield-check feature-icon"></i>
                        <h3 class="card-title">Reliable Support</h3>
                        <p class="card-text">Our dedicated team provides 24/7 support to ensure your systems run smoothly at all times.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card feature-card">
                    <div class="card-body text-center">
                        <i class="bi bi-graph-up feature-icon"></i>
                        <h3 class="card-title">Scalable Growth</h3>
                        <p class="card-text">Our solutions grow with your business, providing the flexibility you need for sustainable expansion.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Platforms Section -->
    <section class="bg-light py-5 mb-5">
        <div class="container">
            <h2 class="text-center mb-5">Our Platforms</h2>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h3 class="card-title">GTech Platform</h3>
                            <p class="card-text">Our comprehensive technology services platform offering repair, maintenance, and technical support.</p>
                            <ul class="list-unstyled mb-4">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Hardware Repair</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Web Development</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Mobile App Development</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>IT Consulting</li>
                            </ul>
                            <a href="/gtech" class="btn btn-primary">Explore GTech</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h3 class="card-title">GStore</h3>
                            <p class="card-text">Our online store offering the latest technology products, software, and accessories.</p>
                            <ul class="list-unstyled mb-4">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Mobile Devices</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Computers & Laptops</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Software Licenses</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Accessories & Peripherals</li>
                            </ul>
                            <a href="/gstore" class="btn btn-primary">Shop Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Quick Links Section -->
    <section class="container mb-5">
        <h2 class="text-center mb-5">Quick Links</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-info-circle feature-icon"></i>
                        <h3 class="card-title">About Us</h3>
                        <p class="card-text">Learn more about our company, mission, and values.</p>
                        <a href="/about" class="btn btn-outline-primary">Read More</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-tools feature-icon"></i>
                        <h3 class="card-title">Our Services</h3>
                        <p class="card-text">Explore our range of professional technology services.</p>
                        <a href="/services" class="btn btn-outline-primary">View Services</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-envelope feature-icon"></i>
                        <h3 class="card-title">Contact Us</h3>
                        <p class="card-text">Get in touch with our team for inquiries and support.</p>
                        <a href="/contact" class="btn btn-outline-primary">Contact Now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
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
                        <li><a href="/gtech/services" class="text-white">Services</a></li>
                        <li><a href="/gtech" class="text-white">GTech Platform</a></li>
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;

            $response->getBody()->write($html);
            return $response->withHeader('Content-Type', 'text/html');
        });
    }
    
    // Load API routes
    if (!defined('API_ROUTES_REGISTERED') && file_exists(__DIR__ . '/api.php')) {
        define('API_ROUTES_REGISTERED', true);
        $apiRoutes = require __DIR__ . '/api.php';
        $apiRoutes($app, $container);
    } else if (!defined('API_ROUTES_REGISTERED')) {
        define('API_ROUTES_REGISTERED', true);
        if (class_exists('\App\Utilities\Logger')) {
            \App\Utilities\Logger::warning("API routes file not found: " . __DIR__ . '/api.php');
        }
    }
    
    // Load GStore routes
    if (!defined('GSTORE_ROUTES_REGISTERED') && file_exists(__DIR__ . '/gstore.php')) {
        define('GSTORE_ROUTES_REGISTERED', true);
        $gstoreRoutes = require __DIR__ . '/gstore.php';
        $gstoreRoutes($app, $container);
    } else if (!defined('GSTORE_ROUTES_REGISTERED')) {
        define('GSTORE_ROUTES_REGISTERED', true);
        if (class_exists('\App\Utilities\Logger')) {
            \App\Utilities\Logger::warning("GStore routes file not found: " . __DIR__ . '/gstore.php');
        }
    }
    
    // Load Auth routes - CRITICAL for login functionality
    // Force loading of auth routes regardless of the AUTH_ROUTES_REGISTERED flag
    // This ensures login routes are always available
    if (file_exists(__DIR__ . '/auth.php')) {
        // Unset the flag if it exists to force reload
        if (defined('AUTH_ROUTES_REGISTERED')) {
            // We can't undefine constants in PHP, but we can ensure the routes are loaded
            if (class_exists('\App\Utilities\Logger')) {
                \App\Utilities\Logger::info("Reloading auth routes to ensure login functionality");
            }
        } else {
            define('AUTH_ROUTES_REGISTERED', true);
        }
        
        // Load the auth routes
        $authRoutes = require __DIR__ . '/auth.php';
        $authRoutes($app, $container);
        
        // Explicitly register critical auth routes to ensure they work
        if (!RouteRegistry::isRegistered('GET', '/auth/login')) {
            RouteRegistry::register('GET', '/auth/login');
            $app->get('/auth/login', [\App\Controllers\AuthController::class, 'loginForm'])->setName('auth.login');
        }
        
        if (!RouteRegistry::isRegistered('POST', '/auth/login')) {
            RouteRegistry::register('POST', '/auth/login');
            $app->post('/auth/login', [\App\Controllers\AuthController::class, 'login'])->setName('auth.login.post');
        }
        
        // Register both /login and /auth/login POST routes to handle form submission from either path
        if (!RouteRegistry::isRegistered('POST', '/login')) {
            RouteRegistry::register('POST', '/login');
            $app->post('/login', [\App\Controllers\AuthController::class, 'login'])->setName('login.post');
        }
    } else {
        if (class_exists('\App\Utilities\Logger')) {
            \App\Utilities\Logger::warning("Auth routes file not found: " . __DIR__ . '/auth.php');
        }
    }
    
    // Load User routes
    if (!defined('USER_ROUTES_REGISTERED') && file_exists(__DIR__ . '/user.php')) {
        define('USER_ROUTES_REGISTERED', true);
        $userRoutes = require __DIR__ . '/user.php';
        $userRoutes($app, $container);
    } else if (!defined('USER_ROUTES_REGISTERED')) {
        define('USER_ROUTES_REGISTERED', true);
        if (class_exists('\App\Utilities\Logger')) {
            \App\Utilities\Logger::warning("User routes file not found: " . __DIR__ . '/user.php');
        }
    }
    
    // Load Admin routes
    if (!defined('ADMIN_ROUTES_REGISTERED') && file_exists(__DIR__ . '/admin.php')) {
        define('ADMIN_ROUTES_REGISTERED', true);
        $adminRoutes = require __DIR__ . '/admin.php';
        $adminRoutes($app, $container);
    } else if (!defined('ADMIN_ROUTES_REGISTERED')) {
        define('ADMIN_ROUTES_REGISTERED', true);
        if (class_exists('\App\Utilities\Logger')) {
            \App\Utilities\Logger::warning("Admin routes file not found: " . __DIR__ . '/admin.php');
        }
    }
    
    // Load Pages routes (About, Services, Contact)
    if (!defined('PAGES_ROUTES_REGISTERED')) {
        define('PAGES_ROUTES_REGISTERED', true);
        
        // Create basic page routes directly here since pages.php doesn't exist
        // About page route
        if (!RouteRegistry::isRegistered('GET', '/about')) {
            RouteRegistry::register('GET', '/about');
            $app->get('/about', function (Request $request, Response $response) use ($container) {
                $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
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
                        <a class="nav-link active" href="/about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gtech">GTech Platform</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gstore">GStore</a>
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
    
    <div class="container">
        <h1 class="mb-4">About Gideon's Technology</h1>
        <div class="row">
            <div class="col-md-8">
                <p class="lead">We are a leading technology company dedicated to providing innovative solutions for businesses and individuals.</p>
                <p>Founded in 2010, Gideon's Technology has grown from a small startup to a comprehensive technology provider offering a wide range of services and products.</p>
                <p>Our mission is to empower our clients with cutting-edge technology solutions that drive growth and efficiency.</p>
                <h2 class="mt-4">Our Values</h2>
                <ul>
                    <li><strong>Innovation:</strong> We constantly explore new technologies and approaches.</li>
                    <li><strong>Quality:</strong> We maintain the highest standards in all our products and services.</li>
                    <li><strong>Integrity:</strong> We operate with honesty and transparency in all our dealings.</li>
                    <li><strong>Customer Focus:</strong> We prioritize understanding and meeting our clients' needs.</li>
                </ul>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Contact Us</h3>
                        <p class="card-text">Have questions about our company or services?</p>
                        <a href="/contact" class="btn btn-primary">Get in Touch</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;

                $response->getBody()->write($html);
                return $response->withHeader('Content-Type', 'text/html');
            });
        }
        
        // Services page route - only register if not already registered by services.php
        if (!RouteRegistry::isRegistered('GET', '/services')) {
            RouteRegistry::register('GET', '/services');
            $app->get('/services', function (Request $request, Response $response) {
                $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
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
                        <a class="nav-link" href="/about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gtech">GTech Platform</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gstore">GStore</a>
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
    
    <div class="container">
        <h1 class="mb-4">Our Services</h1>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title">Web Development</h3>
                        <p class="card-text">Custom website development using the latest technologies and frameworks.</p>
                        <ul class="list-unstyled">
                            <li>Responsive Design</li>
                            <li>E-commerce Solutions</li>
                            <li>Content Management Systems</li>
                            <li>Web Applications</li>
                        </ul>
                        <a href="/gtech/services/web-development" class="btn btn-primary">Learn More</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title">Mobile App Development</h3>
                        <p class="card-text">Native and cross-platform mobile applications for iOS and Android.</p>
                        <ul class="list-unstyled">
                            <li>iOS Development</li>
                            <li>Android Development</li>
                            <li>React Native</li>
                            <li>Flutter</li>
                        </ul>
                        <a href="/gtech/services/mobile-development" class="btn btn-primary">Learn More</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title">IT Consulting</h3>
                        <p class="card-text">Expert advice on technology strategy, implementation, and optimization.</p>
                        <ul class="list-unstyled">
                            <li>Technology Assessment</li>
                            <li>Digital Transformation</li>
                            <li>IT Infrastructure</li>
                            <li>Security Audits</li>
                        </ul>
                        <a href="/gtech/services/consulting" class="btn btn-primary">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;

                $response->getBody()->write($html);
                return $response->withHeader('Content-Type', 'text/html');
            });
        }
        
        // Contact page route
        if (!RouteRegistry::isRegistered('GET', '/contact')) {
            RouteRegistry::register('GET', '/contact');
            $app->get('/contact', function (Request $request, Response $response) {
                $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Gideon's Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
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
                        <a class="nav-link" href="/about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gtech">GTech Platform</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gstore">GStore</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/login">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container">
        <h1 class="mb-4">Contact Us</h1>
        <div class="row">
            <div class="col-md-6">
                <form>
                    <div class="mb-3">
                        <label for="name" class="form-label">Your Name</label>
                        <input type="text" class="form-control" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject">
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Our Information</h3>
                        <p class="card-text"><strong>Address:</strong> 123 Tech Street, Silicon Valley, CA 94043</p>
                        <p class="card-text"><strong>Email:</strong> info@gideonstech.com</p>
                        <p class="card-text"><strong>Phone:</strong> (123) 456-7890</p>
                        <p class="card-text"><strong>Hours:</strong> Monday - Friday: 9am - 5pm</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;

                $response->getBody()->write($html);
                return $response->withHeader('Content-Type', 'text/html');
            });
        }
    }
    
    // Load GTech Platform routes
    if (!defined('GTECH_ROUTES_REGISTERED')) {
        define('GTECH_ROUTES_REGISTERED', true);
        
        // Use gtech.php (main version) if it exists
        if (file_exists(__DIR__ . '/gtech.php')) {
            $gtechRoutes = require __DIR__ . '/gtech.php';
            $gtechRoutes($app, $container);
        } 
        // Otherwise, log a warning
        else if (class_exists('\App\Utilities\Logger')) {
            \App\Utilities\Logger::warning("GTech routes file not found: " . __DIR__ . '/gtech.php');
        }
        
        // DO NOT load gtech-fixed.php or gtech_services.php directly
        // They should only be included from within gtech.php if needed
    }
    
    // Load Services routes
    if (!defined('SERVICES_ROUTES_REGISTERED') && file_exists(__DIR__ . '/services.php')) {
        define('SERVICES_ROUTES_REGISTERED', true);
        $servicesRoutes = require __DIR__ . '/services.php';
        $servicesRoutes($app, $container);
    } else if (!defined('SERVICES_ROUTES_REGISTERED')) {
        define('SERVICES_ROUTES_REGISTERED', true);
        if (class_exists('\App\Utilities\Logger')) {
            \App\Utilities\Logger::warning("Services routes file not found: " . __DIR__ . '/services.php');
        }
    }
    
    // Load GStore routes
    if (!defined('GSTORE_ROUTES_REGISTERED') && file_exists(__DIR__ . '/gstore.php')) {
        define('GSTORE_ROUTES_REGISTERED', true);
        $gstoreRoutes = require __DIR__ . '/gstore.php';
        $gstoreRoutes($app, $container);
    } else if (!defined('GSTORE_ROUTES_REGISTERED')) {
        define('GSTORE_ROUTES_REGISTERED', true);
        if (class_exists('\App\Utilities\Logger')) {
            \App\Utilities\Logger::warning("GStore routes file not found: " . __DIR__ . '/gstore.php');
        }
    }
    
    // We don't need a separate gideonstech.php file
    // The main company homepage is handled directly in this file at the root route
    
    // Load admin payment routes
    if (!defined('ADMIN_PAYMENT_ROUTES_REGISTERED') && file_exists(__DIR__ . '/admin_payment.php')) {
        define('ADMIN_PAYMENT_ROUTES_REGISTERED', true);
        $adminPaymentRoutes = require __DIR__ . '/admin_payment.php';
        $adminPaymentRoutes($app, $container);
    } else if (!defined('ADMIN_PAYMENT_ROUTES_REGISTERED')) {
        define('ADMIN_PAYMENT_ROUTES_REGISTERED', true);
        if (class_exists('\App\Utilities\Logger')) {
            \App\Utilities\Logger::warning("Admin payment routes file not found: " . __DIR__ . '/admin_payment.php');
        }
    }
    
    // Load fallback routes to ensure consistent navigation
    if (file_exists(__DIR__ . '/fallback_routes.php')) {
        $fallbackRoutes = require __DIR__ . '/fallback_routes.php';
        $fallbackRoutes($app, $container);
    }
    
    // Test route - simple endpoint to verify the application is working
    if (!RouteRegistry::isRegistered('GET', '/test-route')) {
        RouteRegistry::register('GET', '/test-route');
        $app->get('/test-route', function (Request $request, Response $response) {
            $response->getBody()->write("<h1>Test Route</h1><p>The application is working correctly!</p>");
            return $response;
        });
    }
    
    // API test route - returns JSON response
    if (!RouteRegistry::isRegistered('GET', '/api/test')) {
        RouteRegistry::register('GET', '/api/test');
        $app->get('/api/test', function (Request $request, Response $response) {
            $data = [
                'status' => 'success',
                'message' => 'API is working correctly',
                'timestamp' => time()
            ];
            
            $payload = json_encode($data);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        });
    }
    
    // 404 handler - this should be the last route registered
    $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function (Request $request, Response $response) {
        // Get the requested path for debugging
        $requestPath = $request->getUri()->getPath();
        
        // Log the 404 error for debugging
        if (class_exists('\App\Utilities\Logger')) {
            \App\Utilities\Logger::warning("404 Not Found: {$requestPath}", [
                'method' => $request->getMethod(),
                'headers' => $request->getHeaders()
            ]);
        }
        
        // Check if we have an error handler
        if (class_exists('\App\Core\ErrorHandler')) {
            $errorHandler = ErrorHandler::getInstance();
            $errorHandler->show404("The requested page '{$requestPath}' could not be found.");
            return $response->withStatus(404);
        }
        
        // Fallback if error handler doesn't exist
        $response->getBody()->write('<h1>404 Not Found</h1><p>The requested page "' . htmlspecialchars($requestPath) . '" could not be found.</p>');
        return $response->withStatus(404);
    });
    
    // Set a global flag to indicate that Slim has handled the request
    // This will be used by the fallback router to prevent duplicate handling
    $GLOBALS['ROUTE_HANDLED_BY_SLIM'] = true;
    
    return $app;
};
