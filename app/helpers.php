<?php

if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function env($key, $default = null) {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        if (strlen($value) > 1 && $value[0] === '"' && $value[strlen($value) - 1] === '"') {
            return substr($value, 1, -1);
        }

        return $value;
    }
}

if (!function_exists('base_path')) {
    /**
     * Get the application base path.
     *
     * @param  string  $path
     * @return string
     */
    function base_path($path = '') {
        return BASE_PATH . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (!function_exists('config_path')) {
    /**
     * Get the configuration path.
     *
     * @param  string  $path
     * @return string
     */
    function config_path($path = '') {
        return CONFIG_PATH . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (!function_exists('storage_path')) {
    /**
     * Get the storage path.
     *
     * @param  string  $path
     * @return string
     */
    function storage_path($path = '') {
        return STORAGE_PATH . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (!function_exists('app_path')) {
    /**
     * Get the application path.
     *
     * @param  string  $path
     * @return string
     */
    function app_path($path = '') {
        return APP_PATH . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (!function_exists('config')) {
    /**
     * Get / set the specified configuration value.
     *
     * @param  array|string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    function config($key = null, $default = null) {
        if (is_null($key)) {
            return [];
        }

        if (is_array($key)) {
            return $key;
        }

        $configPath = config_path(explode('.', $key)[0] . '.php');
        if (!file_exists($configPath)) {
            return $default;
        }

        $config = require $configPath;
        $keys = explode('.', $key);
        array_shift($keys);

        foreach ($keys as $segment) {
            if (!is_array($config) || !array_key_exists($segment, $config)) {
                return $default;
            }
            $config = $config[$segment];
        }

        return $config;
    }
}

/**
 * Get the app container instance or resolve a service from the container
 *
 * @param string|null $abstract Service to resolve from the container
 * @param array $parameters Parameters to pass to the service constructor
 * @return mixed
 */
function app($abstract = null, array $parameters = [])
{
    static $app;
    
    if (!$app) {
        $app = new \App\Core\App();
    }
    
    if ($abstract === null) {
        return $app;
    }
    
    return $app->make($abstract, $parameters);
}

if (!function_exists('request')) {
    /**
     * Get the current request instance
     *
     * @return \App\Core\Request
     */
    function request()
    {
        static $request;
        
        if (!$request) {
            $request = new \App\Core\Request();
        }
        
        return $request;
    }
}
