<?php
/**
 * Admin Email Configuration Template
 */

// Include admin header
require_once __DIR__ . '/../admin/partials/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Email Settings</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Email Settings</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-envelope me-1"></i>
            Gmail Configuration
        </div>
        <div class="card-body">
            <form id="email-config-form">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            To use Gmail as your email provider, you need to set up an App Password in your Google Account.
                            <a href="https://support.google.com/accounts/answer/185833" target="_blank" class="alert-link">
                                Learn how to create an App Password
                            </a>
                        </div>
                        
                        <div class="mb-3">
                            <label for="gmail_username" class="form-label">Gmail Address</label>
                            <input type="email" class="form-control" id="gmail_username" name="gmail_username" 
                                   value="<?= htmlspecialchars($gmail_username ?? '') ?>" required>
                            <div class="form-text">Your Gmail address will be used to send emails from your website.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="gmail_app_password" class="form-label">App Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="gmail_app_password" name="gmail_app_password"
                                       value="<?= htmlspecialchars($gmail_app_password ?? '') ?>">
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="gmail_app_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">Leave empty to keep current password.</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="mail_from_name" class="form-label">Sender Name</label>
                            <input type="text" class="form-control" id="mail_from_name" name="mail_from_name" 
                                   value="<?= htmlspecialchars($mail_from_name ?? 'Gideons Technology Ltd') ?>">
                            <div class="form-text">Name that will appear as the sender of emails.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="mail_from_address" class="form-label">Sender Email Address</label>
                            <input type="email" class="form-control" id="mail_from_address" name="mail_from_address" 
                                   value="<?= htmlspecialchars($mail_from_address ?? '') ?>">
                            <div class="form-text">If left empty, your Gmail address will be used.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="admin_email" class="form-label">Admin Email Address</label>
                            <input type="email" class="form-control" id="admin_email" name="admin_email" 
                                   value="<?= htmlspecialchars($admin_email ?? '') ?>">
                            <div class="form-text">Email address for receiving system notifications and test emails.</div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Test Email Configuration</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="test_email" class="form-label">Send Test Email To</label>
                                    <input type="email" class="form-control" id="test_email" name="test_email" 
                                           value="<?= htmlspecialchars($admin_email ?? '') ?>">
                                    <div class="form-text">Enter an email address to receive the test email.</div>
                                </div>
                                
                                <button type="button" id="test-email-btn" class="btn btn-outline-primary">
                                    <i class="fas fa-paper-plane me-1"></i> Send Test Email
                                </button>
                                <div class="mt-2" id="email-test-result"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save Email Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const emailForm = document.getElementById('email-config-form');
    
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
    
    // Test email configuration
    document.getElementById('test-email-btn').addEventListener('click', function() {
        const testEmail = document.getElementById('test_email').value;
        const resultElement = document.getElementById('email-test-result');
        
        if (!testEmail) {
            resultElement.innerHTML = '<i class="fas fa-times-circle text-danger"></i> Please enter a test email address';
            resultElement.className = 'mt-2 text-danger';
            return;
        }
        
        // Show testing message
        resultElement.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Sending test email...';
        resultElement.className = 'mt-2';
        
        // Get current form data to use updated settings for test
        const formData = new FormData(emailForm);
        formData.append('test_email', testEmail);
        
        // Send test request
        fetch('/admin/email/test', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resultElement.innerHTML = '<i class="fas fa-check-circle text-success"></i> ' + data.message;
                resultElement.className = 'mt-2 text-success';
            } else {
                resultElement.innerHTML = '<i class="fas fa-times-circle text-danger"></i> ' + data.message;
                resultElement.className = 'mt-2 text-danger';
            }
        })
        .catch(error => {
            resultElement.innerHTML = '<i class="fas fa-times-circle text-danger"></i> Failed to send test email';
            resultElement.className = 'mt-2 text-danger';
            console.error('Error:', error);
        });
    });
    
    // Save email configuration
    emailForm.addEventListener('submit', function(event) {
        event.preventDefault();
        
        const formData = new FormData(emailForm);
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';
        
        fetch('/admin/email/update', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="fas fa-save me-1"></i> Save Email Settings';
            
            if (data.success) {
                showNotification('success', data.message);
            } else {
                showNotification('danger', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="fas fa-save me-1"></i> Save Email Settings';
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
