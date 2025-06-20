<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Two-Factor Authentication Setup</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success">
                            <?= $success ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger">
                            <?= $error ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!$twoFactorEnabled): ?>
                        <div class="alert alert-info">
                            <h5>Enhance Your Account Security</h5>
                            <p>Two-factor authentication adds an extra layer of security to your account. After enabling, you'll need both your password and a verification code from your phone to sign in.</p>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <h5>Step 1: Scan this QR code</h5>
                                    <p>Use an authenticator app like Google Authenticator, Authy, or Microsoft Authenticator to scan this QR code.</p>
                                    <div class="text-center p-3 border rounded bg-light">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode($qrCodeUrl) ?>" 
                                             alt="QR Code" class="img-fluid">
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <h5>Manual Setup</h5>
                                    <p>If you can't scan the QR code, enter this code manually in your app:</p>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="<?= $secret ?>" readonly>
                                        <button class="btn btn-outline-secondary" type="button" onclick="copySecret()">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <h5>Step 2: Verify Setup</h5>
                                    <p>Enter the 6-digit verification code from your authenticator app to verify the setup.</p>
                                    <form action="/user/two-factor/enable" method="post">
                                        <?php echo \App\Core\CsrfProtection::tokenField(); ?>
                                        <input type="hidden" name="secret" value="<?= $secret ?>">
                                        <div class="mb-3">
                                            <label for="code" class="form-label">Verification Code</label>
                                            <input type="text" class="form-control" id="code" name="code" 
                                                   placeholder="Enter 6-digit code" required pattern="[0-9]{6}" maxlength="6">
                                        </div>
                                        <button type="submit" class="btn btn-primary">Verify and Enable</button>
                                    </form>
                                </div>
                                
                                <div class="alert alert-warning">
                                    <h5><i class="fas fa-exclamation-triangle"></i> Important</h5>
                                    <p>Store your recovery codes in a safe place. If you lose your device, you'll need these codes to access your account.</p>
                                    <p>Recovery codes will be provided after you verify setup.</p>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success">
                            <h5><i class="fas fa-check-circle"></i> Two-Factor Authentication is Enabled</h5>
                            <p>Your account is protected with an additional layer of security.</p>
                        </div>
                        
                        <div class="mb-4">
                            <h5>Recovery Codes</h5>
                            <p>Keep these recovery codes in a safe place. If you lose your phone, you can use these codes to access your account.</p>
                            <div class="p-3 border rounded bg-light">
                                <pre class="mb-0"><?php foreach ($recoveryCodes as $code): ?>
<?= $code ?>
<?php endforeach; ?></pre>
                            </div>
                            <div class="mt-2">
                                <button class="btn btn-sm btn-outline-secondary" onclick="copyRecoveryCodes()">
                                    <i class="fas fa-copy"></i> Copy Codes
                                </button>
                                <a href="/user/two-factor/regenerate-recovery-codes" class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-sync"></i> Generate New Codes
                                </a>
                            </div>
                        </div>
                        
                        <form action="/user/two-factor/disable" method="post" class="mt-4">
                            <?php echo \App\Core\CsrfProtection::tokenField(); ?>
                            <div class="alert alert-danger">
                                <h5>Disable Two-Factor Authentication</h5>
                                <p>Warning: This will make your account less secure.</p>
                            </div>
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to disable two-factor authentication?')">
                                Disable Two-Factor Authentication
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copySecret() {
    const secretInput = document.querySelector('input[value="<?= $secret ?>"]');
    secretInput.select();
    document.execCommand('copy');
    alert('Secret copied to clipboard!');
}

function copyRecoveryCodes() {
    const codes = `<?php echo implode("\n", $recoveryCodes ?? []); ?>`;
    const el = document.createElement('textarea');
    el.value = codes;
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
    alert('Recovery codes copied to clipboard!');
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
