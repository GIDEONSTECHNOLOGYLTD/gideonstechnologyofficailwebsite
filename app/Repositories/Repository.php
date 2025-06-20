<?php

namespace App\Repositories;

use App\Models\Model;

/**
 * Base Repository Class
 * 
 * Implements the repository pattern for data access
 */
abstract class Repository
{
    /**
     * The model instance
     * @var Model
     */
    protected $model;
    
    /**
     * Constructor
     * 
     * @param Model $model The model instance
     */
    public function __construct(Model $model)
    {   
        $this->model = $model;
    }
    
    /**
     * Get all records
     * 
     * @return array
     */
    public function all(): array
    {
        return $this->model->all();
    }
    
    /**
     * Find a record by ID
     * 
     * @param int|string $id
     * @return Model|null
     */
    public function find($id)
    {
        return $this->model->find($id);
    }
    
    /**
     * Find by specific criteria
     * 
     * @param array $criteria
     * @param string $operator
     * @return array
     */
    public function findBy(array $criteria, string $operator = 'AND'): array
    {
        return $this->model->where($criteria, $operator);
    }
    
    /**
     * Find a single record by a field value
     * 
     * @param string $field
     * @param mixed $value
     * @return Model|null
     */
    public function findOneBy(string $field, $value)
    {
        return $this->model->findBy($field, $value);
    }
    
    /**
     * Create a new record
     * 
     * @param array $data
     * @return Model|false
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }
    
    /**
     * Update a record
     * 
     * @param int|string $id
     * @param array $data
     * @return bool
     */
    public function update($id, array $data): bool
    {
        return $this->model->update($id, $data);
    }
    
    /**
     * Delete a record
     * 
     * @param int|string $id
     * @return bool
     */
    public function delete($id): bool
    {
        return $this->model->delete($id);
    }
    
    /**
     * Count records
     * 
     * @param array $criteria
     * @return int
     */
    public function count(array $criteria = []): int
    {
        return $this->model->count($criteria);
    }
    
    /**
     * Paginate results
     * 
     * @param int $page
     * @param int $perPage
     * @param string $orderBy
     * @param string $direction
     * @return array
     */
    public function paginate(int $page = 1, int $perPage = 15, string $orderBy = 'id', string $direction = 'ASC'): array
    {
        return $this->model->paginate($page, $perPage, $orderBy, $direction);
    }
    
    /**
     * Begin a database transaction
     * 
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->model->beginTransaction();
    }
    
    /**
     * Commit a database transaction
     * 
     * @return bool
     */
    public function commit(): bool
    {
        return $this->model->commit();
    }
    
    /**
     * Rollback a database transaction
     * 
     * @return bool
     */
    public function rollback(): bool
    {
        return $this->model->rollback();
    }
    
    /**
     * Execute a raw SQL query
     * 
     * @param string $sql
     * @param array $params
     * @return \PDOStatement|false
     */
    public function raw(string $sql, array $params = [])
    {
        return $this->model->raw($sql, $params);
    }
}
