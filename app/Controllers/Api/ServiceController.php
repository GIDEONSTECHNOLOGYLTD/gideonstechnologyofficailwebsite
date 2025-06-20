<?php

namespace App\Controllers\Api;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ServiceController extends ApiBaseController
{
    public function getServices(Request $request, Response $response): Response
    {
        try {
            // This would typically come from a database
            $services = [
                [
                    'id' => 1,
                    'name' => 'Web Development',
                    'description' => 'Custom web development services',
                    'price' => 'From $1000',
                    'image' => '/images/services/web-dev.jpg'
                ],
                [
                    'id' => 2,
                    'name' => 'Mobile App Development',
                    'description' => 'iOS and Android app development',
                    'price' => 'From $2000',
                    'image' => '/images/services/mobile-dev.jpg'
                ],
                [
                    'id' => 3,
                    'name' => 'Hardware Repair',
                    'description' => 'Computer and device repair services',
                    'price' => 'From $50',
                    'image' => '/images/services/hardware-repair.jpg'
                ]
            ];
            
            return $this->success($response, $services, 'Services retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($response, 'Failed to retrieve services: ' . $e->getMessage(), 500);
        }
    }
    
    public function getService(Request $request, Response $response, array $args): Response
    {
        try {
            $id = $args['id'] ?? null;
            if (!$id) {
                return $this->error($response, 'Service ID is required', 400);
            }
            
            // This would typically come from a database
            $services = [
                1 => [
                    'id' => 1,
                    'name' => 'Web Development',
                    'description' => 'Custom web development services',
                    'details' => 'We create responsive, modern websites using the latest technologies.',
                    'price' => 'From $1000',
                    'image' => '/images/services/web-dev.jpg'
                ],
                2 => [
                    'id' => 2,
                    'name' => 'Mobile App Development',
                    'description' => 'iOS and Android app development',
                    'details' => 'Native and cross-platform mobile applications built with React Native or Flutter.',
                    'price' => 'From $2000',
                    'image' => '/images/services/mobile-dev.jpg'
                ],
                3 => [
                    'id' => 3,
                    'name' => 'Hardware Repair',
                    'description' => 'Computer and device repair services',
                    'details' => 'Professional repair for laptops, desktops, phones, and tablets.',
                    'price' => 'From $50',
                    'image' => '/images/services/hardware-repair.jpg'
                ]
            ];
            
            if (!isset($services[$id])) {
                return $this->error($response, 'Service not found', 404);
            }
            
            return $this->success($response, $services[$id], 'Service retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($response, 'Failed to retrieve service: ' . $e->getMessage(), 500);
        }
    }
}