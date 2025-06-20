<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Flash\Messages;

/**
 * Base Controller
 * 
 * Provides common functionality for all controllers
 */
class Controller
{
    /**
     * @var Twig
     */
    protected $view;

    /**
     * @var Messages
     */
    protected $flash;

    /**
     * Controller constructor.
     *
     * @param Twig $view
     * @param Messages $flash
     */
    public function __construct(Twig $view, Messages $flash)
    {
        $this->view = $view;
        $this->flash = $flash;
    }

    /**
     * Render a template
     *
     * @param Response $response
     * @param string $template
     * @param array $data
     * @return Response
     */
    protected function render(Response $response, string $template, array $data = []): Response
    {
        return $this->view->render($response, $template, $data);
    }

    /**
     * Get JSON request data
     *
     * @param Request $request
     * @return array
     */
    protected function getJsonInput(Request $request): array
    {
        $input = json_decode($request->getBody()->getContents(), true);
        return is_array($input) ? $input : [];
    }

    /**
     * Create a JSON response
     *
     * @param Response $response
     * @param mixed $data
     * @param int $status
     * @return Response
     */
    protected function jsonResponse(Response $response, $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}
