<?php require APP_PATH . '/views/layouts/header.php'; ?>

<div class="payment-success-container">
    <div class="container">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="success-card">
                    <div class="success-icon">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <h2>Payment Successful!</h2>
                    <p>Your order has been processed successfully.</p>

                    <div class="order-details">
                        <h3>Order Details</h3>
                        <div class="detail-item">
                            <strong>Order Number:</strong> <?php echo htmlspecialchars($payment['order_id']); ?>
                        </div>
                        <div class="detail-item">
                            <strong>Payment ID:</strong> <?php echo htmlspecialchars($payment['id']); ?>
                        </div>
                        <div class="detail-item">
                            <strong>Amount:</strong> $<?php echo htmlspecialchars($payment['amount']); ?>
                        </div>
                        <div class="detail-item">
                            <strong>Payment Method:</strong> <?php echo htmlspecialchars($payment['payment_method']); ?>
                        </div>
                        <div class="detail-item">
                            <strong>Status:</strong> <span class="status-success">Completed</span>
                        </div>
                    </div>

                    <div class="next-steps">
                        <h3>Next Steps</h3>
                        <ul>
                            <li>We'll start working on your order immediately</li>
                            <li>You'll receive updates via email</li>
                            <li>Check your dashboard for order status</li>
                        </ul>
                    </div>

                    <div class="action-buttons">
                        <a href="/dashboard/orders" class="btn btn-primary">View Order</a>
                        <a href="/" class="btn btn-secondary">Go Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.payment-success-container {
    padding: 80px 0;
}

.success-card {
    background: white;
    border-radius: 10px;
    padding: 40px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.success-icon {
    font-size: 6em;
    color: #2ecc71;
    margin-bottom: 30px;
}

.success-card h2 {
    color: #2c3e50;
    margin-bottom: 20px;
}

.order-details {
    margin: 40px 0;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
}

.detail-item {
    margin: 15px 0;
    font-size: 1.1em;
}

.detail-item strong {
    color: #3498db;
}

.status-success {
    color: #2ecc71;
    font-weight: bold;
}

.next-steps {
    margin: 40px 0;
}

.next-steps ul {
    list-style: none;
    padding: 0;
}

.next-steps li {
    margin: 10px 0;
    padding-left: 20px;
    position: relative;
}

.next-steps li:before {
    content: "•";
    color: #2ecc71;
    position: absolute;
    left: 0;
}

.action-buttons {
    margin-top: 40px;
}

.action-buttons .btn {
    margin: 0 10px;
    padding: 12px 30px;
    font-size: 1.1em;
}

@media (max-width: 768px) {
    .payment-success-container {
        padding: 40px 0;
    }
    
    .success-card {
        padding: 20px;
    }
    
    .action-buttons .btn {
        width: 100%;
        margin: 10px 0;
    }
}
</style>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
