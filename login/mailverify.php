<?php
session_start(); // Ensure session start for security

require_once 'config.php'; // Include your database connection setup

if (!isset($_GET["token"]) || empty($_GET["token"])) {
    die("Verification token is missing.");
}

// Better token validation and sanitization
$verificationCode = filter_input(INPUT_GET, "token", FILTER_SANITIZE_STRING);

// Check if token has valid format (should be a string of hexadecimal characters)
if (!preg_match('/^[a-f0-9]+$/i', $verificationCode)) {
    die("Invalid verification token format.");
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Use prepared statement to avoid SQL injection
$stmt = $conn->prepare("UPDATE gtechUsers SET is_verified = 1 WHERE verification_code = ? AND is_verified = 0");
$stmt->bind_param('s', $verificationCode);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $message = "Email verified successfully.";
        $success = true;
        // Redirect to the dashboard after a delay
        header("refresh:2;url=dashboard.php");
    } else {
        $message = "Token already used or invalid.";
        $success = false;
    }
} else {
    $message = "Error updating record: " . $stmt->error;
    $success = false;
}

$stmt->close();
$conn->close();
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
                <p><?php echo htmlspecialchars($message); ?></p>
                <p>Please try again or <a href="../contact.html">contact support</a>.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
