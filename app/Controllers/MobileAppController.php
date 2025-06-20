<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Device;
use App\Services\NotificationService;

class MobileAppController extends BaseController {
    private $user;
    private $product;
    private $order;
    private $device;
    private $notification;

    public function __construct(
        User $user,
        Product $product,
        Order $order,
        Device $device,
        NotificationService $notification
    ) {
        parent::__construct();
        $this->user = $user;
        $this->product = $product;
        $this->order = $order;
        $this->device = $device;
        $this->notification = $notification;
    }

    /**
     * Get and validate JSON input
     * @param array $required Required fields
     * @return array Validated data
     * @throws \InvalidArgumentException if validation fails
     */
    private function getJsonInput(array $required = []): array {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON payload');
        }

        if (!is_array($data)) {
            throw new \InvalidArgumentException('JSON payload must be an object');
        }

        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        return $data;
    }

    public function login() {
        $data = $this->getJsonInput(['email', 'password']);
        try {
            $user = $this->user->login($data['email'], $data['password']);
            if ($user) {
                return $this->jsonResponse([
                    'status' => 'success',
                    'data' => [
                        'user' => $user,
                        'token' => $this->user->generateApiToken()
                    ]
                ]);
            }
            return $this->jsonResponse(['status' => 'error', 'message' => 'Invalid credentials'], 401);
        } catch (\Exception $e) {
            return $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function register() {
        $data = $this->getJsonInput(['email', 'password', 'name', 'phone']);
        try {
            $userId = $this->user->register($data);
            return $this->jsonResponse([
                'status' => 'success',
                'data' => ['user_id' => $userId]
            ], 201);
        } catch (\Exception $e) {
            return $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function searchProducts() {
        $data = $this->getJsonInput(['query']);
        try {
            $products = $this->product->search($data['query']);
            return $this->jsonResponse([
                'status' => 'success',
                'data' => ['products' => $products]
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateCart() {
        $data = $this->getJsonInput(['user_id', 'items']);
        try {
            $cart = $this->order->updateCart($data['user_id'], $data['items']);
            return $this->jsonResponse([
                'status' => 'success',
                'data' => ['cart' => $cart]
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function createOrder() {
        $data = $this->getJsonInput(['user_id', 'items', 'shipping_address', 'payment_method']);
        try {
            $order = $this->order->create($data);
            return $this->jsonResponse([
                'status' => 'success',
                'data' => ['order' => $order]
            ], 201);
        } catch (\Exception $e) {
            return $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function registerDevice() {
        $data = $this->getJsonInput(['user_id', 'device_token', 'platform']);
        try {
            $this->device->register($data['user_id'], $data['device_token'], $data['platform']);
            return $this->jsonResponse(['status' => 'success']);
        } catch (\Exception $e) {
            return $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function sendNotification() {
        $data = $this->getJsonInput(['user_id', 'message']);
        try {
            $this->notification->send($data['user_id'], $data['message'], $data['data'] ?? null);
            return $this->jsonResponse(['status' => 'success']);
        } catch (\Exception $e) {
            return $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    private function jsonResponse($data, $status = 200) {
        return response()
            ->json($data)
            ->header('Content-Type', 'application/json')
            ->setStatusCode($status);
    }
}
