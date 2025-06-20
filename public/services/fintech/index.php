<?php
// FinTech service page
$html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FinTech Solutions - Gideon's Technology</title>
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
                    <h1 class="display-4 fw-bold">FinTech Solutions</h1>
                    <p class="lead mb-4">Payment processing, financial software integration, and secure transaction systems.</p>
                    <a href="/contact" class="btn btn-light btn-lg">Get a Free Consultation</a>
                </div>
                <div class="col-lg-4 d-none d-lg-block">
                    <img src="/images/fintech.jpg" alt="FinTech Solutions" class="img-fluid rounded shadow" onerror="this.src='https://via.placeholder.com/500x350?text=FinTech+Solutions'">
                </div>
            </div>
        </div>
    </header>

    <!-- Services Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Our FinTech Services</h2>
            <div class="row g-4">
                <!-- Payment Gateway Integration -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="mb-3 text-primary">
                                <i class="bi bi-credit-card fs-1"></i>
                            </div>
                            <h3 class="card-title h4">Payment Gateway Integration</h3>
                            <p class="card-text">Seamlessly integrate payment gateways into your website or application for secure transactions.</p>
                            <ul class="list-unstyled mt-3">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Multiple payment methods</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Secure transaction processing</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>PCI DSS compliance</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Fraud prevention tools</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Financial Software Development -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="mb-3 text-primary">
                                <i class="bi bi-bar-chart fs-1"></i>
                            </div>
                            <h3 class="card-title h4">Financial Software Development</h3>
                            <p class="card-text">Custom financial software solutions designed to streamline your business operations.</p>
                            <ul class="list-unstyled mt-3">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Accounting systems</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Billing and invoicing</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Financial reporting</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Budget management tools</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Secure Transaction Systems -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="mb-3 text-primary">
                                <i class="bi bi-shield-lock fs-1"></i>
                            </div>
                            <h3 class="card-title h4">Secure Transaction Systems</h3>
                            <p class="card-text">Robust security measures to protect financial transactions and sensitive data.</p>
                            <ul class="list-unstyled mt-3">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>End-to-end encryption</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Multi-factor authentication</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Secure data storage</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Regular security audits</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Banking API Integration -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="mb-3 text-primary">
                                <i class="bi bi-bank fs-1"></i>
                            </div>
                            <h3 class="card-title h4">Banking API Integration</h3>
                            <p class="card-text">Connect your applications with banking systems for seamless financial operations.</p>
                            <ul class="list-unstyled mt-3">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Account information access</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Payment initiation</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Transaction history</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Real-time notifications</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Blockchain Solutions -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="mb-3 text-primary">
                                <i class="bi bi-diagram-3 fs-1"></i>
                            </div>
                            <h3 class="card-title h4">Blockchain Solutions</h3>
                            <p class="card-text">Leverage blockchain technology for secure, transparent, and efficient financial processes.</p>
                            <ul class="list-unstyled mt-3">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Smart contracts</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Decentralized applications</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Cryptocurrency integration</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Secure ledger systems</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Financial Analytics -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="mb-3 text-primary">
                                <i class="bi bi-graph-up fs-1"></i>
                            </div>
                            <h3 class="card-title h4">Financial Analytics</h3>
                            <p class="card-text">Data-driven insights to help you make informed financial decisions for your business.</p>
                            <ul class="list-unstyled mt-3">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Performance dashboards</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Predictive analytics</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Custom reporting</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Data visualization</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Why Choose Our FinTech Solutions</h2>
            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <div class="mb-3">
                        <i class="bi bi-shield-check text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h4>Security First</h4>
                    <p>We prioritize the security of your financial data and transactions with industry-leading protocols.</p>
                </div>
                <div class="col-md-4 text-center">
                    <div class="mb-3">
                        <i class="bi bi-gear text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h4>Customizable Solutions</h4>
                    <p>Our solutions are tailored to meet your specific business requirements and objectives.</p>
                </div>
                <div class="col-md-4 text-center">
                    <div class="mb-3">
                        <i class="bi bi-headset text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h4>Dedicated Support</h4>
                    <p>Our team of experts provides ongoing support to ensure your financial systems run smoothly.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Case Studies Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Success Stories</h2>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h4 class="card-title">E-commerce Payment Integration</h4>
                            <p class="text-muted">Online Retail Company</p>
                            <p>We helped an e-commerce business streamline their payment process, resulting in a 35% increase in checkout completion rates and improved customer satisfaction.</p>
                            <div class="d-flex align-items-center mt-3">
                                <div class="me-3">
                                    <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                </div>
                                <div>
                                    <p class="mb-0">Reduced transaction processing time by 50%</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-2">
                                <div class="me-3">
                                    <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                </div>
                                <div>
                                    <p class="mb-0">Implemented multi-currency support for global customers</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h4 class="card-title">Financial Management System</h4>
                            <p class="text-muted">Small Business Consulting Firm</p>
                            <p>We developed a custom financial management system for a consulting firm that automated invoicing, expense tracking, and financial reporting.</p>
                            <div class="d-flex align-items-center mt-3">
                                <div class="me-3">
                                    <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                </div>
                                <div>
                                    <p class="mb-0">Reduced administrative work by 20 hours per week</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-2">
                                <div class="me-3">
                                    <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                </div>
                                <div>
                                    <p class="mb-0">Improved financial visibility with real-time reporting</p>
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
            <h2 class="mb-3">Ready to Transform Your Financial Systems?</h2>
            <p class="lead mb-4">Contact us today for a free consultation and let us help you implement secure and efficient FinTech solutions.</p>
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
