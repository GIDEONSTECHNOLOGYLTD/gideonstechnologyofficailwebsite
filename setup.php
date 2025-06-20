<?php

// Load configurations
require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    // Create SQLite database connection
    $dbPath = __DIR__ . '/database/gtech.db';
    $pdo = new PDO(
        'sqlite:' . $dbPath,
        null,
        null,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    // Enable foreign key support
    $pdo->exec('PRAGMA foreign_keys = ON');
    
    // Run migrations
    $migrations = glob(__DIR__ . '/database/migrations/*.sql');
    natsort($migrations); // Sort by filename to ensure order
    
    foreach ($migrations as $migration) {
        echo "Running migration: " . basename($migration) . "\n";
        $sql = file_get_contents($migration);
        $pdo->exec($sql);
    }
    
    // Create default admin user if not exists
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute(['admin@gideonstech.com']);
    
    if (!$stmt->fetch()) {
        $password = password_hash('password', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (email, password, full_name, role) VALUES (?, ?, ?, ?)');
        $stmt->execute(['admin@gideonstech.com', $password, 'System Admin', 'admin']);
    }
    
    echo "\nDatabase setup completed successfully!\n";
    echo "Default admin credentials:\n";
    echo "Email: admin@gideonstech.com\n";
    echo "Password: password\n";
    
} catch (PDOException $e) {
    die("Setup failed: " . $e->getMessage() . "\n");
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
