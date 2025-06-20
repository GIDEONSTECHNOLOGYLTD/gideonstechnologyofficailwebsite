<?php

namespace App\Services;

use App\Repositories\ProductRepository;

/**
 * Product Service
 * 
 * Handles business logic for products
 */
class ProductService extends BaseService {
    /**
     * Constructor
     * 
     * @param ProductRepository $repository Product repository
     */
    public function __construct(ProductRepository $repository) {
        parent::__construct($repository);
    }
    
    /**
     * Get featured products
     * 
     * @param int $limit Maximum number of products to return
     * @return array Featured products
     */
    public function getFeaturedProducts($limit = 6) {
        return $this->repository->query(
            "SELECT * FROM products WHERE featured = 1 AND status = 'active' ORDER BY sort_order, name LIMIT :limit",
            ['limit' => $limit]
        );
    }
    
    /**
     * Get products by category
     * 
     * @param int $categoryId Category ID
     * @param int $limit Maximum number of products to return
     * @return array Products in the category
     */
    public function getProductsByCategory($categoryId, $limit = 12) {
        return $this->repository->query(
            "SELECT * FROM products WHERE category_id = :category_id AND status = 'active' ORDER BY sort_order, name LIMIT :limit",
            ['category_id' => $categoryId, 'limit' => $limit]
        );
    }
    
    /**
     * Search for products
     * 
     * @param string $query Search query
     * @param int $limit Maximum number of products to return
     * @return array Matching products
     */
    public function searchProducts($query, $limit = 20) {
        $searchTerm = "%{$query}%";
        return $this->repository->query(
            "SELECT * FROM products WHERE (name LIKE :search OR description LIKE :search) AND status = 'active' ORDER BY name LIMIT :limit",
            ['search' => $searchTerm, 'limit' => $limit]
        );
    }
    
    /**
     * Get related products
     * 
     * @param int $productId Product ID
     * @param int $limit Maximum number of products to return
     * @return array Related products
     */
    public function getRelatedProducts($productId, $limit = 4) {
        $product = $this->getById($productId);
        
        if (!$product) {
            return [];
        }
        
        return $this->repository->query(
            "SELECT * FROM products WHERE category_id = :category_id AND id != :product_id AND status = 'active' ORDER BY RAND() LIMIT :limit",
            ['category_id' => $product['category_id'], 'product_id' => $productId, 'limit' => $limit]
        );
    }
    
    /**
     * Validate product data
     * 
     * @param array $data Product data
     * @param int|null $id Product ID (for updates)
     * @return true|array True if valid, array of errors if invalid
     */
    protected function validate(array $data, $id = null) {
        $errors = [];
        
        // Required fields
        $requiredFields = ['name', 'price', 'category_id'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[$field] = "The {$field} field is required";
            }
        }
        
        // Numeric fields
        $numericFields = ['price', 'sale_price', 'category_id', 'sort_order'];
        foreach ($numericFields as $field) {
            if (isset($data[$field]) && !empty($data[$field]) && !is_numeric($data[$field])) {
                $errors[$field] = "The {$field} field must be numeric";
            }
        }
        
        // Price validation
        if (isset($data['price']) && isset($data['sale_price']) && $data['sale_price'] >= $data['price']) {
            $errors['sale_price'] = "The sale price must be less than the regular price";
        }
        
        // Slug validation (unique)
        if (isset($data['slug']) && !empty($data['slug'])) {
            $existingProduct = $this->repository->findOneBy('slug', $data['slug']);
            if ($existingProduct && (!$id || $existingProduct['id'] != $id)) {
                $errors['slug'] = "The slug '{$data['slug']}' is already in use";
            }
        }
        
        return empty($errors) ? true : $errors;
    }
}
