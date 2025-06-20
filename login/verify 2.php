<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Store user details in the session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_username'] = $row['username'];
            $_SESSION['user_email'] = $row['email'];
            header("Location: ../dashboard.php");
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with that username.";
    }
}

if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE verification_code = ?");
    $stmt->bind_param('s', $code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE users SET is_verified = 1 WHERE verification_code = ?");
        $stmt->bind_param('s', $code);
        if ($stmt->execute()) {
            echo "Email verified successfully.";
        } else {
            echo "Error verifying email.";
        }
    } else {
        echo "Invalid verification code.";
    }
} else {
    echo "No verification code provided.";
}

$conn->close();
?>
