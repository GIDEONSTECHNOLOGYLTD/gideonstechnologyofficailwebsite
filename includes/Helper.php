<?php
class Helper {
    // String Utilities
    public static function slugify($text) {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        return strtolower($text);
    }

    public static function truncate($string, $length = 100, $append = '...') {
        if (strlen($string) > $length) {
            $string = substr($string, 0, $length) . $append;
        }
        return $string;
    }

    // Array/Object Utilities
    public static function arrayToObject($array) {
        return json_decode(json_encode($array));
    }

    public static function objectToArray($object) {
        return json_decode(json_encode($object), true);
    }

    // Date/Time Utilities
    public static function formatDate($date, $format = 'Y-m-d H:i:s') {
        return date($format, strtotime($date));
    }

    public static function timeAgo($datetime) {
        $time = strtotime($datetime);
        $now = time();
        $diff = $now - $time;
        
        if ($diff < 60) return 'just now';
        if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
        if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
        if ($diff < 604800) return floor($diff / 86400) . ' days ago';
        return date('F j, Y', $time);
    }

    // File System Utilities
    public static function ensureDirectoryExists($path) {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }

    public static function getFileExtension($filename) {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    public static function sanitizeFilename($filename) {
        $info = pathinfo($filename);
        $ext  = isset($info['extension']) ? '.' . $info['extension'] : '';
        $name = isset($info['filename']) ? $info['filename'] : '';
        
        $name = preg_replace("/[^a-zA-Z0-9\s-]/", '', $name);
        $name = strtolower(trim($name));
        $name = preg_replace("/[\s-]+/", "-", $name);
        
        return $name . $ext;
    }

    // Security Utilities
    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length));
    }

    public static function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    // Validation Utilities
    public static function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function isValidUrl($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    // Format Utilities
    public static function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
    }

    public static function formatCurrency($amount, $currency = 'USD') {
        return number_format($amount, 2) . ' ' . $currency;
    }

    // Debug Utilities
    public static function debug($var) {
        if (DEBUG) {
            echo '<pre>';
            var_dump($var);
            echo '</pre>';
        }
    }

    public static function logDebug($message, $context = []) {
        if (DEBUG) {
            error_log(date('Y-m-d H:i:s') . ' - ' . $message . ' - ' . json_encode($context));
        }
    }
}
?>