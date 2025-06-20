<?php

namespace App\Models;

use PDO;
use App\Utilities\Logger;
use App\Core\Database;

/**
 * Product Model
 * Handles product-related data and operations
 */
class Product extends Model
{
    /**
     * Table name in database
     * @var string
     */
    protected $table = 'products';
    
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
        'sale_price',
        'sku',
        'stock',
        'image',
        'images',
        'featured',
        'status',
        'category_id',
        'weight',
        'dimensions',
        'attributes',
        'sort_order'
    ];
    
    /**
     * Default column values
     * @var array
     */
    protected $defaults = [
        'status' => 'active',
        'featured' => 0,
        'stock' => 0,
        'sort_order' => 0
    ];
    
    /**
     * Whether to use timestamps (created_at, updated_at)
     * @var bool
     */
    protected $timestamps = true;

    /**
     * Get all active products
     * 
     * @return array Array of products
     */
    public function getAll(): array
    {
        try {
            return $this->where(['status' => 'active']);
        } catch (\Exception $e) {
            Logger::error('Error in Product::getAll(): ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get featured products for the store homepage
     * 
     * @param int $limit Number of products to return
     * @return array Array of featured products
     */
    public function getFeatured(int $limit = 6): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM {$this->table} 
                WHERE featured = 1 AND status = 'active'
                ORDER BY sort_order, name
                LIMIT :limit
            ");
            
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $products = [];
            
            foreach ($results as $result) {
                $products[] = $this->hydrate($result);
            }
            
            return $products;
        } catch (\Exception $e) {
            Logger::error('Error in Product::getFeatured(): ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get product by ID with category information
     * 
     * @param int $id Product ID
     * @return array|null Product details or null if not found
     */
    public function getById(int $id): ?array {
        Logger::debug('Fetching product with ID: ' . $id);
        
        try {
            // Validate ID
            if ($id <= 0) {
                Logger::error('Invalid product ID provided: ' . $id);
                return null;
            }
            
            // Query the database for the product with category information
            $stmt = $this->db->prepare("
                SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.id = :id AND p.status = 'active' 
                LIMIT 1
            ");
            
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$product) {
                Logger::info('Product not found with ID: ' . $id);
                return null;
            }
            
            // Get product images if available
            $galleryImages = [];
            if (!empty($product['images'])) {
                try {
                    $galleryImages = json_decode($product['images'], true) ?? [];
                } catch (\Exception $e) {
                    Logger::warning('Failed to parse product images: ' . $e->getMessage());
                }
            }
            
            // Format the product data
            $result = [
                'id' => (int)$product['id'],
                'name' => $product['name'],
                'price' => (float)$product['price'],
                'sale_price' => isset($product['sale_price']) ? (float)$product['sale_price'] : null,
                'description' => $product['description'],
                'short_description' => $product['short_description'] ?? '',
                'image' => $product['image'] ?? 'https://via.placeholder.com/600x400?text=No+Image',
                'gallery' => !empty($galleryImages) ? $galleryImages : [],
                'category' => $product['category_name'] ?? 'Uncategorized',
                'category_id' => (int)$product['category_id'],
                'inStock' => (int)$product['stock'] > 0,
                'stock' => (int)$product['stock'],
                'sku' => $product['sku'] ?? '',
                'slug' => $product['slug'] ?? '',
                'featured' => (bool)$product['featured'],
                'status' => $product['status']
            ];
            
            // Add attributes if they exist
            if (!empty($product['attributes'])) {
                try {
                    $result['attributes'] = json_decode($product['attributes'], true) ?? [];
                } catch (\Exception $e) {
                    Logger::warning('Failed to parse product attributes: ' . $e->getMessage());
                    $result['attributes'] = [];
                }
            } else {
                $result['attributes'] = [];
            }
            
            return $result;
        } catch (\Exception $e) {
            Logger::error('Error in Product::getById(): ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get products by category ID
     * 
     * @param int $categoryId Category ID
     * @param int $limit Maximum number of products to return
     * @return array Array of products in the category
     */
    public function getByCategory(int $categoryId, int $limit = 10): array {
        Logger::debug('Fetching products in category ID: ' . $categoryId);
        
        try {
            // Prepare the query to get products by category
            $sql = "SELECT p.*, c.name as category_name 
                   FROM {$this->table} p 
                   LEFT JOIN categories c ON p.category_id = c.id 
                   WHERE p.category_id = :category_id AND p.status = 'active' 
                   ORDER BY p.sort_order, p.name";
            
            if ($limit > 0) {
                $sql .= " LIMIT :limit";
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
            
            if ($limit > 0) {
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format the products data consistently with other methods
            $formattedProducts = [];
            foreach ($products as $product) {
                $formattedProducts[] = [
                    'id' => (int)$product['id'],
                    'name' => $product['name'],
                    'price' => (float)$product['price'],
                    'description' => $product['description'] ?? '',
                    'image' => $product['image'] ?? 'https://via.placeholder.com/300x200?text=No+Image',
                    'category' => $product['category_name'] ?? 'Uncategorized',
                    'inStock' => (int)($product['stock'] ?? 0) > 0,
                    'stock' => (int)($product['stock'] ?? 0),
                    'rating' => (float)($product['rating'] ?? 0)
                ];
            }
            
            return $formattedProducts;
            
        } catch (\PDOException $e) {
            Logger::error('Database error in Product::getByCategory(): ' . $e->getMessage());
            return [];
        } catch (\Exception $e) {
            Logger::error('Unexpected error in Product::getByCategory(): ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get all product categories
     * 
     * @return array List of all product categories
     */
    public function getAllCategories(): array {
        try {
            // Get database connection
            $connection = Database::getConnection();
            if (!$connection) {
                Logger::error('Database connection failed in Product::getAllCategories()');
                // Fallback to hardcoded categories if database is unavailable
                return [
                    'electronics' => 'Electronics & Gadgets',
                    'computers' => 'Computers & Laptops',
                    'accessories' => 'Accessories',
                    'networking' => 'Networking Equipment',
                    'software' => 'Software',
                    'services' => 'Tech Services'
                ];
            }
            
            // Query the database for all active categories
            $sql = "SELECT id, name, slug FROM categories WHERE is_active = 1 ORDER BY name ASC";
            $stmt = $connection->prepare($sql);
            $stmt->execute();
            
            $categories = [];
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            if (empty($results)) {
                Logger::warning('No categories found in database');
                // Return empty array if no categories found
                return [];
            }
            
            // Format the categories as slug => name for easy use in templates
            foreach ($results as $category) {
                $slug = $category['slug'] ?? strtolower(str_replace(' ', '-', $category['name']));
                $categories[$slug] = $category['name'];
            }
            
            return $categories;
        } catch (\Exception $e) {
            // Handle both PDOException and general Exception
            if ($e instanceof \PDOException) {
                Logger::error('Database error in Product::getAllCategories(): ' . $e->getMessage());
            } else {
                Logger::error('Unexpected error in Product::getAllCategories(): ' . $e->getMessage());
            }
            // Fallback to hardcoded categories if any error occurs
            return [
                'electronics' => 'Electronics & Gadgets',
                'computers' => 'Computers & Laptops',
                'accessories' => 'Accessories',
                'networking' => 'Networking Equipment',
                'software' => 'Software',
                'services' => 'Tech Services'
            ];
        }
    }
    
    /**
     * Search products by keyword or criteria
     * 
     * @param string $query Search query
     * @param int|null $limit Maximum number of products to return
     * @return array Search results
     */
    public function search($query, ?int $limit = null): array
    {
        try {
            // Get database connection
            $connection = Database::getConnection();
            if (!$connection) {
                Logger::error('Database connection failed in Product::search()');
                return [];
            }
            
            // If query is a string, search by keyword
            if (is_string($query)) {
                // Validate search query
                if (empty(trim($query))) {
                    Logger::warning('Empty search query provided to Product::search()');
                    return [];
                }
                
                $searchTerm = "%{$query}%";
                
                $sql = "SELECT p.*, c.name as category_name 
                        FROM {$this->table} p 
                        LEFT JOIN categories c ON p.category_id = c.id 
                        WHERE (p.name LIKE :search OR p.description LIKE :search OR p.sku LIKE :search)
                        AND p.is_active = 1
                        ORDER BY p.name ASC";
                
                if ($limit !== null && $limit > 0) {
                    $sql .= " LIMIT :limit";
                }
                
                $stmt = $connection->prepare($sql);
                $stmt->bindParam(':search', $searchTerm, \PDO::PARAM_STR);
                
                if ($limit !== null && $limit > 0) {
                    $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
                }
                
                $stmt->execute();
                
                $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                
                // Format the products data consistently with other methods
                $products = [];
                foreach ($results as $product) {
                    $products[] = [
                        'id' => (int)$product['id'],
                        'name' => $product['name'],
                        'price' => (float)$product['price'],
                        'description' => $product['description'] ?? '',
                        'image' => $product['image'] ?? 'https://via.placeholder.com/300x200?text=No+Image',
                        'category' => $product['category_name'] ?? 'Uncategorized',
                        'inStock' => (int)($product['stock_quantity'] ?? 0) > 0,
                        'stock_quantity' => (int)($product['stock_quantity'] ?? 0),
                        'rating' => (float)($product['rating'] ?? 0)
                    ];
                }
                
                return $products;
            }
            
            // If query is an array of criteria, use findAllWhere
            if (is_array($query) && !empty($query)) {
                Logger::debug('Searching products with criteria array');
                try {
                    return $this->findAllWhere($query, $limit);
                } catch (\PDOException $e) {
                    Logger::error('Database error in Product::search() with criteria array: ' . $e->getMessage());
                    return [];
                } catch (\Exception $e) {
                    Logger::error('Unexpected error in Product::search() with criteria array: ' . $e->getMessage());
                    return [];
                }
            }
            
            // If query is empty or invalid type
            Logger::warning('Invalid search query type or empty query provided to Product::search()');
            return [];
        } catch (\PDOException $e) {
            Logger::error('Database error in Product::search(): ' . $e->getMessage());
            return [];
        } catch (\Exception $e) {
            Logger::error('Unexpected error in Product::search(): ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Add product to user's wishlist
     * 
     * @param int $userId User ID
     * @param int $productId Product ID
     * @return bool Success status
     */
    public function addToWishlist(int $userId, int $productId): bool {
        Logger::debug('Adding product ' . $productId . ' to wishlist for user ' . $userId);
        
        try {
            // Get database connection
            $connection = Database::getConnection();
            if (!$connection) {
                Logger::error('Database connection failed in Product::addToWishlist()');
                return false;
            }
            
            // Check if product exists
            $productSql = "SELECT id FROM {$this->table} WHERE id = :product_id AND is_active = 1 LIMIT 1";
            $productStmt = $connection->prepare($productSql);
            $productStmt->bindParam(':product_id', $productId, \PDO::PARAM_INT);
            $productStmt->execute();
            
            if (!$productStmt->fetchColumn()) {
                Logger::warning('Attempted to add non-existent product to wishlist: ' . $productId);
                return false;
            }
            
            // In a real app, this would add to a database table
            // For now, we'll use session storage
            if (!isset($_SESSION['wishlist'])) {
                $_SESSION['wishlist'] = [];
            }
            
            // Store as a unique key to prevent duplicates
            $_SESSION['wishlist'][$productId] = [
                'product_id' => $productId,
                'added_at' => date('Y-m-d H:i:s')
            ];
            
            return true;
        } catch (\PDOException $e) {
            Logger::error('Database error in Product::addToWishlist(): ' . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            Logger::error('Unexpected error in Product::addToWishlist(): ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Remove product from user's wishlist
     * 
     * @param int $userId User ID
     * @param int $productId Product ID
     * @return bool Success status
     */
    public function removeFromWishlist(int $userId, int $productId): bool {
        Logger::debug('Removing product ' . $productId . ' from wishlist for user ' . $userId);
        
        try {
            // Validate parameters
            if ($userId <= 0 || $productId <= 0) {
                Logger::warning('Invalid parameters provided to Product::removeFromWishlist(): userId=' . $userId . ', productId=' . $productId);
                return false;
            }
            
            // In a real app, this would remove from a database table
            // For now, we'll use session storage
            if (isset($_SESSION['wishlist']) && isset($_SESSION['wishlist'][$productId])) {
                unset($_SESSION['wishlist'][$productId]);
                Logger::info('Product ' . $productId . ' removed from wishlist for user ' . $userId);
                return true;
            }
            
            Logger::info('Product ' . $productId . ' not found in wishlist for user ' . $userId);
            return false;
        } catch (\Exception $e) {
            Logger::error('Unexpected error in Product::removeFromWishlist(): ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get user's wishlist items
     * 
     * @param int $userId User ID
     * @return array Wishlist items with product details
     */
    public function getWishlistItems(int $userId): array {
        Logger::debug('Fetching wishlist items for user ' . $userId);
        
        try {
            // Validate parameters
            if ($userId <= 0) {
                Logger::warning('Invalid user ID provided to Product::getWishlistItems(): ' . $userId);
                return [];
            }
            
            // Get database connection for product retrieval
            $connection = Database::getConnection();
            if (!$connection) {
                Logger::error('Database connection failed in Product::getWishlistItems()');
                return [];
            }
            
            $items = [];
            
            // In a real app, this would query a database table
            // For now, we'll use session storage
            if (isset($_SESSION['wishlist'])) {
                foreach ($_SESSION['wishlist'] as $productId => $wishlistData) {
                    try {
                        $product = $this->getById($productId);
                        if ($product) {
                            $items[] = array_merge($product, [
                                'added_to_wishlist_at' => $wishlistData['added_at']
                            ]);
                        }
                    } catch (\Exception $e) {
                        // Log error but continue processing other wishlist items
                        Logger::warning('Error fetching wishlist product ID ' . $productId . ': ' . $e->getMessage());
                    }
                }
            }
            
            return $items;
        } catch (\Exception $e) {
            Logger::error('Unexpected error in Product::getWishlistItems(): ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Find all products with specific criteria
     * 
     * @param array $criteria Search criteria
     * @param int|null $limit Maximum number of products to return
     * @return array Products matching criteria
     */
    public function findAllWhere(array $criteria, ?int $limit = null): array
    {
        $where = [];
        $params = [];
        
        foreach ($criteria as $field => $value) {
            // Handle operators in field name (e.g., 'stock_quantity <= ')
            if (strpos($field, ' ') !== false) {
                list($fieldName, $operator) = explode(' ', $field, 2);
                $where[] = "{$fieldName} {$operator} :{$fieldName}";
                $params[":{$fieldName}"] = $value;
            } else {
                $where[] = "{$field} = :{$field}";
                $params[":{$field}"] = $value;
            }
        }
        
        $whereClause = implode(' AND ', $where);
        $sql = "SELECT * FROM {$this->table} WHERE {$whereClause}";
        
        if ($limit) {
            $sql .= " LIMIT :limit";
            $params[':limit'] = $limit;
        }
        
        $stmt = Database::getConnection()->prepare($sql);
        
        foreach ($params as $param => $value) {
            if ($param === ':limit') {
                $stmt->bindValue($param, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($param, $value);
            }
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Find all products ordered by a field
     * 
     * @param string $column Column to order by
     * @param string $direction Order direction (ASC or DESC)
     * @param int|null $limit Maximum number of products to return
     * @return array Ordered products
     */
    public function findAllOrderBy(string $column, string $direction = 'ASC', int $limit = 100, int $offset = 0): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY {$column} {$direction}";
        
        // Always add LIMIT and OFFSET for consistency with parent class
        $sql .= " LIMIT :limit OFFSET :offset";
        
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Find best selling products
     * 
     * @param int $limit Maximum number of products to return
     * @return array Best selling products
     */
    public function findBestSelling(int $limit = 10): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY sales_count DESC LIMIT :limit";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Decrement product stock
     * 
     * @param int $productId Product ID
     * @param int $quantity Quantity to decrement
     * @return bool Success status
     */
    public function decrementStock(int $productId, int $quantity): bool
    {
        $sql = "UPDATE {$this->table} 
                SET stock_quantity = stock_quantity - :quantity 
                WHERE id = :id AND stock_quantity >= :quantity";
                
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->bindValue(':id', $productId, PDO::PARAM_INT);
        $stmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Get new arrivals
     * 
     * @param int $limit Maximum number of products to return
     * @return array New products
     */
    public function getNewArrivals(int $limit = 8): array
    {
        return $this->findAllOrderBy('created_at', 'DESC', $limit);
    }
    
    /**
     * Get best sellers
     * 
     * @param int $limit Maximum number of products to return
     * @return array Best selling products
     */
    public function getBestSellers(int $limit = 4): array
    {
        return $this->findBestSelling($limit);
    }
    
    /**
     * Find products by category
     * 
     * @param int $categoryId Category ID
     * @param int|null $limit Maximum number of products to return
     * @return array Products in category
     */
    public function findByCategory(int $categoryId, ?int $limit = null): array
    {
        $sql = "SELECT p.* FROM {$this->table} p
                INNER JOIN product_categories pc ON p.id = pc.product_id
                WHERE pc.category_id = :category_id AND p.is_active = 1
                ORDER BY p.name ASC";
        
        if ($limit !== null) {
            $sql .= " LIMIT :limit";
        }
        
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        
        if ($limit !== null) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Get related products
     * 
     * @param int $productId Product ID
     * @param int $limit Maximum number of related products to return
     * @return array Related products
     */
    public function getRelated(int $productId, int $limit = 4): array
    {
        // Get product categories
        $sql = "SELECT category_id FROM product_categories WHERE product_id = :product_id";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
        
        $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($categories)) {
            return [];
        }
        
        // Get related products in the same categories
        $categoriesStr = implode(',', $categories);
        
        $sql = "SELECT DISTINCT p.* FROM {$this->table} p
                INNER JOIN product_categories pc ON p.id = pc.product_id
                WHERE pc.category_id IN ({$categoriesStr})
                AND p.id != :product_id
                AND p.is_active = 1
                ORDER BY RAND()
                LIMIT :limit";
                
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Update product stock
     * 
     * @param int $productId Product ID
     * @param int $quantity Quantity change (positive to add, negative to remove)
     * @return bool Success status
     */
    public function updateStock(int $productId, int $quantity): bool
    {
        $sql = "UPDATE {$this->table} 
                SET stock_quantity = stock_quantity + :quantity 
                WHERE id = :product_id";
                
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Increment product sales count
     * 
     * @param int $productId Product ID
     * @param int $quantity Quantity sold
     * @return bool Success status
     */
    public function incrementSalesCount(int $productId, int $quantity = 1): bool
    {
        $sql = "UPDATE {$this->table} 
                SET sales_count = sales_count + :quantity 
                WHERE id = :product_id";
                
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}
