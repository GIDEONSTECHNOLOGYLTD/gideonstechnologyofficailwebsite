<?php
/**
 * Payment cancelled page template
 * 
 * Displayed when a user cancels a payment
 */

// Include header
require_once __DIR__ . '/../partials/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm border-warning">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="fas fa-exclamation-circle me-2"></i> Payment Cancelled</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="fas fa-times-circle text-warning" style="font-size: 4rem;"></i>
                        </div>
                        <h4>Your payment has been cancelled</h4>
                        <p class="lead">No charges have been applied to your account.</p>
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
                                    <th>Amount:</th>
                                    <td><?= htmlspecialchars(number_format($order['total'], 2)) ?> <?= htmlspecialchars($order['currency'] ?? 'GBP') ?></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td><span class="badge bg-warning text-dark">Cancelled</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mt-4 text-center">
                        <p>Would you like to try another payment method?</p>
                        
                        <div class="d-flex justify-content-center gap-2 mt-3">
                            <a href="/payment/<?= htmlspecialchars($order['id']) ?>" class="btn btn-primary">
                                <i class="fas fa-credit-card me-1"></i> Try Payment Again
                            </a>
                            <a href="/orders" class="btn btn-outline-secondary">
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
