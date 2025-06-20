<?php

namespace App\Providers;

use App\Core\ServiceProvider;
use App\Core\Config;
use App\Database\MigrationRunner;
use PDO;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Register database connections
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('db', function ($app) {
            return $this->createConnection(Config::get('database.default'));
        });

        $this->app->singleton('db.migrations', function ($app) {
            return new MigrationRunner($app['db']);
        });

        $this->app->singleton('migration.runner', function ($app) {
            return new MigrationRunner($app['db']);
        });
    }

    /**
     * Create a new database connection
     *
     * @param string $connection
     * @return PDO
     */
    protected function createConnection($connection = 'sqlite')
    {
        $config = Config::get('database.connections.' . $connection);

        if (!$config) {
            throw new \Exception("Database connection [{$connection}] not configured.");
        }

        try {
            switch ($connection) {
                case 'sqlite':
                    $dsn = "sqlite:" . dirname(__DIR__, 2) . '/database/gtech.db';
                    $pdo = new PDO($dsn);
                    break;

                case 'mysql':
                    $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
                    $pdo = new PDO($dsn, $config['username'], $config['password']);
                    break;

                default:
                    throw new \Exception("Unsupported database driver: {$connection}");
            }

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            return $pdo;
        } catch (\PDOException $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Bootstrap database services
     *
     * @return void
     */
    public function boot()
    {
        // No bootstrap actions needed for database service
    }
}