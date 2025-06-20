<?php

namespace App\Core;

/**
 * Query Cache
 * 
 * Provides caching for database queries to improve performance
 */
class QueryCache {
    /**
     * @var CacheManager
     */
    protected $cache;
    
    /**
     * @var int Default cache lifetime in seconds
     */
    protected $defaultLifetime = 600; // 10 minutes
    
    /**
     * @var QueryCache|null Singleton instance
     */
    private static $instance = null;
    
    /**
     * Constructor
     * 
     * @param int|null $defaultLifetime Default cache lifetime in seconds
     */
    private function __construct($defaultLifetime = null) {
        $this->cache = CacheManager::getInstance();
        
        if ($defaultLifetime !== null) {
            $this->defaultLifetime = $defaultLifetime;
        }
    }
    
    /**
     * Get singleton instance
     * 
     * @param int|null $defaultLifetime Default cache lifetime in seconds
     * @return QueryCache
     */
    public static function getInstance($defaultLifetime = null) {
        if (self::$instance === null) {
            self::$instance = new self($defaultLifetime);
        }
        return self::$instance;
    }
    
    /**
     * Get cached query result or execute query and cache result
     * 
     * @param string $sql SQL query
     * @param array $params Query parameters
     * @param callable $callback Function to execute query if not cached
     * @param int|null $lifetime Cache lifetime in seconds (null for default)
     * @param string|null $tag Cache tag for invalidation
     * @return mixed Query result
     */
    public function getOrSet($sql, array $params, callable $callback, $lifetime = null, $tag = null) {
        $cacheKey = $this->generateCacheKey($sql, $params);
        
        // Add tag to cache key if provided
        if ($tag) {
            $cacheKey .= '_' . $tag;
        }
        
        // Try to get from cache
        $result = $this->cache->get($cacheKey);
        
        if ($result !== null) {
            return $result;
        }
        
        // Execute query
        $result = $callback();
        
        // Cache result
        $this->cache->set($cacheKey, $result, $lifetime ?: $this->defaultLifetime);
        
        return $result;
    }
    
    /**
     * Invalidate cache for a specific tag
     * 
     * @param string $tag Cache tag
     * @return bool
     */
    public function invalidateTag($tag) {
        $files = glob($this->cache->getCacheDir() . '/*_' . $tag . '.cache');
        
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        
        return true;
    }
    
    /**
     * Invalidate cache for a table
     * 
     * @param string $table Table name
     * @return bool
     */
    public function invalidateTable($table) {
        return $this->invalidateTag('table_' . $table);
    }
    
    /**
     * Generate a cache key for a query
     * 
     * @param string $sql SQL query
     * @param array $params Query parameters
     * @return string Cache key
     */
    protected function generateCacheKey($sql, array $params) {
        // Normalize SQL by removing extra whitespace
        $normalizedSql = preg_replace('/\s+/', ' ', trim($sql));
        
        // Generate key based on SQL and parameters
        return 'query_' . md5($normalizedSql . '_' . serialize($params));
    }
}
