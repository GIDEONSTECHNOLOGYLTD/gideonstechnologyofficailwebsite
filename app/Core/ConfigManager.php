<?php
/**
 * Config Manager Class
 * 
 * Provides a centralized way to manage application configuration
 * Supports both file-based and database-stored configuration
 */

namespace App\Core;

use App\Database\DatabaseManager;
use App\Utilities\Logger;

class ConfigManager
{
    /**
     * @var ConfigManager Singleton instance
     */
    private static $instance = null;
    
    /**
     * @var array Configuration values
     */
    private $config = [];
    
    /**
     * @var bool Whether config has been loaded
     */
    private $loaded = false;
    
    /**
     * @var string Table name for database config
     */
    private $configTable = 'system_config';
    
    /**
     * Private constructor for singleton pattern
     */
    private function __construct()
    {
        // Load default configuration
        $this->loadDefaults();
    }
    
    /**
     * Get singleton instance
     * 
     * @return ConfigManager
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Load default configuration values
     */
    private function loadDefaults(): void
    {
        // Default configuration values
        $this->config = [
            // General
            'site_name' => 'GIDEONS TECHNOLOGY LTD',
            'site_description' => 'Professional technology services',
            'admin_email' => 'admin@gideonstechnology.com',
            'timezone' => 'Europe/London',
            
            // Display
            'items_per_page' => 12,
            'enable_cache' => true,
            'cache_time' => 3600,
            
            // Payment
            'payment_test_mode' => true,
            'paypal_enabled' => true,
            'stripe_enabled' => true,
            
            // Features
            'enable_blog' => true,
            'enable_newsletter' => true,
            'maintenance_mode' => false,
            'maintenance_message' => 'Site is under maintenance. Please check back later.',
            
            // SEO
            'enable_structured_data' => true,
            'enable_open_graph' => true,
            'google_analytics_id' => '',
            'facebook_pixel_id' => '',
        ];
    }
    
    /**
     * Load configuration from database
     * 
     * @param bool $force Force reload
     * @return bool Success
     */
    public function load(bool $force = false): bool
    {
        // Skip if already loaded and not forcing
        if ($this->loaded && !$force) {
            return true;
        }
        
        try {
            // Get database connection
            $db = DatabaseManager::getInstance()->getConnection();
            
            // Check if config table exists
            $tableExists = false;
            $tables = $db->query("SHOW TABLES LIKE '{$this->configTable}'")->fetchAll();
            $tableExists = count($tables) > 0;
            
            // Create table if it doesn't exist
            if (!$tableExists) {
                $this->createConfigTable();
                $this->seedConfigTable();
            } else {
                // Load config from database
                $stmt = $db->query("SELECT * FROM {$this->configTable}");
                while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                    $this->config[$row['config_key']] = $this->parseConfigValue($row['config_value'], $row['config_type']);
                }
            }
            
            $this->loaded = true;
            return true;
        } catch (\Exception $e) {
            Logger::error('Failed to load configuration: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create the config table
     */
    private function createConfigTable(): void
    {
        $db = DatabaseManager::getInstance()->getConnection();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$this->configTable} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            config_key VARCHAR(255) NOT NULL UNIQUE,
            config_value TEXT,
            config_type VARCHAR(20) DEFAULT 'string',
            display_name VARCHAR(255) NOT NULL,
            description TEXT,
            category VARCHAR(50) DEFAULT 'general',
            is_public BOOLEAN DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        $db->exec($sql);
        Logger::info("Created system_config table");
    }
    
    /**
     * Seed the config table with default values
     */
    private function seedConfigTable(): void
    {
        $db = DatabaseManager::getInstance()->getConnection();
        
        // Define config metadata
        $configItems = [
            [
                'key' => 'site_name',
                'value' => 'GIDEONS TECHNOLOGY LTD',
                'type' => 'string',
                'display_name' => 'Site Name',
                'description' => 'The name of your site',
                'category' => 'general',
                'is_public' => true
            ],
            [
                'key' => 'site_description',
                'value' => 'Professional technology services',
                'type' => 'string',
                'display_name' => 'Site Description',
                'description' => 'A short description of your site',
                'category' => 'general',
                'is_public' => true
            ],
            [
                'key' => 'admin_email',
                'value' => 'admin@gideonstechnology.com',
                'type' => 'string',
                'display_name' => 'Admin Email',
                'description' => 'The main administrator email address',
                'category' => 'general',
                'is_public' => false
            ],
            [
                'key' => 'paypal_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'display_name' => 'Enable PayPal',
                'description' => 'Enable PayPal payment gateway',
                'category' => 'payment',
                'is_public' => false
            ],
            [
                'key' => 'stripe_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'display_name' => 'Enable Stripe',
                'description' => 'Enable Stripe payment gateway',
                'category' => 'payment',
                'is_public' => false
            ],
            [
                'key' => 'paypal_client_id',
                'value' => '',
                'type' => 'string',
                'display_name' => 'PayPal Client ID',
                'description' => 'The client ID from your PayPal developer account',
                'category' => 'payment',
                'is_public' => false
            ],
            [
                'key' => 'stripe_publishable_key',
                'value' => '',
                'type' => 'string',
                'display_name' => 'Stripe Publishable Key',
                'description' => 'Your Stripe publishable key',
                'category' => 'payment',
                'is_public' => false
            ],
            [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'type' => 'boolean',
                'display_name' => 'Maintenance Mode',
                'description' => 'Put the site into maintenance mode',
                'category' => 'system',
                'is_public' => false
            ]
        ];
        
        // Insert items
        $stmt = $db->prepare("INSERT INTO {$this->configTable} 
            (config_key, config_value, config_type, display_name, description, category, is_public) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
            
        foreach ($configItems as $item) {
            $stmt->execute([
                $item['key'],
                $item['value'],
                $item['type'],
                $item['display_name'],
                $item['description'],
                $item['category'],
                $item['is_public'] ? 1 : 0
            ]);
        }
        
        Logger::info("Seeded system_config table with default values");
    }
    
    /**
     * Get a configuration value
     * 
     * @param string $key Config key
     * @param mixed $default Default value if not found
     * @return mixed Config value
     */
    public function get(string $key, $default = null)
    {
        // Load config if not already loaded
        if (!$this->loaded) {
            $this->load();
        }
        
        return $this->config[$key] ?? $default;
    }
    
    /**
     * Set a configuration value
     * 
     * @param string $key Config key
     * @param mixed $value Config value
     * @param bool $saveToDb Whether to save to database
     * @return bool Success
     */
    public function set(string $key, $value, bool $saveToDb = true): bool
    {
        // Update in-memory config
        $this->config[$key] = $value;
        
        // Save to database if requested
        if ($saveToDb) {
            return $this->saveToDb($key, $value);
        }
        
        return true;
    }
    
    /**
     * Save a config value to the database
     * 
     * @param string $key Config key
     * @param mixed $value Config value
     * @return bool Success
     */
    private function saveToDb(string $key, $value): bool
    {
        try {
            $db = DatabaseManager::getInstance()->getConnection();
            
            // Determine value type
            $type = $this->getValueType($value);
            
            // Convert value to string for storage
            $stringValue = $this->valueToString($value);
            
            // Check if key exists
            $stmt = $db->prepare("SELECT COUNT(*) FROM {$this->configTable} WHERE config_key = ?");
            $stmt->execute([$key]);
            $exists = (int)$stmt->fetchColumn() > 0;
            
            if ($exists) {
                // Update existing key
                $stmt = $db->prepare("UPDATE {$this->configTable} SET config_value = ?, config_type = ? WHERE config_key = ?");
                $stmt->execute([$stringValue, $type, $key]);
            } else {
                // Insert new key (with minimal metadata)
                $stmt = $db->prepare("INSERT INTO {$this->configTable} 
                    (config_key, config_value, config_type, display_name, category) 
                    VALUES (?, ?, ?, ?, 'general')");
                $stmt->execute([$key, $stringValue, $type, ucwords(str_replace('_', ' ', $key))]);
            }
            
            return true;
        } catch (\Exception $e) {
            Logger::error('Failed to save config: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get the type of a value
     * 
     * @param mixed $value
     * @return string
     */
    private function getValueType($value): string
    {
        if (is_bool($value)) {
            return 'boolean';
        } elseif (is_int($value)) {
            return 'integer';
        } elseif (is_float($value)) {
            return 'float';
        } elseif (is_array($value)) {
            return 'array';
        } else {
            return 'string';
        }
    }
    
    /**
     * Convert a value to string for storage
     * 
     * @param mixed $value
     * @return string
     */
    private function valueToString($value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif (is_array($value)) {
            return json_encode($value);
        } else {
            return (string)$value;
        }
    }
    
    /**
     * Parse a config value from string based on type
     * 
     * @param string $value
     * @param string $type
     * @return mixed
     */
    private function parseConfigValue(string $value, string $type)
    {
        switch ($type) {
            case 'boolean':
                return $value === 'true' || $value === '1';
            case 'integer':
                return (int)$value;
            case 'float':
                return (float)$value;
            case 'array':
                return json_decode($value, true);
            default:
                return $value;
        }
    }
    
    /**
     * Get all configuration values
     * 
     * @param string|null $category Filter by category
     * @param bool $includePrivate Whether to include private configs
     * @return array Config values
     */
    public function getAll(?string $category = null, bool $includePrivate = true): array
    {
        try {
            $db = DatabaseManager::getInstance()->getConnection();
            
            $sql = "SELECT * FROM {$this->configTable}";
            $params = [];
            
            if ($category !== null) {
                $sql .= " WHERE category = ?";
                $params[] = $category;
                
                if (!$includePrivate) {
                    $sql .= " AND is_public = 1";
                }
            } elseif (!$includePrivate) {
                $sql .= " WHERE is_public = 1";
            }
            
            $sql .= " ORDER BY category, display_name";
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            
            $result = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $result[$row['config_key']] = [
                    'value' => $this->parseConfigValue($row['config_value'], $row['config_type']),
                    'display_name' => $row['display_name'],
                    'description' => $row['description'],
                    'category' => $row['category'],
                    'type' => $row['config_type'],
                    'is_public' => (bool)$row['is_public']
                ];
            }
            
            return $result;
        } catch (\Exception $e) {
            Logger::error('Failed to get all config: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get all available config categories
     * 
     * @return array Categories
     */
    public function getCategories(): array
    {
        try {
            $db = DatabaseManager::getInstance()->getConnection();
            
            $sql = "SELECT DISTINCT category FROM {$this->configTable} ORDER BY category";
            $stmt = $db->query($sql);
            
            $categories = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $categories[] = $row['category'];
            }
            
            return $categories;
        } catch (\Exception $e) {
            Logger::error('Failed to get config categories: ' . $e->getMessage());
            return [];
        }
    }
}
