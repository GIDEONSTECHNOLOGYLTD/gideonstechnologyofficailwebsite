<?php

namespace App\Controllers\Admin;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Utilities\Logger;

class DashboardController
{
    protected $container;
    protected $renderer;
    protected $db;
    
    /**
     * Constructor
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->renderer = $container->get('renderer');
        $this->db = $container->get('db');
    }
    
    /**
     * Admin dashboard index
     */
    public function index(Request $request, Response $response): Response
    {
        try {
            // Get some basic stats for the dashboard
            $stats = [
                'users' => 0,
                'products' => 0,
                'orders' => 0,
                'revenue' => 0
            ];
            
            // Get user count
            try {
                $stmt = $this->db->query("SELECT COUNT(*) as count FROM users");
                $stats['users'] = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];
            } catch (\Exception $e) {
                Logger::error('Error getting user count: ' . $e->getMessage());
            }
            
            // Get product count
            try {
                $stmt = $this->db->query("SELECT COUNT(*) as count FROM products");
                $stats['products'] = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];
            } catch (\Exception $e) {
                Logger::error('Error getting product count: ' . $e->getMessage());
            }
            
            // Get order count
            try {
                $stmt = $this->db->query("SELECT COUNT(*) as count FROM orders");
                $stats['orders'] = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];
            } catch (\Exception $e) {
                Logger::error('Error getting order count: ' . $e->getMessage());
            }
            
            // Get total revenue
            try {
                $stmt = $this->db->query("SELECT SUM(total) as total FROM orders");
                $stats['revenue'] = $stmt->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
            } catch (\Exception $e) {
                Logger::error('Error getting revenue: ' . $e->getMessage());
            }
            
            // Get recent orders
            $recentOrders = [];
            try {
                $stmt = $this->db->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
                $recentOrders = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } catch (\Exception $e) {
                Logger::error('Error getting recent orders: ' . $e->getMessage());
            }
            
            // Get recent users
            $recentUsers = [];
            try {
                $stmt = $this->db->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
                $recentUsers = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } catch (\Exception $e) {
                Logger::error('Error getting recent users: ' . $e->getMessage());
            }
            
            return $this->renderer->render($response, 'admin/dashboard.php', [
                'title' => 'Admin Dashboard',
                'stats' => $stats,
                'recentOrders' => $recentOrders,
                'recentUsers' => $recentUsers,
                'user' => $_SESSION['user'] ?? []
            ]);
        } catch (\Exception $e) {
            Logger::error('Admin dashboard error: ' . $e->getMessage());
            $response->getBody()->write("<h1>Error loading admin dashboard</h1><p>{$e->getMessage()}</p>");
            return $response->withStatus(500);
        }
    }
}
