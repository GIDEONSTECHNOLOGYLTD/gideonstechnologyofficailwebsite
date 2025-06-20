<?php
namespace App\Controllers;

use App\Services\AnalyticsService;

class AnalyticsController extends BaseController {
    private $analytics;

    public function __construct() {
        parent::__construct();
        $this->requireLogin();
        $this->analytics = new AnalyticsService();
    }

    public function index() {
        $data = $this->analytics->getDashboardMetrics();
        $this->view('dashboard/analytics', ['metrics' => $data]);
    }
}