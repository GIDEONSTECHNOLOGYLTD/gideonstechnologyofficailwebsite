<?php
/**
 * Migration: Create Users Table
 * Date: <?= date('Y-m-d H:i:s') ?>
 */

use PDO;

return function (PDO $db) {
    // Create users table
    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) NOT NULL UNIQUE,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(255) NULL,
            role VARCHAR(50) NOT NULL DEFAULT 'user',
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            remember_token VARCHAR(100) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Add indexes
    $db->exec("CREATE INDEX users_username_index ON users (username);");
    $db->exec("CREATE INDEX users_email_index ON users (email);");
    $db->exec("CREATE INDEX users_role_index ON users (role);");

    echo "Migration executed: Created users table\n";
};