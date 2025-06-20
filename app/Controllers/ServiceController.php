<?php

namespace App\Controllers;

use App\Models\Service;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class ServiceController extends BaseController
{
    private $twig;
    private $serviceModel;
    
    public function __construct(Twig $twig)
    {
        parent::__construct();
        $this->twig = $twig;
        $this->serviceModel = new Service();
    }
    
    /**
     * Display services index page
     */
    public function index(Request $request, Response $response): Response
    {
        $services = $this->serviceModel->all();
        
        return $this->twig->render($response, 'services/index.php', [
            'services' => $services,
            'page_title' => 'Our Services'
        ]);
    }
    
    /**
     * Display repair services page - this fixes the 404 error
     */
    public function repair(Request $request, Response $response): Response
    {
        // Get repair services by category
        $repairServices = $this->serviceModel->getByCategory('repair');
        
        return $this->twig->render($response, 'services/repair.php', [
            'services' => $repairServices,
            'page_title' => 'Repair Services',
            'page_description' => 'Professional device repair services for all your technology needs.'
        ]);
    }
    
    /**
     * Display individual service page
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        $slug = $args['slug'];
        $service = $this->serviceModel->getBySlug($slug);
        
        if (!$service) {
            // Service not found - could render 404 page instead
            return $response->withHeader('Location', '/gtech/services')
                             ->withStatus(302);
        }
        
        // Get related services
        $relatedServices = $this->serviceModel->getByCategory($service['category'], 3);
        
        // Get pricing options if available
        $pricingOptions = $this->serviceModel->getPricingOptions($service['id']);
        
        return $this->twig->render($response, 'services/show.php', [
            'service' => $service,
            'related_services' => $relatedServices,
            'pricing_options' => $pricingOptions,
            'page_title' => $service['name']
        ]);
    }
    
    /**
     * Display contact form for service inquiry
     */
    public function contactForm(Request $request, Response $response, array $args): Response
    {
        $serviceId = $args['service_id'];
        $service = $this->serviceModel->find($serviceId);
        
        if (!$service) {
            return $response->withHeader('Location', '/gtech/services')
                            ->withStatus(302);
        }
        
        return $this->twig->render($response, 'services/contact.php', [
            'service' => $service,
            'page_title' => 'Contact Us About ' . $service['name']
        ]);
    }
    
    /**
     * Process service contact form
     */
    public function processContact(Request $request, Response $response, array $args): Response
    {
        $serviceId = $args['service_id'];
        $service = $this->serviceModel->find($serviceId);
        
        if (!$service) {
            return $response->withHeader('Location', '/gtech/services')
                            ->withStatus(302);
        }
        
        $data = $request->getParsedBody();
        
        // Validate form data
        $errors = [];
        if (empty($data['name'])) {
            $errors['name'] = 'Name is required';
        }
        
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Valid email is required';
        }
        
        if (empty($data['message'])) {
            $errors['message'] = 'Message is required';
        }
        
        // If there are validation errors, redisplay the form
        if (!empty($errors)) {
            return $this->twig->render($response, 'services/contact.php', [
                'service' => $service,
                'page_title' => 'Contact Us About ' . $service['name'],
                'errors' => $errors,
                'form_data' => $data
            ]);
        }
        
        // Process the form (send email, save to database, etc.)
        // For example:
        // $emailSent = $this->sendServiceInquiryEmail($service, $data);
        $emailSent = true; // Placeholder
        
        if ($emailSent) {
            // Redirect to thank you page
            return $response->withHeader('Location', '/gtech/thank-you')
                            ->withStatus(302);
        } else {
            // Show error message
            return $this->twig->render($response, 'services/contact.php', [
                'service' => $service,
                'page_title' => 'Contact Us About ' . $service['name'],
                'error' => 'There was a problem sending your inquiry. Please try again.',
                'form_data' => $data
            ]);
        }
    }
}