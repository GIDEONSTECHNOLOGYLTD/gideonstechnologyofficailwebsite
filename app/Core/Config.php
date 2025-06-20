<?php

namespace App\Core;

/**
 * Configuration management class
 * Handles loading configuration from multiple sources with a unified interface
 */
class Config
{
    private static $configs = [];
    private static $rootPath;
    private static $locations = [
        'app' => '/app/config/',
        'root' => '/config/'
    ];

    /**
     * Set the root path for config files
     *
     * @param string $path
     * @return void
     */
    public static function setRootPath($path)
    {
        self::$rootPath = rtrim($path, '/');
    }

    /**
     * Get a configuration value
     *
     * @param string $key Dot notation key (e.g., 'app.debug')
     * @param mixed $default Default value if configuration not found
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        if (empty(self::$rootPath)) {
            self::setRootPath(dirname(__DIR__, 2));
        }

        $parts = explode('.', $key);
        $file = array_shift($parts);
        
        // Load the config file if not already loaded
        if (!isset(self::$configs[$file])) {
            self::load($file);
        }
        
        // If still not loaded, return default
        if (!isset(self::$configs[$file])) {
            return $default;
        }
        
        // Navigate through the config array using the dot notation
        $config = self::$configs[$file];
        foreach ($parts as $part) {
            if (!is_array($config) || !array_key_exists($part, $config)) {
                return $default;
            }
            $config = $config[$part];
        }
        
        return $config;
    }

    /**
     * Load a configuration file
     *
     * @param string $file The configuration file name (without extension)
     * @return void
     */
    public static function load($file)
    {
        foreach (self::$locations as $locKey => $location) {
            $path = self::$rootPath . $location . $file . '.php';
            if (file_exists($path)) {
                self::$configs[$file] = require $path;
                return;
            }
        }
    }

    /**
     * Set a configuration value
     *
     * @param string $key Dot notation key
     * @param mixed $value The value to set
     * @return void
     */
    public static function set($key, $value)
    {
        $parts = explode('.', $key);
        $file = array_shift($parts);
        
        // Ensure the config file is loaded
        if (!isset(self::$configs[$file])) {
            self::load($file);
            
            if (!isset(self::$configs[$file])) {
                self::$configs[$file] = [];
            }
        }
        
        // Navigate through the config array and set the value
        $config = &self::$configs[$file];
        foreach ($parts as $i => $part) {
            if ($i === count($parts) - 1) {
                $config[$part] = $value;
            } else {
                if (!isset($config[$part]) || !is_array($config[$part])) {
                    $config[$part] = [];
                }
                $config = &$config[$part];
            }
        }
    }
    
    /**
     * Check if a configuration exists
     *
     * @param string $key Dot notation key
     * @return bool
     */
    public static function has($key)
    {
        return self::get($key, '__CONFIG_NOT_FOUND__') !== '__CONFIG_NOT_FOUND__';
    }
    
    /**
     * Get all configuration values
     *
     * @return array
     */
    public static function all()
    {
        // Try to load all configuration files if not loaded yet
        $configFiles = ['app', 'database', 'routes', 'constants', 'services', 'mail'];
        
        foreach ($configFiles as $file) {
            if (!isset(self::$configs[$file])) {
                self::load($file);
            }
        }
        
        return self::$configs;
    }
}