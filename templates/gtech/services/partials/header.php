<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Gideon\'s Technology' ?></title>
    <meta name="description" content="<?= $pageDescription ?? 'Professional technology services and solutions' ?>">
    
    <!-- Favicon -->
    <link rel="icon" href="/assets/images/favicon.ico">
    
    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="/assets/css/responsive.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Custom page styles -->
    <?php if (isset($customStyles)): ?>
    <style>
        <?= $customStyles ?>
    </style>
    <?php endif; ?>
</head>
<body class="<?= $bodyClass ?? '' ?>">
    <!-- Header -->
    <header class="site-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-3 col-md-3 col-6">
                    <div class="logo">
                        <a href="/">
                            <img src="/assets/images/logo.png" alt="Gideon's Technology Logo">
                        </a>
                    </div>
                </div>
                <div class="col-lg-9 col-md-9 col-6">
                    <nav class="main-nav">
                        <ul class="nav-menu">
                            <li><a href="/">Home</a></li>
                            <li class="has-dropdown">
                                <a href="/services">Services</a>
                                <ul class="dropdown-menu">
                                    <li><a href="/services/web-development">Web Development</a></li>
                                    <li><a href="/services/mobile-app-development">Mobile App Development</a></li>
                                    <li><a href="/gtech/services/repair">Tech Repair</a></li>
                                    <li><a href="/services/cloud-solutions">Cloud Solutions</a></li>
                                    <li><a href="/services/it-consulting">IT Consulting</a></li>
                                </ul>
                            </li>
                            <li><a href="/about">About Us</a></li>
                            <li><a href="/blog">Blog</a></li>
                            <li><a href="/contact">Contact</a></li>
                            <li class="cta-button"><a href="/get-quote" class="btn btn-primary">Get a Quote</a></li>
                        </ul>
                        <div class="mobile-menu-toggle">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <main id="main-content">