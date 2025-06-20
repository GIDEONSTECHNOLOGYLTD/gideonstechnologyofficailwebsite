<?php
session_start();
require_once 'config.php';

function verifyUserEmail($token) {
    global $conn;
    try {
        $conn->begin_transaction();
        
        $stmt = $conn->prepare("SELECT id FROM users WHERE verification_token = ? AND is_verified = 0");
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Invalid or expired verification token");
        }
        
        $userId = $result->fetch_assoc()['id'];
        
        // Update user verification status
        $stmt = $conn->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        
        $conn->commit();
        return true;
        
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Verification error: " . $e->getMessage());
        throw $e;
    }
}

$error = null;
$success = false;

try {
    if (!isset($_GET["token"])) {
        throw new Exception("Verification token is missing");
    }
    
    $token = filter_input(INPUT_GET, "token", FILTER_SANITIZE_STRING);
    if (verifyUserEmail($token)) {
        $success = true;
    }
    
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - Gideons Technology</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="verification-container">
        <?php if ($success): ?>
            <div class="success-message">
                <h2>Email Verified Successfully!</h2>
                <p>Your account has been verified. You can now <a href="../login.html">login</a>.</p>
            </div>
        <?php else: ?>
            <div class="error-message">
                <h2>Verification Failed</h2>
                <p><?php echo htmlspecialchars($error); ?></p>
                <p>Please try again or <a href="../contact.html">contact support</a>.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
