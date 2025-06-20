<?php $this->layout('admin/layout', ['page' => 'payment', 'messages' => $messages ?? []]); ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Payment Gateway Settings</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
        <li class="breadcrumb-item active">Payment Settings</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-credit-card me-1"></i>
            Payment Configuration
        </div>
        <div class="card-body">
            <!-- Payment Settings Form -->
            <form method="post" action="/admin/payment/save">
                <!-- General Payment Settings -->
                <h4 class="mb-3">General Settings</h4>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label">Test Mode</label>
                    <div class="col-sm-9">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="payment_test_mode" 
                                   name="payment_test_mode" <?= $config['payment_test_mode'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="payment_test_mode">
                                Enable Test Mode
                            </label>
                        </div>
                        <div class="form-text text-muted">
                            When enabled, payments will be processed in sandbox/test mode.
                        </div>
                    </div>
                </div>
                
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label" for="currency">Currency</label>
                    <div class="col-sm-9">
                        <select class="form-select" id="currency" name="currency">
                            <option value="GBP" <?= $config['currency'] === 'GBP' ? 'selected' : '' ?>>British Pound (GBP)</option>
                            <option value="USD" <?= $config['currency'] === 'USD' ? 'selected' : '' ?>>US Dollar (USD)</option>
                            <option value="EUR" <?= $config['currency'] === 'EUR' ? 'selected' : '' ?>>Euro (EUR)</option>
                            <option value="CAD" <?= $config['currency'] === 'CAD' ? 'selected' : '' ?>>Canadian Dollar (CAD)</option>
                            <option value="NGN" <?= $config['currency'] === 'NGN' ? 'selected' : '' ?>>Nigerian Naira (NGN)</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label" for="currency_symbol">Currency Symbol</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="currency_symbol" name="currency_symbol" 
                               value="<?= htmlspecialchars($config['currency_symbol']) ?>">
                    </div>
                </div>
                
                <hr class="my-4">
                
                <!-- PayPal Settings -->
                <h4 class="mb-3">PayPal Settings</h4>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label">Enable PayPal</label>
                    <div class="col-sm-9">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="paypal_enabled" 
                                   name="paypal_enabled" <?= $config['paypal_enabled'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="paypal_enabled">
                                Enable PayPal Payments
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label" for="paypal_client_id">Client ID</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="paypal_client_id" name="paypal_client_id" 
                               value="<?= htmlspecialchars($config['paypal_client_id']) ?>">
                        <div class="form-text text-muted">
                            Your PayPal Client ID from the PayPal Developer Dashboard.
                        </div>
                    </div>
                </div>
                
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label" for="paypal_client_secret">Client Secret</label>
                    <div class="col-sm-9">
                        <input type="password" class="form-control" id="paypal_client_secret" name="paypal_client_secret" 
                               value="<?= htmlspecialchars($config['paypal_client_secret']) ?>">
                        <div class="form-text text-muted">
                            Your PayPal Client Secret from the PayPal Developer Dashboard.
                        </div>
                    </div>
                </div>
                
                <div class="mb-3 row">
                    <div class="col-sm-9 offset-sm-3">
                        <button type="button" class="btn btn-outline-primary" id="test-paypal-btn">
                            <i class="fas fa-vial me-1"></i> Test PayPal Connection
                        </button>
                        <div id="paypal-test-result" class="mt-2"></div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <!-- Stripe Settings -->
                <h4 class="mb-3">Stripe Settings</h4>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label">Enable Stripe</label>
                    <div class="col-sm-9">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="stripe_enabled" 
                                   name="stripe_enabled" <?= $config['stripe_enabled'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="stripe_enabled">
                                Enable Stripe Payments
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label" for="stripe_publishable_key">Publishable Key</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="stripe_publishable_key" name="stripe_publishable_key" 
                               value="<?= htmlspecialchars($config['stripe_publishable_key']) ?>">
                        <div class="form-text text-muted">
                            Your Stripe Publishable Key from the Stripe Dashboard.
                        </div>
                    </div>
                </div>
                
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label" for="stripe_secret_key">Secret Key</label>
                    <div class="col-sm-9">
                        <input type="password" class="form-control" id="stripe_secret_key" name="stripe_secret_key" 
                               value="<?= htmlspecialchars($config['stripe_secret_key']) ?>">
                        <div class="form-text text-muted">
                            Your Stripe Secret Key from the Stripe Dashboard.
                        </div>
                    </div>
                </div>
                
                <div class="mb-3 row">
                    <div class="col-sm-9 offset-sm-3">
                        <button type="button" class="btn btn-outline-primary" id="test-stripe-btn">
                            <i class="fas fa-vial me-1"></i> Test Stripe Connection
                        </button>
                        <div id="stripe-test-result" class="mt-2"></div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <!-- Save Button -->
                <div class="mb-3 row">
                    <div class="col-sm-9 offset-sm-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save Payment Settings
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Test PayPal Connection
    $('#test-paypal-btn').on('click', function() {
        const $btn = $(this);
        const $result = $('#paypal-test-result');
        
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Testing...');
        $result.html('');
        
        $.ajax({
            url: '/admin/payment/test-paypal',
            method: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $result.html('<div class="alert alert-success">' + response.message + '</div>');
                } else {
                    $result.html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function(xhr) {
                $result.html('<div class="alert alert-danger">Connection error. Please try again.</div>');
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fas fa-vial me-1"></i> Test PayPal Connection');
            }
        });
    });
    
    // Test Stripe Connection
    $('#test-stripe-btn').on('click', function() {
        const $btn = $(this);
        const $result = $('#stripe-test-result');
        
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Testing...');
        $result.html('');
        
        $.ajax({
            url: '/admin/payment/test-stripe',
            method: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $result.html('<div class="alert alert-success">' + response.message + '</div>');
                } else {
                    $result.html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function(xhr) {
                $result.html('<div class="alert alert-danger">Connection error. Please try again.</div>');
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fas fa-vial me-1"></i> Test Stripe Connection');
            }
        });
    });
});
</script>
