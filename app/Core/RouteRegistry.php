<?php
/**
 * RouteRegistry Class
 * 
 * This class provides a registry for routes to prevent duplicate route registrations.
 * It's used in conjunction with the global flag system to ensure routes are only registered once.
 */

namespace App\Core;

class RouteRegistry {
    /**
     * @var array Stores registered routes
     */
    private static $registeredRoutes = [];
    
    /**
     * Check if a route is already registered
     * 
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $pattern Route pattern
     * @return bool True if the route is already registered, false otherwise
     */
    public static function isRegistered(string $method, string $pattern): bool {
        $key = self::generateRouteKey($method, $pattern);
        return isset(self::$registeredRoutes[$key]);
    }
    
    /**
     * Register a route
     * 
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $pattern Route pattern
     * @return bool True if the route was registered, false if it was already registered
     */
    public static function register(string $method, string $pattern): bool {
        $key = self::generateRouteKey($method, $pattern);
        
        if (isset(self::$registeredRoutes[$key])) {
            return false;
        }
        
        self::$registeredRoutes[$key] = true;
        return true;
    }
    
    /**
     * Clear all registered routes
     * 
     * This is useful for resetting the registry during application initialization
     * to prevent issues with duplicate route registrations.
     * 
     * @return void
     */
    public static function clear(): void {
        self::$registeredRoutes = [];
    }
    
    /**
     * Generate a unique key for a route
     * 
     * @param string $method HTTP method
     * @param string $pattern Route pattern
     * @return string Unique key
     */
    private static function generateRouteKey(string $method, string $pattern): string {
        return strtoupper($method) . '|' . $pattern;
    }
    
    /**
     * Get all registered routes
     * 
     * @return array Array of registered routes
     */
    public static function getRegisteredRoutes(): array {
        return array_keys(self::$registeredRoutes);
    }
    
    /**
     * Clear all registered routes
     * 
     * @return void
     */
    public static function clearRegisteredRoutes(): void {
        self::$registeredRoutes = [];
    }
}
