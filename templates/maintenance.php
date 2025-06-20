<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance - GIDEONS TECHNOLOGY LTD</title>
    <!-- Facebook domain verification -->
    <meta name="facebook-domain-verification" content="7pytz2k41lbsrw1xj6sq3kb50anvww" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
            color: #343a40;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .maintenance-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .maintenance-content {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 700px;
            width: 100%;
            text-align: center;
        }
        .logo {
            max-width: 200px;
            margin-bottom: 30px;
        }
        .maintenance-icon {
            font-size: 80px;
            margin-bottom: 20px;
            color: #dc3545;
        }
        h1 {
            font-weight: 700;
            margin-bottom: 20px;
            color: #343a40;
        }
        .message {
            font-size: 1.2rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        footer {
            background-color: #343a40;
            color: #fff;
            padding: 20px 0;
            text-align: center;
        }
        .social-icons {
            margin-top: 20px;
        }
        .social-icons a {
            color: #495057;
            font-size: 24px;
            margin: 0 10px;
            transition: all 0.3s;
        }
        .social-icons a:hover {
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="maintenance-content">
            <img src="/assets/img/logo.png" alt="GIDEONS TECHNOLOGY LTD" class="logo">
            <div class="maintenance-icon">
                <i class="fas fa-tools"></i>
            </div>
            <h1>We'll Be Right Back!</h1>
            <div class="message">
                <?= nl2br(htmlspecialchars($message)) ?>
            </div>
            <div class="social-icons">
                <a href="https://www.facebook.com/gideonstechnology" target="_blank" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                <a href="https://www.twitter.com/gideonstech" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="https://www.instagram.com/gideonstechnology" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </div>
    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> GIDEONS TECHNOLOGY LTD. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>
