<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Access Denied' ?> | <?= $appName ?? 'Gideon\'s Technology' ?></title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <style>
        .error-container {
            text-align: center;
            padding: 100px 0;
        }
        .error-code {
            font-size: 120px;
            color: #dc3545;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .error-message {
            font-size: 24px;
            margin-bottom: 30px;
        }
        .back-home {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container error-container">
        <div class="error-code">403</div>
        <h1 class="error-title"><?= $title ?? 'Access Denied' ?></h1>
        <div class="error-message"><?= $message ?? 'You do not have permission to access this resource.' ?></div>
        
        <?php if (isset($_SESSION['flash']['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['flash']['error'][0] ?>
            </div>
        <?php endif; ?>
        
        <div class="back-home">
            <a href="/" class="btn btn-primary">Back to Home</a>
            <?php if (!isset($_SESSION['user'])): ?>
                <a href="/auth/login" class="btn btn-outline-primary ml-3">Login</a>
            <?php endif; ?>
        </div>
    </div>

    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>