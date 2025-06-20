<?php
// Include main site configuration
require_once __DIR__ . '/../../app/config/site.php';

// Session configuration
if (!defined('SESSION_LIFETIME')) define('SESSION_LIFETIME', 7200); // 2 hours
if (!defined('REMEMBER_ME_LIFETIME')) define('REMEMBER_ME_LIFETIME', 30 * 24 * 60 * 60); // 30 days

// Security configuration
if (!defined('PASSWORD_MIN_LENGTH')) define('PASSWORD_MIN_LENGTH', 8);
if (!defined('PASSWORD_RESET_EXPIRY')) define('PASSWORD_RESET_EXPIRY', 3600); // 1 hour

// File upload configuration
if (!defined('MAX_UPLOAD_SIZE')) define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
if (!defined('ALLOWED_FILE_TYPES')) define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']);
if (!defined('UPLOAD_DIR')) define('UPLOAD_DIR', __DIR__ . '/../uploads/');

// Contact form configuration
if (!defined('MAX_MESSAGE_LENGTH')) define('MAX_MESSAGE_LENGTH', 2000);
if (!defined('SERVICES')) define('SERVICES', [
    'web-dev' => 'Web Development',
    'fintech' => 'Fintech Solutions',
    'general-tech' => 'General Technology',
    'repair' => 'Repair Services',
    'graphics' => 'Video & Graphics'
]);
