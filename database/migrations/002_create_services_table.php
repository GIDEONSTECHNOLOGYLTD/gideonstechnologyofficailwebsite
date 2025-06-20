<?php
/**
 * Migration: Create Services Table
 * Date: <?= date('Y-m-d H:i:s') ?>
 */

use PDO;

return function (PDO $db) {
    // Create services table
    $db->exec("
        CREATE TABLE IF NOT EXISTS services (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(120) NOT NULL UNIQUE,
            description TEXT NULL,
            full_description TEXT NULL,
            image VARCHAR(255) NULL,
            featured TINYINT(1) NOT NULL DEFAULT 0,
            active TINYINT(1) NOT NULL DEFAULT 1,
            pricing JSON NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Add indexes
    $db->exec("CREATE INDEX services_slug_index ON services (slug);");
    $db->exec("CREATE INDEX services_featured_index ON services (featured);");
    $db->exec("CREATE INDEX services_active_index ON services (active);");

    // Insert some initial services
    $services = [
        [
            'name' => 'Web Development',
            'slug' => 'web-development',
            'description' => 'Professional websites and web applications',
            'full_description' => 'Our web development services include custom website design, e-commerce solutions, content management systems, and web application development.',
            'image' => 'webdev.jpg',
            'featured' => 1,
            'pricing' => json_encode([
                'basic' => 999,
                'standard' => 2499,
                'premium' => 4999
            ])
        ],
        [
            'name' => 'Mobile App Development',
            'slug' => 'mobile-app-development',
            'description' => 'iOS and Android mobile applications',
            'full_description' => 'We develop native and cross-platform mobile applications for iOS and Android.',
            'image' => 'mobileapp.jpg',
            'featured' => 1,
            'pricing' => json_encode([
                'basic' => 1999,
                'standard' => 3999,
                'premium' => 7999
            ])
        ],
        [
            'name' => 'IT Consulting',
            'slug' => 'it-consulting',
            'description' => 'Strategic technology consulting services',
            'full_description' => 'Our IT consulting services help businesses optimize their technology infrastructure.',
            'image' => 'consulting.jpg',
            'featured' => 1,
            'pricing' => json_encode([
                'hourly' => 150,
                'project' => 'Custom quote',
                'retainer' => 'Starting at $2,500/month'
            ])
        ]
    ];

    $stmt = $db->prepare("
        INSERT INTO services (name, slug, description, full_description, image, featured, pricing)
        VALUES (:name, :slug, :description, :full_description, :image, :featured, :pricing)
    ");

    foreach ($services as $service) {
        $stmt->execute($service);
    }

    echo "Migration executed: Created services table with initial data\n";
};