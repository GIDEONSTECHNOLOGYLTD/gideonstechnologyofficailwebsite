<?php
/**
 * Database Manager Class
 * 
 * Provides centralized database connection management for the application
 * Implements singleton pattern to prevent multiple connections
 */

namespace App\Database;

use PDO;
use PDOException;
use App\Utilities\Logger;

class DatabaseManager
{
    /**
     * @var DatabaseManager|null Singleton instance
     */
    private static $instance = null;
    
    /**
     * @var PDO|null The PDO connection
     */
    private $connection = null;
    
    /**
     * @var array Connection configuration
     */
    private $config = [];
    
    /**
     * Private constructor to enforce singleton pattern
     */
    private function __construct()
    {
        // Load configuration from environment
        $this->config = [
            'driver'   => $_ENV['DB_DRIVER'] ?? 'mysql',
            'host'     => $_ENV['DB_HOST'] ?? 'localhost',
            'port'     => $_ENV['DB_PORT'] ?? '3306',
            'database' => $_ENV['DB_DATABASE'] ?? '',
            'username' => $_ENV['DB_USERNAME'] ?? '',
            'password' => $_ENV['DB_PASSWORD'] ?? '',
            'charset'  => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
            'options'  => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        ];
    }
    
    /**
     * Get singleton instance
     * 
     * @return DatabaseManager
     */
    public static function getInstance(): DatabaseManager
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Get database connection
     * 
     * @return PDO
     * @throws PDOException If connection fails
     */
    public function getConnection(): PDO
    {
        if ($this->connection === null) {
            try {
                // Create DSN based on driver
                switch ($this->config['driver']) {
                    case 'mysql':
                        $dsn = sprintf(
                            '%s:host=%s;port=%s;dbname=%s;charset=%s',
                            $this->config['driver'],
                            $this->config['host'],
                            $this->config['port'],
                            $this->config['database'],
                            $this->config['charset']
                        );
                        break;
                        
                    case 'sqlite':
                        $dsn = 'sqlite:' . $this->config['database'];
                        break;
                        
                    default:
                        throw new \Exception("Unsupported database driver: {$this->config['driver']}");
                }
                
                // Create connection
                $this->connection = new PDO(
                    $dsn,
                    $this->config['username'],
                    $this->config['password'],
                    $this->config['options']
                );
                
                Logger::info("Database connection established");
            } catch (PDOException $e) {
                Logger::error("Database connection failed: " . $e->getMessage());
                throw $e;
            }
        }
        
        return $this->connection;
    }
    
    /**
     * Execute a query and return the statement
     * 
     * @param string $query SQL query
     * @param array $params Parameters to bind
     * @return \PDOStatement
     */
    public function query(string $query, array $params = []): \PDOStatement
    {
        $pdo = $this->getConnection();
        $statement = $pdo->prepare($query);
        $statement->execute($params);
        return $statement;
    }
    
    /**
     * Begin a transaction
     * 
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->getConnection()->beginTransaction();
    }
    
    /**
     * Commit a transaction
     * 
     * @return bool
     */
    public function commit(): bool
    {
        return $this->getConnection()->commit();
    }
    
    /**
     * Rollback a transaction
     * 
     * @return bool
     */
    public function rollback(): bool
    {
        return $this->getConnection()->rollBack();
    }
    
    /**
     * Get the last inserted ID
     * 
     * @param string|null $name Name of the sequence object
     * @return string
     */
    public function lastInsertId(string $name = null): string
    {
        return $this->getConnection()->lastInsertId($name);
    }
    
    /**
     * Set custom configuration (useful for testing)
     * 
     * @param array $config
     * @return void
     */
    public function setConfig(array $config): void
    {
        $this->config = array_merge($this->config, $config);
        $this->connection = null; // Reset connection to use new config
    }
}
