<?php
namespace App\Controllers;

class RepairController extends BaseController {
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        return $this->view('repair/index', [
            'title' => 'Hardware and Software Repair Services',
            'services' => [
                [
                    'name' => 'Computer Repair',
                    'description' => 'Professional desktop and laptop repair services',
                    'price' => 'From $50'
                ],
                [
                    'name' => 'Data Recovery',
                    'description' => 'Recover lost data from damaged storage devices',
                    'price' => 'From $75'
                ],
                [
                    'name' => 'Software Troubleshooting',
                    'description' => 'Fix software issues and system errors',
                    'price' => 'From $40'
                ]
            ]
        ]);
    }
    
    public function request() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Process repair request
            $data = $_POST;
            // Validate and save to database
            return json_encode([
                'success' => true,
                'message' => 'Repair request received successfully',
                'requestId' => 'REP' . time()
            ]);
        }
        
        return $this->view('repair/request');
    }
    
    public function status($id = null) {
        if ($id) {
            // Fetch repair status from database
            return $this->view('repair/status', [
                'status' => 'In Progress',
                'id' => $id
            ]);
        }
        
        return $this->view('repair/check');
    }
}