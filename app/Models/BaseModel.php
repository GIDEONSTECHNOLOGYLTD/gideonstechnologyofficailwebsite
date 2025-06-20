<?php

namespace App\Models;

use App\Core\DatabaseManager;
use PDO;

/**
 * Base Model
 * 
 * Provides common functionality for all models
 */
abstract class BaseModel
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table;

    /**
     * Primary key
     * 
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Fillable fields
     * 
     * @var array
     */
    protected $fillable = [];

    /**
     * Get database connection
     *
     * @return PDO
     */
    protected function getConnection(): PDO
    {
        return DatabaseManager::getConnection();
    }

    /**
     * Find record by primary key
     *
     * @param mixed $id
     * @return array|null
     */
    public function find($id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        
        return $result !== false ? $result : null;
    }

    /**
     * Get all records
     *
     * @return array
     */
    public function all(): array
    {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->getConnection()->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Create new record
     *
     * @param array $data
     * @return int|bool Last insert ID or false on failure
     */
    public function create(array $data)
    {
        // Filter data to include only fillable fields
        $filteredData = array_intersect_key($data, array_flip($this->fillable));
        
        if (empty($filteredData)) {
            return false;
        }
        
        $columns = implode(', ', array_keys($filteredData));
        $placeholders = implode(', ', array_fill(0, count($filteredData), '?'));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->getConnection()->prepare($sql);
        
        $result = $stmt->execute(array_values($filteredData));
        
        return $result ? $this->getConnection()->lastInsertId() : false;
    }

    /**
     * Update record
     *
     * @param mixed $id
     * @param array $data
     * @return bool
     */
    public function update($id, array $data): bool
    {
        // Filter data to include only fillable fields
        $filteredData = array_intersect_key($data, array_flip($this->fillable));
        
        if (empty($filteredData)) {
            return false;
        }
        
        $setStatements = [];
        foreach (array_keys($filteredData) as $column) {
            $setStatements[] = "{$column} = :{$column}";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setStatements) . " WHERE {$this->primaryKey} = :id";
        
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':id', $id);
        
        foreach ($filteredData as $column => $value) {
            $stmt->bindValue(":{$column}", $value);
        }
        
        return $stmt->execute();
    }

    /**
     * Delete record
     *
     * @param mixed $id
     * @return bool
     */
    public function delete($id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->getConnection()->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Find records by field
     *
     * @param string $field
     * @param mixed $value
     * @return array
     */
    public function findBy(string $field, $value): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$field} = :value";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute(['value' => $value]);
        return $stmt->fetchAll();
    }

    /**
     * Find first record by field
     *
     * @param string $field
     * @param mixed $value
     * @return array|null
     */
    public function findOneBy(string $field, $value): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$field} = :value LIMIT 1";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute(['value' => $value]);
        $result = $stmt->fetch();
        
        return $result !== false ? $result : null;
    }

    /**
     * Execute custom query
     *
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get table name
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Set table name
     *
     * @param string $table
     * @return self
     */
    public function setTable(string $table): self
    {
        $this->table = $table;
        return $this;
    }
}