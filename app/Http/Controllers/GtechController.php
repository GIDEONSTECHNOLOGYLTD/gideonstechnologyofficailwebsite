<?php

namespace App\Http\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Gtech Controller
 * 
 * Handles all Gtech services related pages
 */
class GtechController extends Controller
{
    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }
    
    /**
     * Home page
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function home(Request $request, Response $response, array $args = []): Response
    {
        try {
            return $this->render($response, 'gtech/index.php', [
                'title' => 'Gtech Services',
                'description' => 'Welcome to Gideon\'s Technology services platform'
            ]);
        } catch (\Exception $e) {
            return $this->serverError($response, 'An error occurred while loading the Gtech homepage: ' . $e->getMessage());
        }
    }
    
    /**
     * About page
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function about(Request $request, Response $response, array $args = []): Response
    {
        try {
            return $this->render($response, 'gtech/about.php', [
                'title' => 'About Gtech',
                'description' => 'Learn more about Gideon\'s Technology services'
            ]);
        } catch (\Exception $e) {
            return $this->serverError($response, 'An error occurred while loading the about page: ' . $e->getMessage());
        }
    }
    
    /**
     * Contact page
     *
     * @param Request $request
     * @param Response $response,
     * @param array $args
     * @return Response
     */
    public function contact(Request $request, Response $response, array $args = []): Response
    {
        try {
            return $this->render($response, 'gtech/contact.php', [
                'title' => 'Contact Us',
                'description' => 'Get in touch with Gideon\'s Technology'
            ]);
        } catch (\Exception $e) {
            return $this->serverError($response, 'An error occurred while loading the contact page: ' . $e->getMessage());
        }
    }
    
    /**
     * Services page
     *
     * @param Request $request
     * @param Response $response,
     * @param array $args
     * @return Response
     */
    public function services(Request $request, Response $response, array $args = []): Response
    {
        try {
            return $this->render($response, 'gtech/services.php', [
                'title' => 'Our Services',
                'description' => 'Explore the services offered by Gideon\'s Technology'
            ]);
        } catch (\Exception $e) {
            return $this->serverError($response, 'An error occurred while loading the services page: ' . $e->getMessage());
        }
    }
    
    /**
     * Repair Service page
     *
     * @param Request $request
     * @param Response $response,
     * @param array $args
     * @return Response
     */
    public function repairService(Request $request, Response $response, array $args = []): Response
    {
        try {
            return $this->render($response, 'gtech/services/repair.php', [
                'title' => 'Repair Services',
                'description' => 'Hardware and software repair services from Gideon\'s Technology'
            ]);
        } catch (\Exception $e) {
            return $this->serverError($response, 'An error occurred while loading the repair service page: ' . $e->getMessage());
        }
    }
    
    /**
     * Web Development Service page
     *
     * @param Request $request
     * @param Response $response,
     * @param array $args
     * @return Response
     */
    public function webDevService(Request $request, Response $response, array $args = []): Response
    {
        try {
            return $this->render($response, 'gtech/web-development.php', [
                'title' => 'Web Development Services',
                'description' => 'Custom web application development by Gideon\'s Technology'
            ]);
        } catch (\Exception $e) {
            return $this->serverError($response, 'An error occurred while loading the web development page: ' . $e->getMessage());
        }
    }
    
    /**
     * Consulting Service page
     *
     * @param Request $request
     * @param Response $response,
     * @param array $args
     * @return Response
     */
    public function consultingService(Request $request, Response $response, array $args = []): Response
    {
        try {
            return $this->render($response, 'gtech/consulting.php', [
                'title' => 'IT Consulting Services',
                'description' => 'Expert IT consulting services from Gideon\'s Technology'
            ]);
        } catch (\Exception $e) {
            return $this->serverError($response, 'An error occurred while loading the consulting page: ' . $e->getMessage());
        }
    }
}