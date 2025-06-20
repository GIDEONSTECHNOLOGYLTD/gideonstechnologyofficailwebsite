<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Gideon\'s Technology' ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <div class="logo">
                    <a href="/"><?= $appName ?? 'Gideon\'s Technology' ?></a>
                </div>
                <ul class="nav-links">
                    <li><a href="/">Home</a></li>
                    <li><a href="/gtech">GTech</a></li>
                    <li><a href="/gstore">GStore</a></li>
                    <li><a href="/auth/login">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="hero">
            <div class="container">
                <h1><?= $welcomeMessage ?? 'Welcome to Gideon\'s Technology' ?></h1>
                <p>Innovative solutions for all your technology needs</p>
                <div class="cta-buttons">
                    <a href="/gtech" class="btn btn-primary">GTech Services</a>
                    <a href="/gstore" class="btn btn-secondary">GStore Products</a>
                </div>
            </div>
        </div>

        <section class="features">
            <div class="container">
                <h2>What We Offer</h2>
                <div class="feature-grid">
                    <div class="feature-card">
                        <h3>Web Development</h3>
                        <p>Professional websites and web applications</p>
                    </div>
                    <div class="feature-card">
                        <h3>Mobile App Development</h3>
                        <p>iOS and Android mobile applications</p>
                    </div>
                    <div class="feature-card">
                        <h3>Tech Products</h3>
                        <p>Quality technology products at competitive prices</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?= $currentYear ?? date('Y') ?> <?= $appName ?? 'Gideon\'s Technology' ?>. All rights reserved.</p>
        </div>
    </footer>
    
    <script src="/assets/js/main.js"></script>
</body>
</html>