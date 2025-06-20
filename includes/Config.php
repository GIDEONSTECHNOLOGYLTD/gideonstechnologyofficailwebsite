<?php
class Config {
    private static $instance = null;
    private $config = [];
    private $configPath;

    private function __construct() {
        $this->configPath = dirname(__DIR__) . '/config/';
        $this->loadConfigurations();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadConfigurations() {
        foreach (glob($this->configPath . '*.php') as $file) {
            $key = basename($file, '.php');
            $this->config[$key] = require $file;
        }
    }

    public function get($key, $default = null) {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $segment) {
            if (!isset($value[$segment])) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    public function set($key, $value) {
        $keys = explode('.', $key);
        $config = &$this->config;

        foreach ($keys as $index => $segment) {
            if ($index === count($keys) - 1) {
                $config[$segment] = $value;
                break;
            }

            if (!isset($config[$segment])) {
                $config[$segment] = [];
            }

            $config = &$config[$segment];
        }

        return $this;
    }

    public function has($key) {
        return $this->get($key) !== null;
    }

    public function all() {
        return $this->config;
    }

    public function load($file) {
        $path = $this->configPath . $file . '.php';
        if (file_exists($path)) {
            $key = basename($file, '.php');
            $this->config[$key] = require $path;
            return true;
        }
        return false;
    }

    public function save($key) {
        if (!isset($this->config[$key])) {
            return false;
        }

        $path = $this->configPath . $key . '.php';
        $content = "<?php\nreturn " . var_export($this->config[$key], true) . ";\n";
        
        return file_put_contents($path, $content) !== false;
    }

    public function saveAll() {
        foreach (array_keys($this->config) as $key) {
            $this->save($key);
        }
    }

    public function merge($key, array $values) {
        $current = $this->get($key, []);
        if (is_array($current)) {
            $this->set($key, array_merge($current, $values));
            return true;
        }
        return false;
    }

    public function environment() {
        return $this->get('app.environment', 'production');
    }

    public function isDevelopment() {
        return $this->environment() === 'development';
    }

    public function isProduction() {
        return $this->environment() === 'production';
    }

    public function isTesting() {
        return $this->environment() === 'testing';
    }

    public function getConfigPath() {
        return $this->configPath;
    }

    public function setConfigPath($path) {
        $this->configPath = rtrim($path, '/') . '/';
        $this->loadConfigurations();
        return $this;
    }
}