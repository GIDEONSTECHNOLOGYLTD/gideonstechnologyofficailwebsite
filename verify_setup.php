<?php
echo "<h1>Website Setup Verification</h1>";

// 1. Check Database
try {
    $db = new PDO("mysql:host=localhost;dbname=gideonst_site", "gideonst_site", "Site2025@db");
    echo "✅ Database Connection: OK<br>";
    
    // Check tables
    $tables = ['users', 'services', 'orders', 'payments', 'settings'];
    foreach($tables as $table) {
        $result = $db->query("SELECT 1 FROM $table LIMIT 1");
        echo "✅ Table '$table': OK<br>";
    }
} catch(PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "<br>";
}

// 2. Check Files
$files = [
    '/public_html/index.php',
    '/public_html/css/style.css',
    '/public_html/js/main.js',
    '/public_html/includes/config.php',
    '/public_html/.htaccess'
];

foreach($files as $file) {
    if(file_exists($file)) {
        echo "✅ File '$file': OK<br>";
    } else {
        echo "❌ File '$file': Missing<br>";
    }
}

// 3. Check Adminer
$adminer = file_get_contents('https://gideonstechnology.com/adminer/adminer.php');
if($adminer !== false) {
    echo "✅ Adminer: Accessible<br>";
} else {
    echo "❌ Adminer: Not accessible<br>";
}

// 4. Check Admin Login
try {
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ? AND is_admin = 1");
    $stmt->execute(['admin']);
    if($stmt->fetch()) {
        echo "✅ Admin Account: OK<br>";
    } else {
        echo "❌ Admin Account: Missing<br>";
    }
} catch(PDOException $e) {
    echo "❌ Admin Check Error: " . $e->getMessage() . "<br>";
}

echo "<br><strong>If you see any ❌ errors above, please fix them!</strong>";
?>
