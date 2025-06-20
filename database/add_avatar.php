<?php
require_once __DIR__ . '/../app/bootstrap.php';

try {
    $sql = "ALTER TABLE users ADD COLUMN avatar VARCHAR(255) NULL AFTER name";
    $pdo->exec($sql);
    echo "✅ Avatar column added successfully.\n";
} catch (PDOException $e) {
    echo "❌ Error adding avatar column: " . $e->getMessage() . "\n";
}
