<?php

namespace App\Database;

use PDO;
use App\Core\Database;
use App\Utilities\Logger;

/**
 * Migration Manager
 * 
 * Manages database migrations
 */
class MigrationManager
{
    /**
     * Database connection
     * @var PDO
     */
    protected $db;
    
    /**
     * Migrations directory
     * @var string
     */
    protected $migrationsDir;
    
    /**
     * Constructor
     * 
     * @param string|null $migrationsDir Custom migrations directory
     */
    public function __construct(?string $migrationsDir = null)
    {
        $database = new Database();
        $this->db = $database->getPdo();
        
        // Set migrations directory
        $this->migrationsDir = $migrationsDir ?? dirname(__DIR__) . '/database/migrations';
        
        // Ensure migrations directory exists
        if (!is_dir($this->migrationsDir)) {
            mkdir($this->migrationsDir, 0755, true);
        }
        
        // Ensure migrations table exists
        $this->createMigrationsTable();
    }
    
    /**
     * Run all pending migrations
     * 
     * @return array Array of migration results
     */
    public function migrate(): array
    {
        $results = [];
        
        // Get all migration files
        $migrationFiles = $this->getMigrationFiles();
        
        // Get already run migrations
        $ranMigrations = $this->getRanMigrations();
        
        // Filter out already run migrations
        $pendingMigrations = array_diff($migrationFiles, $ranMigrations);
        
        // Sort migrations by filename (which should include timestamp)
        sort($pendingMigrations);
        
        // Run each pending migration
        foreach ($pendingMigrations as $migration) {
            $result = $this->runMigration($migration);
            $results[$migration] = $result;
            
            if (!$result) {
                // Stop on first failure
                break;
            }
        }
        
        return $results;
    }
    
    /**
     * Rollback the last batch of migrations
     * 
     * @return array Array of rollback results
     */
    public function rollback(): array
    {
        $results = [];
        
        // Get the last batch number
        $lastBatch = $this->getLastBatch();
        
        if ($lastBatch === 0) {
            // No migrations to rollback
            return $results;
        }
        
        // Get migrations from the last batch
        $lastBatchMigrations = $this->getMigrationsInBatch($lastBatch);
        
        // Rollback in reverse order
        $lastBatchMigrations = array_reverse($lastBatchMigrations);
        
        foreach ($lastBatchMigrations as $migration) {
            $result = $this->rollbackMigration($migration);
            $results[$migration] = $result;
            
            if (!$result) {
                // Stop on first failure
                break;
            }
        }
        
        return $results;
    }
    
    /**
     * Reset the database by rolling back all migrations
     * 
     * @return array Array of rollback results
     */
    public function reset(): array
    {
        $results = [];
        
        // Get all ran migrations
        $ranMigrations = $this->getRanMigrations();
        
        // Rollback in reverse order of creation
        $ranMigrations = array_reverse($ranMigrations);
        
        foreach ($ranMigrations as $migration) {
            $result = $this->rollbackMigration($migration);
            $results[$migration] = $result;
            
            if (!$result) {
                // Stop on first failure
                break;
            }
        }
        
        return $results;
    }
    
    /**
     * Refresh the database by rolling back all migrations and running them again
     * 
     * @return array Array with 'reset' and 'migrate' keys containing respective results
     */
    public function refresh(): array
    {
        $resetResults = $this->reset();
        $migrateResults = $this->migrate();
        
        return [
            'reset' => $resetResults,
            'migrate' => $migrateResults
        ];
    }
    
    /**
     * Create a new migration file
     * 
     * @param string $name Migration name
     * @return string|false Path to the new migration file or false on failure
     */
    public function create(string $name)
    {
        // Format the migration name
        $name = $this->formatMigrationName($name);
        
        // Create timestamp
        $timestamp = date('Y_m_d_His');
        
        // Create filename
        $filename = $timestamp . '_' . $name . '.php';
        $filepath = $this->migrationsDir . '/' . $filename;
        
        // Create migration file from template
        $template = $this->getMigrationTemplate($name);
        
        if (file_put_contents($filepath, $template) === false) {
            return false;
        }
        
        return $filepath;
    }
    
    /**
     * Get migration status
     * 
     * @return array Array of migration statuses
     */
    public function status(): array
    {
        // Get all migration files
        $migrationFiles = $this->getMigrationFiles();
        
        // Get already run migrations
        $ranMigrations = $this->getRanMigrationDetails();
        
        $status = [];
        
        // Check status of each migration file
        foreach ($migrationFiles as $migration) {
            $className = $this->getMigrationClassName($migration);
            
            if (isset($ranMigrations[$className])) {
                $status[$migration] = [
                    'name' => $className,
                    'batch' => $ranMigrations[$className]['batch'],
                    'ran_at' => $ranMigrations[$className]['created_at'],
                    'status' => 'Ran'
                ];
            } else {
                $status[$migration] = [
                    'name' => $className,
                    'batch' => null,
                    'ran_at' => null,
                    'status' => 'Pending'
                ];
            }
        }
        
        // Sort by filename
        ksort($status);
        
        return $status;
    }
    
    /**
     * Run a specific migration
     * 
     * @param string $migration Migration filename
     * @return bool Success or failure
     */
    protected function runMigration(string $migration): bool
    {
        $className = $this->getMigrationClassName($migration);
        $fullClassName = "\\App\\Database\\Migrations\\{$className}";
        
        // Check if migration class exists
        if (!class_exists($fullClassName)) {
            // Try to require the file
            $migrationFile = $this->migrationsDir . '/' . $migration;
            
            if (!file_exists($migrationFile)) {
                Logger::error("Migration file not found: {$migrationFile}");
                return false;
            }
            
            require_once $migrationFile;
            
            if (!class_exists($fullClassName)) {
                Logger::error("Migration class not found: {$fullClassName}");
                return false;
            }
        }
        
        try {
            // Create migration instance and run it
            $instance = new $fullClassName();
            $result = $instance->up();
            
            if ($result) {
                Logger::info("Migration ran successfully: {$className}");
            } else {
                Logger::error("Migration failed: {$className}");
            }
            
            return $result;
        } catch (\Exception $e) {
            Logger::error("Migration exception: {$e->getMessage()}");
            return false;
        }
    }
    
    /**
     * Rollback a specific migration
     * 
     * @param string $migration Migration name or filename
     * @return bool Success or failure
     */
    protected function rollbackMigration(string $migration): bool
    {
        // If it's a filename, get the class name
        if (strpos($migration, '.php') !== false) {
            $className = $this->getMigrationClassName($migration);
        } else {
            $className = $migration;
        }
        
        $fullClassName = "\\App\\Database\\Migrations\\{$className}";
        
        // Check if migration class exists
        if (!class_exists($fullClassName)) {
            // Try to find the migration file
            $files = glob($this->migrationsDir . '/*_' . $this->formatMigrationName($className) . '.php');
            
            if (empty($files)) {
                Logger::error("Migration file not found for: {$className}");
                return false;
            }
            
            require_once $files[0];
            
            if (!class_exists($fullClassName)) {
                Logger::error("Migration class not found: {$fullClassName}");
                return false;
            }
        }
        
        try {
            // Create migration instance and roll it back
            $instance = new $fullClassName();
            $result = $instance->down();
            
            if ($result) {
                Logger::info("Migration rolled back successfully: {$className}");
            } else {
                Logger::error("Migration rollback failed: {$className}");
            }
            
            return $result;
        } catch (\Exception $e) {
            Logger::error("Migration rollback exception: {$e->getMessage()}");
            return false;
        }
    }
    
    /**
     * Get all migration files
     * 
     * @return array Array of migration filenames
     */
    protected function getMigrationFiles(): array
    {
        $files = glob($this->migrationsDir . '/*.php');
        
        // Extract just the filenames
        $filenames = [];
        foreach ($files as $file) {
            $filenames[] = basename($file);
        }
        
        return $filenames;
    }
    
    /**
     * Get already run migrations
     * 
     * @return array Array of migration names
     */
    protected function getRanMigrations(): array
    {
        $stmt = $this->db->query("SELECT name FROM migrations");
        $migrations = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Convert migration names to filenames
        $filenames = [];
        foreach ($migrations as $migration) {
            $files = glob($this->migrationsDir . '/*_' . $this->formatMigrationName($migration) . '.php');
            if (!empty($files)) {
                $filenames[] = basename($files[0]);
            }
        }
        
        return $filenames;
    }
    
    /**
     * Get details of ran migrations
     * 
     * @return array Array of migration details indexed by name
     */
    protected function getRanMigrationDetails(): array
    {
        $stmt = $this->db->query("SELECT name, batch, created_at FROM migrations");
        $migrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $details = [];
        foreach ($migrations as $migration) {
            $details[$migration['name']] = $migration;
        }
        
        return $details;
    }
    
    /**
     * Get the last batch number
     * 
     * @return int Last batch number
     */
    protected function getLastBatch(): int
    {
        $stmt = $this->db->query("SELECT MAX(batch) FROM migrations");
        $batch = $stmt->fetchColumn();
        
        return $batch ? (int) $batch : 0;
    }
    
    /**
     * Get migrations in a specific batch
     * 
     * @param int $batch Batch number
     * @return array Array of migration names
     */
    protected function getMigrationsInBatch(int $batch): array
    {
        $stmt = $this->db->prepare("SELECT name FROM migrations WHERE batch = ?");
        $stmt->execute([$batch]);
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * Create migrations table if it doesn't exist
     * 
     * @return void
     */
    protected function createMigrationsTable(): void
    {
        $this->db->exec("CREATE TABLE IF NOT EXISTS migrations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            batch INTEGER NOT NULL,
            created_at DATETIME NOT NULL
        )");
    }
    
    /**
     * Get migration class name from filename
     * 
     * @param string $filename Migration filename
     * @return string Migration class name
     */
    protected function getMigrationClassName(string $filename): string
    {
        // Remove timestamp and extension
        $name = preg_replace('/^\d+_/', '', $filename);
        $name = str_replace('.php', '', $name);
        
        // Convert to StudlyCase
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
    }
    
    /**
     * Format migration name for filename
     * 
     * @param string $name Migration name
     * @return string Formatted name
     */
    protected function formatMigrationName(string $name): string
    {
        // Remove any non-alphanumeric characters except underscores
        $name = preg_replace('/[^a-z0-9_]/i', '_', $name);
        
        // Convert to snake_case
        $name = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
        
        // Remove multiple underscores
        $name = preg_replace('/_+/', '_', $name);
        
        // Trim underscores from beginning and end
        return trim($name, '_');
    }
    
    /**
     * Get migration file template
     * 
     * @param string $name Migration name
     * @return string Migration template
     */
    protected function getMigrationTemplate(string $name): string
    {
        $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
        
        return <<<PHP
<?php

namespace App\Database\Migrations;

use App\Database\Migration;

class {$className} extends Migration
{
    /**
     * Run the migration
     * 
     * @return void
     */
    public function upImplementation(): void
    {
        $this->execute("CREATE TABLE IF NOT EXISTS example (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL
        )");
    }
    
    /**
     * Reverse the migration
     * 
     * @return void
     */
    public function downImplementation(): void
    {
        $this->execute("DROP TABLE IF EXISTS example");
    }
}
PHP;
    }
}
