<?php

namespace App\Core;

/**
 * Cache Manager
 * 
 * Provides caching functionality for the application
 */
class CacheManager {
    /**
     * @var string Cache directory
     */
    protected $cacheDir;
    
    /**
     * @var int Default cache lifetime in seconds
     */
    protected $defaultLifetime = 3600; // 1 hour
    
    /**
     * @var CacheManager|null Singleton instance
     */
    private static $instance = null;
    
    /**
     * Constructor
     * 
     * @param string|null $cacheDir Cache directory
     * @param int|null $defaultLifetime Default cache lifetime in seconds
     */
    private function __construct($cacheDir = null, $defaultLifetime = null) {
        $this->cacheDir = $cacheDir ?: dirname(dirname(__DIR__)) . '/storage/cache';
        
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
        
        if ($defaultLifetime !== null) {
            $this->defaultLifetime = $defaultLifetime;
        }
    }
    
    /**
     * Get singleton instance
     * 
     * @param string|null $cacheDir Cache directory
     * @param int|null $defaultLifetime Default cache lifetime in seconds
     * @return CacheManager
     */
    public static function getInstance($cacheDir = null, $defaultLifetime = null) {
        if (self::$instance === null) {
            self::$instance = new self($cacheDir, $defaultLifetime);
        }
        return self::$instance;
    }
    
    /**
     * Get an item from the cache
     * 
     * @param string $key Cache key
     * @param mixed $default Default value if key doesn't exist
     * @return mixed
     */
    public function get($key, $default = null) {
        $filename = $this->getCacheFilename($key);
        
        if (!file_exists($filename)) {
            return $default;
        }
        
        $content = file_get_contents($filename);
        $data = unserialize($content);
        
        if (!is_array($data) || !isset($data['expires_at']) || !isset($data['value'])) {
            return $default;
        }
        
        if ($data['expires_at'] < time()) {
            $this->delete($key);
            return $default;
        }
        
        return $data['value'];
    }
    
    /**
     * Store an item in the cache
     * 
     * @param string $key Cache key
     * @param mixed $value Value to store
     * @param int|null $lifetime Cache lifetime in seconds (null for default)
     * @return bool
     */
    public function set($key, $value, $lifetime = null) {
        $lifetime = $lifetime ?: $this->defaultLifetime;
        $filename = $this->getCacheFilename($key);
        
        $data = [
            'expires_at' => time() + $lifetime,
            'value' => $value
        ];
        
        $content = serialize($data);
        
        return file_put_contents($filename, $content) !== false;
    }
    
    /**
     * Delete an item from the cache
     * 
     * @param string $key Cache key
     * @return bool
     */
    public function delete($key) {
        $filename = $this->getCacheFilename($key);
        
        if (file_exists($filename)) {
            return unlink($filename);
        }
        
        return true;
    }
    
    /**
     * Check if an item exists in the cache and is not expired
     * 
     * @param string $key Cache key
     * @return bool
     */
    public function has($key) {
        $filename = $this->getCacheFilename($key);
        
        if (!file_exists($filename)) {
            return false;
        }
        
        $content = file_get_contents($filename);
        $data = unserialize($content);
        
        if (!is_array($data) || !isset($data['expires_at'])) {
            return false;
        }
        
        if ($data['expires_at'] < time()) {
            $this->delete($key);
            return false;
        }
        
        return true;
    }
    
    /**
     * Clear all cache items
     * 
     * @return bool
     */
    public function clear() {
        $files = glob($this->cacheDir . '/*.cache');
        
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        
        return true;
    }
    
    /**
     * Get cache filename for a key
     * 
     * @param string $key Cache key
     * @return string
     */
    protected function getCacheFilename($key) {
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $key);
        return $this->cacheDir . '/' . $safeName . '.cache';
    }
    
    /**
     * Get cache stats
     * 
     * @return array
     */
    public function getStats() {
        $files = glob($this->cacheDir . '/*.cache');
        $count = count($files);
        $size = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $size += filesize($file);
            }
        }
        
        return [
            'count' => $count,
            'size' => $size,
            'dir' => $this->cacheDir
        ];
    }
}
