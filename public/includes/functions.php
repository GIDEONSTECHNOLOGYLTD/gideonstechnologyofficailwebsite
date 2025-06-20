<?php

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function hasUserPurchased($templateId) {
    global $pdo;
    if (!isset($_SESSION['user_id'])) return false;
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM purchases 
            WHERE user_id = ? AND product_id = ? AND product_type = 'template' AND status = 'completed'");
        $stmt->execute([$_SESSION['user_id'], $templateId]);
        return (bool)$stmt->fetchColumn();
    } catch(PDOException $e) {
        return false;
    }
}
