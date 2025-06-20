<?php
// Include structured data functions
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/include-structured-data.php')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/include-structured-data.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Facebook Domain Verification -->
    <meta name="facebook-domain-verification" content="7pytz2k41lbsrw1xj6sq3kb50anvww" />
    <!-- SEO Meta Tags -->
    <meta name="description" content="Gideon's Technology Ltd - Professional technology services including web development, repair services, and fintech solutions." />
    <meta name="keywords" content="technology, web development, tech repair, fintech, IT services" />
    <meta name="author" content="Gideon's Technology Ltd" />
    <title><?= isset($title) ? $title : 'Gideon\'s Technology' ?></title>
    <!-- Open Graph Tags for Social Media -->
    <meta property="og:title" content="<?= isset($title) ? $title : 'Gideon\'s Technology' ?>" />
    <meta property="og:description" content="Professional technology services including web development, repair services, and fintech solutions." />
    <meta property="og:url" content="https://gideonstechnology.com<?= $_SERVER['REQUEST_URI'] ?? '/' ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Gideon's Technology" />
    <!-- Link to our custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.ico">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Gideon's Technology</div>
            <ul>
                <li><a href="/" class="<?= ($activePage ?? '') === 'home' ? 'active' : '' ?>">Home</a></li>
                <li><a href="/web-development" class="<?= ($activePage ?? '') === 'web' ? 'active' : '' ?>">Web Development</a></li>
                <li><a href="/repair-services" class="<?= ($activePage ?? '') === 'repair' ? 'active' : '' ?>">Repair Services</a></li>
                <li><a href="/fintech-solutions" class="<?= ($activePage ?? '') === 'fintech' ? 'active' : '' ?>">Fintech Solutions</a></li>
                <li><a href="/profile" class="<?= ($activePage ?? '') === 'profile' ? 'active' : '' ?>">Profile</a></li>
                <li><a href="/logout">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <?= $content ?? '' ?>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> Gideon's Technology. All rights reserved.</p>
        </div>
    </footer>

    <!-- Optional JavaScript -->
    <script src="/assets/js/main.js"></script>
    
    <?php if (function_exists('getStructuredDataForBusiness')): ?>
        <!-- Structured data for better SEO -->
        <?= getStructuredDataForBusiness() ?>
        
        <?php if (isset($pageTitle) && isset($pageDescription)): ?>
            <?= getStructuredDataForWebPage($pageTitle, $pageDescription) ?>
        <?php else: ?>
            <?= getStructuredDataForWebPage('Gideon\'s Technology', 'Professional technology services including web development, repair services, and fintech solutions.') ?>
        <?php endif; ?>
        
        <?php if (isset($productData)): ?>
            <?= getStructuredDataForProduct($productData) ?>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>