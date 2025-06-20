<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            padding-top: 40px; 
            background-color: #f8f9fa;
        }
        .error-container { 
            max-width: 600px; 
            margin: 0 auto; 
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .error-icon {
            font-size: 48px;
            color: #dc3545;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container error-container text-center">
        <div class="error-icon">
            <i class="bi bi-exclamation-circle"></i>
        </div>
        <h2>Oops! Something went wrong</h2>
        <p class="lead">We're sorry, but an unexpected error has occurred.</p>
        <p>Our technical team has been notified and is working to resolve the issue.</p>
        <hr>
        <p>Please try again later or contact support if the problem persists.</p>
        <div class="mt-4">
            <a href="/" class="btn btn-primary">Return to Homepage</a>
        </div>
        <?php if (isset($error_id)): ?>
        <div class="mt-3 text-muted">
            <small>Error reference: <?= htmlspecialchars($error_id) ?></small>
        </div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
