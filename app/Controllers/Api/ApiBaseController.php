<?php

namespace App\Controllers\Api;

use Psr\Http\Message\ResponseInterface as Response;

/**
 * Base Controller for API endpoints
 */
class ApiBaseController
{
    /**
     * Send a JSON response
     */
    protected function jsonResponse(Response $response, array $data, int $status = 200): Response
    {
        $response = $response->withHeader('Content-Type', 'application/json')
                            ->withStatus($status);
        $response->getBody()->write(json_encode($data));
        return $response;
    }

    /**
     * Send a success response
     */
    protected function success(Response $response, array $data = [], string $message = 'Success'): Response
    {
        return $this->jsonResponse($response, [
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * Send an error response
     */
    protected function error(Response $response, string $message = 'An error occurred', int $status = 400, array $errors = []): Response
    {
        return $this->jsonResponse($response, [
            'status' => 'error',
            'message' => $message,
            'errors' => $errors
        ], $status);
    }
}