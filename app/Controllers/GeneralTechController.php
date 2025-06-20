<?php
namespace App\Controllers;

use App\Models\Service;
use App\Models\Consultation;

class GeneralTechController extends BaseController {
    private $service;
    private $consultation;

    public function __construct() {
        parent::__construct();
        $this->service = new Service();
        $this->consultation = new Consultation();
    }

    public function index() {
        $services = $this->service->getByCategory('general-tech');
        $this->view('general-tech/index', ['services' => $services]);
    }

    public function services() {
        $services = $this->service->getByCategory('general-tech');
        $this->view('general-tech/services', ['services' => $services]);
    }

    public function consulting() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateRequest([
                'name' => 'required',
                'email' => 'required|email',
                'description' => 'required'
            ]);

            if (empty($errors)) {
                $this->consultation->schedule($_POST);
                $this->view('general-tech/consulting', ['success' => true]);
            } else {
                $this->view('general-tech/consulting', ['errors' => $errors]);
            }
        } else {
            $this->view('general-tech/consulting');
        }
    }
}
