<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Database class
 * Handles database connection and queries
 */
class Database
{
    private $host;
    private $user;
    private $pass;
    private $dbname;
    private $charset;
    private $driver;
    
    private $pdo;
    private $stmt;
    private $error;
    
    /**
     * Constructor - connect to database
     * 
     * @param array $config Optional database configuration
     */
    public function __construct($config = [])
    {
        // Load configuration from environment or provided config
        $this->driver = $config['driver'] ?? getenv('DB_DRIVER') ?? 'sqlite';
        $this->host = $config['host'] ?? getenv('DB_HOST') ?? 'localhost';
        $this->user = $config['username'] ?? getenv('DB_USER') ?? 'root';
        $this->pass = $config['password'] ?? getenv('DB_PASS') ?? '';
        $this->dbname = $config['database'] ?? getenv('DB_NAME') ?? 'gideons_tech';
        $this->charset = $config['charset'] ?? getenv('DB_CHARSET') ?? 'utf8mb4';
        
        // Set options
        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => false
        ];
        
        // Create PDO instance
        try {
            // Try to connect using the configured driver
            if ($this->driver === 'sqlite') {
                // Use SQLite as a fallback
                $dbPath = $config['database'] ?? dirname(dirname(__DIR__)) . '/database/database.sqlite';
                
                // Ensure the database directory exists
                $dbDir = dirname($dbPath);
                if (!file_exists($dbDir)) {
                    mkdir($dbDir, 0777, true);
                }
                
                $dsn = "sqlite:{$dbPath}";
                $this->pdo = new PDO($dsn, null, null, $options);
            } else {
                // Use MySQL or other configured driver
                $dsn = "{$this->driver}:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
                $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
            }
            
            // Log successful connection
            \App\Utilities\Logger::info("Database connected successfully using {$this->driver} driver");
        } catch (PDOException $e) {
            // If MySQL connection fails, try SQLite as fallback
            if ($this->driver !== 'sqlite') {
                \App\Utilities\Logger::warning("Primary database connection failed: {$e->getMessage()}. Trying SQLite fallback...");
                
                try {
                    // Use SQLite as a fallback
                    $dbPath = dirname(dirname(__DIR__)) . '/database/database.sqlite';
                    
                    // Ensure the database directory exists
                    $dbDir = dirname($dbPath);
                    if (!file_exists($dbDir)) {
                        mkdir($dbDir, 0777, true);
                    }
                    
                    $dsn = "sqlite:{$dbPath}";
                    $this->pdo = new PDO($dsn, null, null, $options);
                    
                    // Log fallback success
                    \App\Utilities\Logger::info("Successfully connected to SQLite fallback database");
                    return;
                } catch (PDOException $e2) {
                    // Use DatabaseErrorHandler for standardized error handling
                    $error = DatabaseErrorHandler::handleException($e2, 'Database::__construct', true);
                    // This will throw an exception so execution won't continue
                }
            }
            
            // Use DatabaseErrorHandler for the original error
            DatabaseErrorHandler::handleException($e, 'Database::__construct', true);
        }
    }
    
    /**
     * Prepare statement with query
     * 
     * @param string $sql SQL query
     * @return void
     */
    public function query($sql)
    {
        $this->stmt = $this->pdo->prepare($sql);
        return $this;
    }
    
    /**
     * Bind values to prepared statement
     * 
     * @param array $params Parameters to bind
     * @return $this For method chaining
     */
    public function bind($params)
    {
        if (is_array($params)) {
            foreach ($params as $param => $value) {
                $this->stmt->bindValue($param, $value, $this->getParamType($value));
            }
        }
        return $this;
    }
    
    /**
     * Determine PDO parameter type
     * 
     * @param mixed $value Parameter value
     * @return int PDO parameter type
     */
    private function getParamType($value)
    {
        if (is_int($value)) {
            return PDO::PARAM_INT;
        } elseif (is_bool($value)) {
            return PDO::PARAM_BOOL;
        } elseif (is_null($value)) {
            return PDO::PARAM_NULL;
        } else {
            return PDO::PARAM_STR;
        }
    }
    
    /**
     * Execute the prepared statement
     * 
     * @param bool $throwOnError Whether to throw an exception on error
     * @return bool Success or failure
     * @throws \Exception If $throwOnError is true and an error occurs
     */
    public function execute(bool $throwOnError = false)
    {
        try {
            return $this->stmt->execute();
        } catch (PDOException $e) {
            // Use DatabaseErrorHandler for standardized error handling
            $error = DatabaseErrorHandler::handleException($e, 'Database::execute', $throwOnError);
            $this->error = $error['details'];
            return false;
        }
    }
    
    /**
     * Get single record as object
     * 
     * @return object|false Record object or false
     */
    public function fetch()
    {
        $this->execute();
        return $this->stmt->fetch();
    }
    
    /**
     * Get results as array of objects
     * 
     * @return array Results
     */
    public function fetchAll()
    {
        $this->execute();
        return $this->stmt->fetchAll();
    }
    
    /**
     * Get row count
     * 
     * @return int Row count
     */
    public function rowCount()
    {
        return $this->stmt->rowCount();
    }
    
    /**
     * Get PDO instance
     * 
     * @return PDO The PDO connection instance
     */
    public function getPdo()
    {
        return $this->pdo;
    }
    
    /**
     * Get last inserted ID
     * 
     * @return string Last inserted ID
     */
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Begin a transaction
     * 
     * @return bool Success or failure
     */
    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }
    
    /**
     * Commit a transaction
     * 
     * @return bool Success or failure
     */
    public function commit()
    {
        return $this->pdo->commit();
    }
    
    /**
     * Roll back a transaction
     * 
     * @return bool Success or failure
     */
    public function rollBack()
    {
        return $this->pdo->rollBack();
    }
    
    /**
     * Execute raw SQL
     * 
     * @param string $sql SQL query
     * @return bool Success or failure
     */
    public function exec($sql)
    {
        try {
            return $this->pdo->exec($sql);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            error_log("Database Exec Error: " . $this->error);
            return false;
        }
    }
    
    /**
     * Get database error
     * 
     * @return string Error message
     */
    public function getError()
    {
        return $this->error;
    }
    
    /**
     * Prepare a statement
     * 
     * @param string $sql SQL query
     * @return \PDOStatement Prepared statement
     */
    public function prepare($sql)
    {
        return $this->pdo->prepare($sql);
    }
}
