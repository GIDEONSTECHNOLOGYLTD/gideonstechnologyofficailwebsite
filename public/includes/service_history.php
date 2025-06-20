<?php
function getServiceHistory($userId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT s.*, p.name as project_name, p.status as project_status
            FROM services s
            LEFT JOIN projects p ON s.project_id = p.id
            WHERE s.user_id = ?
            ORDER BY s.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching service history: " . $e->getMessage());
        return [];
    }
}

function getServiceStatusBadge($status) {
    $badges = [
        'pending' => 'warning',
        'in_progress' => 'primary',
        'completed' => 'success',
        'cancelled' => 'danger'
    ];
    
    $status = strtolower($status);
    $badgeClass = $badges[$status] ?? 'secondary';
    
    return sprintf(
        '<span class="badge bg-%s">%s</span>',
        $badgeClass,
        ucfirst(str_replace('_', ' ', $status))
    );
}

function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}

function getServiceIcon($serviceType) {
    $icons = [
        'web-dev' => 'fas fa-laptop-code',
        'fintech' => 'fas fa-money-bill-wave',
        'general-tech' => 'fas fa-cogs',
        'repair' => 'fas fa-tools',
        'graphics' => 'fas fa-photo-video'
    ];
    
    return $icons[$serviceType] ?? 'fas fa-question';
}
?>
