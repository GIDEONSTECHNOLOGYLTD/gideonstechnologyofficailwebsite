<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Database\MigrationManager;
use App\Repositories\UserRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ServiceRepository;

/**
 * Database Setup Script
 * 
 * This script runs database migrations and seeds the database with initial data.
 * Usage: php app/database/setup.php
 */

echo "Database Setup Script\n";
echo "=====================\n\n";

// Run migrations
echo "Running migrations...\n";

try {
    $migrationManager = new MigrationManager();
    $results = $migrationManager->migrate();
    $count = count(array_filter($results));
    echo "Successfully ran {$count} migration(s).\n";
    
    // Display details of run migrations
    foreach ($results as $migration => $success) {
        $status = $success ? 'SUCCESS' : 'FAILED';
        echo "  {$status}: {$migration}\n";
    }
    echo "\n";
} catch (\Exception $e) {
    echo "Error running migrations: {$e->getMessage()}\n";
    exit(1);
}

// Seed the database with initial data
echo "Seeding database with initial data...\n";

try {
    // Create admin user
    $userRepository = new UserRepository();
    
    // Check if admin user already exists
    $adminUser = $userRepository->findOneBy('email', 'admin@example.com');
    
    if (!$adminUser) {
        $adminUser = $userRepository->register([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'admin123',
            'role' => 'admin',
            'status' => 'active'
        ]);
        
        if ($adminUser) {
            echo "Created admin user: admin@example.com\n";
        } else {
            echo "Failed to create admin user\n";
        }
    } else {
        echo "Admin user already exists\n";
    }
    
    // Create product categories
    $categoryRepository = new CategoryRepository();
    
    $productCategories = [
        ['name' => 'Mobile Phones', 'type' => 'product'],
        ['name' => 'Laptops', 'type' => 'product'],
        ['name' => 'Accessories', 'type' => 'product'],
        ['name' => 'Software', 'type' => 'product']
    ];
    
    foreach ($productCategories as $category) {
        // Check if category already exists
        $existingCategory = $categoryRepository->findOneBy('name', $category['name']);
        
        if (!$existingCategory) {
            $newCategory = $categoryRepository->createWithSlug($category);
            
            if ($newCategory) {
                echo "Created category: {$category['name']}\n";
            } else {
                echo "Failed to create category: {$category['name']}\n";
            }
        } else {
            echo "Category already exists: {$category['name']}\n";
        }
    }
    
    // Create service categories
    $serviceCategories = [
        ['name' => 'Repairs', 'type' => 'service'],
        ['name' => 'Installations', 'type' => 'service'],
        ['name' => 'Consultations', 'type' => 'service'],
        ['name' => 'Training', 'type' => 'service']
    ];
    
    foreach ($serviceCategories as $category) {
        // Check if category already exists
        $existingCategory = $categoryRepository->findOneBy('name', $category['name']);
        
        if (!$existingCategory) {
            $newCategory = $categoryRepository->createWithSlug($category);
            
            if ($newCategory) {
                echo "Created category: {$category['name']}\n";
            } else {
                echo "Failed to create category: {$category['name']}\n";
            }
        } else {
            echo "Category already exists: {$category['name']}\n";
        }
    }
    
    // Create sample products
    $productRepository = new ProductRepository();
    
    $products = [
        [
            'name' => 'Smartphone X',
            'description' => 'Latest smartphone with advanced features',
            'price' => 799.99,
            'category_id' => 1, // Mobile Phones
            'featured' => 1,
            'stock' => 50
        ],
        [
            'name' => 'Laptop Pro',
            'description' => 'High-performance laptop for professionals',
            'price' => 1299.99,
            'category_id' => 2, // Laptops
            'featured' => 1,
            'stock' => 25
        ],
        [
            'name' => 'Wireless Earbuds',
            'description' => 'Premium wireless earbuds with noise cancellation',
            'price' => 149.99,
            'category_id' => 3, // Accessories
            'featured' => 1,
            'stock' => 100
        ]
    ];
    
    foreach ($products as $product) {
        // Check if product already exists
        $existingProduct = $productRepository->findOneBy('name', $product['name']);
        
        if (!$existingProduct) {
            $newProduct = $productRepository->createWithSlug($product);
            
            if ($newProduct) {
                echo "Created product: {$product['name']}\n";
            } else {
                echo "Failed to create product: {$product['name']}\n";
            }
        } else {
            echo "Product already exists: {$product['name']}\n";
        }
    }
    
    // Create sample services
    $serviceRepository = new ServiceRepository();
    
    $services = [
        [
            'name' => 'Phone Screen Repair',
            'description' => 'Professional screen replacement service',
            'price' => 99.99,
            'category_id' => 1, // Repairs
            'featured' => 1
        ],
        [
            'name' => 'Computer Setup',
            'description' => 'Complete computer setup and software installation',
            'price' => 149.99,
            'category_id' => 2, // Installations
            'featured' => 1
        ],
        [
            'name' => 'Tech Consultation',
            'description' => 'Expert consultation for your tech needs',
            'price' => 79.99,
            'category_id' => 3, // Consultations
            'featured' => 1
        ]
    ];
    
    foreach ($services as $service) {
        // Check if service already exists
        $existingService = $serviceRepository->findOneBy('name', $service['name']);
        
        if (!$existingService) {
            $newService = $serviceRepository->createWithSlug($service);
            
            if ($newService) {
                echo "Created service: {$service['name']}\n";
            } else {
                echo "Failed to create service: {$service['name']}\n";
            }
        } else {
            echo "Service already exists: {$service['name']}\n";
        }
    }
    
    echo "\nDatabase seeding completed successfully!\n";
} catch (\Exception $e) {
    echo "Error seeding database: {$e->getMessage()}\n";
    exit(1);
}

echo "\nDatabase setup completed successfully!\n";
exit(0);
