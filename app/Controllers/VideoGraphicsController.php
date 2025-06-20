<?php
namespace App\Controllers;

use App\Models\Portfolio;
use App\Models\Service;

class VideoGraphicsController extends BaseController {
    private $portfolio;
    private $service;

    public function __construct() {
        parent::__construct();
        $this->portfolio = new Portfolio();
        $this->service = new Service();
    }

    public function index() {
        $featuredWork = $this->portfolio->getFeatured();
        $this->view('video-graphics/index', ['featured' => $featuredWork]);
    }

    public function services() {
        $services = $this->service->getByCategory('video-graphics');
        $this->view('video-graphics/services', ['services' => $services]);
    }

    public function portfolio() {
        $works = $this->portfolio->getAll();
        $this->view('video-graphics/portfolio', ['works' => $works]);
    }
}
