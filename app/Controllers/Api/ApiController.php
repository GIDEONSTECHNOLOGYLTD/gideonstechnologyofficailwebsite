<?php

namespace App\Controllers\Api;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ApiController extends ApiBaseController
{
    public function index(Request $request, Response $response): Response
    {
        return $this->success($response, [
            'api' => 'Gideon\'s Technology API',
            'version' => '1.0.0',
            'endpoints' => [
                '/api/auth/login' => 'User authentication',
                '/api/auth/register' => 'User registration',
                '/api/services' => 'List all services',
                '/api/services/{id}' => 'Get service details',
                '/api/products' => 'List all products',
                '/api/products/{id}' => 'Get product details',
                '/api/orders' => 'List user orders',
                '/api/orders/{id}' => 'Get order details'
            ]
        ], 'Welcome to Gideon\'s Technology API');
    }
    
    public function healthCheck(Request $request, Response $response): Response
    {
        return $this->success($response, [
            'status' => 'operational',
            'timestamp' => time(),
            'server' => [
                'php_version' => phpversion(),
                'memory_usage' => memory_get_usage(true)
            ]
        ], 'API is running normally');
    }
    
    /**
     * Validate request data against rules
     */
    protected function validateRequest(Request $request, array $rules): array
    {
        $errors = [];
        $data = $request->getParsedBody() ?? [];

        foreach ($rules as $field => $rule) {
            if (!isset($data[$field])) {
                $errors[$field] = "{$field} is required";
                continue;
            }

            $value = $data[$field];

            if (strpos($rule, 'required') !== false && empty($value)) {
                $errors[$field] = "{$field} is required";
            }

            if (strpos($rule, 'email') !== false && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = "Invalid email format";
            }

            if (preg_match('/min:(\d+)/', $rule, $matches)) {
                $min = $matches[1];
                if (strlen($value) < $min) {
                    $errors[$field] = "{$field} must be at least {$min} characters";
                }
            }

            if (preg_match('/max:(\d+)/', $rule, $matches)) {
                $max = $matches[1];
                if (strlen($value) > $max) {
                    $errors[$field] = "{$field} must not exceed {$max} characters";
                }
            }
        }

        return $errors;
    }

    /**
     * Get bearer token from Authorization header
     */
    protected function getBearerToken(Request $request): ?string
    {
        $header = $request->getHeaderLine('Authorization');
        if (preg_match('/Bearer\s+(.+)/', $header, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
