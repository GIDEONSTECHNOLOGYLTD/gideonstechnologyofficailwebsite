<?php

namespace App\Database;

use PDO;

class MigrationRunner
{
    /**
     * The PDO connection instance
     * 
     * @var PDO
     */
    protected $connection;

    /**
     * The path to the migrations directory
     * 
     * @var string
     */
    protected $migrationsPath;

    /**
     * Create a new migration runner instance
     *
     * @param PDO $connection
     * @param string|null $migrationsPath
     */
    public function __construct(PDO $connection, ?string $migrationsPath = null)
    {
        $this->connection = $connection;
        $this->migrationsPath = $migrationsPath ?? dirname(__DIR__, 2) . '/database/migrations';
        
        $this->createMigrationsTable();
    }

    /**
     * Create migrations table if it doesn't exist
     *
     * @return void
     */
    protected function createMigrationsTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            migration VARCHAR(255) NOT NULL,
            batch INTEGER NOT NULL
        )";
        
        $this->connection->exec($sql);
    }

    /**
     * Run all pending migrations
     *
     * @return array
     */
    public function run(): array
    {
        $files = $this->getPendingMigrations();
        $migrationsRun = [];
        $batch = $this->getNextBatchNumber();
        
        if (empty($files)) {
            return $migrationsRun;
        }
        
        foreach ($files as $file) {
            $migration = $this->getMigrationName($file);
            
            $this->runMigration($file, $migration);
            $this->logMigration($migration, $batch);
            
            $migrationsRun[] = $migration;
        }
        
        return $migrationsRun;
    }

    /**
     * Roll back the last batch of migrations
     *
     * @return array
     */
    public function rollback(): array
    {
        $batch = $this->getLastBatchNumber();
        
        if ($batch === 0) {
            return [];
        }
        
        $migrations = $this->getMigrationsForBatch($batch);
        $migrationsRolledBack = [];
        
        foreach (array_reverse($migrations) as $migration) {
            $file = $this->getMigrationFile($migration);
            
            if (!$file) {
                continue;
            }
            
            $this->rollbackMigration($file);
            $this->removeMigrationFromLog($migration);
            
            $migrationsRolledBack[] = $migration;
        }
        
        return $migrationsRolledBack;
    }

    /**
     * Get all pending migrations
     *
     * @return array
     */
    protected function getPendingMigrations(): array
    {
        $files = glob($this->migrationsPath . '/*.php');
        $ranMigrations = $this->getRanMigrations();
        
        return array_filter($files, function ($file) use ($ranMigrations) {
            return !in_array($this->getMigrationName($file), $ranMigrations);
        });
    }

    /**
     * Get all migrations that have already run
     *
     * @return array
     */
    protected function getRanMigrations(): array
    {
        $stmt = $this->connection->prepare('SELECT migration FROM migrations');
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get the next batch number
     *
     * @return int
     */
    protected function getNextBatchNumber(): int
    {
        return $this->getLastBatchNumber() + 1;
    }

    /**
     * Get the last batch number
     *
     * @return int
     */
    protected function getLastBatchNumber(): int
    {
        $stmt = $this->connection->prepare('SELECT MAX(batch) FROM migrations');
        $stmt->execute();
        
        return (int) $stmt->fetchColumn() ?: 0;
    }

    /**
     * Get migrations for a specific batch
     *
     * @param int $batch
     * @return array
     */
    protected function getMigrationsForBatch(int $batch): array
    {
        $stmt = $this->connection->prepare('SELECT migration FROM migrations WHERE batch = ?');
        $stmt->execute([$batch]);
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get migration file for a migration name
     *
     * @param string $migration
     * @return string|null
     */
    protected function getMigrationFile(string $migration): ?string
    {
        $path = $this->migrationsPath . '/' . $migration . '.php';
        
        return file_exists($path) ? $path : null;
    }

    /**
     * Run a migration
     *
     * @param string $file
     * @param string $migration
     * @return void
     */
    protected function runMigration(string $file, string $migration): void
    {
        require_once $file;
        
        $className = $this->getMigrationClassName($migration);
        $instance = new $className();
        
        $instance->up($this->connection);
    }

    /**
     * Rollback a migration
     *
     * @param string $file
     * @return void
     */
    protected function rollbackMigration(string $file): void
    {
        require_once $file;
        
        $className = $this->getMigrationClassName($this->getMigrationName($file));
        $instance = new $className();
        
        $instance->down($this->connection);
    }

    /**
     * Log a migration in the database
     *
     * @param string $migration
     * @param int $batch
     * @return void
     */
    protected function logMigration(string $migration, int $batch): void
    {
        $stmt = $this->connection->prepare('INSERT INTO migrations (migration, batch) VALUES (?, ?)');
        $stmt->execute([$migration, $batch]);
    }

    /**
     * Remove a migration from the log
     *
     * @param string $migration
     * @return void
     */
    protected function removeMigrationFromLog(string $migration): void
    {
        $stmt = $this->connection->prepare('DELETE FROM migrations WHERE migration = ?');
        $stmt->execute([$migration]);
    }

    /**
     * Get migration name from file path
     *
     * @param string $file
     * @return string
     */
    protected function getMigrationName(string $file): string
    {
        return pathinfo($file, PATHINFO_FILENAME);
    }

    /**
     * Get class name from migration name
     *
     * @param string $migration
     * @return string
     */
    protected function getMigrationClassName(string $migration): string
    {
        $parts = explode('_', $migration);
        array_shift($parts); // Remove timestamp
        
        return implode('', array_map('ucfirst', $parts)) . 'Migration';
    }
}