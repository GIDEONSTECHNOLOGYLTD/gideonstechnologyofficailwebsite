<?php
include('config.php'); // Include your database configuration file

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token is valid
    $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($email);
        $stmt->fetch();
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Reset Password - Gideon's Technology</title>
            <link rel="stylesheet" href="assets/css/style.css">
        </head>
        <body>
            <main>
                <div class="signup-wrapper">
                    <div class="signup-contents">
                        <form class="signup-forms" action="update_password.php" method="POST">
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                            <div class="single-signup">
                                <label class="signup-label" for="password">New Password</label>
                                <input type="password" id="password" name="password" class="form--control" required>
                            </div>
                            <button type="submit" class="cmn-btn">Update Password</button>
                        </form>
                    </div>
                </div>
            </main>
        </body>
        </html>
        <?php
    } else {
        echo "Invalid or expired token.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
