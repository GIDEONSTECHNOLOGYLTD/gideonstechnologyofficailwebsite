<?php

/**
 * Migration Helper - Guides you through the migration cleanup and setup process
 */

class MigrationHelper {
    protected $basePath;
    
    public function __construct() {
        $this->basePath = __DIR__;
    }
    
    public function run() {
        $this->welcome();
        
        // Step 1: Setup directories
        $this->runStep("Setting up directories", "php setup-directories.php");
        
        // Step 2: Identify duplicates
        $this->runStep("Identifying duplicate files", "php cleanup.php --report");
        $this->waitForConfirmation();
        
        // Step 3: Backup old files
        $this->runStep("Creating backup of old files", "php cleanup.php --backup");
        $this->waitForConfirmation();
        
        // Step 4: Check migration status
        $this->runStep("Checking migration status", "php console.php db status");
        $this->waitForConfirmation();
        
        // Step 5: Run migrations
        $this->runStep("Running migrations", "php console.php db migrate");
        $this->waitForConfirmation();
        
        // Step 6: Optionally remove duplicates
        echo "\n\nWould you like to remove duplicate files? Only do this if the migrations worked correctly. (y/n): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        if (strtolower(trim($line)) === 'y') {
            $this->runStep("Removing duplicate files", "php cleanup.php --delete");
        } else {
            echo "Skipping duplicate file removal.\n";
        }
        
        $this->finish();
    }
    
    protected function welcome() {
        echo "\n";
        echo "=================================================\n";
        echo "   Migration Helper - Gideon's Technology\n";
        echo "=================================================\n";
        echo "\nThis script will guide you through the migration and cleanup process.\n";
        echo "We'll run each step and wait for your confirmation before proceeding.\n\n";
        echo "Press Enter to begin...";
        fgets(STDIN);
    }
    
    protected function runStep($description, $command) {
        echo "\n\n";
        echo "=================================================\n";
        echo "   $description\n";
        echo "=================================================\n";
        echo "Running: $command\n\n";
        
        // Execute the command
        passthru($command);
    }
    
    protected function waitForConfirmation() {
        echo "\nPress Enter to continue to the next step...";
        fgets(STDIN);
    }
    
    protected function finish() {
        echo "\n\n";
        echo "=================================================\n";
        echo "   Migration Process Complete!\n";
        echo "=================================================\n";
        echo "\nYour database system has been standardized.\n";
        echo "\nYou can now create new migrations with:\n";
        echo "php console.php db make:migration create_new_table\n";
        echo "\nRun your migrations with:\n";
        echo "php console.php db migrate\n";
        echo "\nSeed your database with:\n";
        echo "php console.php db seed\n\n";
    }
}

// Run the helper
$helper = new MigrationHelper();
$helper->run();