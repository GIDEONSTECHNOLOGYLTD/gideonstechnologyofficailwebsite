<?php
require_once __DIR__ . '/../app/bootstrap.php';
require_once 'includes/db.php';

$page_title = "Sign In - " . SITE_NAME;
$meta_description = "Sign in to your Gideons Technology account";
$meta_keywords = "login, sign in, account access";

// Initialize variables
$errors = [];
$email = '';

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: account.php');
    exit;
}

// Check remember me cookie
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    // removed remember logic: custom token store not implemented
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    // Validate email
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }

    // Validate password
    if (empty($password)) {
        $errors['password'] = 'Password is required';
    }

    // If no validation errors, attempt login
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                SELECT id, first_name, last_name, email, password
                FROM users
                WHERE email = ?
            ");
            
            if ($stmt->execute([$email])) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user && password_verify($password, $user['password'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_name'] = trim($user['first_name'] . ' ' . $user['last_name']);
                    
                    // Set remember me cookie if requested
                    if ($remember) {
                        $token = bin2hex(random_bytes(32));
                        setcookie('remember_token', $token, time() + 30*24*60*60, '/', '', true, true);
                    }
                    
                    // Log successful login
                    $stmt = $pdo->prepare("
                        INSERT INTO login_attempts (user_id, ip_address, status)
                        VALUES (?, ?, 'success')
                    ");
                    $stmt->execute([$user['id'], $_SERVER['REMOTE_ADDR']]);

                    // Redirect to account page
                    header('Location: account.php');
                    exit;
                } else {
                    $errors['login'] = 'Invalid email or password';
                    
                    // Log failed login
                    $stmt = $pdo->prepare("
                        INSERT INTO login_attempts (user_id, ip_address, status)
                        VALUES (?, ?, 'failed')
                    ");
                    $stmt->execute([$user['id'] ?? null, $_SERVER['REMOTE_ADDR']]);
                }
            } else {
                $errors['login'] = 'Database error';
            }
        } catch (PDOException $e) {
            $errors['system'] = 'Login failed. Please try again.';
            error_log('Login error: ' . $e->getMessage());
        }
    }
}

require_once 'includes/header.php';
?>

<!-- Login Form -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Sign In</h2>

                    <?php if (!empty($errors['system']) || !empty($errors['login'])): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($errors['system'] ?? $errors['login']); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="login.php" class="needs-validation" novalidate>
                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                   id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['email']); ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                   id="password" name="password" required>
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['password']); ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Remember Me -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Sign In</button>
                        </div>

                        <div class="text-center mt-3">
                            <p class="mb-1">
                                <a href="forgot-password.php">Forgot your password?</a>
                            </p>
                            <p class="mb-0">
                                Don't have an account? <a href="register.php">Create one</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; exit; ?>
