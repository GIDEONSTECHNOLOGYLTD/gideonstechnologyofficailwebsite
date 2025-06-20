<?php require_once APP_PATH . '/views/layouts/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card">
                <div class="card-body">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 64px;"></i>
                    </div>
                    <h2 class="card-title mb-4">Payment Successful!</h2>
                    <p class="card-text mb-4">Thank you for your payment. Your order has been confirmed.</p>
                    
                    <div class="payment-details mb-4">
                        <h5>Payment Details</h5>
                        <p><strong>Order ID:</strong> #<?= htmlspecialchars($payment['order_id']) ?></p>
                        <p><strong>Amount:</strong> <?= htmlspecialchars(number_format($payment['amount'], 2)) ?></p>
                        <p><strong>Payment Method:</strong> <?= htmlspecialchars(ucfirst($payment['payment_method'])) ?></p>
                        <p><strong>Transaction Reference:</strong> <?= htmlspecialchars($payment['transaction_reference']) ?></p>
                    </div>

                    <div class="text-center">
                        <a href="/orders/<?= htmlspecialchars($payment['order_id']) ?>" class="btn btn-primary me-2">View Order</a>
                        <a href="/dashboard" class="btn btn-outline-primary">Go to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
