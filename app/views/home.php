<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gideon's Technology</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Gideon's Technology</div>
            <ul>
                <li><a href="/" class="active">Home</a></li>
                <li><a href="/about">About</a></li>
                <li><a href="/services">Services</a></li>
                <li><a href="/projects">Projects</a></li>
                <li><a href="/contact">Contact</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="hero">
            <h1>Welcome to Gideon's Technology</h1>
            <p>Innovative solutions for modern businesses</p>
            <a href="/services" class="btn">Explore Services</a>
        </section>

        <section class="features">
            <div class="container">
                <h2>What We Offer</h2>
                <div class="feature-grid">
                    <div class="feature-card">
                        <h3>Web Development</h3>
                        <p>Custom websites and web applications built with the latest technologies.</p>
                    </div>
                    <div class="feature-card">
                        <h3>Mobile Apps</h3>
                        <p>Native and cross-platform mobile applications for iOS and Android.</p>
                    </div>
                    <div class="feature-card">
                        <h3>Software Solutions</h3>
                        <p>Bespoke software to streamline your business operations.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> Gideon's Technology. All rights reserved.</p>
        </div>
    </footer>

    <script src="/assets/js/main.js"></script>
</body>
</html>