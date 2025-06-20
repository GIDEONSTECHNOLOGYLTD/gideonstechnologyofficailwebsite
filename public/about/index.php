<?php
// About page
$html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Gideon's Technology</title>
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
                        <a class="nav-link" href="/services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gstore">Store</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/about">About</a>
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
            <h1 class="display-4">About Gideon's Technology</h1>
            <p class="lead">Learn more about our company, mission, and the team behind our success</p>
        </div>
    </header>

    <!-- About Section -->
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <img src="/images/hero-image.jpg" alt="About Gideon's Technology" class="img-fluid rounded shadow" onerror="this.src='https://via.placeholder.com/600x400?text=About+Gideons+Technology'">
                </div>
                <div class="col-lg-6">
                    <h2 class="mb-4">Our Story</h2>
                    <p>Founded in 2020, Gideon's Technology started with a simple mission: to provide cutting-edge technology solutions that help businesses thrive in the digital age. What began as a small startup has grown into a trusted technology partner for businesses of all sizes.</p>
                    <p>Our journey has been marked by innovation, dedication, and a relentless pursuit of excellence. We've helped countless businesses transform their operations, enhance their online presence, and leverage technology to achieve their goals.</p>
                    <p>Today, we continue to push the boundaries of what's possible, staying at the forefront of technological advancements to deliver solutions that drive real results for our clients.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Values -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row mb-5">
                <div class="col-lg-6 mx-auto text-center">
                    <h2 class="mb-4">Our Mission & Values</h2>
                    <p class="lead">We're guided by a clear mission and a set of core values that define everything we do.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h3 class="card-title mb-3">Our Mission</h3>
                            <p class="card-text">To empower businesses through innovative technology solutions that drive growth, efficiency, and competitive advantage in an ever-evolving digital landscape.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h3 class="card-title mb-3">Our Vision</h3>
                            <p class="card-text">To be the leading technology partner for businesses worldwide, known for our innovation, reliability, and commitment to client success.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4 text-center">
                            <div class="mb-3">
                                <i class="bi bi-lightbulb text-primary" style="font-size: 2.5rem;"></i>
                            </div>
                            <h4 class="card-title">Innovation</h4>
                            <p class="card-text">We constantly explore new technologies and approaches to deliver cutting-edge solutions.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4 text-center">
                            <div class="mb-3">
                                <i class="bi bi-people text-primary" style="font-size: 2.5rem;"></i>
                            </div>
                            <h4 class="card-title">Collaboration</h4>
                            <p class="card-text">We work closely with our clients, understanding their needs to deliver tailored solutions.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4 text-center">
                            <div class="mb-3">
                                <i class="bi bi-shield-check text-primary" style="font-size: 2.5rem;"></i>
                            </div>
                            <h4 class="card-title">Excellence</h4>
                            <p class="card-text">We're committed to delivering the highest quality in everything we do.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="py-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-lg-6 mx-auto text-center">
                    <h2 class="mb-4">Meet Our Team</h2>
                    <p class="lead">Our success is driven by our talented team of technology experts.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <img src="https://via.placeholder.com/300x300?text=Gideon+Aina" class="card-img-top" alt="Gideon Aina">
                        <div class="card-body text-center">
                            <h5 class="card-title mb-1">Gideon Aina</h5>
                            <p class="text-muted">Founder & CEO</p>
                            <div class="social-icons mt-3">
                                <a href="#" class="text-dark me-2"><i class="bi bi-linkedin"></i></a>
                                <a href="#" class="text-dark me-2"><i class="bi bi-twitter"></i></a>
                                <a href="#" class="text-dark"><i class="bi bi-envelope"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <img src="https://via.placeholder.com/300x300?text=Sarah+Johnson" class="card-img-top" alt="Sarah Johnson">
                        <div class="card-body text-center">
                            <h5 class="card-title mb-1">Sarah Johnson</h5>
                            <p class="text-muted">CTO</p>
                            <div class="social-icons mt-3">
                                <a href="#" class="text-dark me-2"><i class="bi bi-linkedin"></i></a>
                                <a href="#" class="text-dark me-2"><i class="bi bi-twitter"></i></a>
                                <a href="#" class="text-dark"><i class="bi bi-envelope"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <img src="https://via.placeholder.com/300x300?text=Michael+Chen" class="card-img-top" alt="Michael Chen">
                        <div class="card-body text-center">
                            <h5 class="card-title mb-1">Michael Chen</h5>
                            <p class="text-muted">Lead Developer</p>
                            <div class="social-icons mt-3">
                                <a href="#" class="text-dark me-2"><i class="bi bi-linkedin"></i></a>
                                <a href="#" class="text-dark me-2"><i class="bi bi-twitter"></i></a>
                                <a href="#" class="text-dark"><i class="bi bi-envelope"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <img src="https://via.placeholder.com/300x300?text=Emily+Rodriguez" class="card-img-top" alt="Emily Rodriguez">
                        <div class="card-body text-center">
                            <h5 class="card-title mb-1">Emily Rodriguez</h5>
                            <p class="text-muted">UX/UI Designer</p>
                            <div class="social-icons mt-3">
                                <a href="#" class="text-dark me-2"><i class="bi bi-linkedin"></i></a>
                                <a href="#" class="text-dark me-2"><i class="bi bi-twitter"></i></a>
                                <a href="#" class="text-dark"><i class="bi bi-envelope"></i></a>
                            </div>
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
