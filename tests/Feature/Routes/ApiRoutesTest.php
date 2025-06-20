<?php

namespace Tests\Feature\Routes;

use Tests\TestCase;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\UriFactory;
use Slim\Psr7\Factory\StreamFactory;
use Psr\Http\Message\ResponseInterface;

/**
 * Test for API Routes
 */
class ApiRoutesTest extends TestCase
{
    /**
     * @var App
     */
    protected $app;
    
    /**
     * Set up the test
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a new Slim application for testing
        $this->app = AppFactory::create();
        
        // Load routes
        $routes = require dirname(dirname(dirname(__DIR__))) . '/routes/api.php';
        $routes($this->app, $this->createMockContainer());
    }
    
    /**
     * Create a mock container for dependency injection
     * 
     * @return object
     */
    protected function createMockContainer()
    {
        return new class {
            public function get($id) {
                // Return mock controllers based on the requested ID
                if (strpos($id, 'Controller') !== false) {
                    return $this->createMockController();
                }
                
                return null;
            }
            
            private function createMockController() {
                return new class {
                    public function __call($method, $args) {
                        // Mock controller method that returns a JSON response
                        $response = $args[1];
                        $data = ['status' => 'success', 'method' => $method];
                        $response->getBody()->write(json_encode($data));
                        return $response->withHeader('Content-Type', 'application/json');
                    }
                };
            }
        };
    }
    
    /**
     * Create a server request for testing
     * 
     * @param string $method HTTP method
     * @param string $uri URI
     * @param array $data Request data
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    protected function createRequest($method, $uri, array $data = [])
    {
        $factory = new ServerRequestFactory();
        $uriFactory = new UriFactory();
        
        $request = $factory->createServerRequest($method, $uriFactory->createUri($uri));
        
        if (!empty($data)) {
            $streamFactory = new StreamFactory();
            $stream = $streamFactory->createStream(json_encode($data));
            
            $request = $request
                ->withHeader('Content-Type', 'application/json')
                ->withBody($stream);
        }
        
        return $request;
    }
    
    /**
     * Process a request through the application
     * 
     * @param string $method HTTP method
     * @param string $uri URI
     * @param array $data Request data
     * @return ResponseInterface
     */
    protected function makeRequest($method, $uri, array $data = [])
    {
        $request = $this->createRequest($method, $uri, $data);
        return $this->app->handle($request);
    }
    
    /**
     * Test the products index endpoint
     */
    public function testProductsIndex()
    {
        $response = $this->makeRequest('GET', '/api/v1/products');
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        
        $responseData = json_decode((string) $response->getBody(), true);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('success', $responseData['status']);
    }
    
    /**
     * Test the product show endpoint
     */
    public function testProductShow()
    {
        $response = $this->makeRequest('GET', '/api/v1/products/1');
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        
        $responseData = json_decode((string) $response->getBody(), true);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('success', $responseData['status']);
    }
    
    /**
     * Test the product store endpoint
     */
    public function testProductStore()
    {
        $productData = [
            'name' => 'Test Product',
            'price' => 19.99,
            'category_id' => 1
        ];
        
        $response = $this->makeRequest('POST', '/api/v1/products', $productData);
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        
        $responseData = json_decode((string) $response->getBody(), true);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('success', $responseData['status']);
    }
    
    /**
     * Test the product update endpoint
     */
    public function testProductUpdate()
    {
        $productData = [
            'name' => 'Updated Product',
            'price' => 29.99
        ];
        
        $response = $this->makeRequest('PUT', '/api/v1/products/1', $productData);
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        
        $responseData = json_decode((string) $response->getBody(), true);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('success', $responseData['status']);
    }
    
    /**
     * Test the product delete endpoint
     */
    public function testProductDelete()
    {
        $response = $this->makeRequest('DELETE', '/api/v1/products/1');
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        
        $responseData = json_decode((string) $response->getBody(), true);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('success', $responseData['status']);
    }
    
    /**
     * Test the authentication endpoint
     */
    public function testAuth()
    {
        $authData = [
            'email' => 'test@example.com',
            'password' => 'password'
        ];
        
        $response = $this->makeRequest('POST', '/api/v1/auth/login', $authData);
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        
        $responseData = json_decode((string) $response->getBody(), true);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('success', $responseData['status']);
    }
}
