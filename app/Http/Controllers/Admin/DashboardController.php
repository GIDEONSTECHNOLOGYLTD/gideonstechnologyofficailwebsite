<?php

namespace App\Http\Controllers\Admin;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;

class DashboardController extends Controller
{
    // Add this at the top with other use statements
    use Illuminate\Support\Facades\Cache;
    
    // Inside your DashboardController class
    public function index()
    {
        // Cache the dashboard data for 15 minutes
        $data = Cache::remember('dashboard_stats', now()->addMinutes(15), function () {
            // Your existing stats calculation code here
            $today = now()->startOfDay();
            $weekStart = now()->startOfWeek();
            $weekEnd = now()->endOfWeek();
            
            // User statistics
            $totalUsers = User::count();
            $newUsersThisWeek = User::whereBetween('created_at', [$weekStart, $weekEnd])->count();
            
            // Order statistics
            $totalOrders = Order::count();
            $todayOrders = Order::whereDate('created_at', $today)->count();
            $completedOrders = Order::where('status', 'completed')->count();
            $todayRevenue = Order::whereDate('created_at', $today)
                ->where('status', 'completed')
                ->sum('total_amount');
            $totalRevenue = Order::where('status', 'completed')
                ->sum('total_amount');
                
            // Product statistics
            $totalProducts = Product::count();
            $lowStockProducts = Product::where('stock', '<=', 10)->count();
            $outOfStockProducts = Product::where('stock', '<=', 0)->count();
            
            // Weekly revenue data for the chart
            $weeklyRevenue = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
                ->where('status', 'completed')
                ->whereBetween('created_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [date('D', strtotime($item->date)) => $item->revenue];
                });
            
            return [
                'totalUsers' => $totalUsers,
                'newUsersThisWeek' => $newUsersThisWeek,
                'totalOrders' => $totalOrders,
                'todayOrders' => $todayOrders,
                'completedOrders' => $completedOrders,
                'todayRevenue' => $todayRevenue,
                'totalRevenue' => $totalRevenue,
                'totalProducts' => $totalProducts,
                'lowStockProducts' => $lowStockProducts,
                'outOfStockProducts' => $outOfStockProducts,
                'weekly_revenue' => $weeklyRevenue
            ];
        });
        
        return view('admin.dashboard.index', $data);
    }
    
    /**
     * Get recent orders with formatted data
     */
    protected function getRecentOrders($limit = 10)
    {
        return Order::with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($order) {
                return [
                    'id' => $order->id,
                    'order_number' => 'ORD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                    'customer_name' => $order->user ? $order->user->name : 'Guest',
                    'customer_email' => $order->user ? $order->user->email : $order->email,
                    'total_amount' => $order->total_amount,
                    'status' => $order->status,
                    'status_class' => $this->getStatusClass($order->status),
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at
                ];
            });
    }
    
    /**
     * Get dashboard statistics
     */
    protected function getDashboardStatistics($today, $weekStart, $weekEnd)
    {
        // User statistics
        $totalUsers = User::count();
        $newUsersThisWeek = User::whereBetween('created_at', [$weekStart, $weekEnd])->count();
        $newUsersLastWeek = User::whereBetween('created_at', [
            $weekStart->copy()->subWeek(), 
            $weekEnd->copy()->subWeek()
        ])->count();
        
        $userGrowth = $newUsersLastWeek > 0 
            ? round((($newUsersThisWeek - $newUsersLastWeek) / $newUsersLastWeek) * 100) 
            : ($newUsersThisWeek > 0 ? 100 : 0);
        
        // Order statistics
        $totalOrders = Order::count();
        $todayOrders = Order::whereDate('created_at', $today)->count();
        $completedOrders = Order::where('status', 'completed')->count();
        $todayRevenue = Order::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->sum('total_amount');
        $totalRevenue = Order::where('status', 'completed')
            ->sum('total_amount');
            
        // Product statistics
        $totalProducts = Product::count();
        $lowStockProducts = Product::where('stock', '<=', 10)->count();
        $outOfStockProducts = Product::where('stock', '<=', 0)->count();
        
        // Get popular products
        $popularProducts = $this->getPopularProducts(5);
        
        return [
            'total_users' => $totalUsers,
            'new_users_this_week' => $newUsersThisWeek,
            'user_growth' => $userGrowth,
            'total_orders' => $totalOrders,
            'today_orders' => $todayOrders,
            'completed_orders' => $completedOrders,
            'today_revenue' => $todayRevenue,
            'total_revenue' => $totalRevenue,
            'total_products' => $totalProducts,
            'low_stock_products' => $lowStockProducts,
            'out_of_stock_products' => $outOfStockProducts,
            'popular_products' => $popularProducts,
            'conversion_rate' => $this->calculateConversionRate()
        ];
    }
    
    /**
     * Get popular products with order count
     */
    protected function getPopularProducts($limit = 5)
    {
        return Product::withCount(['orderItems as orders_count'])
            ->orderBy('orders_count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'initial_stock' => $product->initial_stock ?? $product->stock + 10, // Fallback if not set
                    'order_count' => $product->orders_count,
                    'image' => $product->getFirstMediaUrl('products', 'thumb') ?? '/assets/img/default-product.png'
                ];
            });
    }
    
    /**
     * Get recent activities
     */
    protected function getRecentActivities($limit = 5)
    {
        return Activity::with('causer')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function($activity) {
                $description = $this->getActivityDescription($activity);
                
                return [
                    'id' => $activity->id,
                    'description' => $description['text'],
                    'icon' => $description['icon'],
                    'type' => $activity->log_name,
                    'type_class' => $this->getActivityTypeClass($activity->log_name),
                    'causer_name' => $activity->causer ? $activity->causer->name : 'System',
                    'created_at' => $activity->created_at,
                    'time_ago' => $activity->created_at->diffForHumans(),
                    'properties' => $activity->properties
                ];
            });
    }
    
    /**
     * Get weekly revenue data for the chart
     */
    protected function getWeeklyRevenueData($startDate, $endDate)
    {
        $revenueData = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $day = $currentDate->format('Y-m-d');
            $revenueData[$day] = [
                'day' => $currentDate->format('D'),
                'date' => $currentDate->format('M j'),
                'revenue' => 0
            ];
            $currentDate->addDay();
        }
        
        // Get actual revenue data from the database
        $revenueResults = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->get()
            ->keyBy(function($item) {
                return $item->date;
            });
        
        // Merge actual data with the full date range
        foreach ($revenueResults as $date => $result) {
            if (isset($revenueData[$date])) {
                $revenueData[$date]['revenue'] = (float)$result->total;
            }
        }
        
        return array_values($revenueData);
    }
    
    /**
     * Calculate conversion rate (orders/visitors)
     */
    protected function calculateConversionRate()
    {
        // This is a simplified version - you'd typically get this from your analytics
        $totalVisitors = 1000; // Replace with actual visitor count
        $totalOrders = Order::count();
        
        return $totalVisitors > 0 ? round(($totalOrders / $totalVisitors) * 100, 2) : 0;
    }
    
    /**
     * Get status class for UI
     */
    protected function getStatusClass($status)
    {
        $classes = [
            'pending' => 'warning',
            'processing' => 'info',
            'shipped' => 'primary',
            'delivered' => 'success',
            'completed' => 'success',
            'cancelled' => 'danger',
            'refunded' => 'secondary',
            'failed' => 'danger',
        ];
        
        return $classes[strtolower($status)] ?? 'secondary';
    }
    
    /**
     * Get activity type class for UI
     */
    protected function getActivityTypeClass($type)
    {
        $classes = [
            'user' => 'primary',
            'order' => 'success',
            'product' => 'info',
            'category' => 'warning',
            'settings' => 'secondary',
        ];
        
        return $classes[strtolower($type)] ?? 'secondary';
    }
    
    /**
     * Get activity description
     */
    protected function getActivityDescription($activity)
    {
        $descriptions = [
            'created' => [
                'text' => 'created a new ' . $activity->log_name,
                'icon' => 'plus-circle'
            ],
            'updated' => [
                'text' => 'updated a ' . $activity->log_name,
                'icon' => 'edit'
            ],
            'deleted' => [
                'text' => 'deleted a ' . $activity->log_name,
                'icon' => 'trash-alt'
            ],
            'logged_in' => [
                'text' => 'logged in',
                'icon' => 'sign-in-alt'
            ],
            'logged_out' => [
                'text' => 'logged out',
                'icon' => 'sign-out-alt'
            ],
        ];
        
        $action = $activity->description;
        $default = [
            'text' => $activity->description . ' ' . $activity->log_name,
            'icon' => 'info-circle'
        ];
        
        return $descriptions[$action] ?? $default;
    }
}
