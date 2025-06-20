<?php

namespace App\Controllers\Example;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\ServiceManager;
use App\Utilities\Logger;

/**
 * StandardController
 * 
 * This is an example of a standardized controller following best practices.
 * Use this as a template for refactoring existing controllers.
 */
class StandardController
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;
    
    /**
     * @var Logger
     */
    protected $logger;
    
    /**
     * Constructor
     * 
     * @param ServiceManager $serviceManager
     * @param Logger $logger
     */
    public function __construct(ServiceManager $serviceManager, Logger $logger)
    {
        $this->serviceManager = $serviceManager;
        $this->logger = $logger;
    }
    
    /**
     * Index method - displays a list of services
     * 
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        try {
            // Use a service to fetch data
            $services = $this->serviceManager->getAllServices();
            
            // Return a view with data
            return $this->render($response, 'services/index.php', [
                'services' => $services,
                'title' => 'Our Services'
            ]);
        } catch (\Exception $e) {
            // Log the error
            $this->logger->error('Error in StandardController::index: ' . $e->getMessage());
            
            // Return an error page
            return $this->render($response, 'errors/500.php', [
                'message' => 'An error occurred while loading services.'
            ])->withStatus(500);
        }
    }
    
    /**
     * Show method - displays a single service
     * 
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int) $args['id'];
            
            // Use a service to fetch data
            $service = $this->serviceManager->getServiceById($id);
            
            if (!$service) {
                // Log the not found error
                $this->logger->warning("Service not found: {$id}");
                
                // Return a 404 page
                return $this->render($response, 'errors/404.php', [
                    'message' => 'Service not found.'
                ])->withStatus(404);
            }
            
            // Return a view with data
            return $this->render($response, 'services/show.php', [
                'service' => $service,
                'title' => $service['name']
            ]);
        } catch (\Exception $e) {
            // Log the error
            $this->logger->error('Error in StandardController::show: ' . $e->getMessage());
            
            // Return an error page
            return $this->render($response, 'errors/500.php', [
                'message' => 'An error occurred while loading the service.'
            ])->withStatus(500);
        }
    }
    
    /**
     * Create method - displays the form to create a new service
     * 
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function create(Request $request, Response $response): Response
    {
        return $this->render($response, 'services/create.php', [
            'title' => 'Create New Service'
        ]);
    }
    
    /**
     * Store method - handles the form submission to create a new service
     * 
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function store(Request $request, Response $response): Response
    {
        try {
            // Get the form data
            $data = $request->getParsedBody();
            
            // Validate the data
            $validator = new \App\Services\ValidationService();
            $rules = [
                'name' => ['required' => true],
                'description' => ['required' => true],
                'price' => ['required' => true, 'numeric' => true]
            ];
            
            if (!$validator->validate($data, $rules)) {
                // Return the form with validation errors
                return $this->render($response, 'services/create.php', [
                    'title' => 'Create New Service',
                    'errors' => $validator->getErrors(),
                    'data' => $data
                ]);
            }
            
            // Create the service
            $serviceId = $this->serviceManager->createService($data);
            
            // Redirect to the new service
            return $response
                ->withHeader('Location', '/services/' . $serviceId)
                ->withStatus(302);
        } catch (\Exception $e) {
            // Log the error
            $this->logger->error('Error in StandardController::store: ' . $e->getMessage());
            
            // Return an error page
            return $this->render($response, 'errors/500.php', [
                'message' => 'An error occurred while creating the service.'
            ])->withStatus(500);
        }
    }
    
    /**
     * Edit method - displays the form to edit a service
     * 
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function edit(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int) $args['id'];
            
            // Use a service to fetch data
            $service = $this->serviceManager->getServiceById($id);
            
            if (!$service) {
                // Log the not found error
                $this->logger->warning("Service not found: {$id}");
                
                // Return a 404 page
                return $this->render($response, 'errors/404.php', [
                    'message' => 'Service not found.'
                ])->withStatus(404);
            }
            
            // Return a view with data
            return $this->render($response, 'services/edit.php', [
                'service' => $service,
                'title' => 'Edit Service: ' . $service['name']
            ]);
        } catch (\Exception $e) {
            // Log the error
            $this->logger->error('Error in StandardController::edit: ' . $e->getMessage());
            
            // Return an error page
            return $this->render($response, 'errors/500.php', [
                'message' => 'An error occurred while loading the service.'
            ])->withStatus(500);
        }
    }
    
    /**
     * Update method - handles the form submission to update a service
     * 
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int) $args['id'];
            
            // Get the form data
            $data = $request->getParsedBody();
            
            // Validate the data
            $validator = new \App\Services\ValidationService();
            $rules = [
                'name' => ['required' => true],
                'description' => ['required' => true],
                'price' => ['required' => true, 'numeric' => true]
            ];
            
            if (!$validator->validate($data, $rules)) {
                // Return the form with validation errors
                return $this->render($response, 'services/edit.php', [
                    'title' => 'Edit Service',
                    'errors' => $validator->getErrors(),
                    'service' => array_merge(['id' => $id], $data)
                ]);
            }
            
            // Update the service
            $this->serviceManager->updateService($id, $data);
            
            // Redirect to the service
            return $response
                ->withHeader('Location', '/services/' . $id)
                ->withStatus(302);
        } catch (\Exception $e) {
            // Log the error
            $this->logger->error('Error in StandardController::update: ' . $e->getMessage());
            
            // Return an error page
            return $this->render($response, 'errors/500.php', [
                'message' => 'An error occurred while updating the service.'
            ])->withStatus(500);
        }
    }
    
    /**
     * Delete method - handles the request to delete a service
     * 
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function delete(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int) $args['id'];
            
            // Delete the service
            $this->serviceManager->deleteService($id);
            
            // Redirect to the services list
            return $response
                ->withHeader('Location', '/services')
                ->withStatus(302);
        } catch (\Exception $e) {
            // Log the error
            $this->logger->error('Error in StandardController::delete: ' . $e->getMessage());
            
            // Return an error page
            return $this->render($response, 'errors/500.php', [
                'message' => 'An error occurred while deleting the service.'
            ])->withStatus(500);
        }
    }
    
    /**
     * Helper method to render a view
     * 
     * @param Response $response
     * @param string $template
     * @param array $data
     * @return Response
     */
    protected function render(Response $response, string $template, array $data = []): Response
    {
        // Get the view renderer from the container
        $renderer = app()->getContainer()->get('view');
        
        // Render the template with the data
        return $renderer->render($response, $template, $data);
    }
}
