<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use App\Models\Service;
use App\Models\Consultation;
use App\Utilities\Logger;

class GTechController {
    protected $container;
    private $service;
    private $consultation;
    private $template;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        
        // Initialize models only if they exist and are properly implemented
        try {
            if (class_exists('\\App\\Models\\Service')) {
                $this->service = new Service();
            }
            
            if (class_exists('\\App\\Models\\Consultation')) {
                $this->consultation = new Consultation();
            }
            
            // Template model is handled differently - we'll use fallback data instead
            // since we're just moving functionality from GStore to GTech
        } catch (\Exception $e) {
            // Log the error but continue
            if (class_exists('\\App\\Utilities\\Logger')) {
                Logger::error('Error initializing GTech models: ' . $e->getMessage());
            }
        }
    }

    /**
     * Display the GTech platform home page
     */
    public function index(Request $request, Response $response): Response {
        if (class_exists('\App\Utilities\Logger')) {
            Logger::info('GTech platform index page requested');
        }
        
        // Get services if database is available
        try {
            // Check if service property is initialized
            if (isset($this->service)) {
                // Get category IDs for business and individual categories
                // Category IDs: 1 = business, 2 = individual (based on standard category setup)
                $businessServices = $this->service->getByCategory(1); // Business category ID
                $individualServices = $this->service->getByCategory(2); // Individual category ID
            } else {
                throw new \Exception('Service model not available');
            }
        } catch (\Exception $e) {
            // Use default services if database is not available
            $businessServices = [
                ['id' => 1, 'name' => 'IT Infrastructure Setup & Management', 'category' => 'business'],
                ['id' => 2, 'name' => 'Custom Software Development', 'category' => 'business'],
                ['id' => 3, 'name' => 'Cybersecurity Solutions', 'category' => 'business'],
                ['id' => 4, 'name' => 'Data Backup & Recovery', 'category' => 'business'],
                ['id' => 5, 'name' => 'Cloud Migration Services', 'category' => 'business']
            ];
            
            $individualServices = [
                ['id' => 6, 'name' => 'Computer Repair & Maintenance', 'category' => 'individual'],
                ['id' => 7, 'name' => 'Data Recovery', 'category' => 'individual'],
                ['id' => 8, 'name' => 'Smart Home Setup', 'category' => 'individual'],
                ['id' => 9, 'name' => 'Personal Tech Training', 'category' => 'individual'],
                ['id' => 10, 'name' => 'Device Optimization', 'category' => 'individual']
            ];
        }
        
        // Prepare data for the template
        $data = [
            'appName' => isset($this->container) && $this->container->has('settings') ? $this->container->get('settings')['appName'] : 'Gideon\'s Technology',
            'currentYear' => isset($this->container) && $this->container->has('settings') ? $this->container->get('settings')['currentYear'] : date('Y'),
            'page' => 'gtech',
            'businessServices' => $businessServices,
            'individualServices' => $individualServices
        ];
        
        // Check if renderer is available
        if (isset($this->container) && $this->container->has('renderer')) {
            return $this->container->get('renderer')->render($response, 'gtech/index.php', $data);
        } else {
            // Fallback to direct template inclusion
            $templatePath = dirname(dirname(dirname(__FILE__))) . '/templates/gtech/index.php';
            
            if (file_exists($templatePath)) {
                // Extract data to make it available in the template
                extract($data);
                
                // Capture the template output
                ob_start();
                include $templatePath;
                $output = ob_get_clean();
                
                $response->getBody()->write($output);
                return $response;
            } else {
                // Last resort fallback
                $response->getBody()->write('<h1>GTech Platform</h1><p>Template not found. Please check your installation.</p>');
                return $response;
            }
        }
    }
    
    /**
     * Display a specific service page
     */
    public function service(Request $request, Response $response, array $args): Response {
        $serviceId = $args['id'] ?? 0;
        Logger::info('GTech service page requested for service ID: ' . $serviceId);
        
        try {
            $service = $this->service->find($serviceId);
            if (!$service) {
                throw new \Exception('Service not found');
            }
        } catch (\Exception $e) {
            // Use a default service if not found
            $service = [
                'id' => $serviceId,
                'name' => 'Technology Service',
                'description' => 'Comprehensive technology service to meet your needs.',
                'price' => 99.99,
                'category' => 'business'
            ];
        }
        
        return $this->container->get('renderer')->render($response, 'gtech/service.php', [
            'appName' => $this->container->get('settings')['appName'],
            'currentYear' => $this->container->get('settings')['currentYear'],
            'page' => 'gtech',
            'service' => $service
        ]);
    }
    
    /**
     * Handle consultation request submission
     */
    public function submitConsultation(Request $request, Response $response): Response {
        Logger::info('GTech consultation form submitted');
        
        $data = $request->getParsedBody();
        
        // Validate form data
        $errors = [];
        if (empty($data['name'])) {
            $errors['name'] = 'Name is required';
        }
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Valid email is required';
        }
        if (empty($data['service'])) {
            $errors['service'] = 'Service selection is required';
        }
        
        if (!empty($errors)) {
            // Return to form with errors
            return $this->container->get('renderer')->render($response, 'gtech/index.php', [
                'appName' => $this->container->get('settings')['appName'],
                'currentYear' => $this->container->get('settings')['currentYear'],
                'page' => 'gtech',
                'errors' => $errors,
                'formData' => $data
            ]);
        }
        
        // Save consultation request
        try {
            $this->consultation->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'service' => $data['service'],
                'message' => $data['message'] ?? '',
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            // Set success message
            $this->container->get('flash')->addMessage('success', 'Your consultation request has been submitted. We will contact you shortly.');
        } catch (\Exception $e) {
            // Log error
            Logger::error('Failed to save consultation request: ' . $e->getMessage());
            
            // Set error message
            $this->container->get('flash')->addMessage('error', 'There was a problem submitting your request. Please try again later.');
        }
        
        // Redirect to thank you page
        return $response->withHeader('Location', '/gtech/thank-you')->withStatus(302);
    }
    
    /**
     * Display thank you page after consultation submission
     */
    public function thankYou(Request $request, Response $response): Response {
        return $this->container->get('renderer')->render($response, 'gtech/thank-you.php', [
            'appName' => $this->container->get('settings')['appName'],
            'currentYear' => $this->container->get('settings')['currentYear'],
            'page' => 'gtech'
        ]);
    }
    
    /**
     * Display all services page
     */
    public function allServices(Request $request, Response $response): Response {
        if (class_exists('\App\Utilities\Logger')) {
            Logger::info('GTech all services page requested');
        }
        
        // Get all services if available
        try {
            if (isset($this->service)) {
                $services = $this->service->getAll(20);
            } else {
                throw new \Exception('Service model not available');
            }
        } catch (\Exception $e) {
            // Use default services
            $services = [
                ['id' => 1, 'name' => 'Web Development', 'description' => 'Custom websites and web applications', 'category_id' => 1],
                ['id' => 2, 'name' => 'Mobile App Development', 'description' => 'Native and cross-platform mobile apps', 'category_id' => 1],
                ['id' => 3, 'name' => 'E-commerce Solutions', 'description' => 'Online store development and management', 'category_id' => 1],
                ['id' => 4, 'name' => 'Tech Repair', 'description' => 'Hardware and software repair services', 'category_id' => 2],
                ['id' => 5, 'name' => 'IT Consulting', 'description' => 'Professional technology consulting', 'category_id' => 1]
            ];
        }
        
        return $this->container->get('renderer')->render($response, 'gtech/services.php', [
            'appName' => $this->container->get('settings')['appName'],
            'currentYear' => $this->container->get('settings')['currentYear'],
            'page' => 'gtech-services',
            'services' => $services
        ]);
    }
    
    /**
     * Display repair service page
     */
    public function repairService(Request $request, Response $response): Response {
        if (class_exists('\App\Utilities\Logger')) {
            Logger::info('GTech repair service page requested');
        }
        
        return $this->container->get('renderer')->render($response, 'gtech/repair.php', [
            'appName' => $this->container->get('settings')['appName'],
            'currentYear' => $this->container->get('settings')['currentYear'],
            'page' => 'gtech-repair'
        ]);
    }
    
    /**
     * Display all templates page
     */
    public function allTemplates(Request $request, Response $response): Response {
        if (class_exists('\App\Utilities\Logger')) {
            Logger::info('GTech all templates page requested');
        }
        
        // Use default templates since we're just moving functionality from GStore to GTech
        try {
            // Always use the fallback data for templates
            throw new \Exception('Using fallback template data');
        } catch (\Exception $e) {
            // Use default templates
            $templates = [
                ['id' => 1, 'name' => 'Business Pro', 'description' => 'Professional business website template', 'category' => 'website', 'price' => 79.99],
                ['id' => 2, 'name' => 'E-Shop', 'description' => 'Complete e-commerce website template', 'category' => 'ecommerce', 'price' => 99.99],
                ['id' => 3, 'name' => 'Creative Portfolio', 'description' => 'Showcase your work with this portfolio template', 'category' => 'portfolio', 'price' => 59.99],
                ['id' => 4, 'name' => 'Restaurant', 'description' => 'Perfect template for restaurants and cafes', 'category' => 'website', 'price' => 69.99],
                ['id' => 5, 'name' => 'Digital Agency', 'description' => 'Modern template for digital agencies', 'category' => 'website', 'price' => 89.99]
            ];
        }
        
        return $this->container->get('renderer')->render($response, 'gtech/templates.php', [
            'appName' => $this->container->get('settings')['appName'],
            'currentYear' => $this->container->get('settings')['currentYear'],
            'page' => 'gtech-templates',
            'templates' => $templates
        ]);
    }
    
    /**
     * Display e-commerce templates page
     */
    public function ecommerceTemplates(Request $request, Response $response): Response {
        if (class_exists('\App\Utilities\Logger')) {
            Logger::info('GTech e-commerce templates page requested');
        }
        
        // Use default e-commerce templates since we're just moving functionality from GStore to GTech
        try {
            // Always use the fallback data for templates
            throw new \Exception('Using fallback template data');
        } catch (\Exception $e) {
            // Use default e-commerce templates
            $templates = [
                ['id' => 2, 'name' => 'E-Shop', 'description' => 'Complete e-commerce website template', 'category' => 'ecommerce', 'price' => 99.99],
                ['id' => 6, 'name' => 'Fashion Store', 'description' => 'Stylish template for fashion stores', 'category' => 'ecommerce', 'price' => 89.99],
                ['id' => 7, 'name' => 'Electronics Shop', 'description' => 'Perfect for electronics and gadget stores', 'category' => 'ecommerce', 'price' => 94.99]
            ];
        }
        
        return $this->container->get('renderer')->render($response, 'gtech/templates-ecommerce.php', [
            'appName' => $this->container->get('settings')['appName'],
            'currentYear' => $this->container->get('settings')['currentYear'],
            'page' => 'gtech-templates-ecommerce',
            'templates' => $templates
        ]);
    }
    
    /**
     * Display portfolio templates page
     */
    public function portfolioTemplates(Request $request, Response $response): Response {
        if (class_exists('\App\Utilities\Logger')) {
            Logger::info('GTech portfolio templates page requested');
        }
        
        // Use default portfolio templates since we're just moving functionality from GStore to GTech
        try {
            // Always use the fallback data for templates
            throw new \Exception('Using fallback template data');
        } catch (\Exception $e) {
            // Use default portfolio templates
            $templates = [
                ['id' => 3, 'name' => 'Creative Portfolio', 'description' => 'Showcase your work with this portfolio template', 'category' => 'portfolio', 'price' => 59.99],
                ['id' => 8, 'name' => 'Photographer', 'description' => 'Elegant template for photographers', 'category' => 'portfolio', 'price' => 69.99],
                ['id' => 9, 'name' => 'Designer Showcase', 'description' => 'Perfect for graphic designers and artists', 'category' => 'portfolio', 'price' => 64.99]
            ];
        }
        
        return $this->container->get('renderer')->render($response, 'gtech/templates-portfolio.php', [
            'appName' => $this->container->get('settings')['appName'],
            'currentYear' => $this->container->get('settings')['currentYear'],
            'page' => 'gtech-templates-portfolio',
            'templates' => $templates
        ]);
    }
    
    /**
     * Display individual template details
     */
    public function templateDetails(Request $request, Response $response, array $args): Response {
        $templateId = $args['id'] ?? 0;
        
        if (class_exists('\App\Utilities\Logger')) {
            Logger::info('GTech template details page requested for template ID: ' . $templateId);
        }
        
        // Use default template details since we're just moving functionality from GStore to GTech
        try {
            // Always use the fallback data for templates
            throw new \Exception('Using fallback template data');
        } catch (\Exception $e) {
            // Use default template details
            $template = [
                'id' => $templateId,
                'name' => 'Sample Template',
                'description' => 'This is a sample template description.',
                'category' => 'website',
                'price' => 79.99,
                'features' => ['Responsive Design', 'Easy Customization', 'SEO Optimized', 'Fast Loading'],
                'image' => '/assets/images/templates/sample.jpg'
            ];
        }
        
        return $this->container->get('renderer')->render($response, 'gtech/template-details.php', [
            'appName' => $this->container->get('settings')['appName'],
            'currentYear' => $this->container->get('settings')['currentYear'],
            'page' => 'gtech-template-details',
            'template' => $template
        ]);
    }
}
