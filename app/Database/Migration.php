<?php

namespace App\Database;

use PDO;
use App\Core\Database;

/**
 * Base Migration Class
 * 
 * Provides functionality for database migrations
 */
abstract class Migration
{
    /**
     * Database connection
     * @var PDO
     */
    protected $db;
    
    /**
     * Migration name
     * @var string
     */
    protected $name;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getPdo();
        
        // Set migration name based on class name
        $className = (new \ReflectionClass($this))->getShortName();
        $this->name = $className;
    }
    
    /**
     * Run the migration
     * 
     * @return bool Success or failure
     */
    public function up(): bool
    {
        try {
            $this->db->beginTransaction();
            
            // Call the up method in the child class
            if (method_exists($this, 'upImplementation')) {
                $this->upImplementation();
            }
            
            // Record the migration in the migrations table
            $this->recordMigration();
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Migration error ({$this->name}): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Reverse the migration
     * 
     * @return bool Success or failure
     */
    public function down(): bool
    {
        try {
            $this->db->beginTransaction();
            
            // Call the down method in the child class
            if (method_exists($this, 'downImplementation')) {
                $this->downImplementation();
            }
            
            // Remove the migration from the migrations table
            $this->removeMigration();
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Migration rollback error ({$this->name}): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Record migration in the migrations table
     * 
     * @return void
     */
    protected function recordMigration(): void
    {
        // Ensure migrations table exists
        $this->createMigrationsTable();
        
        // Insert migration record
        $stmt = $this->db->prepare("INSERT INTO migrations (name, batch, created_at) VALUES (?, ?, ?)");
        $batch = $this->getLatestBatch() + 1;
        $now = date('Y-m-d H:i:s');
        $stmt->execute([$this->name, $batch, $now]);
    }
    
    /**
     * Remove migration from the migrations table
     * 
     * @return void
     */
    protected function removeMigration(): void
    {
        // Ensure migrations table exists
        $this->createMigrationsTable();
        
        // Delete migration record
        $stmt = $this->db->prepare("DELETE FROM migrations WHERE name = ?");
        $stmt->execute([$this->name]);
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
     * Get the latest batch number
     * 
     * @return int Latest batch number
     */
    protected function getLatestBatch(): int
    {
        // Ensure migrations table exists
        $this->createMigrationsTable();
        
        // Get the latest batch number
        $stmt = $this->db->query("SELECT MAX(batch) FROM migrations");
        $batch = $stmt->fetchColumn();
        
        return $batch ? (int) $batch : 0;
    }
    
    /**
     * Check if a table exists
     * 
     * @param string $table Table name
     * @return bool Whether the table exists
     */
    protected function tableExists(string $table): bool
    {
        $stmt = $this->db->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name=?");
        $stmt->execute([$table]);
        return (bool) $stmt->fetch();
    }
    
    /**
     * Execute a raw SQL query
     * 
     * @param string $sql SQL query
     * @return bool Success or failure
     */
    protected function execute(string $sql): bool
    {
        return $this->db->exec($sql) !== false;
    }
}
