<?php
/**
 * Payment failed page template
 * 
 * Displayed when a payment fails to process
 */

// Include header
require_once __DIR__ . '/../partials/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm border-danger">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i> Payment Failed</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="fas fa-times-circle text-danger" style="font-size: 4rem;"></i>
                        </div>
                        <h4>Your payment could not be processed</h4>
                        <p class="lead">No charges have been applied to your account.</p>
                    </div>
                    
                    <?php if (isset($error) && $error): ?>
                        <div class="alert alert-danger">
                            <strong>Error details:</strong> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    
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
                                    <td><span class="badge bg-danger">Failed</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mt-4 text-center">
                        <h5>Common Reasons for Payment Failure</h5>
                        <ul class="list-group list-group-flush mb-4">
                            <li class="list-group-item">Insufficient funds in account</li>
                            <li class="list-group-item">Incorrect card details entered</li>
                            <li class="list-group-item">Card expired or not activated for online purchases</li>
                            <li class="list-group-item">Bank declined the transaction</li>
                        </ul>
                        
                        <p>Would you like to try again with another payment method?</p>
                        
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
