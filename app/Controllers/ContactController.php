<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

class ContactController extends BaseController
{
    public function __construct(PhpRenderer $renderer)
    {
        parent::__construct($renderer);
    }

    public function index(Request $request, Response $response): Response
    {
        return $this->render($response, 'contact/index.php', [
            'title' => 'Contact Us - Gideon\'s Technology',
            'page' => 'contact'
        ]);
    }

    public function send(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            
            // Validate required fields
            $requiredFields = ['name', 'email', 'subject', 'message'];
            $errors = [];
            
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    $errors[$field] = ucfirst($field) . ' is required';
                }
            }
            
            // Validate email format
            if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Please enter a valid email address';
            }
            
            // If there are validation errors, return to the form with errors
            if (!empty($errors)) {
                return $this->render($response, 'contact/index.php', [
                    'title' => 'Contact Us - Gideon\'s Technology',
                    'page' => 'contact',
                    'errors' => $errors,
                    'data' => $data
                ]);
            }
            
            // Process the contact form submission
            // In a real application, you would send an email, save to database, etc.
            // For demo purposes, we'll just simulate a successful submission
            
            // Redirect with success message
            $_SESSION['flash_message'] = 'Thank you for your message. We will get back to you soon!';
            $_SESSION['flash_type'] = 'success';
            
            return $response->withHeader('Location', '/contact')
                           ->withStatus(302);
            
        } catch (\Exception $e) {
            // Log the error
            error_log('Contact form error: ' . $e->getMessage());
            
            // Return to form with error message
            return $this->render($response, 'contact/index.php', [
                'title' => 'Contact Us - Gideon\'s Technology',
                'page' => 'contact',
                'error_message' => 'There was a problem sending your message. Please try again later.',
                'data' => $data ?? []
            ]);
        }
    }
}