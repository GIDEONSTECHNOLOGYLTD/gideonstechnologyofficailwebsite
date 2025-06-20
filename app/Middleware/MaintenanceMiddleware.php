<?php
/**
 * Maintenance Mode Middleware
 * 
 * Checks if the site is in maintenance mode and redirects non-admin users
 * to a maintenance page while allowing administrators to access the site.
 */

namespace App\Middleware;

use App\Core\ConfigManager;
use App\Utilities\Logger;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Factory\ResponseFactory;
use App\View\PhpRenderer;

class MaintenanceMiddleware implements MiddlewareInterface
{
    /**
     * @var PhpRenderer
     */
    private $renderer;
    
    /**
     * @var array Admin routes that bypass maintenance mode
     */
    private $adminRoutes = [
        '/admin',
        '/admin/',
        '/login',
        '/logout',
    ];
    
    /**
     * Constructor
     * 
     * @param PhpRenderer $renderer
     */
    public function __construct(PhpRenderer $renderer)
    {
        $this->renderer = $renderer;
    }
    
    /**
     * Process middleware
     * 
     * @param Request $request
     * @param RequestHandler $handler
     * @return Response
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        // Skip maintenance check for specific routes
        $path = $request->getUri()->getPath();
        
        // Check if path starts with any of the admin routes
        $isAdminRoute = false;
        foreach ($this->adminRoutes as $adminRoute) {
            if (strpos($path, $adminRoute) === 0) {
                $isAdminRoute = true;
                break;
            }
        }
        
        // Skip maintenance check for admin routes
        if ($isAdminRoute) {
            return $handler->handle($request);
        }
        
        // Check if maintenance mode is enabled
        $configManager = ConfigManager::getInstance();
        $maintenanceMode = $configManager->get('maintenance_mode', false);
        
        // If maintenance mode is not enabled, proceed normally
        if (!$maintenanceMode) {
            return $handler->handle($request);
        }
        
        // Check if user is an admin - allowing admin users to bypass maintenance mode
        $isAdmin = false;
        if (isset($_SESSION['user']) && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin') {
            $isAdmin = true;
        }
        
        // If user is an admin, proceed normally
        if ($isAdmin) {
            return $handler->handle($request);
        }
        
        // Otherwise, show maintenance page
        $maintenanceMessage = $configManager->get('maintenance_message', 'Site is currently under maintenance. Please check back soon.');
        $responseFactory = new ResponseFactory();
        $response = $responseFactory->createResponse(503);
        
        Logger::info('Maintenance mode active: Redirecting visitor to maintenance page', [
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'path' => $path
        ]);
        
        return $this->renderer->render($response, 'maintenance.php', [
            'message' => $maintenanceMessage
        ]);
    }
}
