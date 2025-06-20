<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="error">
                <?php 
                    $error = $_GET['error'];
                    if ($error === 'empty_fields') {
                        echo "Please fill in all fields.";
                    } elseif ($error === 'invalid_credentials') {
                        echo "Invalid username or password.";
                    } elseif ($error === 'invalid_request') {
                        echo "Invalid request. Please try again.";
                    }
                ?>
            </div>
        <?php endif; ?>
        
        <form action="/login/auth" method="post">
            <!-- Add CSRF protection token -->
            <input type="hidden" name="csrf_token" value="<?php echo App\Core\Security::generateCsrfToken(); ?>">
            
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            
            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</body>
</html>