<?php
require_once __DIR__ . '/../config/config.php';

try {
    $db = new PDO('sqlite:' . DB_NAME);
    $db->exec('PRAGMA foreign_keys = ON;');

    // Add admin user
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute(['admin', 'admin@gstore.com', $password, 'admin']);

    // Add some test products
    $products = [
        [
            'name' => 'Premium Laptop',
            'description' => 'High-performance laptop with latest specs',
            'price' => 1299.99,
            'stock' => 10,
            'category' => 'Electronics'
        ],
        [
            'name' => 'Smartphone Pro',
            'description' => 'Latest smartphone with advanced features',
            'price' => 999.99,
            'stock' => 20,
            'category' => 'Electronics'
        ],
        [
            'name' => 'Wireless Headphones',
            'description' => 'Premium wireless headphones with noise cancellation',
            'price' => 299.99,
            'stock' => 15,
            'category' => 'Electronics'
        ]
    ];

    foreach ($products as $product) {
        $stmt = $db->prepare("INSERT INTO products (name, description, price, stock, category) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$product['name'], $product['description'], $product['price'], $product['stock'], $product['category']]);
    }

    // Add some coupons
    $coupons = [
        [
            'code' => 'WELCOME20',
            'discount_type' => 'percentage',
            'discount_value' => 20,
            'min_spend' => 100,
            'usage_limit' => 100,
            'status' => 'active'
        ],
        [
            'code' => 'FREESHIP',
            'discount_type' => 'shipping',
            'discount_value' => 0,
            'usage_limit' => 1,
            'status' => 'active'
        ]
    ];

    foreach ($coupons as $coupon) {
        $min_spend = isset($coupon['min_spend']) ? $coupon['min_spend'] : null;
        $stmt = $db->prepare("INSERT INTO coupons (code, discount_type, discount_value, min_spend, usage_limit, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$coupon['code'], $coupon['discount_type'], $coupon['discount_value'], $min_spend, $coupon['usage_limit'], $coupon['status']]);
    }

    echo "Database seeding completed successfully!\n";
    echo "Admin credentials:\n";
    echo "Username: admin\n";
    echo "Password: admin123\n";

} catch (Exception $e) {
    echo "Error during seeding: " . $e->getMessage() . "\n";
    exit(1);
}
