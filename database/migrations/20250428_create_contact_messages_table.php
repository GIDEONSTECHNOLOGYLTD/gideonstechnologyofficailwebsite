<?php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Core\Database;

$pdo = $app->resolve('pdo');

// Create contact_messages table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS contact_messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        attachment_path VARCHAR(255),
        created_at DATETIME NOT NULL
    )
");

// Create uploads directory if it doesn't exist
if (!file_exists(PUBLIC_PATH . '/uploads')) {
    mkdir(PUBLIC_PATH . '/uploads', 0755, true);
}
