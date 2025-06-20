<?php

namespace App\Repositories;

use PDO;
use App\Core\DatabaseErrorHandler;

/**
 * Base Repository Class
 * 
 * Provides common database operations for all repositories
 */
abstract class BaseRepository {
    /**
     * @var PDO The database connection
     */
    protected $pdo;
    
    /**
     * @var string The table name
     */
    protected $table;
    
    /**
     * @var string The primary key column name
     */
    protected $primaryKey = 'id';
    
    /**
     * @var array Columns that should be treated as dates
     */
    protected $dates = ['created_at', 'updated_at'];
    
    /**
     * @var DatabaseErrorHandler Error handler
     */
    protected $errorHandler;
    
    /**
     * Constructor
     * 
     * @param PDO $pdo Database connection
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->errorHandler = new DatabaseErrorHandler();
    }
    
    /**
     * Find a record by its primary key
     * 
     * @param int|string $id The primary key value
     * @return array|null The record or null if not found
     */
    public function find($id) {
        try {
            // Use query cache if available
            if (class_exists('\App\Core\QueryCache')) {
                $queryCache = \App\Core\QueryCache::getInstance();
                $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
                $params = [':id' => $id];
                
                return $queryCache->getOrSet($sql, $params, function() use ($sql, $id) {
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->bindValue(':id', $id);
                    $stmt->execute();
                    
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    return $result ?: null;
                }, 300, 'table_' . $this->table); // Cache for 5 minutes
            }
            
            // Fallback to direct query if cache not available
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1");
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (\PDOException $e) {
            $this->errorHandler->handleError($e, "Error finding record in {$this->table}");
            return null;
        }
    }
    
    /**
     * Get all records from the table
     * 
     * @param string|null $orderBy Column to order by
     * @param string $direction Sort direction (asc or desc)
     * @return array The records
     */
    public function all($orderBy = null, $direction = 'asc') {
        try {
            $sql = "SELECT * FROM {$this->table}";
            
            if ($orderBy) {
                $sql .= " ORDER BY {$orderBy} {$direction}";
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            $this->errorHandler->handleError($e, "Error getting all records from {$this->table}");
            return [];
        }
    }
    
    /**
     * Create a new record
     * 
     * @param array $data The data to insert
     * @return int|bool The last insert ID or false on failure
     */
    public function create(array $data) {
        try {
            // Add timestamps if they exist in the table
            if (in_array('created_at', $this->dates)) {
                $data['created_at'] = date('Y-m-d H:i:s');
            }
            
            if (in_array('updated_at', $this->dates)) {
                $data['updated_at'] = date('Y-m-d H:i:s');
            }
            
            $columns = array_keys($data);
            $placeholders = array_map(function($col) { return ":$col"; }, $columns);
            
            $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") "
                 . "VALUES (" . implode(', ', $placeholders) . ")";
            
            $stmt = $this->pdo->prepare($sql);
            
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            $stmt->execute();
            return $this->pdo->lastInsertId();
        } catch (\PDOException $e) {
            $this->errorHandler->handleError($e, "Error creating record in {$this->table}");
            return false;
        }
    }
    
    /**
     * Update a record
     * 
     * @param int|string $id The primary key value
     * @param array $data The data to update
     * @return bool Success or failure
     */
    public function update($id, array $data) {
        try {
            // Add updated_at timestamp if it exists in the table
            if (in_array('updated_at', $this->dates)) {
                $data['updated_at'] = date('Y-m-d H:i:s');
            }
            
            $setStatements = array_map(function($col) { return "$col = :$col"; }, array_keys($data));
            
            $sql = "UPDATE {$this->table} SET " . implode(', ', $setStatements) . " "
                 . "WHERE {$this->primaryKey} = :id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id);
            
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            return $stmt->execute();
        } catch (\PDOException $e) {
            $this->errorHandler->handleError($e, "Error updating record in {$this->table}");
            return false;
        }
    }
    
    /**
     * Delete a record
     * 
     * @param int|string $id The primary key value
     * @return bool Success or failure
     */
    public function delete($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
            $stmt->bindValue(':id', $id);
            return $stmt->execute();
        } catch (\PDOException $e) {
            $this->errorHandler->handleError($e, "Error deleting record from {$this->table}");
            return false;
        }
    }
    
    /**
     * Find records by a specific field value
     * 
     * @param string $field The field name
     * @param mixed $value The field value
     * @return array The matching records
     */
    public function findBy($field, $value) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE {$field} = :value");
            $stmt->bindValue(':value', $value);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            $this->errorHandler->handleError($e, "Error finding records by field in {$this->table}");
            return [];
        }
    }
    
    /**
     * Find a single record by a specific field value
     * 
     * @param string $field The field name
     * @param mixed $value The field value
     * @return array|null The record or null if not found
     */
    public function findOneBy($field, $value) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE {$field} = :value LIMIT 1");
            $stmt->bindValue(':value', $value);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (\PDOException $e) {
            $this->errorHandler->handleError($e, "Error finding record by field in {$this->table}");
            return null;
        }
    }
    
    /**
     * Count records in the table
     * 
     * @param string|null $whereClause Optional WHERE clause
     * @param array $params Optional parameters for the WHERE clause
     * @return int The count
     */
    public function count($whereClause = null, array $params = []) {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table}";
            
            if ($whereClause) {
                $sql .= " WHERE {$whereClause}";
            }
            
            $stmt = $this->pdo->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (\PDOException $e) {
            $this->errorHandler->handleError($e, "Error counting records in {$this->table}");
            return 0;
        }
    }
    
    /**
     * Execute a custom query
     * 
     * @param string $sql The SQL query
     * @param array $params The query parameters
     * @param bool $fetchAll Whether to fetch all results or just one
     * @return array|mixed The query results
     */
    public function query($sql, array $params = [], $fetchAll = true) {
        try {
            $stmt = $this->pdo->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue(is_numeric($key) ? $key + 1 : ":$key", $value);
            }
            
            $stmt->execute();
            
            if ($fetchAll) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result ?: null;
            }
        } catch (\PDOException $e) {
            $this->errorHandler->handleError($e, "Error executing custom query");
            return $fetchAll ? [] : null;
        }
    }
}
