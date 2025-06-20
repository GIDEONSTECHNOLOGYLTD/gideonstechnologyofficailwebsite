<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Page Not Found' ?> | <?= $appName ?? 'Gideon\'s Technology' ?></title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <style>
        .error-container {
            text-align: center;
            padding: 100px 0;
        }
        .error-code {
            font-size: 120px;
            color: #17a2b8;
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
        <div class="error-code">404</div>
        <h1 class="error-title"><?= $title ?? 'Page Not Found' ?></h1>
        <div class="error-message"><?= $message ?? 'The page you requested could not be found.' ?></div>
        
        <div class="back-home">
            <a href="/" class="btn btn-primary">Back to Home</a>
            <a href="javascript:history.back()" class="btn btn-outline-secondary ml-3">Go Back</a>
        </div>
    </div>

    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>