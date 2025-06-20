<?php require APP_PATH . '/views/layouts/header.php'; ?>

<div class="checkout-container">
    <div class="container">
        <div class="checkout-header">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/gstore">Shopping Cart</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Checkout</li>
                </ol>
            </nav>
            <h1>Checkout</h1>
        </div>

        <div class="checkout-content">
            <div class="row">
                <div class="col-md-8">
                    <div class="checkout-form">
                        <h2>Shipping Information</h2>
                        <form id="checkoutForm" onsubmit="submitCheckout(event)">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="firstName" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="firstName" name="firstName" 
                                           value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lastName" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lastName" name="lastName" 
                                           value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" required>
                            </div>

                            <div class="mb-3">
                                <label for="address1" class="form-label">Address Line 1</label>
                                <input type="text" class="form-control" id="address1" name="address1" required>
                            </div>

                            <div class="mb-3">
                                <label for="address2" class="form-label">Address Line 2</label>
                                <input type="text" class="form-control" id="address2" name="address2">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city" name="city" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="state" class="form-label">State/Province</label>
                                    <input type="text" class="form-control" id="state" name="state" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="zip" class="form-label">ZIP/Postal Code</label>
                                    <input type="text" class="form-control" id="zip" name="zip" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="country" class="form-label">Country</label>
                                    <select class="form-select" id="country" name="country" required>
                                        <option value="">Select Country</option>
                                        <?php foreach ($countries as $country): ?>
                                        <option value="<?php echo htmlspecialchars($country['code']); ?>">
                                            <?php echo htmlspecialchars($country['name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="saveAddress" name="saveAddress">
                                    <label class="form-check-label" for="saveAddress">
                                        Save this address for future orders
                                    </label>
                                </div>
                            </div>

                            <h2>Payment Method</h2>
                            <div class="payment-methods">
                                <div class="payment-option">
                                    <input type="radio" class="btn-check" name="paymentMethod" id="creditCard" 
                                           value="credit_card" checked>
                                    <label class="btn btn-outline-primary" for="creditCard">
                                        <i class="fa fa-credit-card"></i> Credit Card
                                    </label>
                                </div>
                                <div class="payment-option">
                                    <input type="radio" class="btn-check" name="paymentMethod" id="paypal" 
                                           value="paypal">
                                    <label class="btn btn-outline-primary" for="paypal">
                                        <i class="fa fa-paypal"></i> PayPal
                                    </label>
                                </div>
                                <div class="payment-option">
                                    <input type="radio" class="btn-check" name="paymentMethod" id="bankTransfer" 
                                           value="bank_transfer">
                                    <label class="btn btn-outline-primary" for="bankTransfer">
                                        <i class="fa fa-university"></i> Bank Transfer
                                    </label>
                                </div>
                            </div>

                            <div id="creditCardDetails" class="mt-3">
                                <div class="mb-3">
                                    <label for="cardNumber" class="form-label">Card Number</label>
                                    <input type="text" class="form-control" id="cardNumber" name="cardNumber" 
                                           pattern="\d{16}" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="expiryDate" class="form-label">Expiry Date</label>
                                        <input type="text" class="form-control" id="expiryDate" name="expiryDate" 
                                               placeholder="MM/YY" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="cvv" class="form-label">CVV</label>
                                        <input type="text" class="form-control" id="cvv" name="cvv" 
                                               pattern="\d{3}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fa fa-credit-card"></i> Complete Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="order-summary">
                        <h2>Order Summary</h2>
                        <div class="summary-items">
                            <?php foreach ($cartItems as $item): ?>
                            <div class="summary-item">
                                <div class="item-details">
                                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <p class="item-quantity">
                                        <?php echo htmlspecialchars($item['quantity']); ?> × 
                                        $<?php echo number_format($item['price'], 2); ?>
                                    </p>
                                </div>
                                <div class="item-total">
                                    $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="summary-totals">
                            <div class="summary-total">
                                <span>Subtotal</span>
                                <span>$<?php echo number_format($subtotal, 2); ?></span>
                            </div>
                            <div class="summary-total">
                                <span>Shipping</span>
                                <span>$<?php echo number_format($shipping, 2); ?></span>
                            </div>
                            <div class="summary-total discount" <?php echo $discount ? '' : 'style="display: none;"'; ?>>
                                <span>Discount</span>
                                <span>-$<?php echo number_format($discount, 2); ?></span>
                            </div>
                            <div class="summary-total grand-total">
                                <span>Grand Total</span>
                                <span>$<?php echo number_format($total, 2); ?></span>
                            </div>
                        </div>

                        <div class="shipping-options">
                            <h3>Shipping Options</h3>
                            <div class="shipping-option">
                                <input type="radio" class="btn-check" name="shippingMethod" id="standardShipping" 
                                       value="standard" checked>
                                <label class="btn btn-outline-primary" for="standardShipping">
                                    Standard Shipping - $<?php echo number_format($standardShipping, 2); ?>
                                </label>
                            </div>
                            <div class="shipping-option">
                                <input type="radio" class="btn-check" name="shippingMethod" id="expressShipping" 
                                       value="express">
                                <label class="btn btn-outline-primary" for="expressShipping">
                                    Express Shipping - $<?php echo number_format($expressShipping, 2); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.checkout-container {
    padding: 40px 0;
}

.checkout-header {
    margin-bottom: 40px;
}

.checkout-header h1 {
    color: #2c3e50;
    margin-bottom: 20px;
}

.checkout-content {
    background: white;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.order-summary {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.summary-item {
    display: flex;
    justify-content: space-between;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
}

.summary-item:last-child {
    border-bottom: none;
}

.summary-totals {
    margin-top: 20px;
}

.summary-total {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    color: #666;
}

.summary-total.grand-total {
    font-weight: bold;
    color: #2c3e50;
    margin-top: 20px;
}

.summary-total.grand-total span:last-child {
    color: #e74c3c;
}

.shipping-options {
    margin-top: 20px;
}

.shipping-option {
    margin-bottom: 10px;
}

.payment-methods {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.payment-option {
    flex: 1;
}

.payment-option label {
    width: 100%;
    text-align: center;
}

#creditCardDetails {
    display: none;
}

@media (max-width: 768px) {
    .checkout-content {
        padding: 20px;
    }
    
    .order-summary {
        margin-top: 20px;
    }
    
    .payment-methods {
        flex-direction: column;
    }
    
    .payment-option {
        width: 100%;
    }
}
</style>

<script>
// Show/hide credit card details based on payment method
const paymentMethods = document.querySelectorAll('input[name="paymentMethod"]');
const creditCardDetails = document.getElementById('creditCardDetails');

paymentMethods.forEach(radio => {
    radio.addEventListener('change', () => {
        if (radio.value === 'credit_card') {
            creditCardDetails.style.display = 'block';
        } else {
            creditCardDetails.style.display = 'none';
        }
    });
});

function submitCheckout(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    
    fetch('/gstore/process-order', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '/gstore/orders';
        } else {
            alert(data.message || 'Error processing order');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error processing order');
    });
}

// Add validation for credit card details
const cardNumber = document.getElementById('cardNumber');
const expiryDate = document.getElementById('expiryDate');
const cvv = document.getElementById('cvv');

if (cardNumber) {
    cardNumber.addEventListener('input', (e) => {
        e.target.value = e.target.value.replace(/\D/g, '').replace(/(\d{4})/g, '$1 ').trim();
    });
}

if (expiryDate) {
    expiryDate.addEventListener('input', (e) => {
        e.target.value = e.target.value.replace(/\D/g, '').replace(/(\d{2})/g, '$1/');
    });
}

if (cvv) {
    cvv.addEventListener('input', (e) => {
        e.target.value = e.target.value.replace(/\D/g, '');
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const checkoutForm = document.getElementById('checkoutForm');
    
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Basic validation
            if (!validateCheckoutForm()) {
                return;
            }
            
            const formData = new FormData(event.target);
            
            // Remove sensitive card data before sending to server
            // In a production environment, you should use a secure payment processor
            // that handles the card data directly (e.g., Stripe, PayPal, etc.)
            const sanitizedFormData = new FormData();
            for (let [key, value] of formData.entries()) {
                if (!['cardNumber', 'cvv'].includes(key)) {
                    sanitizedFormData.append(key, value);
                }
            }
            
            // Add a tokenized or masked version for backend processing
            sanitizedFormData.append('cardNumberMasked', maskCardNumber(formData.get('cardNumber')));
            
            fetch('/gstore/process-order', {
                method: 'POST',
                body: sanitizedFormData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '/gstore/order-confirmation?id=' + data.orderId;
                } else {
                    showError(data.message || 'Error processing order');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Error processing order. Please try again later.');
            });
        });
    }

    function maskCardNumber(cardNumber) {
        // Remove any spaces or non-digits
        const cleaned = cardNumber.replace(/\D/g, '');
        // Return last 4 digits with masked prefix
        return 'xxxx-xxxx-xxxx-' + cleaned.slice(-4);
    }

    function validateCheckoutForm() {
        const cardNumber = document.getElementById('cardNumber').value.replace(/\s/g, '');
        const expiryDate = document.getElementById('expiryDate').value;
        const cvv = document.getElementById('cvv').value;
        const firstName = document.getElementById('firstName').value;
        const lastName = document.getElementById('lastName').value;
        const email = document.getElementById('email').value;
        const address1 = document.getElementById('address1').value;
        const city = document.getElementById('city').value;
        const state = document.getElementById('state').value;
        const zip = document.getElementById('zip').value;
        
        let isValid = true;
        
        // Validate card number (using Luhn algorithm)
        if (!isValidCreditCard(cardNumber)) {
            showFieldError('cardNumber', 'Please enter a valid card number');
            isValid = false;
        } else {
            clearFieldError('cardNumber');
        }
        
        // Validate expiry date (MM/YY format)
        const expiryPattern = /^(0[1-9]|1[0-2])\/([0-9]{2})$/;
        if (!expiryPattern.test(expiryDate)) {
            showFieldError('expiryDate', 'Please enter a valid date (MM/YY)');
            isValid = false;
        } else {
            // Check if card is expired
            const [month, year] = expiryDate.split('/');
            const expiry = new Date(2000 + parseInt(year), parseInt(month) - 1, 1);
            const today = new Date();
            if (expiry < today) {
                showFieldError('expiryDate', 'Your card has expired');
                isValid = false;
            } else {
                clearFieldError('expiryDate');
            }
        }
        
        // Validate CVV (3-4 digits)
        if (!/^\d{3,4}$/.test(cvv)) {
            showFieldError('cvv', 'Please enter a valid CVV code');
            isValid = false;
        } else {
            clearFieldError('cvv');
        }
        
        // Validate email
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showFieldError('email', 'Please enter a valid email address');
            isValid = false;
        } else {
            clearFieldError('email');
        }
        
        // Validate other required fields
        const requiredFields = [
            { id: 'firstName', message: 'First name is required' },
            { id: 'lastName', message: 'Last name is required' },
            { id: 'address1', message: 'Address is required' },
            { id: 'city', message: 'City is required' },
            { id: 'state', message: 'State/Province is required' },
            { id: 'zip', message: 'ZIP/Postal Code is required' }
        ];
        
        requiredFields.forEach(field => {
            const value = document.getElementById(field.id).value.trim();
            if (!value) {
                showFieldError(field.id, field.message);
                isValid = false;
            } else {
                clearFieldError(field.id);
            }
        });
        
        return isValid;
    }

    function isValidCreditCard(cardNumber) {
        // Implementation of Luhn algorithm for credit card validation
        if (!cardNumber || !/^\d{13,19}$/.test(cardNumber)) {
            return false;
        }
        
        let sum = 0;
        let double = false;
        
        for (let i = cardNumber.length - 1; i >= 0; i--) {
            let digit = parseInt(cardNumber.charAt(i));
            
            if (double) {
                digit *= 2;
                if (digit > 9) digit -= 9;
            }
            
            sum += digit;
            double = !double;
        }
        
        return sum % 10 === 0;
    }
    
    function showFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        field.classList.add('is-invalid');
        
        const errorDiv = document.getElementById(fieldId + 'Error') || document.createElement('div');
        errorDiv.id = fieldId + 'Error';
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        
        if (!document.getElementById(fieldId + 'Error')) {
            field.parentNode.appendChild(errorDiv);
        }
    }
    
    function clearFieldError(fieldId) {
        const field = document.getElementById(fieldId);
        field.classList.remove('is-invalid');
        
        const errorDiv = document.getElementById(fieldId + 'Error');
        if (errorDiv) {
            errorDiv.remove();
        }
    }
    
    function showError(message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger mt-3';
        alertDiv.textContent = message;
        
        const form = document.getElementById('checkoutForm');
        form.parentNode.insertBefore(alertDiv, form);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    // Add validation for credit card details
    const cardNumber = document.getElementById('cardNumber');
    const expiryDate = document.getElementById('expiryDate');
    const cvv = document.getElementById('cvv');

    if (cardNumber) {
        cardNumber.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/\D/g, '')
                .replace(/(.{4})/g, '$1 ')
                .trim();
        });
    }

    if (expiryDate) {
        expiryDate.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0) {
                if (value.length > 2) {
                    value = value.slice(0, 2) + '/' + value.slice(2, 4);
                }
                // Prevent typing more than MM/YY
                if (value.length > 5) {
                    value = value.slice(0, 5);
                }
            }
            e.target.value = value;
        });
    }

    if (cvv) {
        cvv.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/\D/g, '').slice(0, 4);
        });
    }
});
</script>

<?php require APP_PATH . '/views/layouts/footer.php'; ?>
