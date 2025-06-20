<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use App\Utilities\Logger;
use App\Services\ServiceManager;
use Exception;

/**
 * Services Controller
 * 
 * Handles all service-related routes
 */
class ServicesController extends BaseController
{
    /**
     * Service manager instance
     * @var ServiceManager
     */
    protected $serviceManager;
    
    /**
     * Constructor
     * 
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        
        // Initialize service manager
        $this->serviceManager = new ServiceManager($container->get('db'));
    }

    /**
     * Display services index page
     * 
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        try {
            // Get all services from the service manager
            $services = $this->serviceManager->getAllServices();
            
            // Render the services index template
            return $this->render($response, 'services/index.php', [
                'title' => 'Our Services - Gideon\'s Technology',
                'page' => 'services',
                'services' => $services
            ]);
        } catch (Exception $e) {
            // Log the error
            Logger::error('Error in ServicesController::index: ' . $e->getMessage());
            
            // Return an error page
            return $this->render($response, 'errors/500.php', [
                'title' => 'Error - Gideon\'s Technology',
                'message' => 'An error occurred while loading services.'
            ])->withStatus(500);
        }
    }

    /**
     * Display service details page
     * 
     * @param Request $request
     * @param Response $response
     * @param array $args Route arguments
     * @return Response
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        try {
            // Get service ID from route arguments
            $id = (int) $args['id'];
            
            // Get service from service manager
            $service = $this->serviceManager->getServiceById($id);
            
            // If service not found, return 404 page
            if (!$service) {
                Logger::warning("Service ID {$id} not found");
                return $this->render($response, 'errors/404.php', [
                    'title' => 'Service Not Found - Gideon\'s Technology',
                    'message' => 'The requested service could not be found.'
                ])->withStatus(404);
            }
            
            // Render the service details template
            return $this->render($response, 'services/show.php', [
                'title' => $service['name'] . ' - Gideon\'s Technology',
                'page' => 'services',
                'service' => $service
            ]);
        } catch (Exception $e) {
            // Log the error
            Logger::error("Error in ServicesController::show({$args['id']}): " . $e->getMessage());
            
            // Return an error page
            return $this->render($response, 'errors/500.php', [
                'title' => 'Error - Gideon\'s Technology',
                'message' => 'An error occurred while loading the service details.'
            ])->withStatus(500);
        }
    }

    /**
     * Display web development services page
     * 
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function webDevelopment(Request $request, Response $response): Response
    {
        try {
            // Log the page request
            Logger::info('Web Development services page requested');
            
            // Get service by slug from service manager
            $service = $this->serviceManager->getServiceBySlug('web-development');
            
            // If service not found, return 404 page
            if (!$service) {
                Logger::warning("Web Development service not found");
                return $this->render($response, 'errors/404.php', [
                    'title' => 'Service Not Found - Gideon\'s Technology',
                    'message' => 'The requested service could not be found.'
                ])->withStatus(404);
            }
            
            // Render the web development services template
            return $this->render($response, 'services/web-development.php', [
                'title' => 'Web Development Services - Gideon\'s Technology',
                'page' => 'services-web-development',
                'service' => $service
            ]);
        } catch (Exception $e) {
            // Log the error
            Logger::error("Error in ServicesController::webDevelopment: " . $e->getMessage());
            
            // Return an error page
            return $this->render($response, 'errors/500.php', [
                'title' => 'Error - Gideon\'s Technology',
                'message' => 'An error occurred while loading the web development services page.'
            ])->withStatus(500);
        }
    }
    
    public function ecommerce(Request $request, Response $response): Response {
        Logger::info('E-commerce services page requested');
        
        return $this->container->get('renderer')->render($response, 'services/web-development/ecommerce.php', [
            'appName' => $this->container->get('settings')['appName'],
            'currentYear' => $this->container->get('settings')['currentYear'],
            'page' => 'services-ecommerce'
        ]);
    }
    
    public function fintech(Request $request, Response $response): Response {
        Logger::info('Fintech services page requested');
        
        return $this->container->get('renderer')->render($response, 'services/fintech.php', [
            'appName' => $this->container->get('settings')['appName'],
            'currentYear' => $this->container->get('settings')['currentYear'],
            'page' => 'services-fintech'
        ]);
    }
    
    public function repair(Request $request, Response $response): Response {
        Logger::info('Repair services page requested');
        
        return $this->container->get('renderer')->render($response, 'services/repair.php', [
            'appName' => $this->container->get('settings')['appName'],
            'currentYear' => $this->container->get('settings')['currentYear'],
            'page' => 'services-repair'
        ]);
    }

    public function design(Request $request, Response $response): Response {
        Logger::info('Web Design services page requested');
        
        return $this->container->get('renderer')->render($response, 'services/web-development/design.php', [
            'appName' => $this->container->get('settings')['appName'],
            'currentYear' => $this->container->get('settings')['currentYear'],
            'page' => 'services-design'
        ]);
    }

    public function applications(Request $request, Response $response): Response {
        Logger::info('Web Applications services page requested');
        
        return $this->container->get('renderer')->render($response, 'services/web-development/applications.php', [
            'appName' => $this->container->get('settings')['appName'],
            'currentYear' => $this->container->get('settings')['currentYear'],
            'page' => 'services-applications'
        ]);
    }
}
