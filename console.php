<?php

require_once __DIR__ . '/vendor/autoload.php';

class ConsoleApplication {
    protected $commands = [];

    public function __construct() {
        // Register core commands
        $this->registerCommand('db', new DatabaseCommand());
        $this->registerCommand('help', new HelpCommand($this->commands));
    }

    public function registerCommand($name, $handler) {
        $this->commands[$name] = $handler;
    }

    public function run($argv) {
        // Get command name from arguments or default to help
        $commandName = isset($argv[1]) ? $argv[1] : 'help';

        // Check if command exists
        if (!isset($this->commands[$commandName])) {
            echo "Unknown command: {$commandName}\n";
            echo "Run 'php console.php help' for a list of available commands.\n";
            exit(1);
        }

        // Execute command
        try {
            $this->commands[$commandName]->handle(array_slice($argv, 1));
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
}

class DatabaseCommand {
    public function handle($argv) {
        $command = isset($argv[1]) ? $argv[1] : 'migrate';
        $param = isset($argv[2]) ? $argv[2] : null;
        
        switch ($command) {
            case 'migrate':
            case 'rollback':
            case 'status':
                // Use the migration runner
                require_once __DIR__ . '/migrate.php';
                $runner = new MigrationRunner();
                
                if ($command == 'rollback' && $param !== null) {
                    $runner->rollback((int)$param);
                } else if ($command == 'rollback') {
                    $runner->rollback();
                } else if ($command == 'status') {
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
                } else {
                    $runner->run();
                }
                break;
                
            case 'seed':
                require_once __DIR__ . '/database/DatabaseSeeder.php';
                $seeder = new Database\DatabaseSeeder();
                
                if ($param) {
                    // Seed specific class if provided
                    $seederClass = "\\Database\\Seeds\\" . $param;
                    require_once __DIR__ . "/database/seeds/{$param}.php";
                    
                    if (class_exists($seederClass)) {
                        $seeder->register($seederClass)->run();
                    } else {
                        echo "Error: Seeder class {$seederClass} not found.\n";
                        exit(1);
                    }
                } else {
                    // Run all seeders
                    $seeder->run();
                }
                break;
                
            case 'make:migration':
                if (!$param) {
                    echo "Error: Migration name required.\n";
                    echo "Usage: php console.php db make:migration migration_name\n";
                    exit(1);
                }
                
                require_once __DIR__ . '/create-migration.php';
                break;
                
            default:
                echo "Unknown database command: {$command}\n";
                echo "Available commands: migrate, rollback, status, seed, make:migration\n";
                exit(1);
        }
    }

    public function getHelp() {
        return [
            'name' => 'db',
            'description' => 'Database migration commands',
            'usage' => 'php console.php db [command] [options]',
            'commands' => [
                'migrate' => 'Run all pending migrations',
                'rollback [steps]' => 'Rollback the last migration or specified number of migrations',
                'status' => 'Show status of all migrations',
                'seed [seeder]' => 'Seed the database with sample data (optional specific seeder)',
                'make:migration [name]' => 'Create a new migration file'
            ]
        ];
    }
}

class HelpCommand {
    protected $commands;

    public function __construct($commands) {
        $this->commands = $commands;
    }

    public function handle($argv) {
        $commandName = isset($argv[1]) ? $argv[1] : null;

        if ($commandName && isset($this->commands[$commandName])) {
            // Show help for a specific command
            $this->showCommandHelp($commandName, $this->commands[$commandName]);
        } else {
            // Show general help
            $this->showGeneralHelp();
        }
    }

    protected function showGeneralHelp() {
        echo "\nGideon's Technology Console Tool\n";
        echo "==============================\n\n";
        echo "Available commands:\n";

        foreach ($this->commands as $name => $handler) {
            if (method_exists($handler, 'getHelp')) {
                $help = $handler->getHelp();
                echo sprintf("  %s%s%s\n", 
                    str_pad($name, 15), 
                    $help['description']
                );
            } else {
                echo sprintf("  %s\n", $name);
            }
        }
        
        echo "\nRun 'php console.php help [command]' for more information on a specific command.\n";
    }

    protected function showCommandHelp($name, $handler) {
        if (!method_exists($handler, 'getHelp')) {
            echo "No help available for command: {$name}\n";
            return;
        }

        $help = $handler->getHelp();
        
        echo "\n{$help['name']} - {$help['description']}\n";
        echo str_repeat('=', 60) . "\n\n";
        
        echo "Usage: {$help['usage']}\n\n";
        
        if (isset($help['commands']) && !empty($help['commands'])) {
            echo "Available subcommands:\n";
            foreach ($help['commands'] as $cmd => $desc) {
                echo sprintf("  %s%s\n", 
                    str_pad($cmd, 25), 
                    $desc
                );
            }
        }
        
        if (isset($help['options']) && !empty($help['options'])) {
            echo "\nOptions:\n";
            foreach ($help['options'] as $opt => $desc) {
                echo sprintf("  %s%s\n", 
                    str_pad($opt, 25), 
                    $desc
                );
            }
        }
        
        if (isset($help['examples']) && !empty($help['examples'])) {
            echo "\nExamples:\n";
            foreach ($help['examples'] as $example) {
                echo "  {$example}\n";
            }
        }
        
        echo "\n";
    }

    public function getHelp() {
        return [
            'name' => 'help',
            'description' => 'Display help information',
            'usage' => 'php console.php help [command]',
            'options' => [
                '[command]' => 'The command to display help for'
            ],
            'examples' => [
                'php console.php help',
                'php console.php help db'
            ]
        ];
    }
}

// Run the application
$app = new ConsoleApplication();
$app->run($argv);