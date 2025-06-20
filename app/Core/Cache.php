<?php
namespace App\Core;

class Cache {
    private $cacheDir;
    private $ttl = 3600; // Default 1 hour

    public function __construct() {
        $this->cacheDir = ROOT_PATH . '/cache';
        if (!file_exists($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    public function set($key, $value, $ttl = null) {
        $ttl = $ttl ?? $this->ttl;
        $cacheFile = $this->getCacheFile($key);
        
        $data = [
            'value' => $value,
            'expires' => time() + $ttl
        ];
        
        file_put_contents($cacheFile, json_encode($data));
    }

    public function get($key, $default = null) {
        $cacheFile = $this->getCacheFile($key);
        
        if (!file_exists($cacheFile)) {
            return $default;
        }

        $data = json_decode(file_get_contents($cacheFile), true);
        
        if ($data['expires'] < time()) {
            $this->delete($key);
            return $default;
        }

        return $data['value'];
    }

    public function delete($key) {
        $cacheFile = $this->getCacheFile($key);
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
    }

    public function clear() {
        $files = glob($this->cacheDir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function increment($key) {
        $value = $this->get($key, 0);
        $this->set($key, $value + 1);
    }

    public function decrement($key) {
        $value = $this->get($key, 0);
        $this->set($key, max(0, $value - 1));
    }

    public function ttl($key) {
        $cacheFile = $this->getCacheFile($key);
        
        if (!file_exists($cacheFile)) {
            return 0;
        }

        $data = json_decode(file_get_contents($cacheFile), true);
        return $data['expires'] - time();
    }

    private function getCacheFile($key) {
        return $this->cacheDir . '/' . md5($key);
    }
}
