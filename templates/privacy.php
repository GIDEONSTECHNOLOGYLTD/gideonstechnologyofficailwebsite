<?php
/**
 * Privacy Policy template for Gideons Technology
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $appName ?> - Privacy Policy</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/"><?= $appName ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gtech">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gstore">Store</a>
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

    <!-- Privacy Policy Content -->
    <div class="container py-5">
        <h1 class="mb-4">Privacy Policy</h1>
        
        <div class="card mb-4">
            <div class="card-body">
                <p class="lead">Last updated: <?= date('F j, Y') ?></p>
                <p>This Privacy Policy describes how <?= $appName ?> ("we," "us," or "our") collects, uses, and discloses your personal information when you visit our website, use our services, or make purchases from our online store.</p>
            </div>
        </div>
        
        <div class="mb-4">
            <h2>Information We Collect</h2>
            <p>We may collect several types of information from and about users of our website, including:</p>
            <ul>
                <li>Personal information such as name, email address, postal address, phone number, and payment information when you create an account, make a purchase, or contact us.</li>
                <li>Non-personal information such as browser type, IP address, device information, operating system, and browsing patterns when you navigate our website.</li>
                <li>Information about your transactions and purchase history when you use our services or make purchases.</li>
            </ul>
        </div>
        
        <div class="mb-4">
            <h2>How We Use Your Information</h2>
            <p>We use the information we collect to:</p>
            <ul>
                <li>Process transactions and deliver products or services you have purchased</li>
                <li>Create and maintain your account</li>
                <li>Provide customer support and respond to inquiries</li>
                <li>Send administrative information, such as updates to our terms or privacy policy</li>
                <li>Send marketing communications about our products, services, and promotions (with your consent)</li>
                <li>Improve our website, products, and services</li>
                <li>Protect against fraud and unauthorized transactions</li>
            </ul>
        </div>
        
        <div class="mb-4">
            <h2>Cookies and Tracking Technologies</h2>
            <p>We use cookies and similar tracking technologies to track activity on our website and hold certain information. Cookies are small files that a site or its service provider transfers to your device's hard drive through your web browser. You can set your browser to refuse all cookies or to indicate when a cookie is being sent.</p>
        </div>
        
        <div class="mb-4">
            <h2>Data Security</h2>
            <p>We implement appropriate technical and organizational measures to protect the security of your personal information. However, please be aware that no method of transmission over the internet or electronic storage is 100% secure, and we cannot guarantee absolute security.</p>
        </div>
        
        <div class="mb-4">
            <h2>Third-Party Services</h2>
            <p>We may use third-party service providers to help us operate our business and our website or administer activities on our behalf. These third parties may have access to your personal information only to perform these tasks on our behalf and are obligated not to disclose or use it for any other purpose.</p>
        </div>
        
        <div class="mb-4">
            <h2>Your Rights</h2>
            <p>Depending on your location, you may have certain rights regarding your personal information, such as:</p>
            <ul>
                <li>The right to access and receive a copy of your personal information</li>
                <li>The right to rectify or update your personal information</li>
                <li>The right to delete your personal information</li>
                <li>The right to restrict or object to processing of your personal information</li>
                <li>The right to data portability</li>
            </ul>
            <p>To exercise any of these rights, please contact us using the information provided below.</p>
        </div>
        
        <div class="mb-4">
            <h2>Changes to This Privacy Policy</h2>
            <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last updated" date at the top of this policy.</p>
        </div>
        
        <div class="mb-4">
            <h2>Contact Us</h2>
            <p>If you have any questions or concerns about this Privacy Policy or our data practices, please contact us at:</p>
            <address>
                <strong><?= $appName ?></strong><br>
                123 Tech Street<br>
                Digital City, 10001<br>
                Email: privacy@gideonstech.com<br>
                Phone: +1 (234) 567-8900
            </address>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; <?= $currentYear ?> <?= $appName ?>. All rights reserved.</p>
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