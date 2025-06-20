<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Two-Factor Authentication</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['flash']['error'])): ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['flash']['error'] ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="text-center mb-4">
                        <i class="fas fa-shield-alt fa-4x text-primary mb-3"></i>
                        <h5>Verification Required</h5>
                        <p class="text-muted">Please enter the 6-digit code from your authenticator app to complete the login process.</p>
                    </div>
                    
                    <form action="/auth/verify-2fa" method="post">
                        <?php echo \App\Core\CsrfProtection::tokenField(); ?>
                        
                        <div class="mb-3">
                            <label for="code" class="form-label">Authentication Code</label>
                            <input type="text" class="form-control form-control-lg text-center" id="code" name="code" 
                                   placeholder="000000" required pattern="[0-9]{6}" maxlength="6" autocomplete="off">
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-unlock-alt me-2"></i> Verify
                            </button>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <p class="mb-2">Lost your device?</p>
                        <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#recoveryCodeForm">
                            Use a recovery code
                        </button>
                        
                        <div class="collapse mt-3" id="recoveryCodeForm">
                            <form action="/auth/verify-2fa" method="post">
                                <?php echo \App\Core\CsrfProtection::tokenField(); ?>
                                
                                <div class="mb-3">
                                    <label for="recovery_code" class="form-label">Recovery Code</label>
                                    <input type="text" class="form-control" id="recovery_code" name="recovery_code" 
                                           placeholder="Enter recovery code" required>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-outline-secondary">
                                        Use Recovery Code
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
