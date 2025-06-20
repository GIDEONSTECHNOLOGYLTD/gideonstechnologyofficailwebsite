<?php

namespace App\Services;

use App\Repositories\BaseRepository;
use App\Core\DatabaseErrorHandler;

/**
 * Base Service Class
 * 
 * Provides common business logic operations for all services
 */
abstract class BaseService {
    /**
     * @var BaseRepository The repository instance
     */
    protected $repository;
    
    /**
     * @var DatabaseErrorHandler Error handler
     */
    protected $errorHandler;
    
    /**
     * Constructor
     * 
     * @param BaseRepository $repository The repository instance
     */
    public function __construct(BaseRepository $repository) {
        $this->repository = $repository;
        $this->errorHandler = new DatabaseErrorHandler();
    }
    
    /**
     * Get all records
     * 
     * @param string|null $orderBy Column to order by
     * @param string $direction Sort direction (asc or desc)
     * @return array The records
     */
    public function getAll($orderBy = null, $direction = 'asc') {
        return $this->repository->all($orderBy, $direction);
    }
    
    /**
     * Get a record by ID
     * 
     * @param int|string $id The record ID
     * @return array|null The record or null if not found
     */
    public function getById($id) {
        return $this->repository->find($id);
    }
    
    /**
     * Create a new record
     * 
     * @param array $data The record data
     * @return int|bool The new record ID or false on failure
     */
    public function create(array $data) {
        // Validate data before creating
        $validationResult = $this->validate($data);
        
        if ($validationResult !== true) {
            return $validationResult; // Return validation errors
        }
        
        return $this->repository->create($data);
    }
    
    /**
     * Update a record
     * 
     * @param int|string $id The record ID
     * @param array $data The record data
     * @return bool Success or failure
     */
    public function update($id, array $data) {
        // Validate data before updating
        $validationResult = $this->validate($data, $id);
        
        if ($validationResult !== true) {
            return $validationResult; // Return validation errors
        }
        
        return $this->repository->update($id, $data);
    }
    
    /**
     * Delete a record
     * 
     * @param int|string $id The record ID
     * @return bool Success or failure
     */
    public function delete($id) {
        return $this->repository->delete($id);
    }
    
    /**
     * Validate record data
     * 
     * @param array $data The record data
     * @param int|string|null $id The record ID (for updates)
     * @return true|array True if valid, array of errors if invalid
     */
    abstract protected function validate(array $data, $id = null);
}
