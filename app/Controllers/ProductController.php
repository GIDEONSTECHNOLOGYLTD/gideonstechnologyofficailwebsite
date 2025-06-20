<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Core\Cache;
use PDO;

class ProductController
{
    protected $renderer;
    protected $db;
    protected $cache;
    protected $cacheEnabled = true;
    protected $cacheTTL = 3600; // 1 hour cache lifetime
    
    public function __construct($container = null)
    {
        $this->renderer = $container->get('renderer') ?? null;
        $this->db = $container->get('db') ?? null;
        $this->cache = new Cache();
    }
    
    public function index(Request $request, Response $response)
    {
        // Products listing
        if ($this->renderer) {
            return $this->renderer->render($response, 'gstore/products/index.php', [
                'page' => 'products',
                'title' => 'All Products',
                'products' => $this->getProducts()
            ]);
        }
        
        $response->getBody()->write('Products Listing');
        return $response;
    }
    
    public function show(Request $request, Response $response, $args)
    {
        $id = $args['id'] ?? null;
        
        // Get product details
        $product = $this->getProductById($id);
        
        if (!$product) {
            return $response->withStatus(404);
        }
        
        if ($this->renderer) {
            return $this->renderer->render($response, 'gstore/products/show.php', [
                'page' => 'product-details',
                'title' => $product['name'],
                'product' => $product
            ]);
        }
        
        $response->getBody()->write("Product: {$product['name']}");
        return $response;
    }
    
    public function byCategory(Request $request, Response $response, $args)
    {
        $category = $args['category'] ?? null;
        
        // Get products by category
        $products = $this->getProductsByCategory($category);
        
        if ($this->renderer) {
            return $this->renderer->render($response, 'gstore/products/category.php', [
                'page' => 'product-category',
                'title' => 'Products - ' . ucfirst($category),
                'category' => $category,
                'products' => $products
            ]);
        }
        
        $response->getBody()->write("Products in category: {$category}");
        return $response;
    }
    
    // Admin methods
    public function adminIndex(Request $request, Response $response)
    {
        if ($this->renderer) {
            return $this->renderer->render($response, 'admin/store/products/index.php', [
                'page' => 'admin-products',
                'title' => 'Manage Products',
                'products' => $this->getProducts()
            ]);
        }
        
        $response->getBody()->write('Admin Products Listing');
        return $response;
    }
    
    public function create(Request $request, Response $response)
    {
        if ($this->renderer) {
            return $this->renderer->render($response, 'admin/store/products/create.php', [
                'page' => 'create-product',
                'title' => 'Create Product'
            ]);
        }
        
        $response->getBody()->write('Create Product Form');
        return $response;
    }
    
    public function store(Request $request, Response $response)
    {
        // Process product creation
        // ...
        
        return $response->withHeader('Location', '/admin/store-products')->withStatus(302);
    }
    
    public function edit(Request $request, Response $response, $args)
    {
        $id = $args['id'] ?? null;
        
        // Get product details
        $product = $this->getProductById($id);
        
        if (!$product) {
            return $response->withStatus(404);
        }
        
        if ($this->renderer) {
            return $this->renderer->render($response, 'admin/store/products/edit.php', [
                'page' => 'edit-product',
                'title' => 'Edit Product',
                'product' => $product
            ]);
        }
        
        $response->getBody()->write("Edit Product: {$product['name']}");
        return $response;
    }
    
    public function update(Request $request, Response $response, $args)
    {
        $id = $args['id'] ?? null;
        
        // Process product update
        // ...
        
        return $response->withHeader('Location', '/admin/store-products')->withStatus(302);
    }
    
    public function delete(Request $request, Response $response, $args)
    {
        $id = $args['id'] ?? null;
        
        // Process product deletion
        // ...
        
        return $response->withHeader('Location', '/admin/store-products')->withStatus(302);
    }
    
    // Helper methods with caching implementation
    protected function getProducts()
    {
        $cacheKey = 'products_all';
        
        // Try to get from cache first
        if ($this->cacheEnabled) {
            $cachedData = $this->cache->get($cacheKey);
            if ($cachedData !== null) {
                return $cachedData;
            }
        }
        
        // If we have a database connection, fetch from there
        if ($this->db instanceof PDO) {
            try {
                $stmt = $this->db->query('SELECT * FROM products ORDER BY created_at DESC');
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Store in cache for future requests
                if ($this->cacheEnabled) {
                    $this->cache->set($cacheKey, $products, $this->cacheTTL);
                }
                
                return $products;
            } catch (\Exception $e) {
                // Log the error but continue with fallback data
                error_log('Error fetching products: ' . $e->getMessage());
            }
        }
        
        // Fallback to static data if no DB or on error
        $fallbackData = [
            [
                'id' => 1,
                'name' => 'Laptop Pro X5',
                'price' => 1299.99,
                'image' => 'laptop.jpg',
                'description' => 'High performance laptop for professionals',
                'category' => 'laptops'
            ],
            [
                'id' => 2,
                'name' => 'Smartphone S22',
                'price' => 799.99,
                'image' => 'smartphone.jpg',
                'description' => 'Latest smartphone with excellent camera',
                'category' => 'phones'
            ],
            [
                'id' => 3,
                'name' => 'Wireless Headphones',
                'price' => 149.99,
                'image' => 'headphones.jpg',
                'description' => 'Premium sound quality with noise cancellation',
                'category' => 'accessories'
            ]
        ];
        
        // Cache the fallback data too
        if ($this->cacheEnabled) {
            $this->cache->set($cacheKey, $fallbackData, $this->cacheTTL);
        }
        
        return $fallbackData;
    }
    
    protected function getProductById($id)
    {
        $cacheKey = 'product_' . $id;
        
        // Try to get from cache first
        if ($this->cacheEnabled) {
            $cachedData = $this->cache->get($cacheKey);
            if ($cachedData !== null) {
                return $cachedData;
            }
        }
        
        // If we have a database connection, fetch from there
        if ($this->db instanceof PDO) {
            try {
                $stmt = $this->db->prepare('SELECT * FROM products WHERE id = :id');
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($product) {
                    // Store in cache for future requests
                    if ($this->cacheEnabled) {
                        $this->cache->set($cacheKey, $product, $this->cacheTTL);
                    }
                    return $product;
                }
            } catch (\Exception $e) {
                // Log the error but continue with fallback approach
                error_log('Error fetching product by ID: ' . $e->getMessage());
            }
        }
        
        // Fallback to the original method if no DB or on error
        $products = $this->getProducts();
        
        foreach ($products as $product) {
            if ($product['id'] == $id) {
                // Cache the result
                if ($this->cacheEnabled) {
                    $this->cache->set($cacheKey, $product, $this->cacheTTL);
                }
                return $product;
            }
        }
        
        return null;
    }
    
    protected function getProductsByCategory($category)
    {
        $cacheKey = 'products_category_' . $category;
        
        // Try to get from cache first
        if ($this->cacheEnabled) {
            $cachedData = $this->cache->get($cacheKey);
            if ($cachedData !== null) {
                return $cachedData;
            }
        }
        
        // If we have a database connection, fetch from there
        if ($this->db instanceof PDO) {
            try {
                $stmt = $this->db->prepare('SELECT * FROM products WHERE category = :category ORDER BY created_at DESC');
                $stmt->bindParam(':category', $category, PDO::PARAM_STR);
                $stmt->execute();
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Store in cache for future requests
                if ($this->cacheEnabled && !empty($products)) {
                    $this->cache->set($cacheKey, $products, $this->cacheTTL);
                }
                
                return $products;
            } catch (\Exception $e) {
                // Log the error but continue with fallback approach
                error_log('Error fetching products by category: ' . $e->getMessage());
            }
        }
        
        // Fallback to filtering all products
        $products = $this->getProducts();
        $categoryProducts = [];
        
        foreach ($products as $product) {
            if ($product['category'] == $category) {
                $categoryProducts[] = $product;
            }
        }
        
        return $categoryProducts;
    }
}