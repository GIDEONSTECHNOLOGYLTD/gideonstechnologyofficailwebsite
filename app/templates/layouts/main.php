<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) . ' - ' : '' ?><?= htmlspecialchars($app_name) ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $asset_url ?>/css/styles.css">
    
    <?php if (isset($css)): ?>
        <?= $css ?>
    <?php endif; ?>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="/"><?= htmlspecialchars($app_name) ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link<?= isset($active_page) && $active_page === 'home' ? ' active' : '' ?>" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= isset($active_page) && $active_page === 'services' ? ' active' : '' ?>" href="/services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= isset($active_page) && $active_page === 'gtech' ? ' active' : '' ?>" href="/gtech">GTech Platform</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= isset($active_page) && $active_page === 'gstore' ? ' active' : '' ?>" href="/gstore">Store</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= isset($active_page) && $active_page === 'about' ? ' active' : '' ?>" href="/about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= isset($active_page) && $active_page === 'contact' ? ' active' : '' ?>" href="/contact">Contact</a>
                    </li>
                    <?php if (isset($user)): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> <?= htmlspecialchars($user['name']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/user/profile">My Profile</a></li>
                                <li><a class="dropdown-item" href="/user/orders">My Orders</a></li>
                                <?php if (isset($user['is_admin']) && $user['is_admin']): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="/admin">Admin Panel</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/logout">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link<?= isset($active_page) && $active_page === 'login' ? ' active' : '' ?>" href="/login">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mb-5">
        <?php if (isset($flash) && !empty($flash)): ?>
            <?php foreach ($flash as $type => $message): ?>
                <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <?= $content ?>
    </div>
    
    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><?= htmlspecialchars($app_name) ?></h5>
                    <p>Providing cutting-edge technology solutions since 2020.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="/" class="text-white">Home</a></li>
                        <li><a href="/services" class="text-white">Services</a></li>
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
                <p class="mb-0">&copy; <?= date('Y') ?> <?= htmlspecialchars($app_name) ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?= $asset_url ?>/js/main.js"></script>
    
    <?php if (isset($js)): ?>
        <?= $js ?>
    <?php endif; ?>
</body>
</html>
