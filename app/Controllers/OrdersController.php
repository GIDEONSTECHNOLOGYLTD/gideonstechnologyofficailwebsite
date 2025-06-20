<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

/**
 * @deprecated Use OrderController instead
 */
class OrdersController extends BaseController
{
    public function __construct(PhpRenderer $renderer)
    {
        parent::__construct($renderer);
    }

    public function index(Request $request, Response $response): Response
    {
        // Redirect to the singular OrderController
        return $response->withHeader('Location', '/order')
                       ->withStatus(302);
    }
}