<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Controllers\Api\V1\ProductController;
use App\Repositories\ProductRepository;
use App\Services\ProductService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\ResponseFactory;

/**
 * Test for ProductController
 */
class ProductControllerTest extends TestCase
{
    /**
     * @var ProductController
     */
    protected $controller;
    
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ProductService
     */
    protected $mockService;
    
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ServerRequestInterface
     */
    protected $mockRequest;
    
    /**
     * @var ResponseInterface
     */
    protected $response;
    
    /**
     * Set up the test
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockService = $this->getMockBuilder(ProductService::class)
            ->disableOriginalConstructor()
            ->getMock();
            
        $mockRepository = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
            
        $this->mockRequest = $this->getMockBuilder(ServerRequestInterface::class)
            ->getMock();
            
        $this->response = (new ResponseFactory())->createResponse();
        
        $this->controller = new ProductController($mockRepository);
        
        // Replace the service in the controller with our mock
        $reflection = new \ReflectionClass($this->controller);
        $property = $reflection->getProperty('service');
        $property->setAccessible(true);
        $property->setValue($this->controller, $this->mockService);
    }
    
    /**
     * Test the index action
     */
    public function testIndex()
    {
        $expectedData = [
            ['id' => 1, 'name' => 'Product 1'],
            ['id' => 2, 'name' => 'Product 2']
        ];
        
        $this->mockService->expects($this->once())
            ->method('getAll')
            ->willReturn($expectedData);
        
        $response = $this->controller->index($this->mockRequest, $this->response);
        
        $this->assertEquals(200, $response->getStatusCode());
        
        $responseBody = (string) $response->getBody();
        $responseData = json_decode($responseBody, true);
        
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('success', $responseData['status']);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertEquals($expectedData, $responseData['data']);
    }
    
    /**
     * Test the show action
     */
    public function testShow()
    {
        $expectedProduct = ['id' => 1, 'name' => 'Product 1'];
        
        $this->mockRequest->expects($this->once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn('1');
            
        $this->mockService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($expectedProduct);
        
        $response = $this->controller->show($this->mockRequest, $this->response);
        
        $this->assertEquals(200, $response->getStatusCode());
        
        $responseBody = (string) $response->getBody();
        $responseData = json_decode($responseBody, true);
        
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('success', $responseData['status']);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertEquals($expectedProduct, $responseData['data']);
    }
    
    /**
     * Test the show action with non-existent product
     */
    public function testShowNotFound()
    {
        $this->mockRequest->expects($this->once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn('999');
            
        $this->mockService->expects($this->once())
            ->method('getById')
            ->with(999)
            ->willReturn(null);
        
        $response = $this->controller->show($this->mockRequest, $this->response);
        
        $this->assertEquals(404, $response->getStatusCode());
        
        $responseBody = (string) $response->getBody();
        $responseData = json_decode($responseBody, true);
        
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('error', $responseData['status']);
        $this->assertArrayHasKey('message', $responseData);
    }
    
    /**
     * Test the store action
     */
    public function testStore()
    {
        $productData = [
            'name' => 'New Product',
            'price' => 29.99,
            'category_id' => 1
        ];
        
        $this->mockRequest->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($productData);
            
        $this->mockService->expects($this->once())
            ->method('create')
            ->with($productData)
            ->willReturn(5); // New product ID
        
        $response = $this->controller->store($this->mockRequest, $this->response);
        
        $this->assertEquals(201, $response->getStatusCode());
        
        $responseBody = (string) $response->getBody();
        $responseData = json_decode($responseBody, true);
        
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('success', $responseData['status']);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals(5, $responseData['id']);
    }
    
    /**
     * Test the store action with validation errors
     */
    public function testStoreValidationError()
    {
        $productData = [
            'name' => '', // Empty required field
            'category_id' => 1
        ];
        
        $validationErrors = [
            'name' => 'The name field is required',
            'price' => 'The price field is required'
        ];
        
        $this->mockRequest->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($productData);
            
        $this->mockService->expects($this->once())
            ->method('create')
            ->with($productData)
            ->willReturn($validationErrors);
        
        $response = $this->controller->store($this->mockRequest, $this->response);
        
        $this->assertEquals(422, $response->getStatusCode());
        
        $responseBody = (string) $response->getBody();
        $responseData = json_decode($responseBody, true);
        
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('error', $responseData['status']);
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertEquals($validationErrors, $responseData['errors']);
    }
    
    /**
     * Test the update action
     */
    public function testUpdate()
    {
        $productData = [
            'name' => 'Updated Product',
            'price' => 39.99
        ];
        
        $this->mockRequest->expects($this->once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn('1');
            
        $this->mockRequest->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($productData);
            
        $this->mockService->expects($this->once())
            ->method('update')
            ->with(1, $productData)
            ->willReturn(true);
        
        $response = $this->controller->update($this->mockRequest, $this->response);
        
        $this->assertEquals(200, $response->getStatusCode());
        
        $responseBody = (string) $response->getBody();
        $responseData = json_decode($responseBody, true);
        
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('success', $responseData['status']);
        $this->assertArrayHasKey('message', $responseData);
    }
    
    /**
     * Test the delete action
     */
    public function testDelete()
    {
        $this->mockRequest->expects($this->once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn('1');
            
        $this->mockService->expects($this->once())
            ->method('delete')
            ->with(1)
            ->willReturn(true);
        
        $response = $this->controller->delete($this->mockRequest, $this->response);
        
        $this->assertEquals(200, $response->getStatusCode());
        
        $responseBody = (string) $response->getBody();
        $responseData = json_decode($responseBody, true);
        
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('success', $responseData['status']);
        $this->assertArrayHasKey('message', $responseData);
    }
}
