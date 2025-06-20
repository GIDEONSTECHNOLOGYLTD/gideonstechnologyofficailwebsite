<?php

namespace App\Core;

/**
 * File-based Rate Limiter
 * 
 * Provides rate limiting functionality using file storage
 */
class FileRateLimiter
{
    /**
     * @var string The directory to store rate limiting data
     */
    protected $storageDir;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->storageDir = dirname(__DIR__, 2) . '/storage/framework/cache/';
        
        // Ensure the storage directory exists
        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0755, true);
        }
    }

    /**
     * Check if there are too many attempts for the given key
     *
     * @param string $key The rate limiting key
     * @param int $maxAttempts Maximum number of attempts allowed
     * @param int $decayMinutes Time window in minutes
     * @return bool True if too many attempts
     */
    public function tooManyAttempts(string $key, int $maxAttempts, int $decayMinutes): bool
    {
        $file = $this->getFilePath($key);
        
        if (!file_exists($file)) {
            return false;
        }
        
        $content = json_decode(file_get_contents($file), true);
        $attempts = $content['attempts'] ?? [];
        
        // Filter out old attempts
        $cutoff = time() - ($decayMinutes * 60);
        $attempts = array_filter($attempts, function($timestamp) use ($cutoff) {
            return $timestamp >= $cutoff;
        });
        
        return count($attempts) >= $maxAttempts;
    }

    /**
     * Get the number of seconds until the lock expires
     *
     * @param string $key The rate limiting key
     * @return int Seconds until available
     */
    public function availableIn(string $key): int
    {
        $file = $this->getFilePath($key);
        
        if (!file_exists($file)) {
            return 0;
        }
        
        $content = json_decode(file_get_contents($file), true);
        $attempts = $content['attempts'] ?? [];
        $decayMinutes = $content['decay_minutes'] ?? 60;
        
        if (empty($attempts)) {
            return 0;
        }
        
        $oldestAttempt = min($attempts);
        $lockExpires = $oldestAttempt + ($decayMinutes * 60);
        
        return max(0, $lockExpires - time());
    }

    /**
     * Record a new attempt for the given key
     *
     * @param string $key The rate limiting key
     * @param int $decayMinutes Time window in minutes
     * @return void
     */
    public function hit(string $key, int $decayMinutes = 60): void
    {
        $file = $this->getFilePath($key);
        
        if (file_exists($file)) {
            $content = json_decode(file_get_contents($file), true);
            $attempts = $content['attempts'] ?? [];
            $decayMinutes = $content['decay_minutes'] ?? $decayMinutes;
        } else {
            $attempts = [];
        }
        
        // Add current attempt
        $attempts[] = time();
        
        // Save to file
        file_put_contents($file, json_encode([
            'attempts' => $attempts,
            'decay_minutes' => $decayMinutes
        ]));
    }

    /**
     * Clear all attempts for the given key
     *
     * @param string $key The rate limiting key
     * @return void
     */
    public function clear(string $key): void
    {
        $file = $this->getFilePath($key);
        
        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * Get the file path for the given key
     *
     * @param string $key The rate limiting key
     * @return string File path
     */
    protected function getFilePath(string $key): string
    {
        $hash = md5($key);
        return $this->storageDir . $hash . '.json';
    }
}
