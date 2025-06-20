<?php

namespace App\Repositories;

use App\Models\Product;
use PDO;

/**
 * Product Repository
 * 
 * Implements the repository pattern for Product model
 */
class ProductRepository extends Repository
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(new Product());
    }

    /**
     * Get all active products
     * 
     * @return array Array of Product models
     */
    public function getAll(): array
    {
        return $this->findBy(['status' => 'active']);
    }

    /**
     * Get a single product by ID
     * 
     * @param int $id Product ID
     * @return Product|null Product model or null if not found
     */
    public function getById(int $id)
    {
        return $this->find($id);
    }

    /**
     * Get a single product by slug
     * 
     * @param string $slug Product slug
     * @return Product|null Product model or null if not found
     */
    public function getBySlug(string $slug)
    {
        return $this->findOneBy('slug', $slug);
    }

    /**
     * Get products by category ID
     * 
     * @param int $categoryId Category ID
     * @return array Array of Product models
     */
    public function getByCategory(int $categoryId): array
    {
        return $this->findBy(['category_id' => $categoryId, 'status' => 'active'], 'AND');
    }
    
    /**
     * Get featured products
     * 
     * @param int $limit Maximum number of products to return
     * @return array Array of Product models
     */
    public function getFeatured(int $limit = 6): array
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
        $products = [];
        
        foreach ($results as $result) {
            $products[] = $this->model->hydrate($result);
        }
        
        return $products;
    }

    /**
     * Create a new product with slug generation
     * 
     * @param array $data Product data
     * @return Product|false Product model or false on failure
     */
    public function createWithSlug(array $data)
    {
        // Generate slug if not provided
        if (!isset($data['slug']) && isset($data['name'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }
        
        // Ensure proper defaults
        $data['status'] = $data['status'] ?? 'active';
        $data['featured'] = $data['featured'] ?? 0;
        $data['stock'] = $data['stock'] ?? 0;
        $data['sort_order'] = $data['sort_order'] ?? 0;
        
        return $this->create($data);
    }
    
    /**
     * Search products by name, description, or SKU
     * 
     * @param string $keyword Search keyword
     * @param int $limit Maximum number of products to return
     * @return array Array of Product models
     */
    public function search(string $keyword, int $limit = 10): array
    {
        $keyword = "%{$keyword}%";
        
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->model->getTable()} 
            WHERE (name LIKE :keyword OR description LIKE :keyword OR sku LIKE :keyword) 
            AND status = 'active'
            ORDER BY name
            LIMIT :limit
        ");
        
        $stmt->bindParam(':keyword', $keyword);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $products = [];
        
        foreach ($results as $result) {
            $products[] = $this->model->hydrate($result);
        }
        
        return $products;
    }
    
    /**
     * Generate a unique slug from a name
     * 
     * @param string $name Product name
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
        $existingProduct = $this->findOneBy('slug', $slug);
        
        // If slug exists, append a number
        if ($existingProduct) {
            $i = 1;
            $baseSlug = $slug;
            
            while ($this->findOneBy('slug', $slug)) {
                $slug = $baseSlug . '-' . $i++;
            }
        }
        
        return $slug;
    }
}
