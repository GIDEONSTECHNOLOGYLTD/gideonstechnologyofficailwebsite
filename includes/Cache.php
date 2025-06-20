<?php
class Cache {
    private static $instance = null;
    private $path;
    private $enabled;
    private $defaultTTL;

    private function __construct() {
        $this->path = dirname(__DIR__) . '/storage/cache/';
        $this->enabled = CACHE_ENABLED;
        $this->defaultTTL = CACHE_TTL;
        $this->ensureCacheDirectory();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function ensureCacheDirectory() {
        if (!is_dir($this->path)) {
            mkdir($this->path, 0755, true);
        }
    }

    private function getFilename($key) {
        return $this->path . CACHE_PREFIX . md5($key) . '.cache';
    }

    public function set($key, $value, $ttl = null) {
        if (!$this->enabled) return false;

        $ttl = $ttl ?? $this->defaultTTL;
        $data = [
            'expires' => time() + $ttl,
            'value' => $value
        ];

        return file_put_contents(
            $this->getFilename($key),
            serialize($data)
        ) !== false;
    }

    public function get($key, $default = null) {
        if (!$this->enabled) return $default;

        $filename = $this->getFilename($key);
        if (!file_exists($filename)) {
            return $default;
        }

        $data = unserialize(file_get_contents($filename));
        if ($data['expires'] < time()) {
            $this->delete($key);
            return $default;
        }

        return $data['value'];
    }

    public function delete($key) {
        $filename = $this->getFilename($key);
        if (file_exists($filename)) {
            return unlink($filename);
        }
        return true;
    }

    public function clear() {
        $files = glob($this->path . CACHE_PREFIX . '*.cache');
        foreach ($files as $file) {
            unlink($file);
        }
        return true;
    }

    public function remember($key, $ttl, $callback) {
        $value = $this->get($key);
        if ($value !== null) {
            return $value;
        }

        $value = $callback();
        $this->set($key, $value, $ttl);
        return $value;
    }

    public function increment($key, $value = 1) {
        $current = $this->get($key, 0);
        $new = $current + $value;
        $this->set($key, $new);
        return $new;
    }

    public function decrement($key, $value = 1) {
        return $this->increment($key, -$value);
    }

    public function cleanup() {
        $files = glob($this->path . CACHE_PREFIX . '*.cache');
        foreach ($files as $file) {
            $data = unserialize(file_get_contents($file));
            if ($data['expires'] < time()) {
                unlink($file);
            }
        }
    }
}