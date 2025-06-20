<?php
/**
 * Base Model Class
 * 
 * This class serves as the base for all models in the application.
 * It provides common functionality for database operations.
 */

namespace App\Core;

use PDO;
use PDOException;

class BaseModel
{
    /**
     * @var DatabaseManager Database manager instance
     */
    protected $db;
    
    /**
     * @var string Table name for this model
     */
    protected $table;
    
    /**
     * @var string Primary key column name
     */
    protected $primaryKey = 'id';
    
    /**
     * Constructor
     * 
     * @param DatabaseManager|null $db Database manager instance
     */
    public function __construct(?DatabaseManager $db = null)
    {
        $this->db = $db ?? DatabaseManager::getInstance();
    }
    
    /**
     * Find a record by ID
     * 
     * @param int $id Record ID
     * @return array|null Record data or null if not found
     */
    public function find($id)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Database error in {$this->table}::find(): {$e->getMessage()}");
            return null;
        }
    }
    
    /**
     * Get all records from the table
     * 
     * @param string $orderBy Column to order by
     * @param string $direction Sort direction (ASC or DESC)
     * @return array Array of records
     */
    public function all($orderBy = null, $direction = 'ASC')
    {
        try {
            $sql = "SELECT * FROM {$this->table}";
            
            if ($orderBy) {
                $sql .= " ORDER BY {$orderBy} {$direction}";
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in {$this->table}::all(): {$e->getMessage()}");
            return [];
        }
    }
    
    /**
     * Create a new record
     * 
     * @param array $data Record data
     * @return int|bool New record ID or false on failure
     */
    public function create(array $data)
    {
        try {
            $columns = implode(', ', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));
            
            $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
            $stmt = $this->db->prepare($sql);
            
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Database error in {$this->table}::create(): {$e->getMessage()}");
            return false;
        }
    }
    
    /**
     * Update a record
     * 
     * @param int $id Record ID
     * @param array $data Record data
     * @return bool Success or failure
     */
    public function update($id, array $data)
    {
        try {
            $setClause = '';
            foreach (array_keys($data) as $key) {
                $setClause .= "{$key} = :{$key}, ";
            }
            $setClause = rtrim($setClause, ', ');
            
            $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = :id";
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':id', $id);
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database error in {$this->table}::update(): {$e->getMessage()}");
            return false;
        }
    }
    
    /**
     * Delete a record
     * 
     * @param int $id Record ID
     * @return bool Success or failure
     */
    public function delete($id)
    {
        try {
            $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database error in {$this->table}::delete(): {$e->getMessage()}");
            return false;
        }
    }
    
    /**
     * Find records by a specific field value
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @return array Array of matching records
     */
    public function findBy($field, $value)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE {$field} = :value";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':value', $value);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in {$this->table}::findBy(): {$e->getMessage()}");
            return [];
        }
    }
    
    /**
     * Count records in the table
     * 
     * @param string $whereClause Optional WHERE clause
     * @param array $params Parameters for the WHERE clause
     * @return int Number of records
     */
    public function count($whereClause = '', array $params = [])
    {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table}";
            
            if ($whereClause) {
                $sql .= " WHERE {$whereClause}";
            }
            
            $stmt = $this->db->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Database error in {$this->table}::count(): {$e->getMessage()}");
            return 0;
        }
    }
}
