<?php $this->layout('layouts/app', ['title' => $title ?? 'Admin Panel']) ?>

<?php $this->start('styles') ?>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Custom admin styles -->
<link rel="stylesheet" href="/assets/admin/css/admin.css">
<?php $this->stop() ?>

<?php $this->start('scripts') ?>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom admin scripts -->
<script src="/assets/admin/js/admin.js"></script>
<?php $this->stop() ?>

<!-- Toast container for notifications -->
<div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100"></div>

<!-- Main Wrapper -->
<div class="admin-wrapper">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <h3>Gideons Tech</h3>
            <p>Admin Panel</p>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?= ($active_menu ?? '') === 'dashboard' ? 'active' : '' ?>" href="/admin">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($active_menu ?? '') === 'users' ? 'active' : '' ?>" href="/admin/users">
                        <i class="fas fa-users me-2"></i> Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($active_menu ?? '') === 'products' ? 'active' : '' ?>" href="/admin/products">
                        <i class="fas fa-box me-2"></i> Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($active_menu ?? '') === 'orders' ? 'active' : '' ?>" href="/admin/orders">
                        <i class="fas fa-shopping-cart me-2"></i> Orders
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($active_menu ?? '') === 'settings' ? 'active' : '' ?>" href="/admin/settings">
                        <i class="fas fa-cog me-2"></i> Settings
                    </a>
                </li>
            </ul>
        </nav>
    </aside>
    
    <!-- Main Content -->
    <div class="admin-content">
        <!-- Top Navigation -->
        <header class="admin-header">
            <div class="header-left">
                <button class="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h4 class="page-title"><?= $this->e($title ?? 'Dashboard') ?></h4>
            </div>
            
            <div class="header-right">
                <div class="dropdown">
                    <button class="dropdown-toggle user-dropdown" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="user-avatar">
                            <?= strtoupper(substr($auth->user->name, 0, 1)) ?>
                        </span>
                        <span class="user-name"><?= $this->e($auth->user->name) ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="/profile"><i class="fas fa-user me-2"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="/settings"><i class="fas fa-cog me-2"></i> Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/logout"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>
        
        <!-- Page Content -->
        <main class="admin-main">
            <?php if (isset($flash_messages)): ?>
                <?php foreach ($flash_messages as $type => $messages): ?>
                    <?php foreach ($messages as $message): ?>
                        <div class="alert alert-<?= $type ?> alert-dismissible fade show" role="alert">
                            <?= $this->e($message) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <?= $this->section('content') ?>
        </main>
        
        <!-- Footer -->
        <footer class="admin-footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <p class="mb-0">&copy; <?= date('Y') ?> Gideons Technology. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>

<?php $this->start('scripts') ?>
<!-- Additional admin scripts -->
<script src="/assets/admin/js/admin.js"></script>
<?php $this->stop() ?>
