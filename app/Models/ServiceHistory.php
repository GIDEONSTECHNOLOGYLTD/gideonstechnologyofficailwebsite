<?php

namespace App\Models;

use PDO;
use App\Core\Logger;

class ServiceHistory extends BaseModel
{
    protected $table = 'service_history';
    
    /**
     * Get all service history by user ID
     */
    public function getAllByUserId(int $userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT sh.*, s.name as service_name
                FROM {$this->table} sh
                JOIN services s ON sh.service_id = s.id
                WHERE sh.user_id = ?
                ORDER BY sh.completion_date DESC
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            Logger::error('Error fetching service history: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get service history by ID
     */
    public function getById(int $id)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT sh.*, s.name as service_name
                FROM {$this->table} sh
                JOIN services s ON sh.service_id = s.id
                WHERE sh.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            Logger::error('Error fetching service history: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create new service history record
     */
    public function create(array $data)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} 
                (user_id, service_id, service_request_id, service_type, description, completion_date, technician_id)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['user_id'],
                $data['service_id'],
                $data['service_request_id'] ?? null,
                $data['service_type'],
                $data['description'],
                $data['completion_date'],
                $data['technician_id'] ?? null
            ]);
            return $this->db->lastInsertId();
        } catch (\Exception $e) {
            Logger::error('Error creating service history: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Add feedback to a service history record
     */
    public function addFeedback(int $id, int $rating, string $comment = null)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE {$this->table}
                SET feedback_rating = ?, feedback_comment = ?
                WHERE id = ?
            ");
            $stmt->execute([$rating, $comment, $id]);
            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            Logger::error('Error adding service feedback: ' . $e->getMessage());
            return false;
        }
    }
}
