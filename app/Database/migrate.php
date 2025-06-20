<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Database\MigrationManager;

/**
 * Database Migration Runner
 * 
 * This script runs database migrations to create or update the database schema.
 * Usage: php app/database/migrate.php [--rollback] [--step=N]
 */

// Parse command line arguments
$options = getopt('', ['rollback', 'step::', 'help']);

// Show help if requested
if (isset($options['help'])) {
    echo "Database Migration Runner\n";
    echo "Usage: php app/database/migrate.php [--rollback] [--step=N]\n\n";
    echo "Options:\n";
    echo "  --rollback    Roll back the last batch of migrations\n";
    echo "  --step=N      Number of migrations to roll back (only with --rollback)\n";
    echo "  --help        Display this help message\n";
    exit(0);
}

// Create migration manager
$migrationManager = new MigrationManager();

// Determine action based on options
if (isset($options['rollback'])) {
    // Roll back migrations
    $step = isset($options['step']) ? (int)$options['step'] : 1;
    
    echo "Rolling back migrations...\n";
    
    try {
        $results = $migrationManager->rollback();
        $count = count(array_filter($results));
        echo "Successfully rolled back {$count} migration(s).\n";
        
        // Display details of rolled back migrations
        foreach ($results as $migration => $success) {
            $status = $success ? 'SUCCESS' : 'FAILED';
            echo "  {$status}: {$migration}\n";
        }
    } catch (\Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        exit(1);
    }
} else {
    // Run migrations
    echo "Running migrations...\n";
    
    try {
        $results = $migrationManager->migrate();
        $count = count(array_filter($results));
        echo "Successfully ran {$count} migration(s).\n";
        
        // Display details of run migrations
        foreach ($results as $migration => $success) {
            $status = $success ? 'SUCCESS' : 'FAILED';
            echo "  {$status}: {$migration}\n";
        }
    } catch (\Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        exit(1);
    }
}

exit(0);
