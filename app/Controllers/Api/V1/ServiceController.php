<?php

namespace App\Controllers\Api\V1;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use App\Services\ServiceManager;
use App\Utilities\Logger;

/**
 * API Service Controller
 * 
 * Handles all service-related API endpoints
 */
class ServiceController
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    
    /**
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
        $this->container = $container;
        $this->serviceManager = new ServiceManager($container->get('db'));
    }
    
    /**
     * Get all services
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        try {
            // Get all services
            $services = $this->serviceManager->getAllServices();
            
            // Return JSON response
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $services
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Log error
            Logger::error('Error in API ServiceController::index: ' . $e->getMessage());
            
            // Return error response
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'server_error',
                    'message' => 'An error occurred while retrieving services.'
                ]
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
    
    /**
     * Get service by ID
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        try {
            // Get service ID from route arguments
            $id = (int) $args['id'];
            
            // Get service from service manager
            $service = $this->serviceManager->getServiceById($id);
            
            // If service not found, return 404
            if (!$service) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => [
                        'code' => 'not_found',
                        'message' => 'Service not found.'
                    ]
                ]));
                
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);
            }
            
            // Return JSON response
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $service
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Log error
            Logger::error("Error in API ServiceController::show({$args['id']}): " . $e->getMessage());
            
            // Return error response
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'server_error',
                    'message' => 'An error occurred while retrieving the service.'
                ]
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}
