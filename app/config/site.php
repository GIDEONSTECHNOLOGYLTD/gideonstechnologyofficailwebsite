<?php

// Site Configuration
if (!defined('SITE_NAME')) define('SITE_NAME', 'Gideons Technology');
if (!defined('SITE_URL')) define('SITE_URL', getenv('APP_URL') ?: 'http://localhost:8082');
if (!defined('ADMIN_EMAIL')) define('ADMIN_EMAIL', 'admin@gideonstech.com');

// Database Configuration
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_NAME')) define('DB_NAME', 'gideons_tech');
if (!defined('DB_DSN')) define('DB_DSN', 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4');

// Template Configuration
if (!defined('TEMPLATES_PATH')) define('TEMPLATES_PATH', __DIR__ . '/../../public/assets/templates');
if (!defined('UPLOADS_PATH')) define('UPLOADS_PATH', __DIR__ . '/../../public/assets/uploads');

// Service Types
if (!defined('SERVICE_WEB_DEV')) define('SERVICE_WEB_DEV', 'web-development');
if (!defined('SERVICE_FINTECH')) define('SERVICE_FINTECH', 'fintech');
if (!defined('SERVICE_REPAIR')) define('SERVICE_REPAIR', 'repair');
if (!defined('SERVICE_GENERAL')) define('SERVICE_GENERAL', 'general-tech');
if (!defined('SERVICE_VIDEO')) define('SERVICE_VIDEO', 'videographics');

// User Roles
if (!defined('ROLE_ADMIN')) define('ROLE_ADMIN', 'admin');
if (!defined('ROLE_USER')) define('ROLE_USER', 'user');
if (!defined('ROLE_VENDOR')) define('ROLE_VENDOR', 'vendor');

// File Upload Configuration
if (!defined('ALLOWED_FILE_TYPES')) define('ALLOWED_FILE_TYPES', [
    'image/jpeg',
    'image/png',
    'image/gif',
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
]);
if (!defined('MAX_FILE_SIZE')) define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
