<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../vendor/autoload.php';

try {
    $pdo = new PDO(
        "mysql:host=" . getenv('DB_HOST') . ";dbname=" . getenv('DB_DATABASE'),
        getenv('DB_USERNAME'),
        getenv('DB_PASSWORD')
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Start transaction
    $pdo->beginTransaction();

    // Seed essential data
    
    // 1. Default admin user
    $adminPassword = password_hash('change_this_password', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, is_active) VALUES (?, ?, ?, 1)");
    $stmt->execute(['admin', 'admin@gideonstechnology.com', $adminPassword]);
    
    // 2. Basic product categories
    $categories = ['Web Development', 'Mobile Apps', 'Graphics Design', 'Tech Support'];
    $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
    foreach ($categories as $category) {
        $stmt->execute([$category]);
    }
    
    // 3. Basic service templates
    $templates = [
        ['name' => 'Basic Website', 'category' => 'Web Development', 'price' => 499.99],
        ['name' => 'E-commerce Store', 'category' => 'Web Development', 'price' => 999.99],
        ['name' => 'Mobile App UI', 'category' => 'Mobile Apps', 'price' => 799.99],
        ['name' => 'Logo Design', 'category' => 'Graphics Design', 'price' => 299.99],
    ];
    
    $stmt = $pdo->prepare("INSERT INTO templates (name, category, price) VALUES (?, ?, ?)");
    foreach ($templates as $template) {
        $stmt->execute([$template['name'], $template['category'], $template['price']]);
    }

    // Commit transaction
    $pdo->commit();
    echo "✓ Production data seeded successfully!\n";

} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    echo "Error seeding production data: " . $e->getMessage() . "\n";
    exit(1);
}
