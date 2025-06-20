<?php
require_once __DIR__ . '/../../app/bootstrap.php';

use Faker\Factory;
use App\Repositories\ProductRepository;

// Database seeder for products
// Instantiate Faker and repository
$faker = Factory::create();
$pdo->beginTransaction();
$productRepo = new ProductRepository($pdo);

$categories = ['laptops', 'smartphones', 'accessories'];
foreach ($categories as $cat) {
    for ($i = 0; $i < 10; $i++) {
        $name = ucfirst($cat) . ' ' . ucfirst($faker->word);
        $slug = strtolower(str_replace(' ', '-', $name)) . '-' . $faker->unique()->numberBetween(1000, 9999);
        $description = $faker->sentence(12);
        $price = $faker->randomFloat(2, 50, 2000);
        $stock = $faker->numberBetween(0, 100);
        $stmt = $pdo->prepare(
            'INSERT INTO products (category, name, slug, description, price, stock, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, 1, NOW(), NOW())'
        );
        $stmt->execute([$cat, $name, $slug, $description, $price, $stock]);
    }
}

$pdo->commit();
echo "Seeded products successfully.\n";
?>
