<?php
/**
 * Payment options page template
 * 
 * Displays payment options for an order and allows customer to choose a payment method
 */

// Include header
require_once __DIR__ . '/../partials/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Payment Options</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($testMode) && $testMode): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Test mode is active. No real payments will be processed.
                        </div>
                    <?php endif; ?>
                    
                    <h5>Order Summary</h5>
                    <div class="table-responsive mb-4">
                        <table class="table">
                            <tr>
                                <th>Order Number:</th>
                                <td><?= htmlspecialchars($order['order_number'] ?? $order['id']) ?></td>
                            </tr>
                            <tr>
                                <th>Date:</th>
                                <td><?= htmlspecialchars($order['created_at'] ?? date('Y-m-d H:i')) ?></td>
                            </tr>
                            <tr>
                                <th>Total Amount:</th>
                                <td><?= htmlspecialchars(number_format($order['total'], 2)) ?> <?= htmlspecialchars($order['currency'] ?? 'GBP') ?></td>
                            </tr>
                        </table>
                    </div>
                    
                    <?php if (empty($paymentMethods)): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> No payment methods are currently available. Please contact support.
                        </div>
                    <?php else: ?>
                        <h5>Choose Payment Method</h5>
                        <form id="payment-form" action="/payment/process" method="post">
                            <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id']) ?>">
                            
                            <div class="mb-4">
                                <?php foreach ($paymentMethods as $method): ?>
                                    <div class="form-check custom-radio mb-2">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="payment_<?= htmlspecialchars($method['id']) ?>" 
                                               value="<?= htmlspecialchars($method['id']) ?>"
                                               <?= ($method['id'] === 'paypal') ? 'checked' : '' ?>>
                                        <label class="form-check-label d-flex align-items-center" for="payment_<?= htmlspecialchars($method['id']) ?>">
                                            <?php if ($method['id'] === 'paypal'): ?>
                                                <img src="/assets/img/paypal-logo.png" alt="PayPal" height="30" class="me-2">
                                            <?php elseif ($method['id'] === 'stripe'): ?>
                                                <img src="/assets/img/stripe-logo.png" alt="Stripe" height="30" class="me-2">
                                            <?php endif; ?>
                                            <?= htmlspecialchars($method['name']) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" id="submit-button" class="btn btn-primary btn-lg">
                                    <i class="fas fa-lock me-2"></i> Proceed to Payment
                                </button>
                                <a href="/orders" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Orders
                                </a>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment processing script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');
    
    if (form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Disable submit button to prevent double submission
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';
            
            // Get form data
            const formData = new FormData(form);
            
            // Send AJAX request
            fetch('/payment/process', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // If payment requires redirect (PayPal, Stripe Checkout)
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } 
                    // For direct payment methods that don't require redirect
                    else {
                        window.location.href = '/payment/success?order_id=' + formData.get('order_id');
                    }
                } else {
                    // Show error message
                    alert('Payment Error: ' + (data.message || 'An unknown error occurred'));
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fas fa-lock me-2"></i> Proceed to Payment';
                }
            })
            .catch(error => {
                console.error('Payment error:', error);
                alert('Payment Error: Could not process your request. Please try again.');
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-lock me-2"></i> Proceed to Payment';
            });
        });
    }
});
</script>

<?php
// Include footer
require_once __DIR__ . '/../partials/footer.php';
?>
