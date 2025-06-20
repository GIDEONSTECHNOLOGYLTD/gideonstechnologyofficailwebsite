<?php
/**
 * Admin Payment Configuration Template
 */

// Include admin header
require_once __DIR__ . '/../admin/partials/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Payment Settings</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Payment Settings</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-credit-card me-1"></i>
                Payment Gateway Configuration
            </div>
            <div>
                <span class="badge <?= isset($payment_test_mode) && $payment_test_mode ? 'bg-warning text-dark' : 'bg-success' ?>">
                    <?= isset($payment_test_mode) && $payment_test_mode ? 'Test Mode' : 'Live Mode' ?>
                </span>
            </div>
        </div>
        <div class="card-body">
            <form id="payment-config-form">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="payment_test_mode" name="payment_test_mode" <?= isset($payment_test_mode) && $payment_test_mode ? 'checked' : '' ?>>
                            <label class="form-check-label" for="payment_test_mode">Enable Test Mode</label>
                            <div class="form-text text-warning">
                                <i class="fas fa-info-circle"></i> In test mode, no real payments will be processed.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="currency" class="form-label">Default Currency</label>
                            <select class="form-select" id="currency" name="currency">
                                <?php foreach ($currencies as $code): ?>
                                <option value="<?= htmlspecialchars($code) ?>" <?= ($code === $currency) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($code) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <h5 class="mb-3">Payment Gateways</h5>
                
                <div class="row">
                    <!-- Paystack Gateway -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="paystack" id="paystack_enabled" 
                                           name="payment_gateway[]" <?= isset($paystack_enabled) && $paystack_enabled ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-bold" for="paystack_enabled">
                                        Paystack (Primary)
                                    </label>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="paystack_public_key" class="form-label">Public Key</label>
                                    <input type="text" class="form-control" id="paystack_public_key" name="paystack_public_key" 
                                           value="<?= htmlspecialchars($paystack_public_key ?? '') ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="paystack_secret_key" class="form-label">Secret Key</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="paystack_secret_key" name="paystack_secret_key"
                                               value="<?= htmlspecialchars($paystack_secret_key ?? '') ?>">
                                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="paystack_secret_key">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <button type="button" class="btn btn-sm btn-outline-primary test-gateway" data-gateway="paystack">
                                    <i class="fas fa-vial me-1"></i> Test Connection
                                </button>
                                <div class="mt-2 test-result" id="paystack-test-result"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- PayPal Gateway -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="paypal" id="paypal_enabled" 
                                           name="payment_gateway[]" <?= isset($paypal_enabled) && $paypal_enabled ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-bold" for="paypal_enabled">
                                        PayPal
                                    </label>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="paypal_client_id" class="form-label">Client ID</label>
                                    <input type="text" class="form-control" id="paypal_client_id" name="paypal_client_id" 
                                           value="<?= htmlspecialchars($paypal_client_id ?? '') ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="paypal_client_secret" class="form-label">Client Secret</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="paypal_client_secret" name="paypal_client_secret"
                                               value="<?= htmlspecialchars($paypal_client_secret ?? '') ?>">
                                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="paypal_client_secret">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <button type="button" class="btn btn-sm btn-outline-primary test-gateway" data-gateway="paypal">
                                    <i class="fas fa-vial me-1"></i> Test Connection
                                </button>
                                <div class="mt-2 test-result" id="paypal-test-result"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Stripe Gateway -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="stripe" id="stripe_enabled" 
                                           name="payment_gateway[]" <?= isset($stripe_enabled) && $stripe_enabled ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-bold" for="stripe_enabled">
                                        Stripe
                                    </label>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="stripe_publishable_key" class="form-label">Publishable Key</label>
                                    <input type="text" class="form-control" id="stripe_publishable_key" name="stripe_publishable_key" 
                                           value="<?= htmlspecialchars($stripe_publishable_key ?? '') ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="stripe_secret_key" class="form-label">Secret Key</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="stripe_secret_key" name="stripe_secret_key"
                                               value="<?= htmlspecialchars($stripe_secret_key ?? '') ?>">
                                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="stripe_secret_key">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <button type="button" class="btn btn-sm btn-outline-primary test-gateway" data-gateway="stripe">
                                    <i class="fas fa-vial me-1"></i> Test Connection
                                </button>
                                <div class="mt-2 test-result" id="stripe-test-result"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentForm = document.getElementById('payment-config-form');
    
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            
            if (input.type === 'password') {
                input.type = 'text';
                this.querySelector('i').classList.remove('fa-eye');
                this.querySelector('i').classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                this.querySelector('i').classList.remove('fa-eye-slash');
                this.querySelector('i').classList.add('fa-eye');
            }
        });
    });
    
    // Test gateway connection
    document.querySelectorAll('.test-gateway').forEach(button => {
        button.addEventListener('click', function() {
            const gateway = this.getAttribute('data-gateway');
            const resultElement = document.getElementById(gateway + '-test-result');
            
            // Show testing message
            resultElement.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Testing connection...';
            resultElement.className = 'mt-2 test-result';
            
            // Send test request
            fetch(`/admin/payment/test/${gateway}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultElement.innerHTML = '<i class="fas fa-check-circle text-success"></i> ' + data.message;
                    resultElement.className = 'mt-2 test-result text-success';
                } else {
                    resultElement.innerHTML = '<i class="fas fa-times-circle text-danger"></i> ' + data.message;
                    resultElement.className = 'mt-2 test-result text-danger';
                }
            })
            .catch(error => {
                resultElement.innerHTML = '<i class="fas fa-times-circle text-danger"></i> Connection test failed';
                resultElement.className = 'mt-2 test-result text-danger';
                console.error('Error:', error);
            });
        });
    });
    
    // Save payment configuration
    paymentForm.addEventListener('submit', function(event) {
        event.preventDefault();
        
        const formData = new FormData(paymentForm);
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';
        
        // Check if at least one gateway is selected
        if (!formData.getAll('payment_gateway[]').length) {
            alert('You must select at least one payment gateway.');
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="fas fa-save me-1"></i> Save Changes';
            return;
        }
        
        fetch('/admin/payment/update', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="fas fa-save me-1"></i> Save Changes';
            
            if (data.success) {
                showNotification('success', data.message);
            } else {
                showNotification('danger', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="fas fa-save me-1"></i> Save Changes';
            showNotification('danger', 'An error occurred while saving settings.');
        });
    });
    
    // Show notification
    function showNotification(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
        alertDiv.setAttribute('role', 'alert');
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            alertDiv.classList.remove('show');
            setTimeout(() => alertDiv.remove(), 300);
        }, 5000);
    }
});
</script>

<?php
// Include admin footer
require_once __DIR__ . '/../admin/partials/footer.php';
?>
