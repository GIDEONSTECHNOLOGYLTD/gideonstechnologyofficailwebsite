<?php
// Tech Repair service page
$html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tech Repair Services - Gideon's Technology</title>
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
                    <h1 class="display-4 fw-bold">Tech Repair Services</h1>
                    <p class="lead mb-4">Professional repair services for computers, mobile devices, and other electronics.</p>
                    <a href="/contact" class="btn btn-light btn-lg">Schedule a Repair</a>
                </div>
                <div class="col-lg-4 d-none d-lg-block">
                    <img src="/images/tech-repair.jpg" alt="Tech Repair" class="img-fluid rounded shadow" onerror="this.src='https://via.placeholder.com/500x350?text=Tech+Repair'">
                </div>
            </div>
        </div>
    </header>

    <!-- Services Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Our Repair Services</h2>
            <div class="row g-4">
                <!-- Computer Repair -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="mb-3 text-primary">
                                <i class="bi bi-laptop fs-1"></i>
                            </div>
                            <h3 class="card-title h4">Computer Repair</h3>
                            <p class="card-text">Comprehensive repair services for desktops and laptops of all brands and models.</p>
                            <ul class="list-unstyled mt-3">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Hardware troubleshooting</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Operating system issues</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Virus and malware removal</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Data recovery</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0 pb-4 px-4">
                            <a href="/contact" class="btn btn-outline-primary w-100">Get a Quote</a>
                        </div>
                    </div>
                </div>
                
                <!-- Smartphone Repair -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="mb-3 text-primary">
                                <i class="bi bi-phone fs-1"></i>
                            </div>
                            <h3 class="card-title h4">Smartphone Repair</h3>
                            <p class="card-text">Expert repair services for all major smartphone brands including Apple, Samsung, and more.</p>
                            <ul class="list-unstyled mt-3">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Screen replacement</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Battery replacement</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Water damage repair</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Software troubleshooting</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0 pb-4 px-4">
                            <a href="/contact" class="btn btn-outline-primary w-100">Get a Quote</a>
                        </div>
                    </div>
                </div>
                
                <!-- Tablet Repair -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="mb-3 text-primary">
                                <i class="bi bi-tablet fs-1"></i>
                            </div>
                            <h3 class="card-title h4">Tablet Repair</h3>
                            <p class="card-text">Professional repair services for iPads, Samsung tablets, and other tablet devices.</p>
                            <ul class="list-unstyled mt-3">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Screen replacement</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Battery issues</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Charging port repair</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Software updates</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0 pb-4 px-4">
                            <a href="/contact" class="btn btn-outline-primary w-100">Get a Quote</a>
                        </div>
                    </div>
                </div>
                
                <!-- Game Console Repair -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="mb-3 text-primary">
                                <i class="bi bi-controller fs-1"></i>
                            </div>
                            <h3 class="card-title h4">Game Console Repair</h3>
                            <p class="card-text">Repair services for PlayStation, Xbox, Nintendo, and other gaming consoles.</p>
                            <ul class="list-unstyled mt-3">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Hardware failures</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Disc drive issues</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Controller repairs</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>System updates</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0 pb-4 px-4">
                            <a href="/contact" class="btn btn-outline-primary w-100">Get a Quote</a>
                        </div>
                    </div>
                </div>
                
                <!-- Network Setup & Repair -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="mb-3 text-primary">
                                <i class="bi bi-wifi fs-1"></i>
                            </div>
                            <h3 class="card-title h4">Network Setup & Repair</h3>
                            <p class="card-text">Professional setup and troubleshooting for home and small business networks.</p>
                            <ul class="list-unstyled mt-3">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Router configuration</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>WiFi optimization</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Network security</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Device connectivity issues</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0 pb-4 px-4">
                            <a href="/contact" class="btn btn-outline-primary w-100">Get a Quote</a>
                        </div>
                    </div>
                </div>
                
                <!-- Printer Repair -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="mb-3 text-primary">
                                <i class="bi bi-printer fs-1"></i>
                            </div>
                            <h3 class="card-title h4">Printer Repair</h3>
                            <p class="card-text">Repair and maintenance services for inkjet and laser printers of all brands.</p>
                            <ul class="list-unstyled mt-3">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Paper jam issues</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Print quality problems</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Connectivity troubleshooting</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Driver installation</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0 pb-4 px-4">
                            <a href="/contact" class="btn btn-outline-primary w-100">Get a Quote</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Our Repair Process</h2>
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="row g-4">
                        <div class="col-md-3 text-center">
                            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);">
                                <h3 class="h2 mb-0 text-primary">1</h3>
                            </div>
                            <h4>Diagnosis</h4>
                            <p>We thoroughly examine your device to identify the issue.</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);">
                                <h3 class="h2 mb-0 text-primary">2</h3>
                            </div>
                            <h4>Quote</h4>
                            <p>We provide a transparent quote with no hidden fees.</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);">
                                <h3 class="h2 mb-0 text-primary">3</h3>
                            </div>
                            <h4>Repair</h4>
                            <p>Our certified technicians perform the necessary repairs.</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);">
                                <h3 class="h2 mb-0 text-primary">4</h3>
                            </div>
                            <h4>Quality Check</h4>
                            <p>We test your device to ensure everything works perfectly.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Why Choose Our Repair Services</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="d-flex">
                        <div class="me-3 text-primary">
                            <i class="bi bi-award fs-1"></i>
                        </div>
                        <div>
                            <h4>Certified Technicians</h4>
                            <p>Our repair specialists are certified and experienced in handling all types of devices.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex">
                        <div class="me-3 text-primary">
                            <i class="bi bi-clock-history fs-1"></i>
                        </div>
                        <div>
                            <h4>Quick Turnaround</h4>
                            <p>Most repairs are completed within 24-48 hours to minimize your downtime.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex">
                        <div class="me-3 text-primary">
                            <i class="bi bi-shield-check fs-1"></i>
                        </div>
                        <div>
                            <h4>Warranty</h4>
                            <p>All our repairs come with a 90-day warranty for parts and labor.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex">
                        <div class="me-3 text-primary">
                            <i class="bi bi-cash-stack fs-1"></i>
                        </div>
                        <div>
                            <h4>Competitive Pricing</h4>
                            <p>We offer fair and transparent pricing with no hidden fees or charges.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex">
                        <div class="me-3 text-primary">
                            <i class="bi bi-tools fs-1"></i>
                        </div>
                        <div>
                            <h4>Quality Parts</h4>
                            <p>We use only high-quality replacement parts to ensure long-lasting repairs.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex">
                        <div class="me-3 text-primary">
                            <i class="bi bi-headset fs-1"></i>
                        </div>
                        <div>
                            <h4>Excellent Support</h4>
                            <p>Our friendly team is always ready to answer your questions and provide assistance.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">What Our Customers Say</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="mb-3 text-warning">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
                            <p class="card-text">"They fixed my laptop in just a few hours when other shops said it would take days. Great service and reasonable prices!"</p>
                            <div class="d-flex align-items-center mt-3">
                                <div class="me-3">
                                    <img src="https://via.placeholder.com/50x50" alt="Customer" class="rounded-circle">
                                </div>
                                <div>
                                    <h5 class="mb-0">John Smith</h5>
                                    <p class="text-muted mb-0">Laptop Repair</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="mb-3 text-warning">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
                            <p class="card-text">"I dropped my phone and shattered the screen. They replaced it the same day and it looks brand new. Highly recommend!"</p>
                            <div class="d-flex align-items-center mt-3">
                                <div class="me-3">
                                    <img src="https://via.placeholder.com/50x50" alt="Customer" class="rounded-circle">
                                </div>
                                <div>
                                    <h5 class="mb-0">Sarah Johnson</h5>
                                    <p class="text-muted mb-0">Phone Screen Repair</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="mb-3 text-warning">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
                            <p class="card-text">"They set up our entire office network and continue to provide excellent support whenever we need it. Professional and reliable."</p>
                            <div class="d-flex align-items-center mt-3">
                                <div class="me-3">
                                    <img src="https://via.placeholder.com/50x50" alt="Customer" class="rounded-circle">
                                </div>
                                <div>
                                    <h5 class="mb-0">Michael Brown</h5>
                                    <p class="text-muted mb-0">Network Setup</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-5 bg-primary text-white text-center">
        <div class="container">
            <h2 class="mb-3">Need Your Device Repaired?</h2>
            <p class="lead mb-4">Contact us today to schedule a repair or get a free quote for your device.</p>
            <a href="/contact" class="btn btn-light btn-lg">Schedule a Repair</a>
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
