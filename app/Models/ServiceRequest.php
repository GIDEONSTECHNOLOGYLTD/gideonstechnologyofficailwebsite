<?php

namespace App\Models;

use PDO;
use App\Utilities\Logger;

class ServiceRequest
{
    protected $db;
    
    public function __construct()
    {
        global $container;
        $this->db = $container->get('db');
    }
    
    /**
     * Get all service requests by user ID
     */
    public function getAllByUserId(int $userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT sr.*, s.name as service_name
                FROM service_requests sr
                JOIN services s ON sr.service_id = s.id
                WHERE sr.user_id = :user_id 
                ORDER BY sr.created_at DESC
            ");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            Logger::error('Error fetching service requests: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get active service requests by user ID (pending or in progress)
     */
    public function getActiveByUserId(int $userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT sr.*, s.name as service_name
                FROM service_requests sr
                JOIN services s ON sr.service_id = s.id
                WHERE sr.user_id = :user_id 
                AND sr.status IN ('pending', 'in_progress')
                ORDER BY sr.created_at DESC
            ");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            Logger::error('Error fetching active service requests: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get service request by ID
     */
    public function getById(int $id)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT sr.*, s.name as service_name
                FROM service_requests sr
                JOIN services s ON sr.service_id = s.id
                WHERE sr.id = :id
            ");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            Logger::error('Error fetching service request: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create new service request
     */
    public function create(array $data)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO service_requests 
                (user_id, service_id, title, description, priority) 
                VALUES (:user_id, :service_id, :title, :description, :priority)
            ");
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->bindParam(':service_id', $data['service_id']);
            $stmt->bindParam(':title', $data['title']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':priority', $data['priority']);
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (\Exception $e) {
            Logger::error('Error creating service request: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update service request
     */
    public function update(int $id, array $data)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE service_requests 
                SET title = :title, description = :description, 
                    priority = :priority, status = :status
                WHERE id = :id
            ");
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':title', $data['title']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':priority', $data['priority']);
            $stmt->bindParam(':status', $data['status']);
            return $stmt->execute();
        } catch (\Exception $e) {
            Logger::error('Error updating service request: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cancel service request
     */
    public function cancel(int $id)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE service_requests 
                SET status = 'cancelled'
                WHERE id = :id
            ");
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (\Exception $e) {
            Logger::error('Error cancelling service request: ' . $e->getMessage());
            return false;
        }
    }
}
