<?php

namespace App\Http\Controllers\Services;

use App\Core\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Service Controller
 * 
 * Handles all service-related operations
 */
class ServiceController extends Controller
{
    /**
     * Display a listing of services
     * 
     * @param Request $request The request object
     * @param Response $response The response object
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        // Get services from the database
        $services = []; // Replace with actual service data retrieval
        
        return $this->render($response, 'services/index.php', [
            'services' => $services,
            'title' => 'Our Services'
        ]);
    }
    
    /**
     * Display service request form
     * 
     * @param Request $request The request object
     * @param Response $response The response object
     * @return Response
     */
    public function requestForm(Request $request, Response $response): Response
    {
        return $this->render($response, 'services/request.php', [
            'title' => 'Request Service'
        ]);
    }
    
    /**
     * Process service request submission
     * 
     * @param Request $request The request object
     * @param Response $response The response object
     * @return Response
     */
    public function submitRequest(Request $request, Response $response): Response
    {
        $data = $this->getInput($request, ['name', 'email', 'service', 'description']);
        
        // Validate input
        if (empty($data)) {
            return $this->redirectWithMessage(
                $response, 
                'error', 
                'Please fill in all required fields', 
                'services.request'
            );
        }
        
        // Process service request - example: save to database
        // $serviceModel = new \App\Models\Service();
        // $serviceModel->createRequest($data);
        
        // Success message
        $this->flash('success', 'Your service request has been submitted successfully');
        
        // Redirect to success page
        return $this->redirect($response, '/gtech/services/request/success');
    }
    
    /**
     * Display a specific service
     * 
     * @param Request $request The request object
     * @param Response $response The response object
     * @param array $args Route arguments
     * @return Response
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        $service = $args['service'] ?? null;
        
        if (!$service) {
            $this->flash('error', 'Service not found');
            return $this->redirect($response, '/gtech/services');
        }
        
        // Get service details from database
        // $serviceModel = new \App\Models\Service();
        // $serviceData = $serviceModel->findBySlug($service);
        
        // Example data - replace with actual service data
        $serviceData = [
            'name' => ucfirst($service),
            'description' => 'Detailed description for ' . ucfirst($service) . ' service.',
            'price' => '99.99',
            'duration' => '1-2 hours'
        ];
        
        return $this->render($response, 'services/show.php', [
            'service' => $serviceData,
            'title' => $serviceData['name'] . ' Service'
        ]);
    }
    
    /**
     * Show the form for creating a new service
     * 
     * @param Request $request The request object
     * @param Response $response The response object
     * @return Response
     */
    public function create(Request $request, Response $response): Response
    {
        return $this->render($response, 'services/create.php', [
            'title' => 'Create New Service'
        ]);
    }
    
    /**
     * Store a newly created service
     * 
     * @param Request $request The request object
     * @param Response $response The response object
     * @return Response
     */
    public function store(Request $request, Response $response): Response
    {
        $data = $this->getInput($request, ['name', 'description', 'price']);
        
        // Validate and save data
        // $serviceModel = new \App\Models\Service();
        // $serviceModel->create($data);
        
        $this->flash('success', 'Service created successfully');
        return $this->redirect($response, '/gtech/services');
    }
    
    /**
     * Show the form for editing a service
     * 
     * @param Request $request The request object
     * @param Response $response The response object
     * @param array $args Route arguments
     * @return Response
     */
    public function edit(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'] ?? null;
        
        // Get service data
        // $serviceModel = new \App\Models\Service();
        // $service = $serviceModel->find($id);
        
        // Example data
        $service = [
            'id' => $id,
            'name' => 'Service ' . $id,
            'description' => 'Service description',
            'price' => '99.99'
        ];
        
        return $this->render($response, 'services/edit.php', [
            'service' => $service,
            'title' => 'Edit Service'
        ]);
    }
    
    /**
     * Update a service
     * 
     * @param Request $request The request object
     * @param Response $response The response object
     * @param array $args Route arguments
     * @return Response
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'] ?? null;
        $data = $this->getInput($request, ['name', 'description', 'price']);
        
        // Update service
        // $serviceModel = new \App\Models\Service();
        // $serviceModel->update($id, $data);
        
        $this->flash('success', 'Service updated successfully');
        return $this->redirect($response, '/gtech/services');
    }
    
    /**
     * Delete a service
     * 
     * @param Request $request The request object
     * @param Response $response The response object
     * @param array $args Route arguments
     * @return Response
     */
    public function destroy(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'] ?? null;
        
        // Delete service
        // $serviceModel = new \App\Models\Service();
        // $serviceModel->delete($id);
        
        $this->flash('success', 'Service deleted successfully');
        return $this->redirect($response, '/gtech/services');
    }
}