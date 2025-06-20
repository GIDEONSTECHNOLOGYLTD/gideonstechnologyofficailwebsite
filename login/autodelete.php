<?php
// Include your database connection setup
require_once 'config.php';

// Number of days to wait before deleting unverified users
$expirationDays = 3;

// Calculate the timestamp that represents the cutoff date
$cutoffDate = date('Y-m-d H:i:s', strtotime("-$expirationDays days"));

// Prepare and execute a query to delete unverified users using prepared statement
$sql = "DELETE FROM gtechUsers WHERE is_verified = 0 AND registration_date < ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $cutoffDate);
$result = $stmt->execute();

if ($result) {
    echo "Unverified users older than $expirationDays days have been deleted.";
} else {
    echo "Error deleting unverified users: " . $conn->error;
}

// Close the prepared statement
$stmt->close();

// Close the database connection
$conn->close();

// Add a comment about setting up a cron job
// Example cron entry (run daily at midnight):
// 0 0 * * * /usr/bin/php /path/to/your/autodelete.php > /dev/null 2>&1
?>