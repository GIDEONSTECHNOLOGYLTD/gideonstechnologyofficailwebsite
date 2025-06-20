<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Flash\Messages;

/**
 * Admin Controller
 * 
 * Handles admin panel functionality
 */
class AdminController extends Controller
{
    /**
     * @var User User model
     */
    protected $userModel;
    
    /**
     * @var Order Order model
     */
    protected $orderModel;
    
    /**
     * @var Product Product model
     */
    protected $productModel;

    protected $service;
    protected $view;
    protected $flash;
    
    /**
     * Constructor
     * 
     * @param Twig|null $view View renderer
     * @param Messages|null $flash Flash messages
     */
    public function __construct(?Twig $view = null, ?Messages $flash = null)
    {
        parent::__construct($view, $flash);
        $this->userModel = new User();
        $this->orderModel = new Order();
        $this->productModel = new Product();
        $this->service = new Service();
    }
    
    /**
     * Admin dashboard
     * 
     * @param Request $request The request
     * @param Response $response The response
     * @return Response Rendered dashboard
     */
    public function dashboard(Request $request, Response $response): Response
    {
        // Get statistics for dashboard
        $userCount = $this->userModel->countAll();
        $orderCount = $this->orderModel->countAll();
        $revenueTotal = $this->orderModel->calculateTotalRevenue();
        $recentUsers = $this->userModel->getRecent(5);
        $recentOrders = $this->orderModel->getRecent(5);
        
        return $this->view->render($response, 'admin/dashboard.twig', [
            'user_count' => $userCount,
            'order_count' => $orderCount,
            'revenue_total' => $revenueTotal,
            'recent_users' => $recentUsers,
            'recent_orders' => $recentOrders
        ]);
    }
    
    /**
     * List users
     * 
     * @param Request $request The request
     * @param Response $response The response
     * @return Response Rendered user list
     */
    public function users(Request $request, Response $response): Response
    {
        $users = $this->userModel->findAll();
        
        return $this->view->render($response, 'admin/users/index.twig', [
            'users' => $users
        ]);
    }
    
    /**
     * List orders
     * 
     * @param Request $request The request
     * @param Response $response The response
     * @return Response Rendered order list
     */
    public function orders(Request $request, Response $response): Response
    {
        $orders = $this->orderModel->findAll();
        
        return $this->view->render($response, 'admin/orders/index.twig', [
            'orders' => $orders
        ]);
    }
    
    /**
     * List services
     * 
     * @param Request $request The request
     * @param Response $response The response
     * @return Response Rendered service list
     */
    public function services(Request $request, Response $response): Response
    {
        $services = $this->service->findAll();
        
        return $this->view->render($response, 'admin/services/index.twig', [
            'services' => $services
        ]);
    }
    
    /**
     * Admin profile
     * 
     * @param Request $request The request
     * @param Response $response The response
     * @return Response Rendered profile
     */
    public function profile(Request $request, Response $response): Response
    {
        $adminUser = $request->getAttribute('user');
        
        if (!$adminUser) {
            return $response->withHeader('Location', '/auth/login')
                          ->withStatus(302);
        }
        
        return $this->view->render($response, 'admin/profile.twig', [
            'admin' => $adminUser
        ]);
    }
    
    /**
     * Update user profile
     * 
     * @param Request $request Request
     * @param Response $response Response
     * @return Response
     */
    public function updateProfile(Request $request, Response $response): Response
    {
        $adminUser = $request->getAttribute('user');
        $data = $request->getParsedBody();
        
        if (!$adminUser) {
            return $response->withHeader('Location', '/auth/login')
                          ->withStatus(302);
        }
        
        $errors = $this->validateProfileData($data);
        
        if (!empty($errors)) {
            return $this->view->render($response, 'admin/profile.twig', [
                'admin' => $adminUser,
                'errors' => $errors,
                'form_data' => $data
            ]);
        }
        
        // Update user profile (name and email)
        $this->userModel->update($adminUser->id, [
            'name' => $data['name'],
            'email' => $data['email'],
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        // If password is provided, update it
        if (!empty($data['password'])) {
            $this->userModel->updatePassword($adminUser->id, $data['password']);
        }
        
        $this->flash->addMessage('success', 'Profile updated successfully.');
        
        return $response->withHeader('Location', '/admin/profile')
                      ->withStatus(302);
    }
    
    /**
     * Validate profile data
     * 
     * @param array $data Profile data
     * @return array Validation errors
     */
    protected function validateProfileData(array $data): array
    {
        $errors = [];
        
        // Validate name
        if (empty($data['name'])) {
            $errors['name'] = 'Name is required.';
        }
        
        // Validate email
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email is invalid.';
        }
        
        // Validate password
        if (!empty($data['password'])) {
            if (strlen($data['password']) < 8) {
                $errors['password'] = 'Password must be at least 8 characters.';
            } elseif ($data['password'] !== $data['password_confirm']) {
                $errors['password_confirm'] = 'Passwords do not match.';
            }
        }
        
        return $errors;
    }
}