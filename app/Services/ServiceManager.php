<?php

namespace App\Services;

use PDO;
use Exception;
use App\Utilities\Logger;

/**
 * Service Manager
 * 
 * Handles business logic for services
 */
class ServiceManager
{
    /**
     * Database connection
     * @var PDO
     */
    private $db;
    
    /**
     * Constructor
     * 
     * @param PDO $db Database connection
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }
    
    /**
     * Get all services
     * 
     * @return array List of services
     * @throws Exception If database error occurs
     */
    public function getAllServices(): array
    {
        try {
            // In a real application, this would fetch from the database
            // For demonstration, we'll return static data
            $services = [
                [
                    'id' => 1,
                    'name' => 'Web Development',
                    'slug' => 'web-development',
                    'description' => 'Professional web development services for businesses of all sizes.',
                    'icon' => 'bi-globe',
                    'featured' => true
                ],
                [
                    'id' => 2,
                    'name' => 'Mobile App Development',
                    'slug' => 'mobile-app-development',
                    'description' => 'Custom mobile applications for iOS and Android platforms.',
                    'icon' => 'bi-phone',
                    'featured' => true
                ],
                [
                    'id' => 3,
                    'name' => 'IT Consulting',
                    'slug' => 'it-consulting',
                    'description' => 'Expert IT consulting to help your business leverage technology effectively.',
                    'icon' => 'bi-people',
                    'featured' => true
                ],
                [
                    'id' => 4,
                    'name' => 'Cloud Solutions',
                    'slug' => 'cloud-solutions',
                    'description' => 'Secure and scalable cloud infrastructure solutions.',
                    'icon' => 'bi-cloud',
                    'featured' => false
                ],
                [
                    'id' => 5,
                    'name' => 'Cybersecurity',
                    'slug' => 'cybersecurity',
                    'description' => 'Comprehensive security solutions to protect your digital assets.',
                    'icon' => 'bi-shield-lock',
                    'featured' => false
                ],
                [
                    'id' => 6,
                    'name' => 'Data Analytics',
                    'slug' => 'data-analytics',
                    'description' => 'Turn your data into actionable insights with our analytics services.',
                    'icon' => 'bi-graph-up',
                    'featured' => false
                ]
            ];
            
            Logger::info('Retrieved all services successfully');
            return $services;
            
            // Real database implementation would look like this:
            // $stmt = $this->db->query("SELECT * FROM services ORDER BY name");
            // return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            Logger::error('Error retrieving services: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get service by ID
     * 
     * @param int $id Service ID
     * @return array|null Service data or null if not found
     * @throws Exception If database error occurs
     */
    public function getServiceById(int $id): ?array
    {
        try {
            // In a real application, this would fetch from the database
            // For demonstration, we'll return static data
            $services = $this->getAllServices();
            
            foreach ($services as $service) {
                if ($service['id'] === $id) {
                    Logger::info("Retrieved service ID {$id} successfully");
                    return $service;
                }
            }
            
            Logger::warning("Service ID {$id} not found");
            return null;
            
            // Real database implementation would look like this:
            // $stmt = $this->db->prepare("SELECT * FROM services WHERE id = ?");
            // $stmt->execute([$id]);
            // $service = $stmt->fetch(PDO::FETCH_ASSOC);
            // return $service ?: null;
        } catch (Exception $e) {
            Logger::error("Error retrieving service ID {$id}: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get service by slug
     * 
     * @param string $slug Service slug
     * @return array|null Service data or null if not found
     * @throws Exception If database error occurs
     */
    public function getServiceBySlug(string $slug): ?array
    {
        try {
            // In a real application, this would fetch from the database
            // For demonstration, we'll return static data
            $services = $this->getAllServices();
            
            foreach ($services as $service) {
                if ($service['slug'] === $slug) {
                    Logger::info("Retrieved service with slug '{$slug}' successfully");
                    return $service;
                }
            }
            
            Logger::warning("Service with slug '{$slug}' not found");
            return null;
            
            // Real database implementation would look like this:
            // $stmt = $this->db->prepare("SELECT * FROM services WHERE slug = ?");
            // $stmt->execute([$slug]);
            // $service = $stmt->fetch(PDO::FETCH_ASSOC);
            // return $service ?: null;
        } catch (Exception $e) {
            Logger::error("Error retrieving service with slug '{$slug}': " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get featured services
     * 
     * @param int $limit Maximum number of services to return
     * @return array List of featured services
     * @throws Exception If database error occurs
     */
    public function getFeaturedServices(int $limit = 3): array
    {
        try {
            // In a real application, this would fetch from the database
            // For demonstration, we'll filter static data
            $services = $this->getAllServices();
            $featured = array_filter($services, function($service) {
                return $service['featured'] === true;
            });
            
            // Limit the number of results
            $featured = array_slice($featured, 0, $limit);
            
            Logger::info("Retrieved {$limit} featured services successfully");
            return $featured;
            
            // Real database implementation would look like this:
            // $stmt = $this->db->prepare("SELECT * FROM services WHERE featured = 1 ORDER BY name LIMIT ?");
            // $stmt->execute([$limit]);
            // return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            Logger::error('Error retrieving featured services: ' . $e->getMessage());
            throw $e;
        }
    }
}
