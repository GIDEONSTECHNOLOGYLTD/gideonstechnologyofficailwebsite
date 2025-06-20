<?php
namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Transaction;

class AnalyticsService {
    private $order;
    private $user;
    private $product;
    private $transaction;

    public function __construct() {
        $this->order = new Order();
        $this->user = new User();
        $this->product = new Product();
        $this->transaction = new Transaction();
    }

    public function getDashboardMetrics() {
        return [
            'orders' => $this->getOrderMetrics(),
            'users' => $this->getUserMetrics(),
            'revenue' => $this->getRevenueMetrics(),
            'products' => $this->getProductMetrics()
        ];
    }

    private function getOrderMetrics() {
        $stmt = $this->order->getDb()->prepare("
            SELECT 
                COUNT(*) as total_orders,
                COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as last_30_days,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed
            FROM orders
        ");
        $stmt->execute();
        return $stmt->fetch();
    }

    private function getUserMetrics() {
        $stmt = $this->user->getDb()->prepare("
            SELECT 
                COUNT(*) as total_users,
                COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as new_users,
                COUNT(CASE WHEN last_login >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as active_users
            FROM users
        ");
        $stmt->execute();
        return $stmt->fetch();
    }

    private function getRevenueMetrics() {
        $stmt = $this->transaction->getDb()->prepare("
            SELECT 
                SUM(amount) as total_revenue,
                SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN amount ELSE 0 END) as monthly_revenue,
                SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN amount ELSE 0 END) as weekly_revenue
            FROM transactions
            WHERE status = 'completed'
        ");
        $stmt->execute();
        return $stmt->fetch();
    }

    private function getProductMetrics() {
        $stmt = $this->product->getDb()->prepare("
            SELECT 
                COUNT(*) as total_products,
                COUNT(CASE WHEN status = 'active' THEN 1 END) as active_products,
                COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as new_products
            FROM products
        ");
        $stmt->execute();
        return $stmt->fetch();
    }
}