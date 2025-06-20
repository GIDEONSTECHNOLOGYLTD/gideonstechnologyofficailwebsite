<?php

// Define base path constant if not already defined
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__);
}

// Load database configuration
$dbConfig = require BASE_PATH . '/config/database.php';

try {
    // Create PDO connection
    $dsn = "{$dbConfig['driver']}:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}";
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
    
    echo "Connected to database successfully.\n";
    
    // Create users table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(50) DEFAULT 'user',
        remember_token VARCHAR(100) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        two_factor_enabled TINYINT(1) DEFAULT 0,
        two_factor_secret VARCHAR(255) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    echo "Users table created successfully.\n";
    
    // Create two_factor_recovery_codes table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS two_factor_recovery_codes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        code VARCHAR(20) NOT NULL,
        used TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    echo "Two-factor recovery codes table created successfully.\n";
    
    // Create user_activity_logs table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_activity_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        description VARCHAR(255) NOT NULL,
        ip_address VARCHAR(45) DEFAULT NULL,
        user_agent VARCHAR(255) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    echo "User activity logs table created successfully.\n";
    
    // Create a test user if none exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users");
    $stmt->execute();
    $userCount = $stmt->fetchColumn();
    
    if ($userCount == 0) {
        // Create a test admin user
        $name = 'Admin User';
        $email = 'admin@example.com';
        $password = password_hash('password123', PASSWORD_DEFAULT);
        $role = 'admin';
        
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $password, $role]);
        
        echo "Test admin user created successfully.\n";
        echo "Email: {$email}\n";
        echo "Password: password123\n";
        
        // Create a test regular user
        $name = 'Test User';
        $email = 'user@example.com';
        $password = password_hash('password123', PASSWORD_DEFAULT);
        $role = 'user';
        
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $password, $role]);
        
        echo "Test regular user created successfully.\n";
        echo "Email: {$email}\n";
        echo "Password: password123\n";
    } else {
        echo "Users already exist in the database.\n";
    }
    
    echo "Database setup completed successfully.\n";
    
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
