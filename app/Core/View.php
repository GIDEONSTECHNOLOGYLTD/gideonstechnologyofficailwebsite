<?php

namespace App\Core;

/**
 * View Class
 * Handles template rendering and view logic
 */
class View
{
    /**
     * @var array Data to pass to the view
     */
    private $data = [];
    
    /**
     * @var string Base view path
     */
    private $viewPath = '';
    
    /**
     * @var string Layout to use
     */
    private $layout = 'main';
    
    /**
     * @var array Registered view helpers
     */
    private static $helpers = [];
    
    /**
     * Constructor
     * 
     * @param string $viewPath Base view path
     */
    public function __construct($viewPath = '')
    {
        if (empty($viewPath)) {
            $viewPath = dirname(__DIR__, 2) . '/resources/views';
        }
        $this->viewPath = $viewPath;
    }
    
    /**
     * Set view data
     * 
     * @param array $data Data to set
     * @return View For method chaining
     */
    public function setData(array $data)
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }
    
    /**
     * Set single data item
     * 
     * @param string $key Data key
     * @param mixed $value Data value
     * @return View For method chaining
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }
    
    /**
     * Get view data
     * 
     * @return array All view data
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * Set layout to use
     * 
     * @param string $layout Layout name
     * @return View For method chaining
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
        return $this;
    }
    
    /**
     * Render a view template
     * 
     * @param string $view View name
     * @param array $data Data to pass to view
     * @param bool $useLayout Whether to use layout
     * @return string Rendered view
     */
    public function render($view, array $data = [], $useLayout = true)
    {
        // Merge data
        $data = array_merge($this->data, $data);
        
        // Get view path
        $viewFile = $this->resolvePath($view);
        
        if (!file_exists($viewFile)) {
            throw new \Exception("View file not found: {$viewFile}");
        }
        
        // Extract data to make variables available in view
        extract($data, EXTR_SKIP);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        include $viewFile;
        
        // Get buffered content
        $content = ob_get_clean();
        
        // If not using layout, return content directly
        if (!$useLayout) {
            return $content;
        }
        
        // Otherwise, render with layout
        return $this->renderWithLayout($content, $data);
    }
    
    /**
     * Render view with layout
     * 
     * @param string $content Content to inject into layout
     * @param array $data Data to pass to layout
     * @return string Rendered view with layout
     */
    private function renderWithLayout($content, array $data = [])
    {
        // Set content in data
        $data['content'] = $content;
        
        // Get layout path
        $layoutFile = $this->resolvePath("layouts/{$this->layout}");
        
        if (!file_exists($layoutFile)) {
            throw new \Exception("Layout file not found: {$layoutFile}");
        }
        
        // Extract data to make variables available in layout
        extract($data, EXTR_SKIP);
        
        // Start output buffering
        ob_start();
        
        // Include the layout file
        include $layoutFile;
        
        // Return buffered content
        return ob_get_clean();
    }
    
    /**
     * Resolve view path
     * 
     * @param string $view View name
     * @return string Absolute path to view file
     */
    private function resolvePath($view)
    {
        // Check if view has extension
        if (substr($view, -4) !== '.php') {
            $view .= '.php';
        }
        
        // Return absolute path
        return $this->viewPath . '/' . $view;
    }
    
    /**
     * Render a partial view
     * 
     * @param string $view Partial view name
     * @param array $data Data to pass to partial
     * @return string Rendered partial
     */
    public function partial($view, array $data = [])
    {
        // Prefix partials directory if not already included
        if (strpos($view, 'partials/') !== 0) {
            $view = 'partials/' . $view;
        }
        
        // Render without layout
        return $this->render($view, $data, false);
    }
    
    /**
     * Register a view helper
     * 
     * @param string $name Helper name
     * @param callable $callback Helper callback
     */
    public static function registerHelper($name, callable $callback)
    {
        self::$helpers[$name] = $callback;
    }
    
    /**
     * Call a view helper
     * 
     * @param string $name Helper name
     * @param array $args Helper arguments
     * @return mixed Helper result
     */
    public function __call($name, $args)
    {
        if (isset(self::$helpers[$name])) {
            return call_user_func_array(self::$helpers[$name], $args);
        }
        
        throw new \Exception("View helper not found: {$name}");
    }
    
    /**
     * HTML escape string
     * 
     * @param string $string String to escape
     * @return string Escaped string
     */
    public function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Generate URL
     * 
     * @param string $path URL path
     * @return string Generated URL
     */
    public function url($path = '')
    {
        $baseUrl = isset($_SERVER['BASE_URL']) ? $_SERVER['BASE_URL'] : '';
        return $baseUrl . '/' . ltrim($path, '/');
    }
    
    /**
     * Include a CSS file
     * 
     * @param string $path CSS file path
     * @return string HTML tag
     */
    public function css($path)
    {
        $url = $this->url('assets/css/' . $path);
        return "<link rel=\"stylesheet\" href=\"{$url}\">";
    }
    
    /**
     * Include a JavaScript file
     * 
     * @param string $path JavaScript file path
     * @return string HTML tag
     */
    public function js($path)
    {
        $url = $this->url('assets/js/' . $path);
        return "<script src=\"{$url}\"></script>";
    }
}
