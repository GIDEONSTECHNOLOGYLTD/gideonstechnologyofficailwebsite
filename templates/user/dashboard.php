<?php
/**
 * User Dashboard Template
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard | <?= $appName ?? 'Gideons Technology' ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/"><?= $appName ?? 'Gideons Technology' ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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
                        <a class="nav-link" href="/logout">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- User Dashboard Content -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <!-- Sidebar -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-person-circle"></i> Welcome, <?= htmlspecialchars($username ?? 'User') ?>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="/user/dashboard" class="list-group-item list-group-item-action active">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                        <a href="/user/profile" class="list-group-item list-group-item-action">
                            <i class="bi bi-person me-2"></i> Profile
                        </a>
                        <a href="/user/orders" class="list-group-item list-group-item-action">
                            <i class="bi bi-box me-2"></i> Orders
                        </a>
                        <a href="/user/settings" class="list-group-item list-group-item-action">
                            <i class="bi bi-gear me-2"></i> Settings
                        </a>
                        <a href="/logout" class="list-group-item list-group-item-action text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <!-- Main Content -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="m-0">User Dashboard</h4>
                    </div>
                    <div class="card-body">
                        <h5>Welcome to Your User Dashboard</h5>
                        <p>Here you can manage your account, view orders, and access all of our services.</p>
                        
                        <div class="row mt-4">
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="bi bi-shop display-4 text-primary mb-2"></i>
                                        <h5 class="card-title">GStore</h5>
                                        <p class="card-text">Browse and purchase from our tech store.</p>
                                        <a href="/gstore" class="btn btn-outline-primary">Visit Store</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="bi bi-laptop display-4 text-primary mb-2"></i>
                                        <h5 class="card-title">GTech Services</h5>
                                        <p class="card-text">Access our technology services platform.</p>
                                        <a href="/gtech" class="btn btn-outline-primary">Explore Services</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="bi bi-headset display-4 text-primary mb-2"></i>
                                        <h5 class="card-title">Support</h5>
                                        <p class="card-text">Get help with any of our products or services.</p>
                                        <a href="/support" class="btn btn-outline-primary">Contact Support</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h5 class="mt-4">Recent Activity</h5>
                        <div class="list-group">
                            <div class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Logged in successfully</h6>
                                    <small>Just now</small>
                                </div>
                                <p class="mb-1">You logged into your account.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
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

    <!-- JavaScript Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>