<?php
$app = require_once __DIR__ . '/../../app/bootstrap.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    redirect('/login');
}

// Render the dashboard view
$app->view()->render('dashboard/index');
?>
