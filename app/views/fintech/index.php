<?php require APP_PATH . '/views/layouts/header.php'; ?>

<div class="fintech-container">
    <div class="container">
        <div class="fintech-header">
            <h1>Financial Services</h1>
            <p>Secure and efficient financial solutions</p>
        </div>

        <div class="fintech-features">
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fa fa-credit-card"></i>
                        <h3>Wallet Management</h3>
                        <p>Manage your funds securely</p>
                        <a href="/fintech/wallet" class="btn btn-primary">View Wallet</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fa fa-exchange"></i>
                        <h3>Money Transfer</h3>
                        <p>Send and receive payments</p>
                        <a href="/fintech/transfer" class="btn btn-primary">Transfer</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fa fa-history"></i>
                        <h3>Transaction History</h3>
                        <p>Track all your transactions</p>
                        <a href="/fintech/transactions" class="btn btn-primary">View History</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="fintech-services">
            <h2>Our Financial Services</h2>
            <div class="row">
                <?php foreach ($services as $service): ?>
                <div class="col-md-4 mb-4">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fa <?php echo htmlspecialchars($service['icon']); ?>"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                        <p><?php echo htmlspecialchars($service['description']); ?></p>
                        <div class="service-actions">
                            <span class="price">$<?php echo htmlspecialchars($service['price']); ?></span>
                            <a href="/services/<?php echo htmlspecialchars($service['id']); ?>" class="btn btn-outline-primary">Learn More</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="featured-services">
            <h2>Featured Financial Services</h2>
            <div class="row">
                <?php foreach ($featured as $service): ?>
                <div class="col-md-4 mb-4">
                    <div class="featured-card">
                        <div class="featured-icon">
                            <i class="fa <?php echo htmlspecialchars($service['icon']); ?>"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                        <p><?php echo htmlspecialchars($service['description']); ?></p>
                        <div class="featured-actions">
                            <span class="price">$<?php echo htmlspecialchars($service['price']); ?></span>
                            <a href="/order/<?php echo htmlspecialchars($service['id']); ?>" class="btn btn-primary">Order Now</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
.fintech-container {
    padding: 40px 0;
}

.fintech-header {
    text-align: center;
    margin-bottom: 50px;
}

.fintech-header h1 {
    font-size: 2.5em;
    color: #2c3e50;
    margin-bottom: 15px;
}

.fintech-features {
    margin-bottom: 50px;
}

.feature-card {
    background: white;
    border-radius: 10px;
    padding: 30px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
}

.feature-card:hover {
    transform: translateY(-5px);
}

.feature-card i {
    font-size: 2.5em;
    color: #3498db;
    margin-bottom: 20px;
}

.fintech-services h2,
.featured-services h2 {
    text-align: center;
    margin-bottom: 40px;
    color: #2c3e50;
}

.service-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
}

.service-card:hover {
    transform: translateY(-5px);
}

.service-icon {
    text-align: center;
    margin-bottom: 20px;
}

.service-icon i {
    font-size: 2em;
    color: #3498db;
}

.service-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 20px;
}

.price {
    color: #e74c3c;
    font-weight: bold;
}

.featured-card {
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
    border-radius: 10px;
    padding: 30px;
    text-align: center;
    transition: transform 0.3s;
}

.featured-card:hover {
    transform: translateY(-5px);
}

.featured-icon {
    margin-bottom: 20px;
}

.featured-icon i {
    font-size: 2.5em;
}

.featured-actions {
    margin-top: 20px;
}

.featured-actions .btn {
    background: white;
    color: #3498db;
}

.featured-actions .btn:hover {
    background: #2980b9;
    color: white;
}

@media (max-width: 768px) {
    .fintech-container {
        padding: 20px 0;
    }
    
    .fintech-header h1 {
        font-size: 2em;
    }
    
    .feature-card {
        padding: 20px;
    }
}
</style>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
