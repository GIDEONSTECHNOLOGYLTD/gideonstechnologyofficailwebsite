<?php
/**
 * Web Applications Services template for Gideons Technology
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $appName ?? 'Gideons Technology' ?> - Web Applications</title>
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
                            <li><a class="dropdown-item active" href="applications.php">Web Applications</a></li>
                            <li><a class="dropdown-item" href="ecommerce.php">E-commerce</a></li>
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
                    <h1 class="display-4 fw-bold">Web Applications</h1>
                    <p class="lead">Custom web applications that streamline operations and deliver exceptional user experiences</p>
                    <a href="/contact" class="btn btn-light btn-lg mt-3">Request a Consultation</a>
                </div>
                <div class="col-lg-4">
                    <img src="https://via.placeholder.com/500x300?text=Web+Applications" alt="Web Applications" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Our Web Application Services</h2>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-gear-wide-connected fs-1 text-primary mb-3"></i>
                            <h3 class="card-title h4">Custom Development</h3>
                            <p class="card-text">Tailored web applications designed specifically for your business processes and requirements.</p>
                            <ul class="list-unstyled text-start">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Requirements analysis</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Custom architecture</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Scalable solutions</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Ongoing maintenance</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-building fs-1 text-primary mb-3"></i>
                            <h3 class="card-title h4">Enterprise Applications</h3>
                            <p class="card-text">Robust solutions for large organizations that need to manage complex business operations.</p>
                            <ul class="list-unstyled text-start">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>ERP systems</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>CRM integration</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Workflow automation</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Business intelligence</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-cloud-arrow-up fs-1 text-primary mb-3"></i>
                            <h3 class="card-title h4">SaaS Development</h3>
                            <p class="card-text">Turn your business idea into a scalable Software-as-a-Service product.</p>
                            <ul class="list-unstyled text-start">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Multi-tenant architecture</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Subscription management</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>API development</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Usage analytics</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-hdd-network fs-1 text-primary mb-3"></i>
                            <h3 class="card-title h4">Web Portals</h3>
                            <p class="card-text">Centralized platforms that connect your customers, partners, or employees.</p>
                            <ul class="list-unstyled text-start">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Customer portals</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Partner portals</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Employee intranets</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Knowledge bases</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-phone fs-1 text-primary mb-3"></i>
                            <h3 class="card-title h4">Progressive Web Apps</h3>
                            <p class="card-text">Web applications that deliver app-like experiences with offline capabilities.</p>
                            <ul class="list-unstyled text-start">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Offline functionality</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Push notifications</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Device feature access</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>App-like experience</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-arrow-repeat fs-1 text-primary mb-3"></i>
                            <h3 class="card-title h4">Application Modernization</h3>
                            <p class="card-text">Transform legacy applications into modern, cloud-based solutions.</p>
                            <ul class="list-unstyled text-start">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Legacy system assessment</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Refactoring & migration</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Cloud architecture</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Continuous deployment</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Technologies Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Technologies We Use</h2>
            
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h3 class="h5 mb-0">Frontend Technologies</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    React.js
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Angular
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Vue.js
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    TypeScript
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Bootstrap
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Tailwind CSS
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h3 class="h5 mb-0">Backend Technologies</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Node.js
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Python (Django, Flask)
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    PHP (Laravel, Symfony)
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Java (Spring)
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    .NET Core
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    GraphQL
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h3 class="h5 mb-0">Database & Deployment</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    MySQL / PostgreSQL
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    MongoDB
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Redis
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    AWS / Azure / GCP
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Docker & Kubernetes
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    CI/CD Pipelines
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Development Process -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Our Development Process</h2>
            
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
                                    <h4>Discovery & Planning</h4>
                                    <p>We analyze your business requirements and develop a comprehensive project plan.</p>
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
                                    <h4>Architecture & Design</h4>
                                    <p>Our architects create a solid foundation with scalable, secure system architecture.</p>
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
                                    <h4>Development</h4>
                                    <p>Using agile methodologies, we build your application in iterative sprints.</p>
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
                                    <h4>Quality Assurance</h4>
                                    <p>Rigorous testing ensures your application is reliable, secure, and performs well.</p>
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
                                    <h4>Deployment</h4>
                                    <p>We handle the deployment to your preferred environment and ensure smooth operation.</p>
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
                                    <h4>Support & Maintenance</h4>
                                    <p>Ongoing support, updates, and enhancements to keep your application running optimally.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Case Studies Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Case Studies</h2>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h3 class="card-title">Healthcare Patient Portal</h3>
                            <p class="text-muted mb-3">For a regional healthcare provider</p>
                            <p>We developed a secure patient portal that allows patients to access their medical records, schedule appointments, and communicate with healthcare providers.</p>
                            <p><strong>Technologies used:</strong> React.js, Node.js, MongoDB, AWS</p>
                            <p><strong>Results:</strong> 40% reduction in administrative costs and improved patient satisfaction scores.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h3 class="card-title">Inventory Management System</h3>
                            <p class="text-muted mb-3">For a manufacturing company</p>
                            <p>A custom inventory management system that integrates with their ERP and provides real-time tracking, automated reordering, and comprehensive reporting.</p>
                            <p><strong>Technologies used:</strong> Angular, .NET Core, SQL Server, Azure</p>
                            <p><strong>Results:</strong> 25% reduction in inventory costs and eliminated stockouts.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h3 class="card-title">E-Learning Platform</h3>
                            <p class="text-muted mb-3">For an educational institution</p>
                            <p>A comprehensive learning management system with course content delivery, assessment tools, student progress tracking, and community features.</p>
                            <p><strong>Technologies used:</strong> Vue.js, Laravel, PostgreSQL, Docker</p>
                            <p><strong>Results:</strong> Expanded student reach by 200% and increased course completion rates.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h3 class="card-title">Real Estate Management App</h3>
                            <p class="text-muted mb-3">For a property management company</p>
                            <p>A SaaS application that streamlines property listings, tenant applications, maintenance requests, and financial reporting.</p>
                            <p><strong>Technologies used:</strong> React, Django, PostgreSQL, AWS</p>
                            <p><strong>Results:</strong> Reduced administrative overhead by 35% and improved tenant satisfaction.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-primary text-white">
        <div class="container text-center">
            <h2 class="mb-4">Ready to Build Your Custom Web Application?</h2>
            <p class="lead mb-4">Contact us today to discuss your project requirements.</p>
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