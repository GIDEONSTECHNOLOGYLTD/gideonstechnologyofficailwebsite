<?php

namespace App\Repositories;

use App\Models\Service;
use PDO;

/**
 * Service Repository
 * 
 * Implements the repository pattern for Service model
 */
class ServiceRepository extends Repository
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(new Service());
    }
    
    /**
     * Get featured services
     * 
     * @param int $limit Number of services to return
     * @return array List of featured services
     */
    public function getFeatured(int $limit = 3): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->model->getTable()} 
            WHERE featured = 1 AND status = 'active'
            ORDER BY sort_order, name
            LIMIT :limit
        ");
        
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $services = [];
        
        foreach ($results as $result) {
            $services[] = $this->model->hydrate($result);
        }
        
        return $services;
    }
    
    /**
     * Get service by slug
     * 
     * @param string $slug Service slug
     * @return Service|null Service model or null if not found
     */
    public function findBySlug(string $slug)
    {
        return $this->findOneBy('slug', $slug);
    }
    
    /**
     * Get services by category
     * 
     * @param int $categoryId Category ID
     * @param int $limit Number of services to return
     * @return array List of services
     */
    public function findByCategory(int $categoryId, int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->model->getTable()} 
            WHERE category_id = :category_id AND status = 'active'
            ORDER BY sort_order, name
            LIMIT :limit
        ");
        
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $services = [];
        
        foreach ($results as $result) {
            $services[] = $this->model->hydrate($result);
        }
        
        return $services;
    }
    
    /**
     * Search services
     * 
     * @param string $keyword Search keyword
     * @param int $limit Number of services to return
     * @return array List of services
     */
    public function search(string $keyword, int $limit = 10): array
    {
        $keyword = "%{$keyword}%";
        
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->model->getTable()} 
            WHERE (name LIKE :name OR description LIKE :description OR keywords LIKE :keywords) 
            AND status = 'active'
            ORDER BY name
            LIMIT :limit
        ");
        
        $stmt->bindParam(':name', $keyword);
        $stmt->bindParam(':description', $keyword);
        $stmt->bindParam(':keywords', $keyword);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $services = [];
        
        foreach ($results as $result) {
            $services[] = $this->model->hydrate($result);
        }
        
        return $services;
    }
    
    /**
     * Create a new service with slug generation
     * 
     * @param array $data Service data
     * @return Service|false Service model or false on failure
     */
    public function createWithSlug(array $data)
    {
        // Generate slug if not provided
        if (!isset($data['slug']) && isset($data['name'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }
        
        return $this->create($data);
    }
    
    /**
     * Generate a unique slug from a name
     * 
     * @param string $name Service name
     * @return string Unique slug
     */
    protected function generateSlug(string $name): string
    {
        // Convert to lowercase and replace spaces with hyphens
        $slug = strtolower(str_replace(' ', '-', $name));
        
        // Remove any characters that aren't alphanumeric or hyphens
        $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
        
        // Remove multiple hyphens
        $slug = preg_replace('/-+/', '-', $slug);
        
        // Trim hyphens from beginning and end
        $slug = trim($slug, '-');
        
        // Check if slug already exists
        $existingService = $this->findOneBy('slug', $slug);
        
        // If slug exists, append a number
        if ($existingService) {
            $i = 1;
            $baseSlug = $slug;
            
            while ($this->findOneBy('slug', $slug)) {
                $slug = $baseSlug . '-' . $i++;
            }
        }
        
        return $slug;
    }
}
