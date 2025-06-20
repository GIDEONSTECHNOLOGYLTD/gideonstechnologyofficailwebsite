<?php

namespace App\Http\Controllers\Admin;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use Respect\Validation\Validator as v;

class OrderController extends Controller
{
    /**
     * Display a listing of orders
     */
    public function index(Request $request, Response $response): Response
    {
        try {
            $status = $request->getQueryParams()['status'] ?? null;
            $query = Order::with(['user', 'items.product']);
            
            if ($status && in_array($status, ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])) {
                $query->where('status', $status);
            }
            
            $orders = $query->latest()->get();
            
            return $this->render($response, 'admin/orders/index.php', [
                'title' => 'Manage Orders',
                'orders' => $orders,
                'status' => $status,
                'active_menu' => 'orders'
            ]);
        } catch (\Exception $e) {
            $this->container->get('logger')->error('Order List Error: ' . $e->getMessage());
            $this->flash('error', 'Failed to load orders.');
            return $response->withHeader('Location', '/admin')->withStatus(302);
        }
    }

    /**
     * Display the specified order
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        try {
            $order = Order::with(['user', 'items.product'])->findOrFail($args['id']);
            
            return $this->render($response, 'admin/orders/show.php', [
                'title' => 'Order Details',
                'order' => $order,
                'active_menu' => 'orders'
            ]);
            
        } catch (\Exception $e) {
            $this->container->get('logger')->error('Order Show Error: ' . $e->getMessage());
            $this->flash('error', 'Order not found');
            return $response->withHeader('Location', '/admin/orders')->withStatus(302);
        }
    }

    /**
     * Show the form for editing an order
     */
    public function edit(Request $request, Response $response, array $args): Response
    {
        try {
            $order = Order::with(['user', 'items.product'])->findOrFail($args['id']);
            $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
            
            return $this->render($response, 'admin/orders/edit.php', [
                'title' => 'Edit Order',
                'order' => $order,
                'statuses' => $statuses,
                'active_menu' => 'orders'
            ]);
            
        } catch (\Exception $e) {
            $this->container->get('logger')->error('Order Edit Error: ' . $e->getMessage());
            $this->flash('error', 'Order not found');
            return $response->withHeader('Location', '/admin/orders')->withStatus(302);
        }
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();
        
        try {
            $order = Order::findOrFail($args['id']);
            
            $validation = $this->validateOrderData($data, $order);
            
            if (!$validation['valid']) {
                $this->flash('error', implode(' ', $validation['errors']));
                return $response->withHeader('Location', "/admin/orders/{$order->id}/edit")->withStatus(302);
            }
            
            $originalStatus = $order->status;
            $newStatus = $data['status'];
            
            // Update order status
            $order->status = $newStatus;
            $order->shipping_tracking = $data['shipping_tracking'] ?? null;
            $order->notes = $data['notes'] ?? null;
            
            // Update order items if provided
            if (!empty($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $itemId => $itemData) {
                    $item = OrderItem::find($itemId);
                    if ($item && $item->order_id === $order->id) {
                        $item->quantity = $itemData['quantity'];
                        $item->price = $itemData['price'];
                        $item->save();
                    }
                }
            }
            
            // Recalculate order total
            $order->total = $order->items->sum(function($item) {
                return $item->quantity * $item->price;
            });
            
            $order->save();
            
            // Trigger status change events if needed
            if ($originalStatus !== $newStatus) {
                $this->handleOrderStatusChange($order, $originalStatus, $newStatus);
            }
            
            $this->flash('success', 'Order updated successfully');
            return $response->withHeader('Location', '/admin/orders/' . $order->id)->withStatus(302);
            
        } catch (\Exception $e) {
            $this->container->get('logger')->error('Order Update Error: ' . $e->getMessage());
            $this->flash('error', 'Failed to update order');
            return $response->withHeader('Location', "/admin/orders/{$args['id']}/edit")->withStatus(302);
        }
    }

    /**
     * Delete the specified order
     */
    public function delete(Request $request, Response $response, array $args): Response
    {
        try {
            $order = Order::findOrFail($args['id']);
            
            // Only allow deletion of pending or cancelled orders
            if (!in_array($order->status, ['pending', 'cancelled'])) {
                $this->flash('error', 'Only pending or cancelled orders can be deleted');
                return $response->withHeader('Location', '/admin/orders')->withStatus(302);
            }
            
            // Delete order items
            OrderItem::where('order_id', $order->id)->delete();
            
            // Delete the order
            $order->delete();
            
            $this->flash('success', 'Order deleted successfully');
            return $response->withHeader('Location', '/admin/orders')->withStatus(302);
            
        } catch (\Exception $e) {
            $this->container->get('logger')->error('Order Delete Error: ' . $e->getMessage());
            $this->flash('error', 'Failed to delete order');
            return $response->withHeader('Location', '/admin/orders')->withStatus(302);
        }
    }
    
    /**
     * Validate order data
     */
    private function validateOrderData(array $data, Order $order): array
    {
        $errors = [];
        
        $statusValidator = v::in(['pending', 'processing', 'shipped', 'delivered', 'cancelled']);
        $trackingValidator = v::optional(v::stringType()->length(0, 255));
        $notesValidator = v::optional(v::stringType());
        
        if (!$statusValidator->validate($data['status'] ?? '')) {
            $errors[] = 'Invalid order status';
        }
        
        if (isset($data['shipping_tracking']) && !$trackingValidator->validate($data['shipping_tracking'])) {
            $errors[] = 'Invalid tracking number';
        }
        
        if (isset($data['notes']) && !$notesValidator->validate($data['notes'])) {
            $errors[] = 'Invalid notes';
        }
        
        // Validate order items if provided
        if (!empty($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $itemId => $itemData) {
                $item = OrderItem::find($itemId);
                if (!$item || $item->order_id !== $order->id) {
                    $errors[] = 'Invalid order item';
                    break;
                }
                
                if (!isset($itemData['quantity']) || !is_numeric($itemData['quantity']) || $itemData['quantity'] < 1) {
                    $errors[] = 'Invalid quantity for item ' . $item->product->name;
                }
                
                if (!isset($itemData['price']) || !is_numeric($itemData['price']) || $itemData['price'] < 0) {
                    $errors[] = 'Invalid price for item ' . $item->product->name;
                }
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Handle order status change events
     */
    private function handleOrderStatusChange(Order $order, string $oldStatus, string $newStatus): void
    {
        // Log the status change
        $this->container->get('logger')->info("Order #{$order->id} status changed from {$oldStatus} to {$newStatus}");
        
        // TODO: Add notifications, emails, or other actions based on status changes
        // For example:
        // - Send email to customer when order is shipped
        // - Update inventory when order is delivered
        // - Send cancellation email when order is cancelled
        
        // Example implementation (would need to be expanded with actual notification logic)
        /*
        if ($newStatus === 'shipped' && $oldStatus !== 'shipped') {
            $this->sendOrderShippedEmail($order);
        } elseif ($newStatus === 'delivered' && $oldStatus !== 'delivered') {
            $this->updateInventory($order);
            $this->sendOrderDeliveredEmail($order);
        } elseif ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
            $this->restoreInventory($order);
            $this->sendOrderCancelledEmail($order);
        }
        */
    }
}
