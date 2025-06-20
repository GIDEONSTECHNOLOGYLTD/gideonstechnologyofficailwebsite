<?php
$page_title = "Reset Password - " . SITE_NAME;
$meta_description = "Reset your password for your Gideons Technology account";
$meta_keywords = "reset password, new password, account recovery";

session_start();
require_once 'includes/config.php';
require_once 'includes/db.php';

// Initialize variables
$errors = [];
$success = false;
$token = $_GET['token'] ?? '';
$valid_token = false;
$user_id = null;

// Validate token
if (!empty($token)) {
    $stmt = $pdo->prepare("
        SELECT user_id 
        FROM password_resets 
        WHERE token = ? AND expires_at > NOW() AND used = 0
    ");
    
    if ($stmt->execute([$token])) {
        if ($result = $stmt->fetch()) {
            $valid_token = true;
            $user_id = $result['user_id'];
        }
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_token) {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate password
    if (empty($password)) {
        $errors['password'] = 'Password is required';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters';
    }

    // Validate password confirmation
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match';
    }

    // If no errors, update password
    if (empty($errors)) {
        // Hash new password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Update user's password
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        if ($stmt->execute([$hashed_password, $user_id])) {
            // Mark token as used
            $stmt = $pdo->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
            $stmt->execute([$token]);
            
            // Delete any other reset tokens for this user
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE user_id = ? AND token != ?");
            $stmt->execute([$user_id, $token]);
            
            $success = true;
        } else {
            $errors['system'] = 'Failed to update password. Please try again.';
            error_log('Failed to update password for user: ' . $user_id);
        }
    }
}

require_once 'includes/header.php';
?>

<!-- Reset Password Section -->
<section class="reset-password-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4">Reset Password</h2>

                        <?php if (!$valid_token && !$success): ?>
                            <div class="alert alert-danger">
                                Invalid or expired password reset link. Please request a new one from the 
                                <a href="forgot-password.php">forgot password page</a>.
                            </div>
                        <?php elseif ($success): ?>
                            <div class="alert alert-success">
                                Your password has been successfully reset.
                            </div>
                            <div class="text-center">
                                <a href="login.php" class="btn btn-primary">Sign In</a>
                            </div>
                        <?php else: ?>
                            <?php if (!empty($errors['system'])): ?>
                                <div class="alert alert-danger">
                                    <?php echo htmlspecialchars($errors['system']); ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" class="needs-validation" novalidate>
                                <!-- Password -->
                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                           id="password" name="password" required>
                                    <?php if (isset($errors['password'])): ?>
                                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['password']); ?></div>
                                    <?php endif; ?>
                                    <div class="form-text">Must be at least 8 characters long</div>
                                </div>

                                <!-- Confirm Password -->
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" 
                                           id="confirm_password" name="confirm_password" required>
                                    <?php if (isset($errors['confirm_password'])): ?>
                                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['confirm_password']); ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">Reset Password</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
