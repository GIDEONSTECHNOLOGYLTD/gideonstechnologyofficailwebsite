<?php
declare(strict_types=1);

namespace App\Core;

class Migrate {
    private static $migrationsDir;
    
    private static function initialize(): void {
        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH', dirname(dirname(__DIR__)));
        }
        self::$migrationsDir = ROOT_PATH . '/database/migrations';
    }
    
    public static function up(null|string $migrationName = null): void {
        self::initialize();
        
        $files = glob(self::$migrationsDir . '/*.php');
        
        if (empty($files)) {
            echo "No migrations found.\n";
            return;
        }
        
        usort($files, function($a, $b) {
            return strcmp(basename($a), basename($b));
        });
        
        foreach ($files as $file) {
            $baseName = basename($file, '.php');
            
            // Skip if specific migration is requested and this isn't it
            if ($migrationName !== null && stripos($baseName, $migrationName) === false) {
                continue;
            }
            
            $className = '\\Database\\Migrations\\' . ucwords(str_replace('_', '', $baseName));
            
            require_once $file;
            
            if (class_exists($className)) {
                $migration = new $className();
                $migration->up();
                echo "Applied migration: " . basename($file) . "\n";
            }
        }
    }
    
    public static function down(): void {
        self::initialize();
        
        $files = glob(self::$migrationsDir . '/*.php');
        
        if (empty($files)) {
            echo "No migrations found.\n";
            return;
        }
        
        usort($files, function($a, $b) {
            return strcmp(basename($b), basename($a));
        });
        
        foreach ($files as $file) {
            $baseName = basename($file, '.php');
            $className = '\\Database\\Migrations\\' . ucwords(str_replace('_', '', $baseName));
            
            require_once $file;
            
            if (class_exists($className)) {
                $migration = new $className();
                $migration->down();
                echo "Rolled back migration: " . basename($file) . "\n";
            }
        }
    }
    
}
