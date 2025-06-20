<?php

if (!function_exists('env')) {
    function env($key, $default = null)
    {
        $value = getenv($key);
        
        if ($value === false) {
            return $default;
        }
        
        switch (strtolower($value)) {
            case 'true':
                return true;
            case 'false':
                return false;
            case 'null':
                return null;
        }
        
        if (preg_match('/\d+\.\d+/', $value)) {
            return (float) $value;
        }
        
        if (preg_match('/\d+/', $value)) {
            return (int) $value;
        }
        
        return $value;
    }
}
