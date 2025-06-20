<?php

namespace App\Models;

use PDO;

/**
 * Service Model
 * Handles business logic related to services offered
 */
class Service extends Model
{
    /**
     * Table name in database
     * @var string
     */
    protected $table = 'services';
    
    /**
     * Primary key column name
     * @var string
     */
    protected $primaryKey = 'id';
    
    /**
     * Columns that can be filled via mass assignment
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'category_id',
        'featured',
        'status',
        'icon',
        'sort_order',
        'keywords'
    ];

        /**
     * Default column values
     * @var array
     */
    protected $defaults = [
        'status' => 'active',
        'featured' => 0,
        'sort_order' => 0
    ];
    
    /**
     * Whether to use timestamps (created_at, updated_at)
     * @var bool
     */
    protected $timestamps = true;
    
    /**
     * Get featured services
     * 
     * @param int $limit Number of services to return
     * @return array List of featured services
     */
    public function getFeatured(int $limit = 3): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE featured = 1 AND status = 'active'
            ORDER BY sort_order, name
            LIMIT :limit
        ");
        
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $services = [];
        
        foreach ($results as $result) {
            $services[] = $this->hydrate($result);
        }
        
        return $services;
    }
    
    /**
     * Get service by slug
     * 
     * @param string $slug Service slug
     * @return Service|null Service model or null if not found
     */
    public function getBySlug(string $slug)
    {
        return $this->findBy('slug', $slug);
    }
    
    /**
     * Get services by category
     * 
     * @param int $categoryId Category ID
     * @param int $limit Number of services to return
     * @return array List of services
     */
    public function getByCategory(int $categoryId, int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
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
            $services[] = $this->hydrate($result);
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
            SELECT * FROM {$this->table} 
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
            $services[] = $this->hydrate($result);
        }
        
        return $services;
    }
    
    /**
     * Get service pricing options
     * 
     * @param int $serviceId Service ID
     * @return array List of pricing options
     */
    public function getPricingOptions(int $serviceId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM service_pricing 
            WHERE service_id = :service_id
            ORDER BY price ASC
        ");
        
        $stmt->bindParam(':service_id', $serviceId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get active services
     * 
     * @param int $limit Maximum number of services to return
     * @return array List of active services
     */
    public function getActiveServices(int $limit = 10): array
    {
        return $this->where(['status' => 'active'], 'AND');
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
        // Convert to lowercase and replace spaces with dashes
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Check if slug already exists
        $existingService = $this->findBy('slug', $slug);
        
        // If slug exists, append a number
        if ($existingService) {
            $i = 1;
            $baseSlug = $slug;
            
            while ($this->findBy('slug', $slug)) {
                $slug = $baseSlug . '-' . $i++;
            }
        }
        
        return $slug;
    }
    
    /**
     * Get all services with optional limit
     * 
     * @param int $limit Maximum number of services to return
     * @return array Array of services
     */
    public function getAll(int $limit = 20): array
    {
        try {
            $db = $this->db ?? $this->getConnection();
            $sql = "SELECT * FROM {$this->table} WHERE status = 'active' ORDER BY sort_order ASC";
            
            if ($limit > 0) {
                $sql .= " LIMIT :limit";
            }
            
            $stmt = $db->prepare($sql);
            
            if ($limit > 0) {
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            // Log the error
            error_log('Error in Service::getAll(): ' . $e->getMessage());
            return [];
        }
    }
}