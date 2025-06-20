<?php

namespace App\Core;

use Psr\Http\Message\ResponseInterface as Response;

/**
 * View Manager
 * 
 * Handles rendering templates and views with a consistent layout system
 */
class ViewManager {
    /**
     * @var string Base path for templates
     */
    protected $templatePath;
    
    /**
     * @var array Global data available to all templates
     */
    protected $globals = [];
    
    /**
     * @var string Default layout template
     */
    protected $defaultLayout = 'layouts/main.php';
    
    /**
     * Constructor
     * 
     * @param string $templatePath Base path for templates
     * @param array $globals Global data available to all templates
     */
    public function __construct($templatePath = null, array $globals = []) {
        $this->templatePath = $templatePath ?: dirname(dirname(__DIR__)) . '/app/templates';
        $this->globals = array_merge($this->getDefaultGlobals(), $globals);
    }
    
    /**
     * Get default global variables
     * 
     * @return array Default globals
     */
    protected function getDefaultGlobals() {
        return [
            'app_name' => getenv('APP_NAME') ?: 'Gideon\'s Technology',
            'app_url' => getenv('APP_URL') ?: 'http://localhost',
            'current_year' => date('Y'),
            'asset_url' => getenv('ASSET_URL') ?: '/assets',
        ];
    }
    
    /**
     * Add a global variable
     * 
     * @param string $key Variable name
     * @param mixed $value Variable value
     * @return $this
     */
    public function addGlobal($key, $value) {
        $this->globals[$key] = $value;
        return $this;
    }
    
    /**
     * Set the default layout
     * 
     * @param string $layout Layout template name
     * @return $this
     */
    public function setDefaultLayout($layout) {
        $this->defaultLayout = $layout;
        return $this;
    }
    
    /**
     * Render a template
     * 
     * @param Response $response PSR-7 response
     * @param string $template Template name
     * @param array $data Template data
     * @param string|null $layout Layout template (null for no layout)
     * @return Response PSR-7 response with rendered template
     */
    public function render(Response $response, $template, array $data = [], $layout = null) {
        // Merge global data with template data
        $data = array_merge($this->globals, $data);
        
        // Determine full template path
        $templateFile = $this->templatePath . '/' . $template;
        
        if (!file_exists($templateFile)) {
            throw new \RuntimeException("Template not found: {$template}");
        }
        
        // Capture the template content
        ob_start();
        extract($data);
        include $templateFile;
        $content = ob_get_clean();
        
        // If layout is specified, render the content within the layout
        if ($layout !== null) {
            $layoutFile = $this->templatePath . '/' . ($layout ?: $this->defaultLayout);
            
            if (!file_exists($layoutFile)) {
                throw new \RuntimeException("Layout not found: {$layout}");
            }
            
            // Add content to data for the layout
            $data['content'] = $content;
            
            // Render the layout
            ob_start();
            extract($data);
            include $layoutFile;
            $content = ob_get_clean();
        }
        
        // Write the content to the response body
        $response->getBody()->write($content);
        
        return $response->withHeader('Content-Type', 'text/html');
    }
    
    /**
     * Render JSON response
     * 
     * @param Response $response PSR-7 response
     * @param mixed $data Data to encode as JSON
     * @param int $status HTTP status code
     * @return Response PSR-7 response with JSON
     */
    public function renderJson(Response $response, $data, $status = 200) {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        
        $response->getBody()->write($json);
        
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
    
    /**
     * Render an error page
     * 
     * @param Response $response PSR-7 response
     * @param int $status HTTP status code
     * @param string $message Error message
     * @param array $data Additional data
     * @return Response PSR-7 response with error page
     */
    public function renderError(Response $response, $status, $message = '', array $data = []) {
        $errorTemplate = "errors/{$status}.php";
        $fallbackTemplate = 'errors/generic.php';
        
        $templateFile = $this->templatePath . '/' . $errorTemplate;
        
        if (!file_exists($templateFile)) {
            $templateFile = $this->templatePath . '/' . $fallbackTemplate;
            
            if (!file_exists($templateFile)) {
                // If no error template exists, return a simple error message
                $response->getBody()->write("<h1>Error {$status}</h1><p>{$message}</p>");
                return $response->withStatus($status);
            }
        }
        
        $data = array_merge($this->globals, $data, [
            'status' => $status,
            'message' => $message,
        ]);
        
        ob_start();
        extract($data);
        include $templateFile;
        $content = ob_get_clean();
        
        $response->getBody()->write($content);
        
        return $response->withStatus($status);
    }
}
