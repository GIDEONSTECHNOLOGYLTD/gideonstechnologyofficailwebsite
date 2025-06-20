<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Account</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="/user/dashboard" class="list-group-item list-group-item-action active">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                    <a href="/user/profile" class="list-group-item list-group-item-action">
                        <i class="fas fa-user me-2"></i> Profile
                    </a>
                    <a href="/user/orders" class="list-group-item list-group-item-action">
                        <i class="fas fa-shopping-bag me-2"></i> Orders
                    </a>
                    <a href="/user/two-factor" class="list-group-item list-group-item-action">
                        <i class="fas fa-shield-alt me-2"></i> Security
                    </a>
                    <a href="/auth/logout" class="list-group-item list-group-item-action text-danger">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <div class="row">
                <!-- Welcome Card -->
                <div class="col-12 mb-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h4 class="card-title">Welcome back, <?= htmlspecialchars($user['name'] ?? 'User') ?>!</h4>
                            <p class="card-text">Here's an overview of your account and recent activity.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Account Security Status -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">Account Security</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-lock me-2"></i> Password
                                    </div>
                                    <span class="badge bg-success rounded-pill">Set</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-shield-alt me-2"></i> Two-Factor Authentication
                                    </div>
                                    <?php if ($user['two_factor_enabled'] ?? false): ?>
                                        <span class="badge bg-success rounded-pill">Enabled</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning rounded-pill">Disabled</span>
                                    <?php endif; ?>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-envelope me-2"></i> Email Verification
                                    </div>
                                    <?php if ($user['email_verified'] ?? false): ?>
                                        <span class="badge bg-success rounded-pill">Verified</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning rounded-pill">Unverified</span>
                                    <?php endif; ?>
                                </li>
                            </ul>
                            <div class="mt-3">
                                <a href="/user/two-factor" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-cog me-2"></i> Security Settings
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Orders -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">Recent Orders</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($recentOrders)): ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($recentOrders as $order): ?>
                                        <a href="/user/orders/<?= $order['id'] ?>" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">Order #<?= $order['id'] ?></h6>
                                                <small><?= date('M d, Y', strtotime($order['created_at'])) ?></small>
                                            </div>
                                            <p class="mb-1">Total: $<?= number_format($order['total'], 2) ?></p>
                                            <small class="text-muted">
                                                Status: 
                                                <?php if ($order['status'] === 'completed'): ?>
                                                    <span class="text-success">Completed</span>
                                                <?php elseif ($order['status'] === 'processing'): ?>
                                                    <span class="text-primary">Processing</span>
                                                <?php elseif ($order['status'] === 'shipped'): ?>
                                                    <span class="text-info">Shipped</span>
                                                <?php else: ?>
                                                    <span class="text-secondary"><?= ucfirst($order['status']) ?></span>
                                                <?php endif; ?>
                                            </small>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-shopping-bag fa-3x mb-3 text-muted"></i>
                                    <p>You haven't placed any orders yet.</p>
                                    <a href="/gstore" class="btn btn-primary btn-sm">
                                        <i class="fas fa-store me-2"></i> Browse Products
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="mt-3">
                                <a href="/user/orders" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-list me-2"></i> View All Orders
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Account Activity -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Recent Activity</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($recentActivity)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Activity</th>
                                                <th>IP Address</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentActivity as $activity): ?>
                                                <tr>
                                                    <td><?= date('M d, Y H:i', strtotime($activity['created_at'])) ?></td>
                                                    <td><?= htmlspecialchars($activity['description']) ?></td>
                                                    <td><?= htmlspecialchars($activity['ip_address']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-history fa-3x mb-3 text-muted"></i>
                                    <p>No recent activity to display.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
