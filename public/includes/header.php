<?php
// Bootstrap already included by the parent file
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db.php';

$isLoggedIn = isset($_SESSION['user_id']) && isset($_SESSION['user_email']);
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Get user data for header if logged in
$user = $isLoggedIn ? getUserByEmail($_SESSION['user_email']) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php echo $meta_description ?? SITE_NAME . ' - Professional Technology Services'; ?>">
    <meta name="keywords" content="<?php echo $meta_keywords ?? 'web development, fintech, technology, IT services'; ?>">
    <title><?php echo $page_title ?? SITE_NAME; ?></title>

    <!-- Favicon -->
    <link rel="icon" href="<?php echo SITE_URL; ?>/assets/img/favicon.ico" type="image/x-icon">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo SITE_URL; ?>/assets/css/style.css" rel="stylesheet">

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js" defer></script>
</head>
<body>
    <!-- Preloader -->
    <div id="preloader" class="preloader">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Header -->
    <header class="header fixed-top">
        <nav class="navbar navbar-expand-lg navbar-light bg-white">
            <div class="container">
                <a class="navbar-brand" href="<?php echo SITE_URL; ?>">
                    <img src="<?php echo SITE_URL; ?>/assets/img/logo.png" alt="<?php echo SITE_NAME; ?>" height="40">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarMain">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'index' ? 'active' : ''; ?>" 
                               href="<?php echo SITE_URL; ?>">Home</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown">
                                Services
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/services/web-dev">Web Development</a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/services/fintech">Fintech Solutions</a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/services/general-tech">General Tech</a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/services/repair">Repair Services</a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/services/videographics">Video & Graphics</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'about' ? 'active' : ''; ?>" 
                               href="<?php echo SITE_URL; ?>/about.php">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'contact' ? 'active' : ''; ?>" 
                               href="<?php echo SITE_URL; ?>/contact.php">Contact</a>
                        </li>
                        <?php if ($isLoggedIn): ?>
                            <?php if (isAdmin()): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo isActive('admin/products'); ?>" href="<?php echo SITE_URL; ?>/admin/products">Admin</a>
                            </li>
                            <?php endif; ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                    <img src="<?php echo isset($user['avatar']) && $user['avatar'] ? SITE_URL . '/assets/img/' . htmlspecialchars($user['avatar']) : SITE_URL . '/assets/img/avatar-placeholder.svg'; ?>" 
                                         alt="Profile" class="rounded-circle me-2" width="32" height="32">
                                    <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/account.php">My Account</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/logout.php">Logout</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="btn btn-primary ms-2" href="<?php echo SITE_URL; ?>/login.php">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="btn btn-outline-primary ms-2" href="<?php echo SITE_URL; ?>/register.php">Register</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="flex-shrink-0"><?php echo "\n"; ?>
