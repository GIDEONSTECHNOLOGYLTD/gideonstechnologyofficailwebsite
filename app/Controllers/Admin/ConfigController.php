<?php
/**
 * Admin Config Controller
 * 
 * Manages system configuration via the admin interface
 */

namespace App\Controllers\Admin;

use App\Core\ConfigManager;
use App\Utilities\Logger;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ConfigController
{
    /**
     * @var \App\View\PhpRenderer
     */
    private $renderer;
    
    /**
     * @var ConfigManager
     */
    private $configManager;
    
    /**
     * Constructor
     * 
     * @param \App\View\PhpRenderer $renderer
     */
    public function __construct(\App\View\PhpRenderer $renderer)
    {
        $this->renderer = $renderer;
        $this->configManager = ConfigManager::getInstance();
        $this->configManager->load();
    }
    
    /**
     * Display configuration dashboard
     * 
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        // Get all configuration categories
        $categories = $this->configManager->getCategories();
        
        // Get current category from query param or default to 'general'
        $currentCategory = $request->getQueryParams()['category'] ?? 'general';
        if (!in_array($currentCategory, $categories)) {
            $currentCategory = 'general';
        }
        
        // Get configurations for current category
        $configs = $this->configManager->getAll($currentCategory);
        
        // Flash messages
        $messages = isset($_SESSION['flash']) ? $_SESSION['flash'] : [];
        unset($_SESSION['flash']);
        
        return $this->renderer->render($response, 'admin/config/index.php', [
            'page' => 'config',
            'categories' => $categories,
            'currentCategory' => $currentCategory,
            'configs' => $configs,
            'messages' => $messages
        ]);
    }
    
    /**
     * Save configuration changes
     * 
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function save(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        // Get category
        $category = $data['category'] ?? 'general';
        
        // Process and save each config item
        foreach ($data as $key => $value) {
            // Skip non-config fields (like csrf token, category)
            if ($key === 'category' || $key === 'csrf_token') {
                continue;
            }
            
            // Handle boolean values
            if (isset($data[$key . '_type']) && $data[$key . '_type'] === 'boolean') {
                $value = isset($data[$key]) ? true : false;
            }
            
            // Save to database
            $this->configManager->set($key, $value);
        }
        
        // Log action
        Logger::info('Admin updated configuration settings for category: ' . $category);
        
        // Set flash message
        $_SESSION['flash'] = ['success' => 'Configuration settings saved successfully.'];
        
        // Redirect back to same category
        return $response
            ->withHeader('Location', '/admin/config?category=' . $category)
            ->withStatus(302);
    }
    
    /**
     * Display maintenance mode page
     * 
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function maintenance(Request $request, Response $response): Response
    {
        $maintenanceMode = $this->configManager->get('maintenance_mode', false);
        $maintenanceMessage = $this->configManager->get('maintenance_message', 'Site is under maintenance');
        
        return $this->renderer->render($response, 'admin/config/maintenance.php', [
            'page' => 'maintenance',
            'maintenanceMode' => $maintenanceMode,
            'maintenanceMessage' => $maintenanceMessage,
        ]);
    }
    
    /**
     * Update maintenance mode settings
     * 
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function updateMaintenance(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        $maintenanceMode = isset($data['maintenance_mode']) && $data['maintenance_mode'] === 'on';
        $maintenanceMessage = $data['maintenance_message'] ?? 'Site is under maintenance';
        
        // Save settings
        $this->configManager->set('maintenance_mode', $maintenanceMode);
        $this->configManager->set('maintenance_message', $maintenanceMessage);
        
        // Log action
        Logger::info('Admin updated maintenance mode: ' . ($maintenanceMode ? 'enabled' : 'disabled'));
        
        // Set flash message
        $_SESSION['flash'] = ['success' => 'Maintenance mode settings updated successfully.'];
        
        // Redirect back
        return $response
            ->withHeader('Location', '/admin/config/maintenance')
            ->withStatus(302);
    }
}
