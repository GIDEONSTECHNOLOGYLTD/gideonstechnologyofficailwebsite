<?php

namespace App\Controllers\Admin;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Utilities\Logger;

class SettingsController
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
     * Display settings page
     */
    public function index(Request $request, Response $response): Response
    {
        try {
            // Get current settings
            $settings = [];
            
            try {
                $stmt = $this->db->query("SELECT * FROM settings");
                $dbSettings = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                
                foreach ($dbSettings as $setting) {
                    $settings[$setting['key']] = $setting['value'];
                }
            } catch (\Exception $e) {
                Logger::error('Error getting settings: ' . $e->getMessage());
                // Use default settings if table doesn't exist
                $settings = [
                    'site_name' => 'Gideon\'s Technology',
                    'site_description' => 'Technology solutions for everyone',
                    'contact_email' => 'contact@gideonstech.com',
                    'contact_phone' => '+1234567890',
                    'address' => '123 Tech Street, Silicon Valley, CA'
                ];
            }
            
            return $this->renderer->render($response, 'admin/settings.php', [
                'title' => 'Site Settings',
                'settings' => $settings,
                'user' => $_SESSION['user'] ?? []
            ]);
        } catch (\Exception $e) {
            Logger::error('Settings page error: ' . $e->getMessage());
            $response->getBody()->write("<h1>Error loading settings</h1><p>{$e->getMessage()}</p>");
            return $response->withStatus(500);
        }
    }
    
    /**
     * Update settings
     */
    public function update(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            
            // Validate settings
            if (empty($data['site_name'])) {
                throw new \Exception('Site name is required');
            }
            
            // Check if settings table exists
            $tableExists = false;
            try {
                $stmt = $this->db->query("SHOW TABLES LIKE 'settings'");
                $tableExists = $stmt->rowCount() > 0;
            } catch (\Exception $e) {
                Logger::error('Error checking settings table: ' . $e->getMessage());
            }
            
            // Create settings table if it doesn't exist
            if (!$tableExists) {
                try {
                    $this->db->exec("CREATE TABLE settings (\n                        id INT(11) NOT NULL AUTO_INCREMENT,\n                        `key` VARCHAR(255) NOT NULL,\n                        value TEXT,\n                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,\n                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n                        PRIMARY KEY (id),\n                        UNIQUE KEY (`key`)\n                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
                } catch (\Exception $e) {
                    Logger::error('Error creating settings table: ' . $e->getMessage());
                    throw new \Exception('Could not create settings table: ' . $e->getMessage());
                }
            }
            
            // Update settings
            foreach ($data as $key => $value) {
                if ($key === '_csrf_token') continue; // Skip CSRF token
                
                try {
                    // Try to update existing setting
                    $stmt = $this->db->prepare("INSERT INTO settings (`key`, value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE value = :value");
                    $stmt->bindParam(':key', $key);
                    $stmt->bindParam(':value', $value);
                    $stmt->execute();
                } catch (\Exception $e) {
                    Logger::error("Error updating setting {$key}: " . $e->getMessage());
                }
            }
            
            // Set flash message
            if (method_exists($this->container, 'get') && $this->container->has('flash')) {
                $this->container->get('flash')->addMessage('success', 'Settings updated successfully');
            }
            
            return $response->withHeader('Location', '/admin/settings')->withStatus(302);
        } catch (\Exception $e) {
            Logger::error('Settings update error: ' . $e->getMessage());
            
            // Set flash message
            if (method_exists($this->container, 'get') && $this->container->has('flash')) {
                $this->container->get('flash')->addMessage('error', 'Error updating settings: ' . $e->getMessage());
            }
            
            return $response->withHeader('Location', '/admin/settings')->withStatus(302);
        }
    }
}
