<?php
// Database credentials
$servername = "localhost";
$username = "gideonst"; // Updated from root to match your hosting
$password = ""; // Add your actual database password here
$dbname = "gtech"; // Updated to match your database name

// Create connection with error handling
try {
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    
    // Check connection
    if (!$conn) {
        throw new Exception("Connection failed: " . mysqli_connect_error());
    }
    
    // Set charset to prevent character encoding issues
    mysqli_set_charset($conn, "utf8mb4");
    
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    die("Connection error. Please try again later.");
}
?>
