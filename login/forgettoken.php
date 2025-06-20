<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        if (userExists($email)) {
            $resetToken = generateResetToken();
            storeResetToken($email, $resetToken);
            sendResetEmail($email, $resetToken);
            echo "Password reset instructions have been sent to your email.";
        } else {
            echo "User account not found. Please check your email address.";
        }
    } else {
        echo "Invalid email address.";
    }
}

function userExists($email) {
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM gtechUsers WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();
    return $exists;
}

function generateResetToken() {
    return bin2hex(random_bytes(16));
}

function storeResetToken($email, $token) {
    global $conn;
    $expirationTime = date('Y-m-d H:i:s', strtotime('+1 hour'));
    $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expiration_time) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $email, $token, $expirationTime);
    $stmt->execute();
    $stmt->close();
}

function sendResetEmail($email, $token) {
    $resetLink = "https://gideonstechnology.com/login/reset-password.php?token=$token";
    $subject = "Password Reset";
    $message = "Click the following link to reset your password: $resetLink";
    mail($email, $subject, $message);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Add your head content here -->
</head>
<body>
  <!-- Add your body content here -->
</body>
</html>