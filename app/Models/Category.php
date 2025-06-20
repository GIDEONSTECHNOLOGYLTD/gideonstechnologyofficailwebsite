<?php

namespace App\Models;

use PDO;
use App\Utilities\Logger;

/**
 * Category Model
 * 
 * Handles categories for products, services, and templates
 */
class Category extends Model
{
    /**
     * Table name in database
     * @var string
     */
    protected $table = 'categories';
    
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
        'parent_id',
        'type',
        'icon',
        'image',
        'sort_order',
        'status'
    ];
    
    /**
     * Default column values
     * @var array
     */
    protected $defaults = [
        'status' => 'active',
        'type' => 'product',
        'sort_order' => 0
    ];
    
    /**
     * Whether to use timestamps (created_at, updated_at)
     * @var bool
     */
    protected $timestamps = true;
    
    /**
     * Get all categories
     * 
     * @return array All categories
     */
    public function getAll(): array
    {
        try {
            return $this->all();
        } catch (\Exception $e) {
            Logger::error('Error in Category::getAll(): ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get all active categories
     * 
     * @return array Active categories
     */
    public function findAllActive(): array
    {
        try {
            return $this->where(['status' => 'active']);
        } catch (\Exception $e) {
            Logger::error('Error in Category::findAllActive(): ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get categories by type
     * 
     * @param string $type Category type (product, service, template)
     * @return array Categories of the specified type
     */
    public function findByType(string $type): array
    {
        try {
            return $this->where(['type' => $type, 'status' => 'active'], 'AND');
        } catch (\Exception $e) {
            Logger::error('Error in Category::findByType(): ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Find category by slug
     * 
     * @param string $slug Category slug
     * @return Category|null Category model or null if not found
     */
    public function findBySlug(string $slug)
    {
        try {
            // Validate slug
            if (empty($slug) || !is_string($slug)) {
                Logger::error('Invalid slug provided to Category::findBySlug()');
                return null;
            }
            
            return $this->findOneBy('slug', $slug);
        } catch (\Exception $e) {
            Logger::error('Error in Category::findBySlug(): ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get all parent categories (those with no parent)
     * 
     * @param string $type Optional category type filter
     * @return array Parent categories
     */
    public function findAllParents(string $type = null): array
    {
        try {
            $conditions = ['parent_id' => null, 'status' => 'active'];
            
            // Add type filter if provided
            if ($type !== null) {
                $conditions['type'] = $type;
            }
            
            return $this->where($conditions, 'AND');
        } catch (\Exception $e) {
            Logger::error('Error in Category::findAllParents(): ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get all subcategories for a parent category
     * 
     * @param int $parentId Parent category ID
     * @param string $type Optional category type filter
     * @return array Subcategories
     */
    public function findSubcategories(int $parentId, string $type = null): array
    {
        try {
            // Validate parent ID
            if ($parentId <= 0) {
                Logger::warning('Invalid parent ID provided to Category::findSubcategories(): ' . $parentId);
                return [];
            }
            
            $conditions = [
                'parent_id' => $parentId,
                'status' => 'active'
            ];
            
            // Add type filter if provided
            if ($type !== null) {
                $conditions['type'] = $type;
            }
            
            return $this->where($conditions, 'AND');
        } catch (\Exception $e) {
            Logger::error('Error in Category::findSubcategories(): ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get product count by category
     * 
     * @param int $categoryId Category ID
     * @return int Product count
     */
    public function getProductCount(int $categoryId): int
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM products WHERE category_id = :category_id AND status = 'active'");
            $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return isset($result['count']) ? (int)$result['count'] : 0;
        } catch (\Exception $e) {
            Logger::error('Error in Category::getProductCount(): ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Create a new category with slug generation
     * 
     * @param array $data Category data
     * @return Category|false Category model or false on failure
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
     * @param string $name Category name
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
        $existingCategory = $this->findOneBy('slug', $slug);
        
        // If slug exists, append a number
        if ($existingCategory) {
            $i = 1;
            $baseSlug = $slug;
            
            while ($this->findOneBy('slug', $slug)) {
                $slug = $baseSlug . '-' . $i++;
            }
        }
        
        return $slug;
    }
    
    /**
     * Get hierarchy of categories for navigation
     * 
     * @return array Category hierarchy
     */
    public function getCategoryHierarchy(): array
    {
        $parents = $this->findAllParents();
        
        foreach ($parents as &$parent) {
            $parent->subcategories = $this->findSubcategories($parent->id);
        }
        
        return $parents;
    }
}