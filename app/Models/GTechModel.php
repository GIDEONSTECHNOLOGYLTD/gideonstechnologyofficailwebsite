<?php

namespace App\Models;

use App\Core\Database;
use PDOException;
use App\Utilities\Logger;

/**
 * GTech Model
 * 
 * Model for GTech services and related functionality
 */
class GTechModel
{
    /**
     * Get all services
     * 
     * @return array List of services
     */
    public static function getAllServices(): array
    {
        try {
            $query = "SELECT * FROM gtech_services ORDER BY service_order ASC";
            return Database::query($query);
        } catch (PDOException $e) {
            Logger::error('Failed to get services: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get a service by ID
     * 
     * @param int $id Service ID
     * @return array|null Service details or null if not found
     */
    public static function getServiceById(int $id): ?array
    {
        try {
            $query = "SELECT * FROM gtech_services WHERE id = :id";
            return Database::queryOne($query, ['id' => $id]);
        } catch (PDOException $e) {
            Logger::error('Failed to get service: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get testimonials
     * 
     * @param int $limit Maximum number of testimonials to return
     * @return array List of testimonials
     */
    public static function getTestimonials(int $limit = 5): array
    {
        try {
            $query = "SELECT * FROM gtech_testimonials WHERE active = 1 ORDER BY created_at DESC LIMIT :limit";
            return Database::query($query, ['limit' => $limit]);
        } catch (PDOException $e) {
            Logger::error('Failed to get testimonials: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Submit a contact request
     * 
     * @param array $data Contact form data
     * @return bool Whether the contact request was successfully submitted
     */
    public static function submitContactRequest(array $data): bool
    {
        try {
            Database::beginTransaction();
            
            $query = "INSERT INTO gtech_contact_requests (name, email, phone, subject, message, created_at) 
                     VALUES (:name, :email, :phone, :subject, :message, NOW())";
            
            $params = [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'subject' => $data['subject'] ?? 'General Inquiry',
                'message' => $data['message']
            ];
            
            $result = Database::execute($query, $params);
            Database::commit();
            
            return $result > 0;
        } catch (PDOException $e) {
            Database::rollback();
            Logger::error('Failed to submit contact request: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get team members
     * 
     * @return array List of team members
     */
    public static function getTeamMembers(): array
    {
        try {
            $query = "SELECT * FROM gtech_team_members WHERE active = 1 ORDER BY member_order ASC";
            return Database::query($query);
        } catch (PDOException $e) {
            Logger::error('Failed to get team members: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get projects/portfolio items
     * 
     * @param int $limit Maximum number of projects to return
     * @return array List of projects
     */
    public static function getProjects(int $limit = 6): array
    {
        try {
            $query = "SELECT * FROM gtech_projects WHERE published = 1 ORDER BY completed_date DESC LIMIT :limit";
            return Database::query($query, ['limit' => $limit]);
        } catch (PDOException $e) {
            Logger::error('Failed to get projects: ' . $e->getMessage());
            return [];
        }
    }
}