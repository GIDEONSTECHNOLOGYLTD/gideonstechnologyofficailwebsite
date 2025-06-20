<?php

namespace App\Models;

use PDO;
use App\Core\Database;
use Exception;

/**
 * Base Model Class
 * 
 * Provides ORM functionality for all models
 */
class Model
{
    /**
     * Database connection
     * @var PDO
     */
    protected $db;
    
    /**
     * Table name in database
     * @var string
     */
    protected $table;
    
    /**
     * Primary key column name
     * @var string
     */
    protected $primaryKey = 'id';
    
    /**
     * Columns that can be filled via mass assignment
     * @var array
     */
    protected $fillable = [];
    
    /**
     * Columns that should be hidden from array/JSON output
     * @var array
     */
    protected $hidden = [];
    
    /**
     * Default column values
     * @var array
     */
    protected $defaults = [];
    
    /**
     * Whether to use timestamps (created_at, updated_at)
     * @var bool
     */
    protected $timestamps = true;
    
    /**
     * Model attributes/data
     * @var array
     */
    protected $attributes = [];
    
    /**
     * Constructor
     * 
     * @param PDO|null $db Database connection (optional)
     */
    public function __construct(?PDO $db = null)
    {
        // If no DB connection provided, get from container
        if ($db === null) {
            $database = new Database();
            $this->db = $database->getPdo();
        } else {
            $this->db = $db;
        }
        
        // If table name not set in child class, generate from class name
        if (empty($this->table)) {
            $className = (new \ReflectionClass($this))->getShortName();
            $this->table = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className)) . 's';
        }
    }
    
    /**
     * Find a record by its primary key
     * 
     * @param int|string $id Primary key value
     * @return static|null Model instance or null if not found
     * @throws \Exception If a database error occurs and throwOnError is true
     */
    public function find($id, bool $throwOnError = false)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1");
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                return null;
            }
            
            return $this->hydrate($result);
        } catch (\PDOException $e) {
            // Use DatabaseErrorHandler for standardized error handling
            \App\Core\DatabaseErrorHandler::handleException($e, get_class($this) . '::find', $throwOnError);
            return null;
        }
    }
    
    /**
     * Find a record by a specific field value
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @return static|null Model instance or null if not found
     */
    public function findBy(string $field, $value)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$field} = :value LIMIT 1");
        $stmt->bindValue(':value', $value);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return null;
        }
        
        return $this->hydrate($result);
    }
    
    /**
     * Find all records
     * 
     * @return array Array of model instances
     */
    public function all(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $models = [];
        
        foreach ($results as $result) {
            $models[] = $this->hydrate($result);
        }
        
        return $models;
    }
    
    /**
     * Find all records ordered by a specific field
     * 
     * @param string $field Field to order by
     * @param string $direction Direction (ASC or DESC)
     * @param int $limit Maximum number of records to return
     * @param int $offset Offset for pagination
     * @return array Array of model instances
     */
    public function findAllOrderBy(string $field, string $direction = 'ASC', int $limit = 100, int $offset = 0): array
    {
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY {$field} {$direction} LIMIT :limit OFFSET :offset");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $models = [];
        
        foreach ($results as $result) {
            $models[] = $this->hydrate($result);
        }
        
        return $models;
    }
    
    /**
     * Find records with where conditions
     * 
     * @param array $conditions Where conditions as field => value pairs
     * @param string $operator Operator to join conditions (AND or OR)
     * @return array Array of model instances
     */
    public function where(array $conditions, string $operator = 'AND'): array
    {
        $operator = strtoupper($operator) === 'OR' ? 'OR' : 'AND';
        $whereClause = [];
        $params = [];
        
        foreach ($conditions as $field => $value) {
            $whereClause[] = "{$field} = :{$field}";
            $params[":{$field}"] = $value;
        }
        
        $whereStr = implode(" {$operator} ", $whereClause);
        $sql = "SELECT * FROM {$this->table} WHERE {$whereStr}";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value);
        }
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $models = [];
        
        foreach ($results as $result) {
            $models[] = $this->hydrate($result);
        }
        
        return $models;
    }
    
    /**
     * Create a new record
     * 
     * @param array $data Data to create record with
     * @return static|false New model instance or false on failure
     */
    public function create(array $data)
    {
        // Filter data to only include fillable fields
        $data = $this->filterFillable($data);
        
        // Apply default values for missing fields
        $data = array_merge($this->defaults, $data);
        
        // Add timestamps if enabled
        if ($this->timestamps) {
            $now = date('Y-m-d H:i:s');
            $data['created_at'] = $now;
            $data['updated_at'] = $now;
        }
        
        // Build query
        $fields = array_keys($data);
        $placeholders = array_map(function($field) {
            return ":{$field}";
        }, $fields);
        
        $fieldsStr = implode(', ', $fields);
        $placeholdersStr = implode(', ', $placeholders);
        
        $sql = "INSERT INTO {$this->table} ({$fieldsStr}) VALUES ({$placeholdersStr})";
        
        try {
            $stmt = $this->db->prepare($sql);
            
            foreach ($data as $field => $value) {
                $stmt->bindValue(":{$field}", $value);
            }
            
            $result = $stmt->execute();
            
            if (!$result) {
                return false;
            }
            
            // Get the last insert ID and find the new record
            $id = $this->db->lastInsertId();
            return $this->find($id);
        } catch (\Exception $e) {
            error_log("Error creating record: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update an existing record
     * 
     * @param int|string $id Primary key value
     * @param array $data Data to update
     * @return bool Success or failure
     */
    public function update($id, array $data): bool
    {
        // Filter data to only include fillable fields
        $data = $this->filterFillable($data);
        
        // Add updated_at timestamp if timestamps enabled
        if ($this->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        // Build query
        $setClause = [];
        foreach ($data as $field => $value) {
            $setClause[] = "{$field} = :{$field}";
        }
        
        $setStr = implode(', ', $setClause);
        $sql = "UPDATE {$this->table} SET {$setStr} WHERE {$this->primaryKey} = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id);
            
            foreach ($data as $field => $value) {
                $stmt->bindValue(":{$field}", $value);
            }
            
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Error updating record: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a record
     * 
     * @param int|string $id Primary key value
     * @return bool Success or failure
     */
    public function delete($id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id);
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Error deleting record: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Count records in table
     * 
     * @param array $conditions Optional where conditions
     * @return int Number of records
     */
    public function count(array $conditions = []): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $field => $value) {
                $whereClause[] = "{$field} = :{$field}";
                $params[":{$field}"] = $value;
            }
            $whereStr = implode(" AND ", $whereClause);
            $sql .= " WHERE {$whereStr}";
        }
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value);
        }
        
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }
    
    /**
     * Paginate results
     * 
     * @param int $page Page number (1-based)
     * @param int $perPage Items per page
     * @param string $orderBy Field to order by
     * @param string $direction Sort direction
     * @return array Array with 'data' and 'pagination' keys
     */
    public function paginate(int $page = 1, int $perPage = 15, string $orderBy = 'id', string $direction = 'ASC'): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        
        // Get total count
        $total = $this->count();
        $totalPages = ceil($total / $perPage);
        
        // Get data for current page
        $data = $this->findAllOrderBy($orderBy, $direction, $perPage, $offset);
        
        return [
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => $totalPages,
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $total)
            ]
        ];
    }
    
    /**
     * Begin a transaction
     * 
     * @return bool Success or failure
     */
    public function beginTransaction(): bool
    {
        return $this->db->beginTransaction();
    }
    
    /**
     * Commit a transaction
     * 
     * @return bool Success or failure
     */
    public function commit(): bool
    {
        return $this->db->commit();
    }
    
    /**
     * Rollback a transaction
     * 
     * @return bool Success or failure
     */
    public function rollback(): bool
    {
        return $this->db->rollBack();
    }
    
    /**
     * Execute a raw SQL query
     * 
     * @param string $sql SQL query
     * @param array $params Parameters for prepared statement
     * @return \PDOStatement|false PDOStatement or false on failure
     */
    public function raw(string $sql, array $params = [])
    {
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $param => $value) {
            $stmt->bindValue(is_numeric($param) ? $param + 1 : $param, $value);
        }
        
        $stmt->execute();
        return $stmt;
    }
    
    /**
     * Get attribute value
     * 
     * @param string $key Attribute name
     * @return mixed Attribute value or null if not exists
     */
    public function __get(string $key)
    {
        return $this->attributes[$key] ?? null;
    }
    
    /**
     * Set attribute value
     * 
     * @param string $key Attribute name
     * @param mixed $value Attribute value
     */
    public function __set(string $key, $value)
    {
        $this->attributes[$key] = $value;
    }
    
    /**
     * Check if attribute exists
     * 
     * @param string $key Attribute name
     * @return bool Whether attribute exists
     */
    public function __isset(string $key)
    {
        return isset($this->attributes[$key]);
    }
    
    /**
     * Convert model to array
     * 
     * @return array Model data as array
     */
    public function toArray(): array
    {
        $data = $this->attributes;
        
        // Remove hidden fields
        foreach ($this->hidden as $field) {
            unset($data[$field]);
        }
        
        return $data;
    }
    
    /**
     * Convert model to JSON
     * 
     * @param int $options JSON encode options
     * @return string JSON representation of model
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
    
    /**
     * Filter data to only include fillable fields
     * 
     * @param array $data Data to filter
     * @return array Filtered data
     */
    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }
    
    /**
     * Hydrate a model instance with data
     * 
     * @param array $data Data to hydrate with
     * @return static Hydrated model instance
     */
    protected function hydrate(array $data)
    {
        $model = new static($this->db);
        $model->attributes = $data;
        return $model;
    }
}