<?php
require_once BASE_PATH . '/app/bootstrap.php';
use App\Repositories\TemplateRepository;

// Helper to fetch user purchased templates via repository
function getPurchasedTemplates($userId) {
    try {
        $repo = new TemplateRepository();
        return $repo->getPurchased((int)$userId);
    } catch (Exception $e) {
        error_log("Error fetching purchased templates: " . $e->getMessage());
        return [];
    }
}
