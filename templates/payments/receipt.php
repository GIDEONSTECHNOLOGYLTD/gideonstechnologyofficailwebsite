<?php
/**
 * Payment receipt page template
 * 
 * Displays successful payment confirmation and order details
 */

// Include header
require_once __DIR__ . '/../partials/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm border-success">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-check-circle me-2"></i> Payment Successful</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h4>Thank you for your payment!</h4>
                        <p class="lead">Your order has been successfully processed.</p>
                    </div>
                    
                    <div class="alert alert-light border">
                        <h5>Order Details</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tr>
                                    <th>Order Number:</th>
                                    <td><?= htmlspecialchars($order['order_number'] ?? $order['id']) ?></td>
                                </tr>
                                <tr>
                                    <th>Date:</th>
                                    <td><?= htmlspecialchars($order['created_at'] ?? date('Y-m-d H:i')) ?></td>
                                </tr>
                                <tr>
                                    <th>Amount Paid:</th>
                                    <td><?= htmlspecialchars(number_format($order['total'], 2)) ?> <?= htmlspecialchars($order['currency'] ?? 'GBP') ?></td>
                                </tr>
                                <tr>
                                    <th>Payment Method:</th>
                                    <td><?= htmlspecialchars(ucfirst($order['payment_method'] ?? $transaction['payment_method'] ?? 'Online Payment')) ?></td>
                                </tr>
                                <tr>
                                    <th>Transaction ID:</th>
                                    <td><?= htmlspecialchars($transaction['transaction_id'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td><span class="badge bg-success">Paid</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h5>What Happens Next?</h5>
                        <p>
                            We've sent a confirmation email to your registered email address. 
                            If you have any questions about your order, please contact our support team.
                        </p>
                        
                        <div class="d-flex justify-content-center mt-4">
                            <a href="/orders" class="btn btn-primary me-2">
                                <i class="fas fa-clipboard-list me-1"></i> View My Orders
                            </a>
                            <a href="/" class="btn btn-outline-secondary">
                                <i class="fas fa-home me-1"></i> Return to Homepage
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
require_once __DIR__ . '/../partials/footer.php';
?>
