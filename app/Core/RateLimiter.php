<?php
namespace App\Core;

class RateLimiter {
    private $redis;
    private const DEFAULT_DECAY_MINUTES = 1;
    private const DEFAULT_MAX_ATTEMPTS = 60;

    public function __construct() {
        $this->redis = new \Redis();
        $this->redis->connect('127.0.0.1', 6379);
    }

    public function check($key, $maxAttempts = null, $decayMinutes = null) {
        $maxAttempts = $maxAttempts ?? self::DEFAULT_MAX_ATTEMPTS;
        $decayMinutes = $decayMinutes ?? self::DEFAULT_DECAY_MINUTES;

        $key = $this->formatKey($key);
        $current = $this->getCurrentAttempts($key);

        if ($current >= $maxAttempts) {
            $ttl = $this->redis->ttl($key);
            throw new \Exception(
                "Too many attempts. Please try again in " . 
                ceil($ttl / 60) . " minutes."
            );
        }

        $this->increment($key, $decayMinutes);
    }

    private function getCurrentAttempts($key) {
        return (int) $this->redis->get($key) ?: 0;
    }

    private function increment($key, $decayMinutes) {
        $this->redis->multi()
            ->incr($key)
            ->expire($key, $decayMinutes * 60)
            ->exec();
    }

    private function formatKey($key) {
        return sprintf(
            'rate_limit:%s:%s',
            $key,
            $this->resolveRequestSignature()
        );
    }

    private function resolveRequestSignature() {
        return sha1(
            implode('|', [
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ])
        );
    }

    public function clear($key) {
        $this->redis->del($this->formatKey($key));
    }

    public function remaining($key, $maxAttempts = null) {
        $maxAttempts = $maxAttempts ?? self::DEFAULT_MAX_ATTEMPTS;
        $key = $this->formatKey($key);
        $current = $this->getCurrentAttempts($key);
        
        return max(0, $maxAttempts - $current);
    }

    public function reset($key) {
        $this->clear($key);
    }

    public function isAllowed($key, $maxAttempts = null) {
        try {
            $this->check($key, $maxAttempts);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
