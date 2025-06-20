<?php

require 'vendor/autoload.php';

class MigrationRunner {
    protected $pdo;
    protected $batch;
    protected $dbPath;

    public function __construct() {
        $this->dbPath = __DIR__ . '/database/gtech.db';
        $this->pdo = new PDO('sqlite:' . $this->dbPath);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->ensureTrackingTable();
        $this->batch = $this->getNextBatch();
    }

    protected function ensureTrackingTable() {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS migration_history (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                migration_name TEXT NOT NULL,
                batch INTEGER NOT NULL,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                status TEXT CHECK(status IN ('success', 'failed')) NOT NULL DEFAULT 'success',
                error_message TEXT
            )
        ");
        
        $this->pdo->exec("
            CREATE INDEX IF NOT EXISTS idx_migration_name ON migration_history(migration_name)
        ");
        
        $this->pdo->exec("
            CREATE INDEX IF NOT EXISTS idx_batch ON migration_history(batch)
        ");
    }

    protected function getNextBatch() {
        $stmt = $this->pdo->query('SELECT COALESCE(MAX(batch), 0) + 1 FROM migration_history');
        return $stmt->fetchColumn();
    }

    protected function hasBeenExecuted($migrationName) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM migration_history WHERE migration_name = ? AND status = 'success'");
        $stmt->execute([$migrationName]);
        return $stmt->fetchColumn() > 0;
    }

    protected function hasBeenRun($migrationName) {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM migration_history WHERE migration_name = ?');
        $stmt->execute([$migrationName]);
        return $stmt->fetchColumn() > 0;
    }

    protected function recordMigration($migrationName, $status = 'success', $errorMessage = null) {
        $stmt = $this->pdo->prepare(
            'INSERT INTO migration_history (migration_name, batch, status, error_message) 
             VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$migrationName, $this->batch, $status, $errorMessage]);
    }

    public function run() {
        $migrations = glob(__DIR__ . '/database/migrations/*.php');
        sort($migrations); // Sort by date

        $hadErrors = false;

        foreach ($migrations as $file) {
            $baseName = basename($file, '.php');
            
            // Skip if already executed successfully
            if ($this->hasBeenExecuted($baseName)) {
                echo "Skipping {$baseName} - already executed\n";
                continue;
            }

            try {
                require_once $file;

                // Get the class name from the file name
                $parts = explode('_', $baseName);
                array_shift($parts); // Remove the date part
                $className = implode('', array_map(function($part) {
                    return str_replace(' ', '', ucwords(str_replace('_', ' ', $part)));
                }, $parts));
                
                // Convert to namespace format
                $class = "Database\\Migrations\\{$className}";
                
                if (!class_exists($class)) {
                    throw new \Exception("Migration class {$class} not found");
                }
                
                // Create migration instance and run it
                $migration = new $class();
                
                // Wrap in transaction
                $this->pdo->beginTransaction();
                try {
                    $migration->up();
                    $this->pdo->commit();
                    
                    // Record successful migration
                    $this->recordMigration($baseName, 'success');
                    echo "Migrated: {$baseName}\n";
                } catch (\Exception $e) {
                    $this->pdo->rollBack();
                    
                    // Check if error is due to duplicate index or column
                    $error = $e->getMessage();
                    if (strpos($error, 'Duplicate key name') !== false ||
                        strpos($error, 'Column already exists') !== false ||
                        strpos($error, 'already exists') !== false) {
                        // Consider this a success since the object already exists
                        $this->recordMigration($baseName, 'success');
                        echo "Skipped duplicate in {$baseName}: {$error}\n";
                    } else {
                        throw $e;
                    }
                }

            } catch (\Exception $e) {
                $hadErrors = true;
                // Record failed migration
                $this->recordMigration($baseName, 'failed', $e->getMessage());
                echo "Error in {$baseName}: {$e->getMessage()}\n";
                
                // Continue with next migration
                continue;
            }
        }

        if ($hadErrors) {
            echo "\nCompleted with some errors. Check migration_history table for details.\n";
            exit(1);
        } else {
            echo "\nAll migrations completed successfully!\n";
        }
    }
    
    public function rollback($steps = 1) {
        // Get the latest batch(es) to roll back
        $stmt = $this->pdo->prepare("
            SELECT DISTINCT batch FROM migration_history 
            WHERE status = 'success' 
            ORDER BY batch DESC 
            LIMIT ?
        ");
        $stmt->execute([$steps]);
        $batches = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($batches)) {
            echo "No migrations to roll back.\n";
            return;
        }
        
        $batchList = implode(',', $batches);
        
        // Get migrations to roll back in reverse order
        $stmt = $this->pdo->prepare("
            SELECT migration_name FROM migration_history
            WHERE batch IN ({$batchList}) AND status = 'success'
            ORDER BY id DESC
        ");
        $stmt->execute();
        $migrations = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($migrations)) {
            echo "No migrations found to roll back for batch(es) {$batchList}.\n";
            return;
        }
        
        $rolledBack = 0;
        
        foreach ($migrations as $migrationName) {
            try {
                $file = __DIR__ . "/database/migrations/{$migrationName}.php";
                
                if (!file_exists($file)) {
                    echo "Migration file not found: {$migrationName}.php. Skipping rollback.\n";
                    continue;
                }
                
                require_once $file;
                
                // Get the class name from the file name
                $parts = explode('_', $migrationName);
                array_shift($parts); // Remove the date part
                $className = implode('', array_map(function($part) {
                    return str_replace(' ', '', ucwords(str_replace('_', ' ', $part)));
                }, $parts));
                
                // Convert to namespace format
                $class = "Database\\Migrations\\{$className}";
                
                if (!class_exists($class)) {
                    echo "Migration class {$class} not found. Skipping rollback.\n";
                    continue;
                }
                
                // Create migration instance and rollback
                $migration = new $class();
                
                // Wrap in transaction
                $this->pdo->beginTransaction();
                try {
                    $migration->down();
                    
                    // Remove from migration history
                    $stmt = $this->pdo->prepare("DELETE FROM migration_history WHERE migration_name = ?");
                    $stmt->execute([$migrationName]);
                    
                    $this->pdo->commit();
                    echo "Rolled back: {$migrationName}\n";
                    $rolledBack++;
                } catch (\Exception $e) {
                    $this->pdo->rollBack();
                    echo "Error rolling back {$migrationName}: {$e->getMessage()}\n";
                }
                
            } catch (\Exception $e) {
                echo "Error processing rollback for {$migrationName}: {$e->getMessage()}\n";
            }
        }
        
        echo "\nRolled back {$rolledBack} migrations.\n";
    }
    
    public function status() {
        $migrations = glob(__DIR__ . '/database/migrations/*.php');
        sort($migrations);
        
        $status = [];
        
        foreach ($migrations as $file) {
            $migrationName = basename($file, '.php');
            
            $stmt = $this->pdo->prepare("
                SELECT batch, status, error_message FROM migration_history 
                WHERE migration_name = ? 
                ORDER BY executed_at DESC 
                LIMIT 1
            ");
            $stmt->execute([$migrationName]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $status[] = [
                'migration' => $migrationName,
                'ran' => ($row && $row['status'] == 'success'),
                'batch' => $row ? $row['batch'] : null,
                'status' => $row ? $row['status'] : 'pending',
                'error' => $row && $row['status'] == 'failed' ? $row['error_message'] : null
            ];
        }
        
        return $status;
    }
}

// Run migrations if executed directly
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    $command = $argv[1] ?? 'run';
    $param = $argv[2] ?? null;
    
    try {
        $runner = new MigrationRunner();
        
        switch ($command) {
            case 'run':
            case 'migrate':
                $runner->run();
                break;
                
            case 'rollback':
                $steps = $param ? (int)$param : 1;
                $runner->rollback($steps);
                break;
                
            case 'status':
                $status = $runner->status();
                
                echo "\nMigration Status:\n";
                echo str_repeat('-', 80) . "\n";
                echo sprintf("%-40s %-10s %-10s %s\n", 'Migration', 'Ran?', 'Batch', 'Status');
                echo str_repeat('-', 80) . "\n";
                
                foreach ($status as $migration) {
                    echo sprintf(
                        "%-40s %-10s %-10s %s\n",
                        $migration['migration'],
                        $migration['ran'] ? 'Yes' : 'No',
                        $migration['batch'] ?: 'N/A',
                        $migration['status']
                    );
                    
                    if ($migration['error']) {
                        echo "   Error: " . $migration['error'] . "\n";
                    }
                }
                echo str_repeat('-', 80) . "\n";
                break;
                
            default:
                echo "Unknown command: {$command}\n";
                echo "Available commands: migrate, rollback [steps], status\n";
                exit(1);
        }
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}
