<?php
// Database connection
$servername = 'localhost';
$username = 'gideonst';
$password = '';
$dbname = 'gtech';

$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, 'utf8mb4');
?>
