<?php
/**
 * Gideon's Technology - Main Application Entry Point
 * 
 * This file serves as the front controller for the application
 * running on the Slim PHP framework.
 */

// Set correct path for working directory
chdir(dirname(__DIR__));

// Load application bootstrap
$app = require __DIR__ . '/../bootstrap.php';

// Run the application
$app->run();