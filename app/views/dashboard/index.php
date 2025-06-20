<?php include_once APP_PATH . '/views/layouts/header.php'; ?>

<div class="dashboard-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../assets/icon.png" alt="Logo" class="sidebar-logo">
            <span>Gideons Tech</span>
        </div>
        <div class="sidebar-menu">
            <div class="sidebar-item active">
                <i class="fa fa-home"></i>
                <span>Dashboard</span>
            </div>
            <div class="sidebar-item">
                <a href="/dashboard/profile">
                    <i class="fa fa-user"></i>
                    <span>Profile</span>
                </a>
            </div>
            <div class="sidebar-item">
                <a href="/dashboard/orders">
                    <i class="fa fa-shopping-cart"></i>
                    <span>My Orders</span>
                </a>
            </div>
            <div class="sidebar-item">
                <a href="/dashboard/templates">
                    <i class="fa fa-code"></i>
                    <span>My Templates</span>
                </a>
            </div>
            <div class="sidebar-item">
                <a href="/dashboard/payments">
                    <i class="fa fa-credit-card"></i>
                    <span>My Payments</span>
                </a>
            </div>
            <div class="sidebar-item">
                <a href="/web-dev/templates/purchased">
                    <i class="fa fa-code"></i>
                    <span>My Templates</span>
                </a>
            </div>
            <div class="sidebar-item">
                <a href="/dashboard/settings">
                    <i class="fa fa-cog"></i>
                    <span>Settings</span>
                </a>
            </div>
            <div class="sidebar-item">
                <a href="/dashboard/logout">
                    <i class="fa fa-sign-out"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="dashboard-header">
            <h2>Welcome back, <?php echo htmlspecialchars($user['username']); ?>!</h2>
            <p>Your personalized dashboard</p>
        </div>

        <div class="quick-actions">
            <div class="action-card">
                <i class="fa fa-shopping-cart"></i>
                <h3>Place Order</h3>
                <p>Quickly place a new order</p>
                <a href="/services" class="btn btn-primary">View Services</a>
            </div>
            <div class="action-card">
                <i class="fa fa-credit-card"></i>
                <h3>Make Payment</h3>
                <p>Process payments here</p>
                <a href="/dashboard/payments" class="btn btn-primary">Make Payment</a>
            </div>
            <div class="action-card">
                <i class="fa fa-file-text-o"></i>
                <h3>Order History</h3>
                <p>Check your order history</p>
                <a href="/dashboard/orders" class="btn btn-primary">View Orders</a>
            </div>
            <div class="action-card">
                <i class="fa fa-envelope"></i>
                <h3>Send Message</h3>
                <p>Contact support</p>
                <a href="/contact" class="btn btn-primary">Contact Us</a>
            </div>
        </div>

        <div class="dashboard-sections">
            <div class="section recent-orders">
                <h3>Recent Orders</h3>
                <div class="orders-list">
                    <?php foreach ($recentOrders as $order): ?>
                    <div class="order-item">
                        <div class="order-info">
                            <h4><?php echo htmlspecialchars($order['service_name']); ?></h4>
                            <p>Order #<?php echo htmlspecialchars($order['id']); ?></p>
                            <p>Status: <span class="status-<?php echo htmlspecialchars($order['status']); ?>">
                                <?php echo htmlspecialchars($order['status']); ?></span></p>
                        </div>
                        <div class="order-actions">
                            <a href="/dashboard/orders/view/<?php echo htmlspecialchars($order['id']); ?>" class="btn btn-sm btn-primary">View Details</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="section recent-payments">
                <h3>Recent Payments</h3>
                <div class="payments-list">
                    <?php foreach ($recentPayments as $payment): ?>
                    <div class="payment-item">
                        <div class="payment-info">
                            <h4><?php echo htmlspecialchars($payment['service_name']); ?></h4>
                            <p>Payment #<?php echo htmlspecialchars($payment['id']); ?></p>
                            <p>Amount: $<?php echo htmlspecialchars($payment['amount']); ?></p>
                            <p>Status: <span class="status-<?php echo htmlspecialchars($payment['status']); ?>">
                                <?php echo htmlspecialchars($payment['status']); ?></span></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="section featured-services">
                <h3>Featured Services</h3>
                <div class="services-grid">
                    <?php foreach ($recentServices as $service): ?>
                    <div class="service-card">
                        <h4><?php echo htmlspecialchars($service['name']); ?></h4>
                        <p><?php echo htmlspecialchars($service['description']); ?></p>
                        <p class="price">$<?php echo htmlspecialchars($service['price']); ?></p>
                        <a href="/services/<?php echo htmlspecialchars($service['id']); ?>" class="btn btn-primary">Order Now</a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Purchased Templates Section -->
            <div class="section purchased-templates">
                <h3>My Purchased Templates</h3>
                <?php if (empty($purchasedTemplates)): ?>
                    <div class="alert alert-info">
                        You haven't purchased any website templates yet. 
                        <a href="/web-dev/templates">Browse our templates</a> to get started!
                    </div>
                <?php else: ?>
                    <div class="templates-grid">
                        <?php foreach ($purchasedTemplates as $template): ?>
                        <div class="template-card">
                            <div class="template-preview">
                                <img src="/public/images/<?php echo htmlspecialchars($template['preview_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($template['name']); ?>">
                            </div>
                            <div class="template-info">
                                <h4><?php echo htmlspecialchars($template['name']); ?></h4>
                                <p class="purchase-date">Purchased: <?php echo date('M d, Y', strtotime($template['purchase_date'])); ?></p>
                                <div class="template-actions">
                                    <a href="/web-dev/template/<?php echo $template['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                    <a href="/web-dev/template/<?php echo $template['id']; ?>/download" class="btn btn-sm btn-success">Download</a>
                                    <a href="<?php echo htmlspecialchars($template['demo_url']); ?>" target="_blank" class="btn btn-sm btn-secondary">Live Demo</a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-3">
                        <a href="/web-dev/templates/purchased" class="btn btn-outline-primary">View All Purchased Templates</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.dashboard-container {
    display: flex;
    min-height: 100vh;
}

.sidebar {
    width: 250px;
    background: #2c3e50;
    color: white;
    padding: 20px;
}

.sidebar-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 30px;
}

.sidebar-logo {
    width: 40px;
    height: 40px;
}

.sidebar-menu {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.sidebar-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.sidebar-item:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.sidebar-item.active {
    background-color: rgba(255, 255, 255, 0.1);
}

.sidebar-item i {
    width: 20px;
}

.main-content {
    flex: 1;
    padding: 20px;
}

.dashboard-header {
    margin-bottom: 30px;
}

.quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.action-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: transform 0.3s;
}

.action-card:hover {
    transform: translateY(-5px);
}

.action-card i {
    font-size: 2em;
    color: #3498db;
    margin-bottom: 15px;
}

.dashboard-sections {
    display: grid;
    gap: 20px;
}

.section {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.orders-list, .payments-list {
    margin-top: 20px;
}

.order-item, .payment-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.status-pending { color: #f1c40f; }
.status-completed { color: #2ecc71; }
.status-cancelled { color: #e74c3c; }

.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.service-card {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    transition: transform 0.3s;
}

.service-card:hover {
    transform: translateY(-5px);
}

.service-card .price {
    color: #e74c3c;
    font-weight: bold;
    font-size: 1.2em;
    margin: 10px 0;
}

.btn {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 5px;
    text-decoration: none;
    color: white;
    background: #3498db;
    transition: background-color 0.3s;
}

.btn:hover {
    background: #2980b9;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 0.9em;
}
</style>

<?php include_once APP_PATH . '/views/layouts/footer.php'; ?>
