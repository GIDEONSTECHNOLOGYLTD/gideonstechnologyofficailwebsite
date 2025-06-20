<?php
require_once __DIR__ . '/../../app/bootstrap.php';
$pdo->exec("TRUNCATE TABLE products");

try {
    $categories = ['laptops', 'smartphones', 'accessories'];
    foreach ($categories as $cat) {
        for ($i = 1; $i <= 10; $i++) {
            $name = ucfirst($cat) . ' Product ' . $i;
            $slug = strtolower($cat . '-product-' . $i);
            $description = 'Sample product #' . $i . ' in ' . ucfirst($cat);
            $price = mt_rand(5000, 200000) / 100;
            $stock = mt_rand(0, 100);

            $stmt = $pdo->prepare(
                'INSERT INTO products (category, name, slug, description, price, stock, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, 1, NOW(), NOW())'
            );
            $stmt->execute([$cat, $name, $slug, $description, $price, $stock]);
        }
    }
    echo "✅ Seeded products successfully via fixed seeder.\n";
} catch (Exception $e) {
    echo "❌ Seeding failed: " . $e->getMessage() . "\n";
}
