<?php
require_once __DIR__ . '/../app/bootstrap.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS `products` (
      `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      `category` VARCHAR(255) NOT NULL,
      `name` VARCHAR(255) NOT NULL,
      `slug` VARCHAR(255) NOT NULL UNIQUE,
      `image` VARCHAR(255) DEFAULT NULL,
      `description` TEXT,
      `price` DECIMAL(10,2) NOT NULL,
      `sale_price` DECIMAL(10,2) DEFAULT NULL,
      `stock` INT DEFAULT 0,
      `is_featured` TINYINT(1) DEFAULT 0,
      `is_active` TINYINT(1) DEFAULT 1,
      `sort_order` INT DEFAULT 0,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $pdo->exec($sql);
    echo "✅ Created products table successfully.\n";
} catch (PDOException $e) {
    echo "❌ Error creating products table: " . $e->getMessage() . "\n";
}
