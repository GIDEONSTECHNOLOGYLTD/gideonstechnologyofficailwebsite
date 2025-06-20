<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'gideonst_site');
define('DB_PASS', 'Site2025@db');
define('DB_NAME', 'gideonst_site');

// Site configuration
define('SITE_URL', 'https://gideonstechnology.com');
define('SITE_NAME', 'Gideons Technology');
define('ADMIN_EMAIL', 'admin@gideonstechnology.com');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Database connection
try {
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Helper functions
function redirect($url) {
    header("Location: " . $url);
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

function sanitize($input) {
    return htmlspecialchars(strip_tags($input));
}
?>
