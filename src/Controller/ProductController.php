<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProductController
{
    public function getAllProducts(Request $request, Response $response): Response
    {
        $products = [
            ['id' => 1, 'name' => 'Product A', 'price' => 99.99],
            ['id' => 2, 'name' => 'Product B', 'price' => 149.99],
        ];
        
        $response->getBody()->write(json_encode($products));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getProductById(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $product = ['id' => $id, 'name' => 'Product ' . $id, 'price' => $id * 50 + 49.99];
        
        $response->getBody()->write(json_encode($product));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function createProduct(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $product = [
            'id' => 3, 
            'name' => $data['name'] ?? 'New Product', 
            'price' => $data['price'] ?? 199.99
        ];
        
        $response->getBody()->write(json_encode($product));
        return $response->withHeader('Content-Type', 'application/json')
                       ->withStatus(201);
    }

    public function updateProduct(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $data = $request->getParsedBody();
        $product = [
            'id' => $id, 
            'name' => $data['name'] ?? 'Updated Product', 
            'price' => $data['price'] ?? 249.99
        ];
        
        $response->getBody()->write(json_encode($product));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function deleteProduct(Request $request, Response $response, array $args): Response
    {
        return $response->withStatus(204);
    }
}