<?php

namespace App\Http\Controllers\Admin;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Respect\Validation\Validator as v;

class ProductController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index(Request $request, Response $response): Response
    {
        try {
            $products = Product::with('category')->latest()->get();
            
            return $this->render($response, 'admin/products/index.php', [
                'title' => 'Manage Products',
                'products' => $products,
                'active_menu' => 'products'
            ]);
        } catch (\Exception $e) {
            $this->container->get('logger')->error('Product List Error: ' . $e->getMessage());
            $this->flash('error', 'Failed to load products.');
            return $response->withHeader('Location', '/admin')->withStatus(302);
        }
    }

    /**
     * Show the form for creating a new product
     */
    public function create(Request $request, Response $response): Response
    {
        try {
            $categories = Category::all();
            
            return $this->render($response, 'admin/products/create.php', [
                'title' => 'Add New Product',
                'categories' => $categories,
                'active_menu' => 'products'
            ]);
        } catch (\Exception $e) {
            $this->container->get('logger')->error('Product Create Form Error: ' . $e->getMessage());
            $this->flash('error', 'Failed to load product form');
            return $response->withHeader('Location', '/admin/products')->withStatus(302);
        }
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $files = $request->getUploadedFiles();
        
        $validation = $this->validateProductData($data, $files);
        
        if (!$validation['valid']) {
            $this->flash('error', implode(' ', $validation['errors']));
            return $response->withHeader('Location', '/admin/products/create')->withStatus(302);
        }
        
        try {
            $product = new Product();
            $product->name = $data['name'];
            $product->description = $data['description'];
            $product->price = $data['price'];
            $product->category_id = $data['category_id'];
            $product->stock = $data['stock'] ?? 0;
            $product->sku = $data['sku'] ?? '';
            $product->is_featured = isset($data['is_featured']) ? 1 : 0;
            $product->status = $data['status'] ?? 'active';
            
            // Handle file upload
            if (!empty($files['image'])) {
                $uploadedFile = $files['image'];
                if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                    $filename = $this->moveUploadedFile($this->container->get('upload_directory'), $uploadedFile);
                    $product->image = $filename;
                }
            }
            
            $product->save();
            
            $this->flash('success', 'Product created successfully');
            return $response->withHeader('Location', '/admin/products')->withStatus(302);
            
        } catch (\Exception $e) {
            $this->container->get('logger')->error('Product Create Error: ' . $e->getMessage());
            $this->flash('error', 'Failed to create product');
            return $response->withHeader('Location', '/admin/products/create')->withStatus(302);
        }
    }

    /**
     * Display the specified product
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        try {
            $product = Product::with('category')->findOrFail($args['id']);
            
            return $this->render($response, 'admin/products/show.php', [
                'title' => 'View Product',
                'product' => $product,
                'active_menu' => 'products'
            ]);
            
        } catch (\Exception $e) {
            $this->container->get('logger')->error('Product Show Error: ' . $e->getMessage());
            $this->flash('error', 'Product not found');
            return $response->withHeader('Location', '/admin/products')->withStatus(302);
        }
    }

    /**
     * Show the form for editing a product
     */
    public function edit(Request $request, Response $response, array $args): Response
    {
        try {
            $product = Product::findOrFail($args['id']);
            $categories = Category::all();
            
            return $this->render($response, 'admin/products/edit.php', [
                'title' => 'Edit Product',
                'product' => $product,
                'categories' => $categories,
                'active_menu' => 'products'
            ]);
            
        } catch (\Exception $e) {
            $this->container->get('logger')->error('Product Edit Error: ' . $e->getMessage());
            $this->flash('error', 'Product not found');
            return $response->withHeader('Location', '/admin/products')->withStatus(302);
        }
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();
        $files = $request->getUploadedFiles();
        
        try {
            $product = Product::findOrFail($args['id']);
            
            $validation = $this->validateProductData($data, $files, $product->id);
            
            if (!$validation['valid']) {
                $this->flash('error', implode(' ', $validation['errors']));
                return $response->withHeader('Location', "/admin/products/{$product->id}/edit")->withStatus(302);
            }
            
            $product->name = $data['name'];
            $product->description = $data['description'];
            $product->price = $data['price'];
            $product->category_id = $data['category_id'];
            $product->stock = $data['stock'] ?? 0;
            $product->sku = $data['sku'] ?? '';
            $product->is_featured = isset($data['is_featured']) ? 1 : 0;
            $product->status = $data['status'] ?? 'active';
            
            // Handle file upload if new image is provided
            if (!empty($files['image']) && $files['image']->getError() === UPLOAD_ERR_OK) {
                // Delete old image if exists
                if ($product->image) {
                    $this->deleteUploadedFile($this->container->get('upload_directory'), $product->image);
                }
                
                $filename = $this->moveUploadedFile($this->container->get('upload_directory'), $files['image']);
                $product->image = $filename;
            }
            
            $product->save();
            
            $this->flash('success', 'Product updated successfully');
            return $response->withHeader('Location', '/admin/products')->withStatus(302);
            
        } catch (\Exception $e) {
            $this->container->get('logger')->error('Product Update Error: ' . $e->getMessage());
            $this->flash('error', 'Failed to update product');
            return $response->withHeader('Location', "/admin/products/{$args['id']}/edit")->withStatus(302);
        }
    }

    /**
     * Remove the specified product
     */
    public function delete(Request $request, Response $response, array $args): Response
    {
        try {
            $product = Product::findOrFail($args['id']);
            
            // Delete associated image if exists
            if ($product->image) {
                $this->deleteUploadedFile($this->container->get('upload_directory'), $product->image);
            }
            
            $product->delete();
            
            $this->flash('success', 'Product deleted successfully');
            return $response->withHeader('Location', '/admin/products')->withStatus(302);
            
        } catch (\Exception $e) {
            $this->container->get('logger')->error('Product Delete Error: ' . $e->getMessage());
            $this->flash('error', 'Failed to delete product');
            return $response->withHeader('Location', '/admin/products')->withStatus(302);
        }
    }
    
    /**
     * Validate product data
     */
    private function validateProductData(array $data, array $files, ?int $productId = null): array
    {
        $errors = [];
        
        $nameValidator = v::notEmpty()->length(2, 255);
        $descriptionValidator = v::notEmpty();
        $priceValidator = v::numeric()->positive();
        $categoryValidator = v::notEmpty()->numeric();
        $stockValidator = v::optional(v::intVal()->min(0));
        $imageValidator = v::optional(v::image());
        
        if (!$nameValidator->validate($data['name'] ?? '')) {
            $errors[] = 'Product name is required and must be 2-255 characters';
        }
        
        if (!$descriptionValidator->validate($data['description'] ?? '')) {
            $errors[] = 'Product description is required';
        }
        
        if (!$priceValidator->validate($data['price'] ?? '')) {
            $errors[] = 'Valid price is required';
        }
        
        if (!$categoryValidator->validate($data['category_id'] ?? '')) {
            $errors[] = 'Category is required';
        } else if (!Category::where('id', $data['category_id'])->exists()) {
            $errors[] = 'Selected category does not exist';
        }
        
        if (isset($data['stock']) && !$stockValidator->validate($data['stock'])) {
            $errors[] = 'Stock must be a positive integer';
        }
        
        // Check for duplicate SKU if provided
        if (!empty($data['sku'])) {
            $query = Product::where('sku', $data['sku']);
            
            if ($productId) {
                $query->where('id', '!=', $productId);
            }
            
            if ($query->exists()) {
                $errors[] = 'SKU already exists';
            }
        }
        
        // Validate image if uploaded
        if (!empty($files['image']) && $files['image']->getError() === UPLOAD_ERR_OK) {
            $image = $files['image'];
            $fileInfo = [
                'name' => $image->getClientFilename(),
                'type' => $image->getClientMediaType(),
                'size' => $image->getSize(),
                'tmp_name' => $image->file
            ];
            
            if (!$imageValidator->validate($fileInfo)) {
                $errors[] = 'Invalid image file. Please upload a valid image (JPG, PNG, GIF)';
            } elseif ($image->getSize() > 5 * 1024 * 1024) { // 5MB max
                $errors[] = 'Image size should not exceed 5MB';
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Move uploaded file to uploads directory
     */
    private function moveUploadedFile(string $directory, \Slim\Psr7\UploadedFile $uploadedFile): string
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);
        
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
        
        return $filename;
    }
    
    /**
     * Delete uploaded file
     */
    private function deleteUploadedFile(string $directory, string $filename): bool
    {
        $filePath = $directory . DIRECTORY_SEPARATOR . $filename;
        
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        
        return false;
    }
}
