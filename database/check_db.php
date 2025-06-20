<?php
require_once __DIR__ . '/../config/app.php';

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Check tables
    echo "\n=== Tables ===\n";
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        echo "✓ {$table}\n";
        
        // Show table structure
        echo "\nStructure:\n";
        $columns = $pdo->query("DESCRIBE `{$table}`")->fetchAll();
        foreach ($columns as $col) {
            echo "  - {$col['Field']}: {$col['Type']}";
            if ($col['Key'] === 'PRI') echo " (PRIMARY)";
            if ($col['Key'] === 'UNI') echo " (UNIQUE)";
            echo "\n";
        }
        
        // Show indexes
        echo "\nIndexes:\n";
        $indexes = $pdo->query("SHOW INDEX FROM `{$table}`")->fetchAll();
        $currentIndex = '';
        foreach ($indexes as $idx) {
            if ($currentIndex !== $idx['Key_name']) {
                $currentIndex = $idx['Key_name'];
                echo "  - {$currentIndex} (";
                echo $idx['Non_unique'] ? "NON-UNIQUE" : "UNIQUE";
                echo ")\n";
            }
            echo "    · Column: {$idx['Column_name']}\n";
        }
        echo "\n";
    }

    // Check engine and charset
    echo "\n=== Table Properties ===\n";
    foreach ($tables as $table) {
        $info = $pdo->query("SHOW TABLE STATUS LIKE '{$table}'")->fetch();
        echo "{$table}:\n";
        echo "  Engine: {$info['Engine']}\n";
        echo "  Collation: {$info['Collation']}\n";
        echo "  Rows: {$info['Rows']}\n";
        echo "\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
