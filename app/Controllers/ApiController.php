<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ApiController
{
    public function products(Request $request, Response $response): Response
    {
        try {
            $data = [
                'status' => 'success',
                'products' => [
                    ['id' => 1, 'name' => 'Product 1', 'price' => 19.99],
                    ['id' => 2, 'name' => 'Product 2', 'price' => 29.99],
                    ['id' => 3, 'name' => 'Product 3', 'price' => 39.99]
                ]
            ];
            
            $payload = json_encode($data);
            $response->getBody()->write($payload);
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withStatus(200);
        } catch (\Exception $e) {
            error_log('Error in products method: ' . $e->getMessage());
            
            $error = ['error' => 'An error occurred while fetching products'];
            $response->getBody()->write(json_encode($error));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }

    public function searchProducts(Request $request, Response $response): Response
    {
        try {
            // Get search query from request
            $params = $request->getQueryParams();
            $query = $params['q'] ?? '';
            
            $data = [
                'status' => 'success',
                'query' => $query,
                'results' => [
                    ['id' => 1, 'name' => 'Product ' . $query, 'price' => 19.99],
                    ['id' => 2, 'name' => 'Another ' . $query, 'price' => 29.99]
                ]
            ];
            
            $payload = json_encode($data);
            $response->getBody()->write($payload);
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withStatus(200);
        } catch (\Exception $e) {
            error_log('Error in searchProducts method: ' . $e->getMessage());
            
            $error = ['error' => 'An error occurred while searching products'];
            $response->getBody()->write(json_encode($error));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }

    public function createOrder(Request $request, Response $response): Response
    {
        try {
            // Get order data from request body
            $data = $request->getParsedBody();
            
            // Sample response with order creation confirmation
            $result = [
                'status' => 'success',
                'message' => 'Order created successfully',
                'order_id' => uniqid('order_'),
                'timestamp' => date('Y-m-d H:i:s'),
                'data' => $data
            ];
            
            $payload = json_encode($result);
            $response->getBody()->write($payload);
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withStatus(201);
        } catch (\Exception $e) {
            error_log('Error in createOrder method: ' . $e->getMessage());
            
            $error = ['error' => 'An error occurred while creating order'];
            $response->getBody()->write(json_encode($error));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }

    public function userProfile(Request $request, Response $response): Response
    {
        try {
            // Sample user profile data
            $profile = [
                'id' => 123,
                'username' => 'user123',
                'email' => 'user@example.com',
                'name' => 'John Doe',
                'joined' => '2023-01-15'
            ];
            
            $payload = json_encode(['profile' => $profile]);
            $response->getBody()->write($payload);
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withStatus(200);
        } catch (\Exception $e) {
            error_log('Error in userProfile method: ' . $e->getMessage());
            
            $error = ['error' => 'An error occurred while fetching user profile'];
            $response->getBody()->write(json_encode($error));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }

    /**
     * Return web development templates
     */
    public function getTemplates(Request $request, Response $response): Response
    {
        $templates = [
            ['id' => 1, 'name' => 'Basic Template', 'description' => 'A simple starter template'],
            ['id' => 2, 'name' => 'Portfolio Template', 'description' => 'Template for showcasing work'],
            ['id' => 3, 'name' => 'Blog Template', 'description' => 'Template for blog sites'],
            ['id' => 4, 'name' => 'E-commerce Template', 'description' => 'Template for online stores'],
            ['id' => 5, 'name' => 'Landing Page', 'description' => 'Template for product landing pages']
        ];
        
        $payload = json_encode(['templates' => $templates]);
        $response->getBody()->write($payload);
        
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withStatus(200);
    }
    
    /**
     * Return orders list
     */
    public function getOrders(Request $request, Response $response): Response
    {
        $orders = [
            ['id' => 1, 'customer' => 'John Doe', 'amount' => 99.99, 'status' => 'completed'],
            ['id' => 2, 'customer' => 'Jane Smith', 'amount' => 149.99, 'status' => 'pending'],
            ['id' => 3, 'customer' => 'Bob Johnson', 'amount' => 75.50, 'status' => 'processing'],
            ['id' => 4, 'customer' => 'Alice Williams', 'amount' => 299.99, 'status' => 'shipped'],
            ['id' => 5, 'customer' => 'Chris Davis', 'amount' => 49.95, 'status' => 'completed']
        ];
        
        $payload = json_encode(['orders' => $orders]);
        $response->getBody()->write($payload);
        
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withStatus(200);
    }
}
