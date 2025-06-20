<?php
namespace App\Controllers;

use App\Models\Template;
use App\Models\Project;

class WebDevelopmentController extends BaseController {
    private Template $template;
    private Project $project;

    public function __construct(Template $template, Project $project) {
        parent::__construct();
        $this->template = $template;
        $this->project = $project;
    }

    public function index() {
        $templates = $this->template->getFeatured();
        $projects = $this->project->getRecent();
        $this->view('webdev/index', [
            'templates' => $templates,
            'projects' => $projects
        ]);
    }

    public function projects() {
        $projects = $this->project->getAll();
        $this->view('webdev/projects', ['projects' => $projects]);
    }

    public function services() {
        $this->view('webdev/services');
    }

    public function templates() {
        $templates = $this->template->getAll();
        $this->view('webdev/templates', ['templates' => $templates]);
    }

    public function template($id) {
        $template = $this->template->getById($id);
        $this->view('webdev/template', ['template' => $template]);
    }

    public function purchaseTemplate($id) {
        $this->requireLogin();
        try {
            $purchase = $this->template->purchase($id, $this->user['id']);
            $this->redirect("/web-dev/templates/purchased");
        } catch (\Exception $e) {
            $this->view('webdev/template', [
                'template' => $this->template->getById($id),
                'error' => $e->getMessage()
            ]);
        }
    }

    public function purchasedTemplates() {
        $this->requireLogin();
        $templates = $this->template->getPurchased($this->user['id']);
        $this->view('webdev/purchased', ['templates' => $templates]);
    }
}
