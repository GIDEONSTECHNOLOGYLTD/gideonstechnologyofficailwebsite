<?php
// Show all errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load environment variables if possible
function loadEnv() {
    $envFile = __DIR__ . '/.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
                putenv("$name=$value");
            }
        }
    }
}

loadEnv();

// Database connection settings from environment variables
$servername = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: '';
$password = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: '';

if (empty($username) || empty($dbname)) {
    echo "ERROR: Database credentials not found. Please set DB_HOST, DB_USER, DB_PASS, and DB_NAME in your .env file.<br>";
    exit(1);
}

echo "Testing database connection...<br>";
echo "Server: $servername<br>";
echo "Database: $dbname<br><br>";

try {
    // Attempt connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check connection
    if (!$conn) {
        throw new Exception('Connection failed: ' . mysqli_connect_error());
    }

    echo "SUCCESS: Connected to database!<br>";
    echo "Server version: " . mysqli_get_server_info($conn) . "<br>";
    echo "Character set: " . mysqli_character_set_name($conn) . "<br>";

    // Try to get list of tables
    $result = mysqli_query($conn, "SHOW TABLES");
    if ($result) {
        echo "<br>Tables in database:<br>";
        while ($row = mysqli_fetch_row($result)) {
            echo "- " . $row[0] . "<br>";
        }
    }

    mysqli_close($conn);

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "<br>";
    echo "<br>Debugging info:<br>";
    echo "1. Make sure the database exists<br>";
    echo "2. Check username and password<br>";
    echo "3. Verify database host (might be different from 'localhost')<br>";
    echo "4. Check if user has proper permissions<br>";
}
?>
