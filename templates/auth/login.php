<?php
/**
 * Login Page Template
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= htmlspecialchars($appName ?? 'Gideon\'s Technology') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .login-container {
            max-width: 400px;
            margin: 2rem auto;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            background-color: #fff;
        }
        .gtech-logo {
            max-width: 180px;
            margin-bottom: 1.5rem;
        }
        .form-control {
            padding: 0.75rem;
            border-radius: 0.25rem;
            margin-bottom: 1rem;
        }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
            padding: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .footer {
            background-color: #212529;
            color: white;
            padding: 1rem 0;
            position: absolute;
            bottom: 0;
            width: 100%;
        }
        .footer a {
            color: white;
            text-decoration: none;
            margin: 0 0.5rem;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Gideons Technology</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gtech/services">Web Development</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gstore">Store</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/login">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/auth/register">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="login-container text-center">
            <div class="text-center mb-4">
                <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMDAiIGhlaWdodD0iODAiIHZpZXdCb3g9IjAgMCAyMDAgODAiPgogIDxzdHlsZT4KICAgIC5sb2dvLWcgewogICAgICBmaWxsOiAjNDI4NWY0OwogICAgfQogICAgLmxvZ28tdGVjaCB7CiAgICAgIGZpbGw6ICNmNDQyODU7CiAgICB9CiAgICAuc3BsYXNoIHsKICAgICAgZmlsbDogIzQyODVmNDsKICAgICAgb3BhY2l0eTogMC44OwogICAgfQogIDwvc3R5bGU+CiAgPGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoNDAsIDEwKSI+CiAgICA8Y2lyY2xlIGNsYXNzPSJzcGxhc2giIGN4PSIyMCIgY3k9IjIwIiByPSIyMCIgLz4KICAgIDx0ZXh0IHg9IjEwIiB5PSIzMCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXdlaWdodD0iYm9sZCIgZm9udC1zaXplPSIzMCIgY2xhc3M9ImxvZ28tZyI+RzwvdGV4dD4KICA8L2c+CiAgPHRleHQgeD0iODAiIHk9IjQwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtd2VpZ2h0PSJib2xkIiBmb250LXNpemU9IjI0IiBjbGFzcz0ibG9nby10ZWNoIj5URUNII3RleHQ8L3RleHQ+Cjwvc3ZnPgo=" alt="GTech Logo" class="gtech-logo">
                <h2 class="mb-3">Please sign in</h2>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="alert alert-<?= $_SESSION['flash_message']['type'] === 'error' ? 'danger' : $_SESSION['flash_message']['type'] ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['flash_message']['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['flash_message']); ?>
            <?php endif; ?>
            
            <form action="/auth/login" method="POST">
                <!-- CSRF Protection -->
                <input type="hidden" name="csrf_token" value="<?= \App\Core\CsrfProtection::generateToken() ?>">
                <div class="mb-3">
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email address" value="<?= htmlspecialchars($email ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                </div>
                <div class="form-check mb-3 text-start">
                    <input class="form-check-input" type="checkbox" value="remember-me" id="remember-me" name="remember">
                    <label class="form-check-label" for="remember-me">
                        Remember me
                    </label>
                </div>
                <button class="btn btn-lg btn-primary w-100" type="submit">Sign in</button>
                
                <div class="mt-3 text-center">
                    <a href="/auth/forgot-password" class="text-decoration-none">Forgot password?</a>
                </div>
                
                <div class="mt-2 text-center">
                    Don't have an account? <a href="/auth/register" class="text-decoration-none">Register</a>
                </div>
            </form>
            
            <div class="mt-4 text-center">
                <p>© 2025 Gideons Technology</p>
            </div>
        </div>
    </div>

    <footer class="footer mt-auto py-3">
        <div class="container text-center">
            <span>© 2025 Gideons Technology. All rights reserved.</span>
            <div class="mt-2">
                <a href="/">Home</a>
                <a href="/contact">Contact</a>
                <a href="/privacy-policy">Privacy Policy</a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>