<?php require_once APP_PATH . '/views/layouts/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payment for Order #<?= htmlspecialchars($order['id']) ?></h3>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5>Order Details</h5>
                        <p><strong>Amount:</strong> <?= htmlspecialchars(number_format($order['amount'], 2)) ?></p>
                        <p><strong>Status:</strong> <?= htmlspecialchars(ucfirst($order['status'])) ?></p>
                    </div>

                    <form action="/payment/process" method="POST">
                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id']) ?>">
                        
                        <div class="mb-4">
                            <h5>Select Payment Method</h5>
                            <div class="row">
                                <?php if (in_array('stripe', $paymentMethods)): ?>
                                <div class="col-md-4">
                                    <div class="form-check payment-method">
                                        <input class="form-check-input" type="radio" name="payment_method" id="stripe" value="stripe" checked>
                                        <label class="form-check-label" for="stripe">
                                            <img src="/assets/images/stripe.png" alt="Stripe" class="img-fluid">
                                            Credit Card
                                        </label>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (in_array('paypal', $paymentMethods)): ?>
                                <div class="col-md-4">
                                    <div class="form-check payment-method">
                                        <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal">
                                        <label class="form-check-label" for="paypal">
                                            <img src="/assets/images/paypal.png" alt="PayPal" class="img-fluid">
                                            PayPal
                                        </label>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (in_array('paystack', $paymentMethods)): ?>
                                <div class="col-md-4">
                                    <div class="form-check payment-method">
                                        <input class="form-check-input" type="radio" name="payment_method" id="paystack" value="paystack">
                                        <label class="form-check-label" for="paystack">
                                            <img src="/assets/images/paystack.png" alt="Paystack" class="img-fluid">
                                            Paystack
                                        </label>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg">Proceed to Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.payment-method {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.payment-method:hover {
    border-color: #007bff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.payment-method img {
    height: 40px;
    margin-bottom: 10px;
}

.payment-method input:checked + label {
    color: #007bff;
}

.payment-method:has(input:checked) {
    border-color: #007bff;
    background-color: #f8f9fa;
}
</style>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
