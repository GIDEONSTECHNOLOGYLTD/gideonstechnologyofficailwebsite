<?php

namespace App\Controllers\Admin;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Utilities\Logger;

class ProductController
{
    protected $container;
    protected $renderer;
    protected $db;
    
    /**
     * Constructor
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->renderer = $container->get('renderer');
        $this->db = $container->get('db');
    }
    
    /**
     * Display list of products
     */
    public function index(Request $request, Response $response): Response
    {
        // Get all products from database
        $stmt = $this->db->query("SELECT * FROM products ORDER BY created_at DESC");
        $products = $stmt->fetchAll();
        
        return $this->renderer->render($response, 'admin/products/index.php', [
            'title' => 'Manage Products',
            'products' => $products
        ]);
    }
    
    /**
     * Display product creation form
     */
    public function create(Request $request, Response $response): Response
    {
        // Get categories for dropdown
        $stmt = $this->db->query("SELECT id, name FROM categories ORDER BY name");
        $categories = $stmt->fetchAll();
        
        return $this->renderer->render($response, 'admin/products/create.php', [
            'title' => 'Create Product',
            'categories' => $categories
        ]);
    }
    
    /**
     * Store a new product
     */
    public function store(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        // Validate input
        $errors = [];
        if (empty($data['name'])) $errors['name'] = 'Name is required';
        if (empty($data['price'])) $errors['price'] = 'Price is required';
        if (!is_numeric($data['price'])) $errors['price'] = 'Price must be a number';
        if (empty($data['category_id'])) $errors['category_id'] = 'Category is required';
        
        // If validation fails, return to form with errors
        if (!empty($errors)) {
            // Get categories for dropdown
            $stmt = $this->db->query("SELECT id, name FROM categories ORDER BY name");
            $categories = $stmt->fetchAll();
            
            return $this->renderer->render($response, 'admin/products/create.php', [
                'title' => 'Create Product',
                'errors' => $errors,
                'data' => $data,
                'categories' => $categories
            ]);
        }
        
        // Handle file upload if present
        $uploadedFiles = $request->getUploadedFiles();
        $image = null;
        
        if (isset($uploadedFiles['image']) && $uploadedFiles['image']->getError() === UPLOAD_ERR_OK) {
            $extension = pathinfo($uploadedFiles['image']->getClientFilename(), PATHINFO_EXTENSION);
            $basename = bin2hex(random_bytes(8));
            $filename = sprintf('%s.%0.8s', $basename, $extension);
            
            $directory = __DIR__ . '/../../../public/uploads/products';
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            
            $uploadedFiles['image']->moveTo($directory . '/' . $filename);
            $image = '/uploads/products/' . $filename;
        }
        
        // Insert product
        $stmt = $this->db->prepare(
            "INSERT INTO products (name, description, price, image, category_id, stock, created_at, updated_at) 
             VALUES (:name, :description, :price, :image, :category_id, :stock, NOW(), NOW())"
        );
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':category_id', $data['category_id']);
        $stmt->bindParam(':stock', $data['stock'] ?? 0);
        $stmt->execute();
        
        // Log the action
        Logger::info("Admin created new product: {$data['name']}");
        
        // Redirect with success message
        $this->container->get('flash')->addMessage('success', 'Product created successfully');
        return $response->withHeader('Location', '/admin/products')->withStatus(302);
    }
    
    /**
     * Display product edit form
     */
    public function edit(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        
        // Get product from database
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $product = $stmt->fetch();
        
        if (!$product) {
            $this->container->get('flash')->addMessage('error', 'Product not found');
            return $response->withHeader('Location', '/admin/products')->withStatus(302);
        }
        
        // Get categories for dropdown
        $stmt = $this->db->query("SELECT id, name FROM categories ORDER BY name");
        $categories = $stmt->fetchAll();
        
        return $this->renderer->render($response, 'admin/products/edit.php', [
            'title' => 'Edit Product',
            'product' => $product,
            'categories' => $categories
        ]);
    }
    
    /**
     * Update product
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $data = $request->getParsedBody();
        
        // Validate input
        $errors = [];
        if (empty($data['name'])) $errors['name'] = 'Name is required';
        if (empty($data['price'])) $errors['price'] = 'Price is required';
        if (!is_numeric($data['price'])) $errors['price'] = 'Price must be a number';
        if (empty($data['category_id'])) $errors['category_id'] = 'Category is required';
        
        // If validation fails, return to form with errors
        if (!empty($errors)) {
            // Get categories for dropdown
            $stmt = $this->db->query("SELECT id, name FROM categories ORDER BY name");
            $categories = $stmt->fetchAll();
            
            return $this->renderer->render($response, 'admin/products/edit.php', [
                'title' => 'Edit Product',
                'errors' => $errors,
                'product' => array_merge(['id' => $id], $data),
                'categories' => $categories
            ]);
        }
        
        // Get current product data
        $stmt = $this->db->prepare("SELECT image FROM products WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $product = $stmt->fetch();
        
        // Handle file upload if present
        $uploadedFiles = $request->getUploadedFiles();
        $image = $product['image']; // Keep existing image by default
        
        if (isset($uploadedFiles['image']) && $uploadedFiles['image']->getError() === UPLOAD_ERR_OK) {
            $extension = pathinfo($uploadedFiles['image']->getClientFilename(), PATHINFO_EXTENSION);
            $basename = bin2hex(random_bytes(8));
            $filename = sprintf('%s.%0.8s', $basename, $extension);
            
            $directory = __DIR__ . '/../../../public/uploads/products';
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            
            $uploadedFiles['image']->moveTo($directory . '/' . $filename);
            $image = '/uploads/products/' . $filename;
            
            // Delete old image if it exists
            if ($product['image'] && file_exists(__DIR__ . '/../../../public' . $product['image'])) {
                unlink(__DIR__ . '/../../../public' . $product['image']);
            }
        }
        
        // Update product
        $stmt = $this->db->prepare(
            "UPDATE products SET 
             name = :name, 
             description = :description, 
             price = :price, 
             image = :image, 
             category_id = :category_id, 
             stock = :stock, 
             updated_at = NOW() 
             WHERE id = :id"
        );
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':category_id', $data['category_id']);
        $stmt->bindParam(':stock', $data['stock'] ?? 0);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        // Log the action
        Logger::info("Admin updated product: {$data['name']}");
        
        // Redirect with success message
        $this->container->get('flash')->addMessage('success', 'Product updated successfully');
        return $response->withHeader('Location', '/admin/products')->withStatus(302);
    }
    
    /**
     * Delete product
     */
    public function delete(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        
        // Get product data for logging and image deletion
        $stmt = $this->db->prepare("SELECT name, image FROM products WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $product = $stmt->fetch();
        
        if (!$product) {
            $this->container->get('flash')->addMessage('error', 'Product not found');
            return $response->withHeader('Location', '/admin/products')->withStatus(302);
        }
        
        // Delete product
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        // Delete product image if it exists
        if ($product['image'] && file_exists(__DIR__ . '/../../../public' . $product['image'])) {
            unlink(__DIR__ . '/../../../public' . $product['image']);
        }
        
        // Log the action
        Logger::info("Admin deleted product: {$product['name']}");
        
        // Redirect with success message
        $this->container->get('flash')->addMessage('success', 'Product deleted successfully');
        return $response->withHeader('Location', '/admin/products')->withStatus(302);
    }
}
