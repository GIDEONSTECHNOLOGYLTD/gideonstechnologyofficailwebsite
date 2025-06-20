<?php
/**
 * GStore Index Page
 * 
 * This is the main entry point for the GStore section
 */

// Define base path
define('BASE_PATH', dirname(dirname(__DIR__)));

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load the autoloader
require BASE_PATH . '/vendor/autoload.php';

// Initialize database connection
try {
    $config = require BASE_PATH . '/app/config/database.php';
    $dbPath = $config['default']['database'];
    $dsn = "sqlite:{$dbPath}";
    $pdo = new PDO($dsn, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    // Get products (since there's no 'featured' column, just get the first 6)
    $stmt = $pdo->query("SELECT id, name, description, price, stock, category FROM products LIMIT 6");
    $featuredProducts = $stmt->fetchAll();
    
    // Add image paths and other necessary fields
    foreach ($featuredProducts as &$product) {
        // Set image path based on category
        $category = strtolower($product['category'] ?? 'general');
        $product['image'] = $category . '.jpg';
        
        // Add fields that the template might expect
        $product['on_sale'] = false;
        $product['sale_price'] = null;
    }
    
    // If no products found, create sample products
    if (empty($featuredProducts)) {
        $featuredProducts = [
            [
                'id' => 1, 
                'name' => 'Professional Website Template', 
                'description' => 'A clean, modern template perfect for business websites', 
                'price' => 99.99, 
                'image' => 'website.jpg',
                'on_sale' => false,
                'sale_price' => null
            ],
            [
                'id' => 2, 
                'name' => 'E-commerce Template', 
                'description' => 'Complete online store template with product pages and checkout', 
                'price' => 149.99, 
                'image' => 'ecommerce.jpg',
                'on_sale' => true,
                'sale_price' => 129.99
            ],
            [
                'id' => 3, 
                'name' => 'Portfolio Template', 
                'description' => 'Showcase your work with this elegant portfolio template', 
                'price' => 79.99, 
                'image' => 'portfolio.jpg',
                'on_sale' => false,
                'sale_price' => null
            ]
        ];
    }
} catch (Exception $e) {
    // If database connection fails, use sample products
    $featuredProducts = [
        [
            'id' => 1, 
            'name' => 'Professional Website Template', 
            'description' => 'A clean, modern template perfect for business websites', 
            'price' => 99.99, 
            'image' => 'website.jpg',
            'on_sale' => false,
            'sale_price' => null
        ],
        [
            'id' => 2, 
            'name' => 'E-commerce Template', 
            'description' => 'Complete online store template with product pages and checkout', 
            'price' => 149.99, 
            'image' => 'ecommerce.jpg',
            'on_sale' => true,
            'sale_price' => 129.99
        ],
        [
            'id' => 3, 
            'name' => 'Portfolio Template', 
            'description' => 'Showcase your work with this elegant portfolio template', 
            'price' => 79.99, 
            'image' => 'portfolio.jpg',
            'on_sale' => false,
            'sale_price' => null
        ]
    ];
}

// Set up template variables
$pageTitle = 'GStore - Premium Templates | Gideon\'s Technology';
$pageDescription = 'Browse our collection of premium website templates, themes, and digital assets.';
$bodyClass = 'gstore-page';
$appName = 'Gideon\'s Technology';
$currentYear = date('Y');

// Create placeholder image if it doesn't exist
$placeholderDir = BASE_PATH . '/public/assets/images';
$placeholderPath = $placeholderDir . '/placeholder.jpg';
if (!file_exists($placeholderPath)) {
    if (!is_dir($placeholderDir)) {
        mkdir($placeholderDir, 0755, true);
    }
    // Create a simple placeholder image
    $img = imagecreatetruecolor(600, 400);
    $bgColor = imagecolorallocate($img, 240, 240, 240);
    $textColor = imagecolorallocate($img, 100, 100, 100);
    imagefill($img, 0, 0, $bgColor);
    imagestring($img, 5, 200, 180, 'Template Image', $textColor);
    imagejpeg($img, $placeholderPath);
    imagedestroy($img);
}

// Include the template
include BASE_PATH . '/templates/gstore/index.php';
