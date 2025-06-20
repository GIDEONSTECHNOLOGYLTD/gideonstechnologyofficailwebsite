<?php
/**
 * Base Repository Class
 * 
 * Provides common database operations for entities
 * All specific repositories should extend this class
 */

namespace App\Database\Repository;

use App\Database\DatabaseManager;
use App\Utilities\Logger;
use PDO;

abstract class BaseRepository
{
    /**
     * @var PDO The database connection
     */
    protected $connection;
    
    /**
     * @var string The table name
     */
    protected $table;
    
    /**
     * @var string The primary key column name
     */
    protected $primaryKey = 'id';
    
    /**
     * Constructor
     * 
     * @param string|null $table The table name (if null, will be derived from class name)
     */
    public function __construct(?string $table = null)
    {
        // Get database connection
        $this->connection = DatabaseManager::getInstance()->getConnection();
        
        // Set table name if provided
        if ($table !== null) {
            $this->table = $table;
        } else {
            // Try to derive table name from class name
            $className = basename(str_replace('\\', '/', get_class($this)));
            $tableName = str_replace('Repository', '', $className);
            $this->table = $this->toSnakeCase($tableName);
        }
    }
    
    /**
     * Find an entity by ID
     * 
     * @param int|string $id The entity ID
     * @return array|null The entity data or null if not found
     */
    public function findById($id): ?array
    {
        $query = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':id', $id);
        $statement->execute();
        
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result !== false ? $result : null;
    }
    
    /**
     * Find all entities
     * 
     * @param int|null $limit Maximum number of entities to return
     * @param int $offset Offset for pagination
     * @return array Array of entities
     */
    public function findAll(?int $limit = null, int $offset = 0): array
    {
        $query = "SELECT * FROM {$this->table}";
        
        if ($limit !== null) {
            $query .= " LIMIT :limit OFFSET :offset";
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
            $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        } else {
            $statement = $this->connection->prepare($query);
        }
        
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Find by a specific field value
     * 
     * @param string $field The field name
     * @param mixed $value The field value
     * @return array Matching entities
     */
    public function findBy(string $field, $value): array
    {
        $query = "SELECT * FROM {$this->table} WHERE {$field} = :value";
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':value', $value);
        $statement->execute();
        
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Find one entity by a specific field value
     * 
     * @param string $field The field name
     * @param mixed $value The field value
     * @return array|null The entity data or null if not found
     */
    public function findOneBy(string $field, $value): ?array
    {
        $query = "SELECT * FROM {$this->table} WHERE {$field} = :value LIMIT 1";
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':value', $value);
        $statement->execute();
        
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result !== false ? $result : null;
    }
    
    /**
     * Find by multiple conditions
     * 
     * @param array $criteria Associative array of field-value pairs
     * @param array $orderBy Associative array of field-direction pairs
     * @param int|null $limit Maximum number of entities to return
     * @param int $offset Offset for pagination
     * @return array Matching entities
     */
    public function findByMultiple(
        array $criteria,
        array $orderBy = [],
        ?int $limit = null,
        int $offset = 0
    ): array {
        // Build WHERE clause
        $whereConditions = [];
        $params = [];
        
        foreach ($criteria as $field => $value) {
            $paramName = ':' . $field;
            $whereConditions[] = "{$field} = {$paramName}";
            $params[$paramName] = $value;
        }
        
        $whereClause = count($whereConditions) > 0 
            ? 'WHERE ' . implode(' AND ', $whereConditions) 
            : '';
        
        // Build ORDER BY clause
        $orderByParts = [];
        foreach ($orderBy as $field => $direction) {
            $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
            $orderByParts[] = "{$field} {$direction}";
        }
        
        $orderByClause = count($orderByParts) > 0 
            ? 'ORDER BY ' . implode(', ', $orderByParts) 
            : '';
        
        // Build LIMIT clause
        $limitClause = $limit !== null 
            ? "LIMIT {$limit} OFFSET {$offset}" 
            : '';
        
        // Build complete query
        $query = "SELECT * FROM {$this->table} {$whereClause} {$orderByClause} {$limitClause}";
        
        // Execute query
        $statement = $this->connection->prepare($query);
        foreach ($params as $name => $value) {
            $statement->bindValue($name, $value);
        }
        
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Insert a new entity
     * 
     * @param array $data Associative array of field-value pairs
     * @return int|string The new entity ID
     */
    public function create(array $data)
    {
        // Filter out non-existent fields
        $data = $this->filterFields($data);
        
        if (empty($data)) {
            throw new \InvalidArgumentException("No valid fields provided for creation");
        }
        
        // Build query
        $fields = array_keys($data);
        $placeholders = array_map(function($field) {
            return ':' . $field;
        }, $fields);
        
        $query = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(', ', $fields),
            implode(', ', $placeholders)
        );
        
        // Execute query
        try {
            $statement = $this->connection->prepare($query);
            
            foreach ($data as $field => $value) {
                $statement->bindValue(':' . $field, $value);
            }
            
            $statement->execute();
            return $this->connection->lastInsertId();
        } catch (\PDOException $e) {
            Logger::error("Error creating entity in {$this->table}: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Update an existing entity
     * 
     * @param int|string $id The entity ID
     * @param array $data Associative array of field-value pairs
     * @return bool True if successful
     */
    public function update($id, array $data): bool
    {
        // Filter out non-existent fields
        $data = $this->filterFields($data);
        
        if (empty($data)) {
            return false; // Nothing to update
        }
        
        // Build SET clause
        $setClauses = array_map(function($field) {
            return "{$field} = :{$field}";
        }, array_keys($data));
        
        $query = sprintf(
            "UPDATE %s SET %s WHERE %s = :id",
            $this->table,
            implode(', ', $setClauses),
            $this->primaryKey
        );
        
        // Execute query
        try {
            $statement = $this->connection->prepare($query);
            
            // Bind values
            $statement->bindValue(':id', $id);
            foreach ($data as $field => $value) {
                $statement->bindValue(':' . $field, $value);
            }
            
            $statement->execute();
            return $statement->rowCount() > 0;
        } catch (\PDOException $e) {
            Logger::error("Error updating entity in {$this->table}: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Delete an entity by ID
     * 
     * @param int|string $id The entity ID
     * @return bool True if successful
     */
    public function delete($id): bool
    {
        $query = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        
        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id);
            $statement->execute();
            
            return $statement->rowCount() > 0;
        } catch (\PDOException $e) {
            Logger::error("Error deleting entity from {$this->table}: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Count entities matching criteria
     * 
     * @param array $criteria Associative array of field-value pairs
     * @return int Number of matching entities
     */
    public function count(array $criteria = []): int
    {
        // Build WHERE clause
        $whereConditions = [];
        $params = [];
        
        foreach ($criteria as $field => $value) {
            $paramName = ':' . $field;
            $whereConditions[] = "{$field} = {$paramName}";
            $params[$paramName] = $value;
        }
        
        $whereClause = count($whereConditions) > 0 
            ? 'WHERE ' . implode(' AND ', $whereConditions) 
            : '';
        
        // Build query
        $query = "SELECT COUNT(*) FROM {$this->table} {$whereClause}";
        
        // Execute query
        $statement = $this->connection->prepare($query);
        foreach ($params as $name => $value) {
            $statement->bindValue($name, $value);
        }
        
        $statement->execute();
        return (int) $statement->fetchColumn();
    }
    
    /**
     * Execute a raw query
     * 
     * @param string $query The SQL query
     * @param array $params The query parameters
     * @return \PDOStatement
     */
    public function executeRaw(string $query, array $params = []): \PDOStatement
    {
        $statement = $this->connection->prepare($query);
        $statement->execute($params);
        return $statement;
    }
    
    /**
     * Filter fields that don't exist in the database table
     * 
     * @param array $data The data to filter
     * @return array Filtered data
     */
    protected function filterFields(array $data): array
    {
        try {
            // Get table columns
            $columnsQuery = $this->connection->query("DESCRIBE {$this->table}");
            if (!$columnsQuery) {
                // If DESCRIBE doesn't work (SQLite), try another approach
                $columnsQuery = $this->connection->query("PRAGMA table_info({$this->table})");
                if (!$columnsQuery) {
                    // If we can't get column info, return data as is
                    return $data;
                }
                
                $columns = [];
                while ($row = $columnsQuery->fetch(PDO::FETCH_ASSOC)) {
                    $columns[] = $row['name'];
                }
            } else {
                $columns = [];
                while ($row = $columnsQuery->fetch(PDO::FETCH_ASSOC)) {
                    $columns[] = $row['Field'];
                }
            }
            
            // Filter data
            return array_intersect_key($data, array_flip($columns));
        } catch (\PDOException $e) {
            Logger::warning("Could not filter fields for {$this->table}: " . $e->getMessage());
            return $data; // Return data as is
        }
    }
    
    /**
     * Convert a camel case string to snake case
     * 
     * @param string $input The input string
     * @return string The snake case string
     */
    protected function toSnakeCase(string $input): string
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $input));
    }
}
