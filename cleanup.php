<?php

/**
 * Cleanup script to identify and manage duplicate database files
 */

class ProjectCleanup {
    protected $basePath;
    protected $duplicates = [];
    protected $oldFormat = [];
    protected $backupDir;

    public function __construct() {
        $this->basePath = __DIR__;
        $this->backupDir = $this->basePath . '/backup_' . date('Ymd_His');
    }

    public function scan() {
        echo "Scanning project for duplicate/obsolete database files...\n";
        
        // Scan migrations directory
        $this->scanMigrations();
        
        // Scan for duplicate Schema classes
        $this->scanSchemaClasses();
        
        // Scan for duplicate database connections
        $this->scanDatabaseConnections();
        
        return $this;
    }

    protected function scanMigrations() {
        $migrationPath = $this->basePath . '/database/migrations';
        if (!is_dir($migrationPath)) {
            echo "Migrations directory not found.\n";
            return;
        }
        
        // Look for .sql migration files (old format)
        $sqlFiles = glob($migrationPath . '/*.sql');
        foreach ($sqlFiles as $file) {
            $this->oldFormat[] = $file;
        }
        
        // Group migrations by their name (without timestamp)
        $migrations = [];
        $files = glob($migrationPath . '/*.php');
        
        foreach ($files as $file) {
            $basename = basename($file);
            
            // Skip migration_template.php
            if ($basename === 'migration_template.php') {
                continue;
            }
            
            // Extract name without timestamp
            if (preg_match('/^\d+_(.+)\.php$/', $basename, $matches)) {
                $name = $matches[1];
                if (!isset($migrations[$name])) {
                    $migrations[$name] = [];
                }
                $migrations[$name][] = $file;
            }
        }
        
        // Find duplicates
        foreach ($migrations as $name => $files) {
            if (count($files) > 1) {
                $this->duplicates['migrations'][$name] = $files;
            }
        }
    }

    protected function scanSchemaClasses() {
        $files = [];
        $directories = [
            $this->basePath . '/app/core',
            $this->basePath . '/app/providers', 
            $this->basePath . '/database'
        ];
        
        foreach ($directories as $dir) {
            if (is_dir($dir)) {
                $schemaFiles = $this->findFilesWithClass($dir, 'Schema');
                $files = array_merge($files, $schemaFiles);
            }
        }
        
        // Skip our new Schema.php file
        $files = array_filter($files, function($file) {
            return basename($file) !== 'Schema.php' || 
                   dirname($file) !== $this->basePath . '/database';
        });
        
        if (!empty($files)) {
            $this->duplicates['schema_classes'] = $files;
        }
    }

    protected function scanDatabaseConnections() {
        $files = [];
        $directories = [
            $this->basePath . '/app/core',
            $this->basePath . '/app/providers', 
            $this->basePath . '/database'
        ];
        
        foreach ($directories as $dir) {
            if (is_dir($dir)) {
                $dbFiles = $this->findFilesWithPattern($dir, '/(database_connect|DB|PDO|conn|connect)/i');
                $files = array_merge($files, $dbFiles);
            }
        }
        
        // Skip our new migration files
        $files = array_filter($files, function($file) {
            $skip = [
                'Migration.php',
                'migrate.php',
                'console.php',
                'DatabaseSeeder.php'
            ];
            return !in_array(basename($file), $skip);
        });
        
        if (!empty($files)) {
            $this->duplicates['db_connections'] = $files;
        }
    }
    
    protected function findFilesWithClass($directory, $className) {
        $result = [];
        
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory)
        );
        
        foreach ($files as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $content = file_get_contents($file->getPathname());
                if (preg_match('/class\s+' . $className . '\s+/i', $content)) {
                    $result[] = $file->getPathname();
                }
            }
        }
        
        return $result;
    }
    
    protected function findFilesWithPattern($directory, $pattern) {
        $result = [];
        
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory)
        );
        
        foreach ($files as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $content = file_get_contents($file->getPathname());
                if (preg_match($pattern, $content)) {
                    $result[] = $file->getPathname();
                }
            }
        }
        
        return $result;
    }

    public function report() {
        echo "\n=== Project Cleanup Report ===\n\n";
        
        if (empty($this->duplicates) && empty($this->oldFormat)) {
            echo "No duplicates or obsolete files found.\n";
            return;
        }
        
        if (!empty($this->oldFormat)) {
            echo count($this->oldFormat) . " SQL migration files (old format):\n";
            foreach ($this->oldFormat as $file) {
                echo " - " . basename($file) . "\n";
            }
            echo "\n";
        }
        
        if (!empty($this->duplicates['migrations'])) {
            echo count($this->duplicates['migrations']) . " duplicate migrations found:\n";
            foreach ($this->duplicates['migrations'] as $name => $files) {
                echo " - {$name} (" . count($files) . " versions)\n";
                foreach ($files as $file) {
                    echo "   * " . basename($file) . "\n";
                }
            }
            echo "\n";
        }
        
        if (!empty($this->duplicates['schema_classes'])) {
            echo count($this->duplicates['schema_classes']) . " duplicate Schema classes found:\n";
            foreach ($this->duplicates['schema_classes'] as $file) {
                echo " - " . str_replace($this->basePath . '/', '', $file) . "\n";
            }
            echo "\n";
        }
        
        if (!empty($this->duplicates['db_connections'])) {
            echo count($this->duplicates['db_connections']) . " potential duplicate DB connections found:\n";
            foreach ($this->duplicates['db_connections'] as $file) {
                echo " - " . str_replace($this->basePath . '/', '', $file) . "\n";
            }
            echo "\n";
        }
        
        echo "Run with --backup to move these files to a backup directory.\n";
        echo "Run with --delete to remove these files (use with caution!).\n";
    }

    public function backup() {
        if (empty($this->duplicates) && empty($this->oldFormat)) {
            echo "No duplicates or obsolete files found to backup.\n";
            return;
        }
        
        // Create backup directory
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
            mkdir($this->backupDir . '/migrations', 0755, true);
            mkdir($this->backupDir . '/schema', 0755, true);
            mkdir($this->backupDir . '/db', 0755, true);
        }
        
        // Backup SQL migrations
        foreach ($this->oldFormat as $file) {
            $dest = $this->backupDir . '/migrations/' . basename($file);
            copy($file, $dest);
            echo "Backed up: " . basename($file) . "\n";
        }
        
        // Backup duplicate migrations
        if (!empty($this->duplicates['migrations'])) {
            foreach ($this->duplicates['migrations'] as $name => $files) {
                // Keep the newest version
                usort($files, function($a, $b) {
                    return filemtime($b) - filemtime($a);
                });
                
                // Backup all but the newest
                for ($i = 1; $i < count($files); $i++) {
                    $dest = $this->backupDir . '/migrations/' . basename($files[$i]);
                    copy($files[$i], $dest);
                    echo "Backed up: " . basename($files[$i]) . "\n";
                }
            }
        }
        
        // Backup Schema classes
        if (!empty($this->duplicates['schema_classes'])) {
            foreach ($this->duplicates['schema_classes'] as $file) {
                $dest = $this->backupDir . '/schema/' . basename($file);
                copy($file, $dest);
                echo "Backed up: " . basename($file) . "\n";
            }
        }
        
        // Backup DB connections
        if (!empty($this->duplicates['db_connections'])) {
            foreach ($this->duplicates['db_connections'] as $file) {
                $dest = $this->backupDir . '/db/' . basename($file);
                copy($file, $dest);
                echo "Backed up: " . basename($file) . "\n";
            }
        }
        
        echo "\nAll files backed up to: " . $this->backupDir . "\n";
    }

    public function delete() {
        if (empty($this->duplicates) && empty($this->oldFormat)) {
            echo "No duplicates or obsolete files found to delete.\n";
            return;
        }
        
        // Delete SQL migrations
        foreach ($this->oldFormat as $file) {
            unlink($file);
            echo "Deleted: " . basename($file) . "\n";
        }
        
        // Delete duplicate migrations
        if (!empty($this->duplicates['migrations'])) {
            foreach ($this->duplicates['migrations'] as $name => $files) {
                // Keep the newest version
                usort($files, function($a, $b) {
                    return filemtime($b) - filemtime($a);
                });
                
                // Delete all but the newest
                for ($i = 1; $i < count($files); $i++) {
                    unlink($files[$i]);
                    echo "Deleted: " . basename($files[$i]) . "\n";
                }
            }
        }
        
        // Delete Schema classes
        if (!empty($this->duplicates['schema_classes'])) {
            foreach ($this->duplicates['schema_classes'] as $file) {
                unlink($file);
                echo "Deleted: " . basename($file) . "\n";
            }
        }
        
        // Delete DB connections - not recommended, ask first
        if (!empty($this->duplicates['db_connections'])) {
            echo "\nWARNING: Detected " . count($this->duplicates['db_connections']) . 
                 " potential duplicate DB connection files.\n";
            echo "It's not safe to automatically delete these. Please review them manually.\n";
        }
    }
}

// Run the script
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    $action = isset($argv[1]) ? $argv[1] : '--report';
    
    $cleanup = new ProjectCleanup();
    $cleanup->scan();
    
    switch ($action) {
        case '--backup':
            $cleanup->report();
            echo "\n";
            $cleanup->backup();
            break;
            
        case '--delete':
            $cleanup->report();
            echo "\nWARNING: This will DELETE the files listed above.\n";
            echo "Are you sure you want to continue? (y/n) ";
            $handle = fopen("php://stdin", "r");
            $line = fgets($handle);
            if (trim($line) === 'y') {
                $cleanup->delete();
            } else {
                echo "Operation cancelled.\n";
            }
            break;
            
        case '--report':
        default:
            $cleanup->report();
            break;
    }
}