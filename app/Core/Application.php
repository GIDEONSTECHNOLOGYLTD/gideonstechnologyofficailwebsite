<?php

namespace App\Core;

use PDO;
use PDOException;

class Application
{
    private static $instance = null;
    private $db = null;
    private $config = [];
    
    private function __construct()
    {
        $this->loadConfig();
        $this->initDatabase();
    }
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Initialize the application and return a Slim App instance
     * 
     * @return \Slim\App The Slim App instance
     */
    public function initialize()
    {
        // Load application configuration
        $this->loadConfig();
        
        // Create a container builder
        $containerBuilder = new \DI\ContainerBuilder();
        $containerBuilder->useAutowiring(true);
        $container = $containerBuilder->build();
        
        // Register controllers and services in the container
        $this->registerServices($container);
        
        // Create and configure the Slim App
        $app = \Slim\Factory\AppFactory::createFromContainer($container);
        
        // Add middleware in the correct order
        $app->addRoutingMiddleware();
        $app->addBodyParsingMiddleware();
        $app->addErrorMiddleware(true, true, true);
        
        return $app;
    }
    
    /**
     * Register services in the DI container
     * 
     * @param \Psr\Container\ContainerInterface $container
     */
    private function registerServices($container)
    {
        // Register controllers
        if (class_exists('\App\Controllers\HomeController')) {
            $container->set('App\\Controllers\\HomeController', function() use ($container) {
                return new \App\Controllers\HomeController($container);
            });
        }
        
        if (class_exists('\App\Controllers\UserController')) {
            $container->set('App\\Controllers\\UserController', function() use ($container) {
                return new \App\Controllers\UserController($container);
            });
        }
        
        if (class_exists('\App\Controllers\OrderController')) {
            $container->set('App\\Controllers\\OrderController', function() use ($container) {
                return new \App\Controllers\OrderController($container);
            });
        }
        
        if (class_exists('\App\Controllers\GStoreController')) {
            $container->set('App\\Controllers\\GStoreController', function() use ($container) {
                return new \App\Controllers\GStoreController($container);
            });
        }
        
        // Register database connection
        $container->set('db', function() {
            return $this->getDb();
        });
        
        // Register configuration
        $container->set('settings', $this->config);
    }
    
    private function loadConfig()
    {
        // Load configuration files from the config directory
        $configFiles = glob(CONFIG_PATH . '/*.php');
        foreach ($configFiles as $file) {
            $key = basename($file, '.php');
            $this->config[$key] = require $file;
        }
    }

    public function config($key, $default = null)
    {
        $keys = explode('.', $key);
        $config = $this->config;

        foreach ($keys as $segment) {
            if (isset($config[$segment])) {
                $config = $config[$segment];
            } else {
                return $default;
            }
        }

        return $config;
    }
    
    private function initDatabase()
    {
        try {
            // Get the database path from config or use a default value
            $dbPath = $this->config('app.database.path', BASE_PATH . '/database/database.sqlite');
            
            // Make sure the database directory exists
            $dbDir = dirname($dbPath);
            if (!empty($dbDir) && !file_exists($dbDir)) {
                mkdir($dbDir, 0777, true);
            }
            
            // Make sure the database file is writable
            if (!empty($dbPath) && file_exists($dbPath) && !is_writable($dbPath)) {
                chmod($dbPath, 0666);
            }
            
            // Connect to the database
            $this->db = new PDO('sqlite:' . $dbPath);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            return $this->db;
        } catch (PDOException $e) {
            throw new \Exception('Database connection failed: ' . $e->getMessage());
        }
    }
    
    public function getDb()
    {
        return $this->db;
    }
    
    public function getConfig($key = null)
    {
        if ($key === null) {
            return $this->config;
        }
        
        return $this->config[$key] ?? null;
    }
}
