<?php
class Template {
    private $templatePath;
    private $data = [];
    private $layout = 'main';
    private $sections = [];
    private $currentSection;

    public function __construct($templatePath = null) {
        $this->templatePath = $templatePath ?? dirname(__DIR__) . '/views/';
    }

    public function render($template, $data = []) {
        $this->data = array_merge($this->data, $data);
        $content = $this->renderTemplate($template);
        
        if ($this->layout) {
            $this->data['content'] = $content;
            return $this->renderTemplate("layouts/{$this->layout}");
        }
        
        return $content;
    }

    private function renderTemplate($template) {
        $file = $this->templatePath . $template . '.php';
        if (!file_exists($file)) {
            throw new Exception("Template file not found: {$file}");
        }

        extract($this->data);
        ob_start();
        include $file;
        return ob_get_clean();
    }

    public function setLayout($layout) {
        $this->layout = $layout;
        return $this;
    }

    public function section($name) {
        $this->currentSection = $name;
        ob_start();
    }

    public function endSection() {
        if (!$this->currentSection) {
            throw new Exception('No section started');
        }

        $this->sections[$this->currentSection] = ob_get_clean();
        $this->currentSection = null;
    }

    public function yield($section) {
        return $this->sections[$section] ?? '';
    }

    public function extend($layout) {
        $this->layout = $layout;
    }

    public function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    public function include($template, $data = []) {
        return $this->render($template, $data);
    }

    public function partial($template, $data = []) {
        return $this->render("partials/{$template}", $data);
    }

    public function exists($template) {
        return file_exists($this->templatePath . $template . '.php');
    }

    public function addGlobal($key, $value) {
        $this->data[$key] = $value;
        return $this;
    }

    public function getTemplatePath() {
        return $this->templatePath;
    }

    public function setTemplatePath($path) {
        $this->templatePath = rtrim($path, '/') . '/';
        return $this;
    }

    public function asset($path) {
        return '/assets/' . ltrim($path, '/');
    }

    public function url($path) {
        return '/' . ltrim($path, '/');
    }

    public function csrf() {
        return '<input type="hidden" name="csrf_token" value="' . 
               $this->escape($_SESSION['csrf_token'] ?? '') . '">';
    }

    public function old($key, $default = '') {
        return $_SESSION['old'][$key] ?? $default;
    }

    public function error($key) {
        return $_SESSION['errors'][$key] ?? null;
    }

    public function hasError($key) {
        return isset($_SESSION['errors'][$key]);
    }

    public function flash($key) {
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }
}