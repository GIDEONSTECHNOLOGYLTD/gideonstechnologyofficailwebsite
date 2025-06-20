<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * DatabaseManager Class
 * 
 * Handles database connections and query execution
 */
class DatabaseManager
{
    /**
     * @var DatabaseManager|null Singleton instance
     */
    private static $instance = null;
    
    /**
     * @var PDO|null PDO connection
     */
    private $connection = null;
    
    /**
     * @var array Configuration
     */
    private $config = [];
    
    /**
     * Private constructor to prevent direct instantiation
     * 
     * @param array $config Database configuration
     */
    private function __construct($config = [])
    {
        $this->config = $config;
    }
    
    /**
     * Get singleton instance
     * 
     * @param array $config Database configuration
     * @return DatabaseManager
     */
    public static function getInstance($config = [])
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
        
        return self::$instance;
    }
    
    /**
     * Initialize the database manager with configuration
     * 
     * @param array $config Database configuration
     * @return void
     */
    public static function initialize($config = [])
    {
        // Get or create the singleton instance with the provided config
        $instance = self::getInstance($config);
        
        // Ensure we have a connection ready
        $instance->getConnection();
        
        return $instance;
    }
    
    /**
     * Get database connection
     * 
     * @return PDO
     */
    public function getConnection()
    {
        if ($this->connection === null) {
            $this->connect();
        }
        
        return $this->connection;
    }
    
    /**
     * Connect to the database
     * 
     * @return void
     * @throws PDOException
     */
    private function connect()
    {
        try {
            // Get database configuration
            $driver = $this->config['driver'] ?? 'mysql';
            $host = $this->config['host'] ?? 'localhost';
            $port = $this->config['port'] ?? '3306';
            $database = $this->config['database'] ?? 'gideons_tech';
            $username = $this->config['username'] ?? 'root';
            $password = $this->config['password'] ?? '';
            $charset = $this->config['charset'] ?? 'utf8mb4';
            
            // Build DSN based on driver
            if ($driver === 'sqlite') {
                $dsn = "sqlite:" . ($this->config['database'] ?? BASE_PATH . '/database/database.sqlite');
            } else {
                $dsn = "{$driver}:host={$host};port={$port};dbname={$database};charset={$charset}";
            }
            
            // Set PDO options
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            // Create PDO connection
            $this->connection = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            throw new PDOException("Database connection failed: " . $e->getMessage(), (int)$e->getCode());
        }
    }
    
    /**
     * Execute a query and return the statement
     * 
     * @param string $query SQL query
     * @param array $params Query parameters
     * @return PDOStatement
     */
    public function query($query, $params = [])
    {
        try {
            $stmt = $this->getConnection()->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new PDOException("Query execution failed: " . $e->getMessage(), (int)$e->getCode());
        }
    }
    
    /**
     * Fetch all records from a query
     * 
     * @param string $query SQL query
     * @param array $params Query parameters
     * @return array
     */
    public function fetchAll($query, $params = [])
    {
        return $this->query($query, $params)->fetchAll();
    }
    
    /**
     * Fetch a single record from a query
     * 
     * @param string $query SQL query
     * @param array $params Query parameters
     * @return array|false
     */
    public function fetch($query, $params = [])
    {
        return $this->query($query, $params)->fetch();
    }
    
    /**
     * Execute an insert query and return the last insert ID
     * 
     * @param string $query SQL query
     * @param array $params Query parameters
     * @return string|false
     */
    public function insert($query, $params = [])
    {
        $this->query($query, $params);
        return $this->getConnection()->lastInsertId();
    }
    
    /**
     * Execute an update query and return the number of affected rows
     * 
     * @param string $query SQL query
     * @param array $params Query parameters
     * @return int
     */
    public function update($query, $params = [])
    {
        return $this->query($query, $params)->rowCount();
    }
    
    /**
     * Execute a delete query and return the number of affected rows
     * 
     * @param string $query SQL query
     * @param array $params Query parameters
     * @return int
     */
    public function delete($query, $params = [])
    {
        return $this->query($query, $params)->rowCount();
    }
    
    /**
     * Begin a transaction
     * 
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->getConnection()->beginTransaction();
    }
    
    /**
     * Commit a transaction
     * 
     * @return bool
     */
    public function commit()
    {
        return $this->getConnection()->commit();
    }
    
    /**
     * Rollback a transaction
     * 
     * @return bool
     */
    public function rollback()
    {
        return $this->getConnection()->rollBack();
    }
}
