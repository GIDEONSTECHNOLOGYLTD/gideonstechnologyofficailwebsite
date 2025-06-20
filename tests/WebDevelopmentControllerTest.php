<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Controllers\WebDevelopmentController;
use App\Models\WebDevelopment;
use App\Core\Security;
use App\Core\Logger;
use App\Core\RateLimiter;

class WebDevelopmentControllerTest extends TestCase {
    private $controller;
    private $webDevelopmentMock;
    private $securityMock;
    private $loggerMock;
    private $rateLimiterMock;

    protected function setUp(): void {
        $this->webDevelopmentMock = $this->createMock(WebDevelopment::class);
        $this->securityMock = $this->createMock(Security::class);
        $this->loggerMock = $this->createMock(Logger::class);
        $this->rateLimiterMock = $this->createMock(RateLimiter::class);

        $this->controller = new WebDevelopmentController();
        $this->controller->webDevelopment = $this->webDevelopmentMock;
        $this->controller->security = $this->securityMock;
        $this->controller->logger = $this->loggerMock;
        $this->controller->rateLimiter = $this->rateLimiterMock;
    }

    public function testIndexLoadsServices() {
        $services = ['service1', 'service2'];
        $this->webDevelopmentMock->expects($this->once())
            ->method('getServices')
            ->willReturn($services);

        $result = $this->controller->index();
        $this->assertArrayHasKey('services', $result);
        $this->assertEquals($services, $result['services']);
    }

    public function testTemplatesWithCategory() {
        $category = 'business';
        $templates = ['template1', 'template2'];
        $categories = ['business', 'ecommerce'];

        $this->rateLimiterMock->expects($this->once())
            ->method('check')
            ->willReturn(true);

        $this->webDevelopmentMock->expects($this->once())
            ->method('getTemplates')
            ->with($category)
            ->willReturn($templates);

        $this->webDevelopmentMock->expects($this->once())
            ->method('getTemplateCategories')
            ->willReturn($categories);

        $result = $this->controller->templates($category);
        $this->assertArrayHasKey('templates', $result);
        $this->assertArrayHasKey('categories', $result);
        $this->assertArrayHasKey('selectedCategory', $result);
    }

    public function testTemplateView() {
        $id = '1';
        $template = ['id' => 1, 'name' => 'Test Template'];

        $this->webDevelopmentMock->expects($this->once())
            ->method('getTemplate')
            ->with($id)
            ->willReturn($template);

        $result = $this->controller->template($id);
        $this->assertArrayHasKey('template', $result);
    }

    public function testPurchaseTemplateSuccess() {
        $id = '1';
        $userId = '123';
        $_SESSION['user_id'] = $userId;
        $_POST = [
            'csrf_token' => 'test_token',
            'customization_options' => ['color' => 'blue']
        ];

        $template = ['id' => 1, 'name' => 'Test Template'];

        $this->rateLimiterMock->expects($this->once())
            ->method('check')
            ->willReturn(true);

        $this->securityMock->expects($this->once())
            ->method('validateCSRFToken')
            ->with('test_token')
            ->willReturn(true);

        $this->securityMock->expects($this->once())
            ->method('sanitizeInput')
            ->willReturn(['customization_options' => ['color' => 'blue']]);

        $this->webDevelopmentMock->expects($this->once())
            ->method('getTemplate')
            ->with($id)
            ->willReturn($template);

        $this->webDevelopmentMock->expects($this->once())
            ->method('purchaseTemplate')
            ->with($userId, $id, ['color' => 'blue']);

        $this->loggerMock->expects($this->once())
            ->method('info')
            ->with('Template purchased successfully', [
                'user_id' => $userId,
                'template_id' => $id
            ]);

        $result = $this->controller->purchaseTemplate($id);
        $this->assertEquals('redirect', $result['type']);
        $this->assertEquals('web-dev/templates/purchased', $result['url']);
    }

    public function testPurchaseTemplateError() {
        $id = '1';
        $_SESSION['user_id'] = null;

        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with('Error in template purchase: User must be logged in', [
                'user_id' => 'guest',
                'template_id' => 'unknown'
            ]);

        $result = $this->controller->purchaseTemplate($id);
        $this->assertEquals('redirect', $result['type']);
        $this->assertEquals('auth/login', $result['url']);
    }
}
