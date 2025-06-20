<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error <?= htmlspecialchars($status ?? '500') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
        .error-code {
            font-size: 72px;
            font-weight: bold;
            color: #6c757d;
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
        <div class="error-code"><?= htmlspecialchars($status ?? '500') ?></div>
        <div class="error-icon">
            <i class="bi bi-exclamation-triangle"></i>
        </div>
        <h2>An Error Occurred</h2>
        <p class="lead"><?= htmlspecialchars($message ?? 'We\'re sorry, but an unexpected error has occurred.') ?></p>
        <p>Error reference: <code><?= htmlspecialchars($error_id) ?></code></p>
        <hr>
        <div class="mt-4">
            <a href="/" class="btn btn-primary">Return to Homepage</a>
            <button onclick="window.history.back()" class="btn btn-outline-secondary ms-2">Go Back</button>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
