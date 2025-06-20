<?php
// Web Development service page
$html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Development Services - Gideon's Technology</title>
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
    <header class="bg-primary text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold">Web Development Services</h1>
                    <p class="lead mb-4">Custom web applications, e-commerce solutions, and responsive websites tailored to your business needs.</p>
                    <a href="/contact" class="btn btn-light btn-lg">Get a Free Consultation</a>
                </div>
                <div class="col-lg-4 d-none d-lg-block">
                    <img src="/images/web-dev.jpg" alt="Web Development" class="img-fluid rounded shadow" onerror="this.src='https://via.placeholder.com/500x350?text=Web+Development'">
                </div>
            </div>
        </div>
    </header>

    <!-- Services Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Our Web Development Services</h2>
            <div class="row g-4">
                <!-- Custom Website Design -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="mb-3 text-primary">
                                <i class="bi bi-laptop fs-1"></i>
                            </div>
                            <h3 class="card-title h4">Custom Website Design</h3>
                            <p class="card-text">We create beautiful, responsive websites that reflect your brand identity and engage your audience.</p>
                            <ul class="list-unstyled mt-3">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Responsive design for all devices</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>User-friendly navigation</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>SEO-optimized structure</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Fast loading speeds</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- E-commerce Solutions -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="mb-3 text-primary">
                                <i class="bi bi-cart-check fs-1"></i>
                            </div>
                            <h3 class="card-title h4">E-commerce Solutions</h3>
                            <p class="card-text">Build your online store with secure payment processing and inventory management systems.</p>
                            <ul class="list-unstyled mt-3">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Secure payment gateways</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Inventory management</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Order tracking system</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Customer account management</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Content Management Systems -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="mb-3 text-primary">
                                <i class="bi bi-pencil-square fs-1"></i>
                            </div>
                            <h3 class="card-title h4">Content Management Systems</h3>
                            <p class="card-text">Easy-to-use CMS solutions that allow you to update your website content without technical knowledge.</p>
                            <ul class="list-unstyled mt-3">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>WordPress development</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Custom CMS solutions</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>User-friendly interfaces</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Content scheduling features</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Web Application Development -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="mb-3 text-primary">
                                <i class="bi bi-code-square fs-1"></i>
                            </div>
                            <h3 class="card-title h4">Web Application Development</h3>
                            <p class="card-text">Custom web applications that automate processes and enhance business operations.</p>
                            <ul class="list-unstyled mt-3">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Custom functionality</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Database integration</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>API development</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Scalable architecture</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Website Maintenance -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="mb-3 text-primary">
                                <i class="bi bi-wrench-adjustable fs-1"></i>
                            </div>
                            <h3 class="card-title h4">Website Maintenance</h3>
                            <p class="card-text">Keep your website running smoothly with regular updates, backups, and security checks.</p>
                            <ul class="list-unstyled mt-3">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Regular updates and backups</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Security monitoring</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Performance optimization</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Technical support</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- SEO Services -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="mb-3 text-primary">
                                <i class="bi bi-graph-up-arrow fs-1"></i>
                            </div>
                            <h3 class="card-title h4">SEO Services</h3>
                            <p class="card-text">Improve your website's visibility in search engines and drive more organic traffic.</p>
                            <ul class="list-unstyled mt-3">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Keyword research</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>On-page optimization</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Technical SEO</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Performance tracking</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Our Development Process</h2>
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="row g-4">
                        <div class="col-md-3 text-center">
                            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);">
                                <h3 class="h2 mb-0 text-primary">1</h3>
                            </div>
                            <h4>Discovery</h4>
                            <p>We learn about your business, goals, and requirements.</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);">
                                <h3 class="h2 mb-0 text-primary">2</h3>
                            </div>
                            <h4>Planning</h4>
                            <p>We create a detailed project plan and design mockups.</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);">
                                <h3 class="h2 mb-0 text-primary">3</h3>
                            </div>
                            <h4>Development</h4>
                            <p>We build your website with regular progress updates.</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);">
                                <h3 class="h2 mb-0 text-primary">4</h3>
                            </div>
                            <h4>Launch</h4>
                            <p>We deploy your website and provide ongoing support.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Technologies Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Technologies We Use</h2>
            <div class="row justify-content-center text-center g-4">
                <div class="col-4 col-md-2">
                    <div class="p-3">
                        <i class="bi bi-filetype-html fs-1 text-primary"></i>
                        <p class="mt-2 mb-0">HTML5</p>
                    </div>
                </div>
                <div class="col-4 col-md-2">
                    <div class="p-3">
                        <i class="bi bi-filetype-css fs-1 text-primary"></i>
                        <p class="mt-2 mb-0">CSS3</p>
                    </div>
                </div>
                <div class="col-4 col-md-2">
                    <div class="p-3">
                        <i class="bi bi-filetype-js fs-1 text-primary"></i>
                        <p class="mt-2 mb-0">JavaScript</p>
                    </div>
                </div>
                <div class="col-4 col-md-2">
                    <div class="p-3">
                        <i class="bi bi-filetype-php fs-1 text-primary"></i>
                        <p class="mt-2 mb-0">PHP</p>
                    </div>
                </div>
                <div class="col-4 col-md-2">
                    <div class="p-3">
                        <i class="bi bi-wordpress fs-1 text-primary"></i>
                        <p class="mt-2 mb-0">WordPress</p>
                    </div>
                </div>
                <div class="col-4 col-md-2">
                    <div class="p-3">
                        <i class="bi bi-bootstrap fs-1 text-primary"></i>
                        <p class="mt-2 mb-0">Bootstrap</p>
                    </div>
                </div>
                <div class="col-4 col-md-2">
                    <div class="p-3">
                        <i class="bi bi-database fs-1 text-primary"></i>
                        <p class="mt-2 mb-0">MySQL</p>
                    </div>
                </div>
                <div class="col-4 col-md-2">
                    <div class="p-3">
                        <i class="bi bi-react fs-1 text-primary"></i>
                        <p class="mt-2 mb-0">React</p>
                    </div>
                </div>
                <div class="col-4 col-md-2">
                    <div class="p-3">
                        <i class="bi bi-node-plus fs-1 text-primary"></i>
                        <p class="mt-2 mb-0">Node.js</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-5 bg-primary text-white text-center">
        <div class="container">
            <h2 class="mb-3">Ready to Start Your Web Project?</h2>
            <p class="lead mb-4">Contact us today for a free consultation and let us help you achieve your online goals.</p>
            <a href="/contact" class="btn btn-light btn-lg">Contact Us</a>
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
