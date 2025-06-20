<?php
/**
 * RouteLoader Class
 * 
 * This class provides a centralized way to load routes from route files
 * and integrates with RouteRegistry to prevent duplicate route registrations.
 */

namespace App\Core;

use Slim\App;
use Psr\Container\ContainerInterface;
use App\Utilities\Logger;

class RouteLoader {
    /**
     * @var array Route files that have been loaded
     */
    private static $loadedFiles = [];
    
    /**
     * Load routes from a file
     * 
     * @param App $app Slim application instance
     * @param ContainerInterface $container DI container
     * @param string $file Path to route file (relative to project root)
     * @param string $flagName Optional flag name for backward compatibility
     * @return bool True if routes were loaded, false if already loaded
     */
    public static function loadRoutes(
        App $app, 
        ContainerInterface $container, 
        string $file, 
        string $flagName = null
    ): bool {
        // Get the absolute path to the route file
        $absolutePath = dirname(dirname(__DIR__)) . '/' . $file;
        
        // Check if file exists
        if (!file_exists($absolutePath)) {
            if (class_exists('\App\Utilities\Logger')) {
                Logger::warning("Route file not found: $absolutePath");
            }
            return false;
        }
        
        // Check if file has already been loaded
        if (isset(self::$loadedFiles[$absolutePath])) {
            if (class_exists('\App\Utilities\Logger')) {
                Logger::debug("Route file already loaded: $file");
            }
            return false;
        }
        
        // Support for legacy flag-based loading
        if ($flagName !== null) {
            if (defined($flagName)) {
                if (class_exists('\App\Utilities\Logger')) {
                    Logger::debug("Route flag already defined: $flagName");
                }
                return false;
            }
            
            // Define the flag for backward compatibility
            define($flagName, true);
        }
        
        // Mark file as loaded
        self::$loadedFiles[$absolutePath] = true;
        
        // Load routes from the file
        if (class_exists('\App\Utilities\Logger')) {
            Logger::info("Loading routes from: $file");
        }
        
        $routes = require $absolutePath;
        
        if (is_callable($routes)) {
            $routes($app, $container);
            return true;
        }
        
        return false;
    }
    
    /**
     * Reset the loaded files tracking
     * This is mainly used for testing or when restarting the application
     */
    public static function reset(): void {
        self::$loadedFiles = [];
    }
    
    /**
     * Get list of loaded route files
     * 
     * @return array List of loaded route files
     */
    public static function getLoadedFiles(): array {
        return array_keys(self::$loadedFiles);
    }
}
