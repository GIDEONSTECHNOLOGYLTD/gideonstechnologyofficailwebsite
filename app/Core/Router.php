<?php
/**
 * Router Class
 * 
 * This class provides routing functionality for the application.
 */

namespace App\Core;

class Router {
    /**
     * @var array Stores registered routes
     */
    private static $routes = [];
    
    /**
     * Register a route
     * 
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $pattern Route pattern
     * @param callable $handler Route handler
     * @return void
     */
    public static function register(string $method, string $pattern, callable $handler): void {
        $key = self::generateRouteKey($method, $pattern);
        self::$routes[$key] = $handler;
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
    public static function getRoutes(): array {
        return self::$routes;
    }
    
    /**
     * Clear all registered routes
     * 
     * @return void
     */
    public static function clearRoutes(): void {
        self::$routes = [];
    }
}
