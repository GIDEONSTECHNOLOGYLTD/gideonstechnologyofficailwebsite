<?php
// Application Constants
define('APP_NAME', 'Gideons Technology');
define('APP_VERSION', '1.0.0');
define('DEBUG', getenv('APP_ENV') === 'development');

// Path Constants
define('ROOT_DIR', dirname(__DIR__));
define('PUBLIC_DIR', ROOT_DIR . '/public');
define('STORAGE_DIR', ROOT_DIR . '/storage');
define('UPLOAD_DIR', PUBLIC_DIR . '/uploads');
define('LOG_DIR', ROOT_DIR . '/logs');

// Security Constants
define('PASSWORD_MIN_LENGTH', 8);
define('TOKEN_EXPIRY', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900); // 15 minutes
define('SESSION_LIFETIME', 1800); // 30 minutes
define('CSRF_TOKEN_LENGTH', 32);

// API Constants
define('API_VERSION', 'v1');
define('API_RATE_LIMIT', 100);
define('API_RATE_WINDOW', 3600);

// Cache Constants
define('CACHE_ENABLED', true);
define('CACHE_TTL', 3600);
define('CACHE_PREFIX', 'gtech_');

// Email Constants
define('SMTP_HOST', getenv('SMTP_HOST'));
define('SMTP_PORT', getenv('SMTP_PORT'));
define('SMTP_USER', getenv('SMTP_USER'));
define('SMTP_PASS', getenv('SMTP_PASS'));
define('EMAIL_FROM', 'noreply@gideonstechnology.com');
define('EMAIL_FROM_NAME', 'Gideons Technology');

// File Upload Constants
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_EXTENSIONS', [
    'jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'
]);

// Pagination Constants
define('DEFAULT_PER_PAGE', 20);
define('MAX_PER_PAGE', 100);

// Response Messages
define('MSG_INVALID_CREDENTIALS', 'Invalid username or password');
define('MSG_ACCOUNT_LOCKED', 'Account temporarily locked. Please try again later');
define('MSG_INVALID_TOKEN', 'Invalid or expired token');
define('MSG_EMAIL_SENT', 'Email has been sent successfully');
define('MSG_PASSWORD_UPDATED', 'Password has been updated successfully');
?>