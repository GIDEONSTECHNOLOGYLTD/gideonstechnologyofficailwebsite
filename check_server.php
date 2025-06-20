<?php
header('Content-Type: text/plain');

echo "Server Configuration Check\n";
echo "=========================\n\n";

echo "1. PHP Version: " . phpversion() . "\n";
echo "2. Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "3. Script Path: " . __FILE__ . "\n";
echo "4. Current Directory: " . getcwd() . "\n";

echo "\n5. Apache Modules:\n";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    echo "   - mod_rewrite: " . (in_array('mod_rewrite', $modules) ? "✅" : "❌") . "\n";
    echo "   - mod_headers: " . (in_array('mod_headers', $modules) ? "✅" : "❌") . "\n";
} else {
    echo "   Unable to check Apache modules\n";
}

echo "\n6. File Permissions:\n";
$files = [
    'index.php',
    '.htaccess',
    'config.php',
    'includes/Database.php'
];

foreach ($files as $file) {
    echo "   - $file: ";
    if (file_exists($file)) {
        $perms = fileperms($file);
        $mode = substr(sprintf('%o', $perms), -4);
        echo "Found (mode: $mode)\n";
    } else {
        echo "Not found\n";
    }
}

echo "\n7. Environment:\n";
echo "   - Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "   - Server Name: " . $_SERVER['SERVER_NAME'] . "\n";
echo "   - Server Protocol: " . $_SERVER['SERVER_PROTOCOL'] . "\n";
echo "   - Request URI: " . $_SERVER['REQUEST_URI'] . "\n";

echo "\n8. Directory Structure:\n";
function listDir($path, $indent = '') {
    if (!is_dir($path)) return;
    
    $items = scandir($path);
    foreach ($items as $item) {
        if ($item == '.' || $item == '..') continue;
        if (is_dir("$path/$item")) {
            echo "$indent- $item/\n";
            if (strlen($indent) < 6) { // Limit depth
                listDir("$path/$item", "$indent  ");
            }
        } else {
            echo "$indent- $item\n";
        }
    }
}

listDir('.');
