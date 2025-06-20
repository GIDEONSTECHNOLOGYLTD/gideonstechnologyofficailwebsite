<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ProductService;
use App\Repositories\ProductRepository;

/**
 * Test for ProductService
 */
class ProductServiceTest extends TestCase
{
    /**
     * @var ProductService
     */
    protected $service;
    
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ProductRepository
     */
    protected $mockRepository;
    
    /**
     * Set up the test
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockRepository = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
            
        $this->service = new ProductService($this->mockRepository);
    }
    
    /**
     * Test getting featured products
     */
    public function testGetFeaturedProducts()
    {
        $expectedData = [
            ['id' => 1, 'name' => 'Featured Product 1', 'featured' => 1],
            ['id' => 2, 'name' => 'Featured Product 2', 'featured' => 1]
        ];
        
        $this->mockRepository->expects($this->once())
            ->method('query')
            ->with(
                $this->stringContains("SELECT * FROM products WHERE featured = 1"),
                $this->arrayHasKey('limit')
            )
            ->willReturn($expectedData);
        
        $result = $this->service->getFeaturedProducts(2);
        
        $this->assertEquals($expectedData, $result);
    }
    
    /**
     * Test getting products by category
     */
    public function testGetProductsByCategory()
    {
        $expectedData = [
            ['id' => 1, 'name' => 'Category Product 1', 'category_id' => 5],
            ['id' => 2, 'name' => 'Category Product 2', 'category_id' => 5]
        ];
        
        $this->mockRepository->expects($this->once())
            ->method('query')
            ->with(
                $this->stringContains("SELECT * FROM products WHERE category_id = :category_id"),
                $this->arrayHasKey('category_id')
            )
            ->willReturn($expectedData);
        
        $result = $this->service->getProductsByCategory(5);
        
        $this->assertEquals($expectedData, $result);
    }
    
    /**
     * Test searching for products
     */
    public function testSearchProducts()
    {
        $expectedData = [
            ['id' => 1, 'name' => 'Test Product'],
            ['id' => 2, 'name' => 'Another Test']
        ];
        
        $this->mockRepository->expects($this->once())
            ->method('query')
            ->with(
                $this->stringContains("SELECT * FROM products WHERE (name LIKE :search OR description LIKE :search)"),
                $this->arrayHasKey('search')
            )
            ->willReturn($expectedData);
        
        $result = $this->service->searchProducts('test');
        
        $this->assertEquals($expectedData, $result);
    }
    
    /**
     * Test getting related products
     */
    public function testGetRelatedProducts()
    {
        $product = ['id' => 1, 'name' => 'Test Product', 'category_id' => 5];
        $expectedData = [
            ['id' => 2, 'name' => 'Related Product', 'category_id' => 5],
            ['id' => 3, 'name' => 'Another Related', 'category_id' => 5]
        ];
        
        $this->mockRepository->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($product);
            
        $this->mockRepository->expects($this->once())
            ->method('query')
            ->with(
                $this->stringContains("SELECT * FROM products WHERE category_id = :category_id AND id != :product_id"),
                $this->callback(function($params) {
                    return $params['category_id'] === 5 && $params['product_id'] === 1;
                })
            )
            ->willReturn($expectedData);
        
        $result = $this->service->getRelatedProducts(1);
        
        $this->assertEquals($expectedData, $result);
    }
    
    /**
     * Test validation with valid data
     */
    public function testValidateWithValidData()
    {
        $data = [
            'name' => 'Test Product',
            'price' => 19.99,
            'category_id' => 5,
            'slug' => 'test-product'
        ];
        
        $this->mockRepository->expects($this->once())
            ->method('findOneBy')
            ->with('slug', 'test-product')
            ->willReturn(null);
        
        // Call the protected validate method using reflection
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('validate');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->service, $data);
        
        $this->assertTrue($result);
    }
    
    /**
     * Test validation with invalid data
     */
    public function testValidateWithInvalidData()
    {
        $data = [
            'name' => '',  // Empty required field
            'price' => 'not-a-number',  // Invalid numeric field
            'category_id' => 5
        ];
        
        // Call the protected validate method using reflection
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('validate');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->service, $data);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('price', $result);
    }
    
    /**
     * Test validation with duplicate slug
     */
    public function testValidateWithDuplicateSlug()
    {
        $data = [
            'name' => 'Test Product',
            'price' => 19.99,
            'category_id' => 5,
            'slug' => 'existing-slug'
        ];
        
        $existingProduct = [
            'id' => 2,
            'name' => 'Existing Product',
            'slug' => 'existing-slug'
        ];
        
        $this->mockRepository->expects($this->once())
            ->method('findOneBy')
            ->with('slug', 'existing-slug')
            ->willReturn($existingProduct);
        
        // Call the protected validate method using reflection
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('validate');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->service, $data);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('slug', $result);
    }
}
