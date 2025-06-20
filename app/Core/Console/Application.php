<?php

namespace App\Core\Console;

class Application
{
    public function run()
    {
        $command = isset($argv[1]) ? $argv[1] : null;
        
        switch ($command) {
            case 'migrate':
                $this->runMigrations();
                break;
            default:
                echo "Available commands:\n";
                echo "  migrate - Run database migrations\n";
        }
    }

    private function runMigrations()
    {
        $migrationPath = __DIR__ . '/../../database/migrations';
        
        if (!file_exists($migrationPath)) {
            echo "Migration directory not found.\n";
            return;
        }

        $files = glob($migrationPath . '/*.php');
        sort($files);

        foreach ($files as $file) {
            require $file;
            $className = pathinfo($file, PATHINFO_FILENAME);
            $migration = new $className();
            
            try {
                $migration->up();
                echo "Ran migration: $className\n";
            } catch (\Exception $e) {
                echo "Error running migration $className: " . $e->getMessage() . "\n";
            }
        }
    }
}
