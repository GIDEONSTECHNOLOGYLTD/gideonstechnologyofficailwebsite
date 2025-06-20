<?php

namespace App\Core;

/**
 * Migration Manager
 * Handles database migrations and versioning
 */
class Migration
{
    private $db;
    private $migrationsPath;
    
    /**
     * Constructor
     * 
     * @param Database $db Database connection
     * @param string $migrationsPath Path to migration files
     */
    public function __construct(Database $db, string $migrationsPath)
    {
        $this->db = $db;
        $this->migrationsPath = $migrationsPath;
        $this->createMigrationsTable();
    }
    
    /**
     * Create migrations table if it doesn't exist
     */
    private function createMigrationsTable()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                batch INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ")->execute();
    }
    
    /**
     * Run pending migrations
     * 
     * @return array Applied migrations
     */
    public function runMigrations()
    {
        $appliedMigrations = [];
        $files = $this->getPendingMigrations();
        
        if (empty($files)) {
            return $appliedMigrations;
        }
        
        $batch = $this->getNextBatchNumber();
        
        foreach ($files as $file) {
            $className = $this->getMigrationClass($file);
            
            if (class_exists($className)) {
                $instance = new $className();
                $instance->up($this->db);
                
                $this->recordMigration($file, $batch);
                $appliedMigrations[] = $file;
            }
        }
        
        return $appliedMigrations;
    }
    
    /**
     * Get pending migrations
     * 
     * @return array Files to migrate
     */
    private function getPendingMigrations()
    {
        $this->db->query("SELECT migration FROM migrations");
        $appliedMigrations = array_column($this->db->fetchAll(), 'migration');
        
        $files = scandir($this->migrationsPath);
        $pendingMigrations = [];
        
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            
            // Skip non-PHP files
            if (!preg_match('/\.php$/', $file)) {
                continue;
            }
            
            // Check if migration has already been applied
            if (!in_array($file, $appliedMigrations)) {
                $pendingMigrations[] = $file;
            }
        }
        
        // Sort by timestamp prefix
        usort($pendingMigrations, function ($a, $b) {
            return $this->extractTimestamp($a) <=> $this->extractTimestamp($b);
        });
        
        return $pendingMigrations;
    }
    
    /**
     * Extract timestamp from migration filename
     * 
     * @param string $filename Migration filename
     * @return int Timestamp
     */
    private function extractTimestamp($filename)
    {
        // Matches patterns like 20231025_create_users_table.php
        if (preg_match('/^(\d+)_/', $filename, $matches)) {
            return (int)$matches[1];
        }
        return 0;
    }
    
    /**
     * Get next batch number
     * 
     * @return int Next batch number
     */
    private function getNextBatchNumber()
    {
        $this->db->query("SELECT MAX(batch) as max_batch FROM migrations");
        $result = $this->db->fetch();
        
        return ($result['max_batch'] ?? 0) + 1;
    }
    
    /**
     * Record a migration
     * 
     * @param string $migration Migration filename
     * @param int $batch Batch number
     */
    private function recordMigration($migration, $batch)
    {
        $this->db->query("
            INSERT INTO migrations (migration, batch) 
            VALUES (:migration, :batch)
        ")->bind([
            ':migration' => $migration,
            ':batch' => $batch
        ])->execute();
    }
    
    /**
     * Rollback the last batch of migrations
     * 
     * @return array Rolled back migrations
     */
    public function rollback()
    {
        $this->db->query("
            SELECT migration FROM migrations
            WHERE batch = (SELECT MAX(batch) FROM migrations)
            ORDER BY id DESC
        ");
        
        $migrations = $this->db->fetchAll();
        $rolledBack = [];
        
        foreach ($migrations as $migration) {
            $className = $this->getMigrationClass($migration['migration']);
            
            if (class_exists($className)) {
                $instance = new $className();
                $instance->down($this->db);
                
                $this->db->query("
                    DELETE FROM migrations WHERE migration = :migration
                ")->bind([
                    ':migration' => $migration['migration']
                ])->execute();
                
                $rolledBack[] = $migration['migration'];
            }
        }
        
        return $rolledBack;
    }
    
    /**
     * Get migration class from filename
     * 
     * @param string $file Filename
     * @return string Class name
     */
    private function getMigrationClass($file)
    {
        // Include the file
        require_once $this->migrationsPath . '/' . $file;
        
        // Convert filename to class name
        // Example: 20231025_create_users_table.php -> CreateUsersTable
        $baseName = basename($file, '.php');
        if (preg_match('/^\d+_(.+)$/', $baseName, $matches)) {
            $parts = explode('_', $matches[1]);
            $className = '';
            
            foreach ($parts as $part) {
                $className .= ucfirst($part);
            }
            
            return $className;
        }
        
        return ucfirst($baseName);
    }
    
    /**
     * Create a new migration file
     * 
     * @param string $name Migration name
     * @return string Created filename
     */
    public function create($name)
    {
        $timestamp = date('YmdHis');
        $filename = "{$timestamp}_{$name}.php";
        $path = $this->migrationsPath . '/' . $filename;
        
        $className = '';
        $parts = explode('_', $name);
        
        foreach ($parts as $part) {
            $className .= ucfirst($part);
        }
        
        $template = <<<PHP
<?php

class {$className}
{
    /**
     * Run the migration
     * 
     * @param \App\Core\Database \$db
     */
    public function up(\App\Core\Database \$db)
    {
        \$db->query("
            -- Your SQL here
        ")->execute();
    }

    /**
     * Reverse the migration
     * 
     * @param \App\Core\Database \$db
     */
    public function down(\App\Core\Database \$db)
    {
        \$db->query("
            -- Your rollback SQL here
        ")->execute();
    }
}
PHP;

        if (file_put_contents($path, $template) === false) {
            throw new \Exception("Failed to create migration file");
        }
        
        return $filename;
    }
}