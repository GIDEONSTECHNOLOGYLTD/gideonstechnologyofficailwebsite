<?php include_once APP_PATH . '/views/layouts/header.php'; ?>

<div class="dashboard-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../assets/icon.png" alt="Logo" class="sidebar-logo">
            <span>Gideons Tech</span>
        </div>
        <div class="sidebar-menu">
            <div class="sidebar-item">
                <a href="/dashboard">
                    <i class="fa fa-home"></i>
                    <span>Dashboard</span>
                </a>
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
            <div class="sidebar-item active">
                <a href="/dashboard/templates">
                    <i class="fa fa-file-code-o"></i>
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
            <h2>My Purchased Templates</h2>
            <p>Manage your website templates</p>
        </div>

        <?php if (empty($purchasedTemplates)): ?>
            <div class="empty-state">
                <i class="fa fa-file-code-o"></i>
                <h3>No Templates Yet</h3>
                <p>You haven't purchased any website templates yet.</p>
                <a href="/web-dev/templates" class="btn btn-primary">Browse Templates</a>
            </div>
        <?php else: ?>
            <div class="templates-grid">
                <?php foreach ($purchasedTemplates as $template): ?>
                <div class="template-card">
                    <div class="template-image">
                        <img src="<?php echo '/public/images/' . htmlspecialchars($template['preview_image']); ?>" alt="<?php echo htmlspecialchars($template['name']); ?>">
                    </div>
                    <div class="template-details">
                        <h3><?php echo htmlspecialchars($template['name']); ?></h3>
                        <p class="template-category"><?php echo htmlspecialchars($template['category']); ?></p>
                        <p class="template-description"><?php echo htmlspecialchars(substr($template['description'], 0, 100)); ?>...</p>
                        <div class="template-meta">
                            <span class="purchase-date">Purchased: <?php echo date('M d, Y', strtotime($template['purchase_date'])); ?></span>
                        </div>
                        <div class="template-actions">
                            <a href="/dashboard/template/<?php echo $template['id']; ?>" class="btn btn-primary">Manage</a>
                            <a href="/web-dev/template/<?php echo $template['id']; ?>/download" class="btn btn-secondary">Download</a>
                            <a href="<?php echo htmlspecialchars($template['demo_url']); ?>" class="btn btn-outline" target="_blank">View Demo</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.templates-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.template-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
}

.template-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.template-image img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}

.template-details {
    padding: 15px;
}

.template-category {
    color: #6c757d;
    font-size: 0.9em;
    margin-bottom: 10px;
}

.template-meta {
    display: flex;
    justify-content: space-between;
    margin: 15px 0;
    font-size: 0.9em;
    color: #6c757d;
}

.template-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.template-actions .btn {
    flex: 1;
    text-align: center;
    padding: 8px;
    font-size: 0.9em;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.empty-state i {
    font-size: 3em;
    color: #6c757d;
    margin-bottom: 20px;
}

.empty-state .btn {
    margin-top: 20px;
}
</style>

<?php include_once APP_PATH . '/views/layouts/footer.php'; ?>