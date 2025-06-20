<?php
/**
 * E-commerce Services template for Gideons Technology
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $appName ?? 'Gideons Technology' ?> - E-commerce</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../../../index.php"><?= $appName ?? 'Gideons Technology' ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../../../index.php">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown">Services</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="applications.php">Web Applications</a></li>
                            <li><a class="dropdown-item active" href="ecommerce.php">E-commerce</a></li>
                            <li><a class="dropdown-item" href="design.php">Web Design</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../../gstore/index.php">Store</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../../contact.php">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../../login.php">Login</a>
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
                    <h1 class="display-4 fw-bold">E-commerce Solutions</h1>
                    <p class="lead">Custom online stores that deliver exceptional shopping experiences and drive sales</p>
                    <a href="/contact" class="btn btn-light btn-lg mt-3">Request a Consultation</a>
                </div>
                <div class="col-lg-4">
                    <img src="https://via.placeholder.com/500x300?text=E-commerce" alt="E-commerce Solutions" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </header>

    <!-- Services Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Our E-commerce Services</h2>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-shop fs-1 text-primary mb-3"></i>
                            <h3 class="card-title h4">Custom E-commerce Development</h3>
                            <p class="card-text">Tailored online stores built to your exact specifications and business requirements.</p>
                            <ul class="list-unstyled text-start">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Unique user experience</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Bespoke features</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Custom integrations</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Scalable architecture</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-wordpress fs-1 text-primary mb-3"></i>
                            <h3 class="card-title h4">WooCommerce Solutions</h3>
                            <p class="card-text">WordPress-based e-commerce stores with powerful customization options.</p>
                            <ul class="list-unstyled text-start">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Custom themes</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Plugin development</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Payment gateway integration</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Shipping optimization</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-cart-check fs-1 text-primary mb-3"></i>
                            <h3 class="card-title h4">Shopify Development</h3>
                            <p class="card-text">Custom Shopify stores with unique themes and advanced functionality.</p>
                            <ul class="list-unstyled text-start">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Custom theme design</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>App development</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Shopify Plus expertise</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Headless implementations</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-cart3 fs-1 text-primary mb-3"></i>
                            <h3 class="card-title h4">Magento Development</h3>
                            <p class="card-text">Enterprise-grade e-commerce solutions for complex business requirements.</p>
                            <ul class="list-unstyled text-start">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>B2B & B2C solutions</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Extension development</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Complex catalog management</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Performance optimization</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-phone fs-1 text-primary mb-3"></i>
                            <h3 class="card-title h4">Mobile Commerce</h3>
                            <p class="card-text">Mobile-first shopping experiences that convert on smartphones and tablets.</p>
                            <ul class="list-unstyled text-start">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Responsive design</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>PWA development</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Mobile app integration</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Mobile payment options</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-graph-up fs-1 text-primary mb-3"></i>
                            <h3 class="card-title h4">E-commerce Marketing</h3>
                            <p class="card-text">Digital marketing strategies to drive traffic and increase conversions.</p>
                            <ul class="list-unstyled text-start">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>SEO optimization</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Conversion rate optimization</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Email marketing integration</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Analytics & reporting</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Key Features We Implement</h2>
            
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h3 class="h5 mb-0">Customer Experience</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Advanced search functionality
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Filtered navigation
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Product recommendations
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Wish lists
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Customer reviews
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Live chat integration
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h3 class="h5 mb-0">Checkout & Payment</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    One-page checkout
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Guest checkout
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Multiple payment gateways
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Buy now, pay later integration
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Saved payment methods
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Subscription payments
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h3 class="h5 mb-0">Store Management</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Inventory management
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Order processing
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    CRM integration
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Shipping automation
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Tax calculation
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Analytics dashboard
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Platforms Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">E-commerce Platforms We Work With</h2>
            
            <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-4 justify-content-center">
                <div class="col text-center">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <i class="bi bi-wordpress fs-1 text-primary mb-3"></i>
                            <h5>WooCommerce</h5>
                        </div>
                    </div>
                </div>
                
                <div class="col text-center">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <i class="bi bi-cart-check fs-1 text-primary mb-3"></i>
                            <h5>Shopify</h5>
                        </div>
                    </div>
                </div>
                
                <div class="col text-center">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <i class="bi bi-cart3 fs-1 text-primary mb-3"></i>
                            <h5>Magento</h5>
                        </div>
                    </div>
                </div>
                
                <div class="col text-center">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <i class="bi bi-shop-window fs-1 text-primary mb-3"></i>
                            <h5>BigCommerce</h5>
                        </div>
                    </div>
                </div>
                
                <div class="col text-center">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <i class="bi bi-bag fs-1 text-primary mb-3"></i>
                            <h5>PrestaShop</h5>
                        </div>
                    </div>
                </div>
                
                <div class="col text-center">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <i class="bi bi-code-slash fs-1 text-primary mb-3"></i>
                            <h5>Custom Solutions</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Case Studies Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">E-commerce Success Stories</h2>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h3 class="card-title">Fashion Retailer</h3>
                            <p class="text-muted mb-3">Custom Shopify implementation</p>
                            <p>We helped a boutique fashion retailer transition from brick-and-mortar to online sales with a custom Shopify store featuring unique product visualizations and personalized recommendations.</p>
                            <p><strong>Results:</strong> 300% increase in online sales within 6 months and 40% higher average order value.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h3 class="card-title">Electronics Distributor</h3>
                            <p class="text-muted mb-3">Magento B2B solution</p>
                            <p>Developed a sophisticated B2B e-commerce platform with custom pricing, approval workflows, and integration with their existing ERP system.</p>
                            <p><strong>Results:</strong> 50% reduction in order processing time and expanded customer base to international markets.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h3 class="card-title">Specialty Food Producer</h3>
                            <p class="text-muted mb-3">WooCommerce subscription service</p>
                            <p>Created a subscription-based e-commerce platform for a specialty food producer, enabling recurring deliveries and flexible subscription management.</p>
                            <p><strong>Results:</strong> 70% of customers converted to subscription model, providing predictable revenue streams.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h3 class="card-title">Sporting Goods Store</h3>
                            <p class="text-muted mb-3">Custom multi-channel solution</p>
                            <p>Implemented an omnichannel retail solution that integrates online store, POS systems, and inventory management across multiple physical locations.</p>
                            <p><strong>Results:</strong> 35% increase in overall sales and improved inventory turnover by 25%.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Development Process -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Our E-commerce Development Process</h2>
            
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <h3 class="mb-0">1</h3>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h4>Discovery & Strategy</h4>
                                    <p>We analyze your business model, target audience, and products to develop a comprehensive e-commerce strategy.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <h3 class="mb-0">2</h3>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h4>Platform Selection</h4>
                                    <p>We recommend the most suitable e-commerce platform based on your requirements, budget, and growth plans.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <h3 class="mb-0">3</h3>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h4>Design & User Experience</h4>
                                    <p>Our designers create an engaging, conversion-focused shopping experience that reflects your brand identity.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <h3 class="mb-0">4</h3>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h4>Development & Integration</h4>
                                    <p>We build your store and integrate necessary systems like payment gateways, shipping calculators, and CRM tools.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <h3 class="mb-0">5</h3>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h4>Testing & Quality Assurance</h4>
                                    <p>Rigorous testing of all store functionality, payment processing, and user flows across devices.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <h3 class="mb-0">6</h3>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h4>Launch & Growth Support</h4>
                                    <p>We provide ongoing support, monitoring, and improvements to maximize your store's performance and conversions.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-primary text-white">
        <div class="container text-center">
            <h2 class="mb-4">Ready to Launch Your E-commerce Store?</h2>
            <p class="lead mb-4">Contact us today to discuss your e-commerce project.</p>
            <a href="/contact" class="btn btn-light btn-lg px-4">Get Started</a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>