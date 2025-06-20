<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/db.php';

$page_title = "Forgot Password - " . SITE_NAME;
$meta_description = "Reset your Gideons Technology account password";
$meta_keywords = "forgot password, reset password, account recovery";

// Initialize variables
$errors = [];
$success = false;
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    // Validate input
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }

    if (empty($errors)) {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        if ($stmt->execute([$email])) {
            if ($user = $stmt->fetch()) {
                // Generate reset token
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Remove any existing reset tokens for this user
                $stmt = $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?");
                if ($stmt->execute([$user['id']])) {
                    // Store new token in database
                    $stmt = $pdo->prepare("
                        INSERT INTO password_resets (user_id, token, expires_at)
                        VALUES (?, ?, ?)
                    ");
                    
                    if ($stmt->execute([$user['id'], $token, $expires])) {
                        // Send reset email
                        $resetLink = SITE_URL . "/reset-password.php?token=" . $token;
                        $to = $email;
                        $subject = "Password Reset Request";
                        $message = "Hello,\n\n";
                        $message .= "You have requested to reset your password. Click the link below to reset it:\n\n";
                        $message .= $resetLink . "\n\n";
                        $message .= "This link will expire in 1 hour.\n\n";
                        $message .= "If you didn't request this, please ignore this email.\n\n";
                        $message .= "Best regards,\n";
                        $message .= SITE_NAME;
                        
                        $headers = "From: noreply@gideonstechnology.com";
                        
                        if (mail($to, $subject, $message, $headers)) {
                            $success = true;
                        } else {
                            $errors['system'] = 'Failed to send reset email. Please try again.';
                            error_log('Failed to send password reset email to: ' . $email);
                        }
                    } else {
                        $errors['system'] = 'Failed to process request. Please try again.';
                        error_log('Failed to create password reset token for user: ' . $user['id']);
                    }
                } else {
                    $errors['system'] = 'Failed to process request. Please try again.';
                    error_log('Failed to remove old password reset tokens for user: ' . $user['id']);
                }
            }
        } else {
            $errors['system'] = 'Failed to process request. Please try again.';
            error_log('Failed to check email existence: ' . $email);
        }
        
        // If no errors occurred but user wasn't found, still show success
        // This prevents email enumeration
        if (empty($errors) && !$success) {
            $success = true;
        }
    }
}

require_once 'includes/header.php';
?>

<!-- Forgot Password Form -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Reset Password</h2>

                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            If an account exists with that email address, we've sent password reset instructions.
                            Please check your email.
                        </div>
                        <div class="text-center mt-3">
                            <a href="login.php" class="btn btn-primary">Return to Login</a>
                        </div>
                    <?php else: ?>
                        <?php if (!empty($errors['system'])): ?>
                            <div class="alert alert-danger">
                                <?php echo htmlspecialchars($errors['system']); ?>
                            </div>
                        <?php endif; ?>

                        <p class="text-muted mb-4">
                            Enter your email address and we'll send you instructions to reset your password.
                        </p>

                        <form method="POST" action="forgot-password.php" class="needs-validation" novalidate>
                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                       id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['email']); ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Send Reset Instructions</button>
                            </div>

                            <p class="text-center mt-3 mb-0">
                                Remember your password? <a href="login.php">Sign In</a>
                            </p>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
