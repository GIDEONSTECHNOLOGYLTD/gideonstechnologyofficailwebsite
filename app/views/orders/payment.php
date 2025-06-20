<?php require APP_PATH . '/views/layouts/header.php'; ?>

<div class="payment-container">
    <div class="container">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="payment-card">
                    <div class="payment-header">
                        <h2>Complete Payment</h2>
                        <p>Order #<?php echo htmlspecialchars($order['id']); ?></p>
                    </div>

                    <div class="payment-details">
                        <div class="service-info">
                            <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                            <p class="service-description"><?php echo htmlspecialchars($service['description']); ?></p>
                            <div class="price-duration">
                                <span class="price">$<?php echo htmlspecialchars($service['price']); ?></span>
                                <span class="duration"><?php echo htmlspecialchars($service['duration']); ?> hours</span>
                            </div>
                        </div>

                        <div class="payment-methods">
                            <h3>Choose Payment Method</h3>
                            <form action="/payment/process" method="POST" id="paymentForm">
                                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
                                <input type="hidden" name="amount" value="<?php echo htmlspecialchars($service['price']); ?>">

                                <div class="payment-option">
                                    <input type="radio" name="payment_method" value="credit_card" id="creditCard" required>
                                    <label for="creditCard">
                                        <i class="fa fa-credit-card"></i>
                                        Credit Card
                                    </label>
                                </div>

                                <div class="payment-option">
                                    <input type="radio" name="payment_method" value="bank_transfer" id="bankTransfer">
                                    <label for="bankTransfer">
                                        <i class="fa fa-bank"></i>
                                        Bank Transfer
                                    </label>
                                </div>

                                <div class="payment-option">
                                    <input type="radio" name="payment_method" value="mobile_money" id="mobileMoney">
                                    <label for="mobileMoney">
                                        <i class="fa fa-mobile"></i>
                                        Mobile Money
                                    </label>
                                </div>

                                <div class="payment-form" id="creditCardForm" style="display: none;">
                                    <div class="form-group">
                                        <label for="cardNumber">Card Number</label>
                                        <input type="text" class="form-control" id="cardNumber" name="card_number" required>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="expiryDate">Expiry Date</label>
                                            <input type="text" class="form-control" id="expiryDate" name="expiry_date" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="cvv">CVV</label>
                                            <input type="text" class="form-control" id="cvv" name="cvv" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="payment-form" id="bankTransferForm" style="display: none;">
                                    <div class="form-group">
                                        <label>Bank Details</label>
                                        <p>Bank Name: Gideons Bank</p>
                                        <p>Account Number: 1234567890</p>
                                        <p>Reference: <?php echo htmlspecialchars($order['id']); ?></p>
                                    </div>
                                </div>

                                <div class="payment-form" id="mobileMoneyForm" style="display: none;">
                                    <div class="form-group">
                                        <label>Mobile Money Details</label>
                                        <p>Network: MTN Mobile Money</p>
                                        <p>Number: +233 555 123 456</p>
                                        <p>Reference: <?php echo htmlspecialchars($order['id']); ?></p>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Complete Payment</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.payment-container {
    padding: 40px 0;
}

.payment-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 30px;
}

.payment-header {
    text-align: center;
    margin-bottom: 30px;
}

.payment-header h2 {
    margin-bottom: 10px;
}

.payment-details {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
}

.service-info {
    margin-bottom: 30px;
}

.service-info h3 {
    color: #2c3e50;
    margin-bottom: 10px;
}

.service-description {
    color: #666;
    margin-bottom: 20px;
}

.price-duration {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
}

.price {
    color: #e74c3c;
    font-weight: bold;
    font-size: 1.2em;
}

.duration {
    color: #666;
}

.payment-methods {
    margin-top: 30px;
}

.payment-option {
    margin-bottom: 20px;
}

.payment-option label {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s;
}

.payment-option input[type="radio"] {
    display: none;
}

.payment-option input[type="radio"]:checked + label {
    background: #3498db;
    color: white;
    border-color: #3498db;
}

.payment-option i {
    font-size: 1.2em;
}

.payment-form {
    margin-top: 20px;
    padding: 20px;
    background: white;
    border-radius: 5px;
}

.form-group {
    margin-bottom: 20px;
}

.form-row {
    display: flex;
    gap: 20px;
}

@media (max-width: 768px) {
    .payment-container {
        padding: 20px;
    }
    
    .payment-card {
        padding: 20px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle payment method selection
    const paymentOptions = document.querySelectorAll('.payment-option input[type="radio"]');
    const paymentForms = document.querySelectorAll('.payment-form');

    paymentOptions.forEach(option => {
        option.addEventListener('change', function() {
            paymentForms.forEach(form => form.style.display = 'none');
            
            if (this.value === 'credit_card') {
                document.getElementById('creditCardForm').style.display = 'block';
            } else if (this.value === 'bank_transfer') {
                document.getElementById('bankTransferForm').style.display = 'block';
            } else if (this.value === 'mobile_money') {
                document.getElementById('mobileMoneyForm').style.display = 'block';
            }
        });
    });

    // Form validation
    const paymentForm = document.getElementById('paymentForm');
    paymentForm.addEventListener('submit', function(e) {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
        if (!selectedMethod) {
            e.preventDefault();
            alert('Please select a payment method');
            return;
        }

        if (selectedMethod.value === 'credit_card') {
            const cardNumber = document.getElementById('cardNumber').value;
            const expiryDate = document.getElementById('expiryDate').value;
            const cvv = document.getElementById('cvv').value;

            if (!cardNumber || !expiryDate || !cvv) {
                e.preventDefault();
                alert('Please fill in all credit card details');
                return;
            }
        }
    });
});
</script>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
