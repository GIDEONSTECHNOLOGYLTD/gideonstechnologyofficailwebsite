<?php
// Root endpoint handler for the API
header('Content-Type: application/json');

echo json_encode([
    'status' => 'success',
    'message' => 'Welcome to the Repair Service API',
    'endpoints' => [
        'repair_status' => '/api/repair/status',
        'repair_submit' => '/api/repair/submit'
    ],
    'version' => '1.0.0'
]);
?>