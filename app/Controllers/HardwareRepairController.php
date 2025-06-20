<?php
namespace App\Controllers;

use App\Models\Service;
use App\Models\RepairRequest;

class HardwareRepairController extends BaseController {
    private $service;
    private $repairRequest;

    public function __construct() {
        parent::__construct();
        $this->service = new Service();
        $this->repairRequest = new RepairRequest();
    }

    public function index() {
        $services = $this->service->getByCategory('hardware-repair');
        $this->view('repair/index', ['services' => $services]);
    }

    public function services() {
        $services = $this->service->getByCategory('hardware-repair');
        $this->view('repair/services', ['services' => $services]);
    }

    public function contact() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateRequest([
                'name' => 'required',
                'email' => 'required|email',
                'device_type' => 'required',
                'issue' => 'required'
            ]);

            if (empty($errors)) {
                try {
                    $this->repairRequest->create($_POST);
                    $this->view('repair/contact', ['success' => true]);
                } catch (\Exception $e) {
                    $this->view('repair/contact', ['error' => $e->getMessage()]);
                }
            } else {
                $this->view('repair/contact', ['errors' => $errors]);
            }
        } else {
            $this->view('repair/contact');
        }
    }
}
