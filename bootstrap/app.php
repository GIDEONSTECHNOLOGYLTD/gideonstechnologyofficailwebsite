<?php

/**
 * Application Bootstrap File
 * Initializes the application environment
 */

// Require the Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Dotenv\Dotenv;

// Define base path
$basePath = dirname(__DIR__);

// Define application paths
if (!defined('BASE_PATH')) {
    define('BASE_PATH', $basePath);
}

if (!defined('APP_PATH')) {
    define('APP_PATH', $basePath . '/app');
}

if (!defined('CONFIG_PATH')) {
    define('CONFIG_PATH', $basePath . '/config');
}

if (!defined('STORAGE_PATH')) {
    define('STORAGE_PATH', $basePath . '/storage');
}

if (!defined('PUBLIC_PATH')) {
    define('PUBLIC_PATH', $basePath . '/public');
}

if (!defined('VIEW_PATH')) {
    define('VIEW_PATH', $basePath . '/resources/views');
}

// Load environment variables
if (file_exists($basePath . '/.env')) {
    $dotenv = \Dotenv\Dotenv::createImmutable($basePath);
    $dotenv->load();
}

// Ensure timezone is set
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'UTC');

// Set error reporting based on environment
$appEnv = $_ENV['APP_ENV'] ?? 'production';
$isDev = $appEnv === 'development' || $appEnv === 'local';

// Always show errors in development
if ($isDev) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    ini_set('html_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
}

// Log all errors
error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../storage/logs/error.log');

// Load configuration with error handling
try {
    $config = [];
    $configFile = CONFIG_PATH . '/app.php';
    
    if (file_exists($configFile)) {
        $config = require $configFile;
    } else {
        throw new RuntimeException('Configuration file not found: ' . $configFile);
    }
    
    // Ensure config has required structure
    if (!isset($config['app'])) {
        $config['app'] = [];
    }
    
    // Merge with environment variables
    $config['app']['env'] = $appEnv;
    $config['app']['debug'] = $isDev;
    $config['app']['name'] = $_ENV['APP_NAME'] ?? ($config['app']['name'] ?? 'Gideon\'s Technology');
    
} catch (Exception $e) {
    // If we can't load the config, use a minimal default
    $config = [
        'app' => [
            'env' => $appEnv,
            'debug' => $isDev,
            'name' => 'Gideon\'s Technology',
        ],
        'database' => [
            'driver' => 'mysql',
            'host' => $_ENV['DB_HOST'] ?? 'localhost',
            'database' => $_ENV['DB_DATABASE'] ?? 'gideons_tech',
            'username' => $_ENV['DB_USERNAME'] ?? 'root',
            'password' => $_ENV['DB_PASSWORD'] ?? '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ]
    ];
    
    if ($isDev) {
        error_log('Configuration error: ' . $e->getMessage());
    }
}

// Register autoloader
spl_autoload_register(function($className) {
    // Convert namespace to full file path
    $path = BASE_PATH;
    $className = ltrim($className, '\\');
    $fileName = '';
    
    if ($lastNsPos = strripos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        
        // Special handling for App\Core namespace
        if ($namespace === 'App\Core') {
            $fileName = 'app' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR;
        } else {
            $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            // Convert 'app/core' to 'app/Core' for case sensitivity
            $fileName = str_replace('app' . DIRECTORY_SEPARATOR . 'core', 'app' . DIRECTORY_SEPARATOR . 'Core', $fileName);
        }
    }
    
    $fileName .= $className . '.php';
    
    // Adjust for PSR-4 naming convention in our project
    $filePath = $path . DIRECTORY_SEPARATOR . $fileName;
    
    // If the file exists, require it
    if (file_exists($filePath)) {
        require_once $filePath;
        return true;
    }
    
    return false;
});

// Set error reporting based on environment
$isDebug = $config['app']['debug'] ?? false;

if ($isDebug) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(0);
}

// Initialize logger
$logger = new \App\Core\Logger();

// Initialize error handler
$errorHandler = \App\Core\ErrorHandler::getInstance($config, $logger);

// Initialize session
session_start([
    'cookie_secure' => isset($_SERVER['HTTPS']),
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'gc_maxlifetime' => $config['security']['session_lifetime'] * 60,
]);

// Define APP_ROOT if not already defined
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

// Initialize container if not already set
if (!isset($container)) {
    $container = new \DI\Container();
    
    // Load settings first
    $settings = require CONFIG_PATH . '/settings.php';
    $container->set('settings', $settings);
    
    // Load container configurations
    if (file_exists(CONFIG_PATH . '/container.php')) {
        $containerConfig = require CONFIG_PATH . '/container.php';
        foreach ($containerConfig as $key => $value) {
            $container->set($key, $value);
        }
    }
}

// Initialize database connection if needed
if (isset($config['database'])) {
    // Ensure we're passing the database configuration as an array
    $dbConfig = $config['database'];
    
    // Check if dbConfig is already an array
    if (!is_array($dbConfig)) {
        // If it's a string (like 'gideons_tech'), convert it to a proper config array
        if (is_string($dbConfig)) {
            $dbConfig = [
                'driver' => 'mysql',
                'host' => 'localhost',
                'database' => $dbConfig,
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'port' => getenv('DB_PORT') ?: '3306',
                'options' => [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ],
            ];
        }
    }
    
    // Initialize the database with the proper configuration
    if (class_exists('\App\Core\DatabaseManager')) {
        // Convert string to array if needed
        if (is_string($dbConfig)) {
            $dbConfig = [
                'driver' => 'mysql',
                'host' => 'localhost',
                'database' => $dbConfig,
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'port' => '3306',
                'options' => [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                ],
            ];
        }
        
        \App\Core\DatabaseManager::initialize($dbConfig);
    }
}

// Initialize Slim app if not already set
if (!isset($app)) {
    $app = AppFactory::createFromContainer($container);
}

// Initialize Router
$router = new App\Core\Router();

// We'll let the public/index.php file handle route loading to avoid duplicates
// This ensures that routes are only loaded once, as per the global flag system
// The ROUTES_REGISTERED flag will be set in public/index.php

// Initialize the database connection
$db = new \App\Core\Database($config['database']);

// Return the app instance for use in other files
return $app;

// Initialize database connection
$db = new \App\Core\Database($config['database']);

// Initialize view renderer
$view = new \App\Core\View();

// Return application components
return [
    'config' => $config,
    'db' => $db,
    'view' => $view,
    'logger' => $logger,
    'router' => $router,
];