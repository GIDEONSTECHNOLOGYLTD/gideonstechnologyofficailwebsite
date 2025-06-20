class DatabaseCommand {
    private $migration;
    private $seeder;

    public function __construct() {
        $this->migration = new Migration();
        $this->seeder = new DatabaseSeeder();
    }

    public function handle($argv) {
        if (count($argv) < 2) {
            $this->showHelp();
            return;
        }

        $command = $argv[1];
        $params = array_slice($argv, 2);

        try {
            switch ($command) {
                case 'migrate':
                    $this->migrate($params);
                    break;
                case 'rollback':
                    $this->rollback($params);
                    break;
                case 'seed':
                    $this->seed($params);
                    break;
                case 'status':
                    $this->status();
                    break;
                case 'reset':
                    $this->reset();
                    break;
                default:
                    $this->showHelp();
            }
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    private function migrate($params) {
        echo "Running migrations...\n";
        $this->migration->run();
        echo "Migrations completed successfully\n";
    }

    private function rollback($params) {
        $steps = isset($params[0]) ? (int)$params[0] : 1;
        echo "Rolling back {$steps} migration(s)...\n";
        $this->migration->rollback($steps);
        echo "Rollback completed successfully\n";
    }

    private function seed($params) {
        echo "Seeding database...\n";
        $this->seeder->run();
        echo "Database seeded successfully\n";
    }

    private function status() {
        $status = $this->migration->status();
        
        echo "\nMigration Status:\n";
        echo str_repeat('-', 60) . "\n";
        echo sprintf("%-40s %-10s %s\n", 'Migration', 'Ran?', 'Batch');
        echo str_repeat('-', 60) . "\n";
        
        foreach ($status as $migration) {
            echo sprintf(
                "%-40s %-10s %s\n",
                $migration['migration'],
                $migration['ran'] ? 'Yes' : 'No',
                $migration['batch'] ?: 'N/A'
            );
        }
        echo str_repeat('-', 60) . "\n";
    }

    private function reset() {
        echo "Resetting database...\n";
        $this->migration->reset();
        echo "Database reset completed\n";
        
        echo "Running migrations...\n";
        $this->migration->run();
        echo "Migrations completed\n";
        
        echo "Seeding database...\n";
        $this->seeder->run();
        echo "Database seeded successfully\n";
    }

    private function showHelp() {
        echo "\nDatabase Management Tool\n";
        echo "Usage: php console.php db [command] [options]\n\n";
        echo "Available commands:\n";
        echo "  migrate              Run all pending migrations\n";
        echo "  rollback [steps]     Rollback the last migration (or specified number of migrations)\n";
        echo "  seed                 Seed the database with test data\n";
        echo "  status              Show migration status\n";
        echo "  reset               Reset and rebuild the database\n";
        echo "\n";
    }
}