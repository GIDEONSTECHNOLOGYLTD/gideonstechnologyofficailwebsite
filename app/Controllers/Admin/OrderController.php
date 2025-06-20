<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Flash\Messages;

/**
 * Admin Order Controller
 * 
 * Handles order management in the admin area
 */
class OrderController extends Controller
{
    /**
     * @var Order Order model
     */
    protected $orderModel;
    
    /**
     * @var User User model
     */
    protected $userModel;
    
    /**
     * @var Product Product model
     */
    protected $productModel;
    
    /**
     * Constructor
     * 
     * @param Twig|null $view View renderer
     * @param Messages|null $flash Flash messages
     */
    public function __construct(?Twig $view = null, ?Messages $flash = null)
    {
        parent::__construct($view, $flash);
        $this->orderModel = new Order();
        $this->userModel = new User();
        $this->productModel = new Product();
    }
    
    /**
     * List all orders
     * 
     * @param Request $request The request
     * @param Response $response The response
     * @return Response Rendered orders list
     */
    public function index(Request $request, Response $response): Response
    {
        // Get page and limit from query parameters
        $params = $request->getQueryParams();
        $page = isset($params['page']) ? (int)$params['page'] : 1;
        $limit = isset($params['limit']) ? (int)$params['limit'] : 20;
        
        // Get search parameters
        $search = $params['search'] ?? '';
        $status = $params['status'] ?? '';
        
        // Get orders with pagination
        $orders = $this->orderModel->paginate($page, $limit);
        
        // Get order statuses for filter
        $statuses = $this->orderModel->getStatuses();
        
        return $this->render($response, 'admin/orders/index.twig', [
            'orders' => $orders['data'],
            'pagination' => [
                'total' => $orders['total'],
                'current_page' => $orders['current_page'],
                'per_page' => $orders['per_page'],
                'last_page' => $orders['last_page']
            ],
            'search' => $search,
            'status' => $status,
            'statuses' => $statuses
        ]);
    }
    
    /**
     * Show order details
     * 
     * @param Request $request The request
     * @param Response $response The response
     * @param array $args Route arguments
     * @return Response Rendered order details
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        // Get order by ID
        $order = $this->orderModel->find($args['id']);
        
        if (!$order) {
            $this->flash->addMessage('error', 'Order not found');
            return $response->withHeader('Location', '/admin/orders')
                           ->withStatus(302);
        }
        
        // Get order items
        $items = $this->orderModel->getOrderItems($order->id);
        
        // Get user info
        $user = $this->userModel->find($order->user_id);
        
        return $this->render($response, 'admin/orders/show.twig', [
            'order' => $order,
            'items' => $items,
            'user' => $user
        ]);
    }
    
    /**
     * Process order status update
     * 
     * @param Request $request The request
     * @param Response $response The response
     * @param array $args Route arguments
     * @return Response Redirect response
     */
    public function updateStatus(Request $request, Response $response, array $args): Response
    {
        // Get order by ID
        $order = $this->orderModel->find($args['id']);
        
        if (!$order) {
            $this->flash->addMessage('error', 'Order not found');
            return $response->withHeader('Location', '/admin/orders')
                           ->withStatus(302);
        }
        
        // Get status from form
        $data = $request->getParsedBody();
        $status = $data['status'] ?? '';
        
        if (empty($status)) {
            $this->flash->addMessage('error', 'Status is required');
            return $response->withHeader('Location', '/admin/orders/' . $order->id)
                           ->withStatus(302);
        }
        
        // Update order status
        $updated = $this->orderModel->updateStatus($order->id, $status);
        
        if ($updated) {
            $this->flash->addMessage('success', 'Order status updated successfully');
        } else {
            $this->flash->addMessage('error', 'Failed to update order status');
        }
        
        return $response->withHeader('Location', '/admin/orders/' . $order->id)
                       ->withStatus(302);
    }
    
    /**
     * Export orders to CSV
     * 
     * @param Request $request The request
     * @param Response $response The response
     * @return Response CSV download response
     */
    public function export(Request $request, Response $response): Response
    {
        // Get query parameters
        $params = $request->getQueryParams();
        $startDate = $params['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $params['end_date'] ?? date('Y-m-d');
        $status = $params['status'] ?? '';
        
        // Get orders for export
        $orders = $this->orderModel->findForExport($startDate, $endDate, $status);
        
        // Generate CSV content
        $csv = "ID,Order Number,Customer,Date,Status,Total\n";
        
        foreach ($orders as $order) {
            $user = $this->userModel->find($order->user_id);
            $customerName = $user ? "{$user->name}" : "N/A";
            
            // Escape fields for CSV
            $customerName = str_replace('"', '""', $customerName);
            
            $csv .= "{$order->id},\"{$order->order_number}\",\"{$customerName}\",{$order->created_at},{$order->status},{$order->total}\n";
        }
        
        // Set response headers for CSV download
        $response = $response->withHeader('Content-Type', 'text/csv')
                           ->withHeader('Content-Disposition', 'attachment; filename="orders-export.csv"');
        
        // Write CSV content to response body
        $response->getBody()->write($csv);
        
        return $response;
    }
    
    /**
     * Delete order
     * 
     * @param Request $request The request
     * @param Response $response The response
     * @param array $args Route arguments
     * @return Response Redirect response
     */
    public function delete(Request $request, Response $response, array $args): Response
    {
        // Get order by ID
        $order = $this->orderModel->find($args['id']);
        
        if (!$order) {
            $this->flash->addMessage('error', 'Order not found');
            return $response->withHeader('Location', '/admin/orders')
                           ->withStatus(302);
        }
        
        // Delete order
        $deleted = $this->orderModel->delete($order->id);
        
        if ($deleted) {
            $this->flash->addMessage('success', 'Order deleted successfully');
        } else {
            $this->flash->addMessage('error', 'Failed to delete order');
        }
        
        return $response->withHeader('Location', '/admin/orders')
                       ->withStatus(302);
    }
}