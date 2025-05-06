<?php
// filepath: c:\xampp\htdocs\Site-Vitrine\public\admin\api\check-alerts.php
require_once '../../../php/config/database.php';
require_once '../../../php/utils/auth.php';

// Vérification de l'authentification
if (!isLoggedIn() || !isAdmin()) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Accès non autorisé']);
    exit;
}

// Variables pour suivre les logs déjà notifiés
session_start();
if (!isset($_SESSION['notified_log_ids'])) {
    $_SESSION['notified_log_ids'] = [];
}

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Récupérer les logs critiques récents non notifiés
    $query = "
        SELECT id, level, details, entity_type, entity_id, created_at
        FROM system_logs
        WHERE 
            level IN ('emergency', 'alert', 'critical', 'error') 
            AND created_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
            AND id NOT IN (" . implode(',', array_map(function($id) { return (int)$id; }, $_SESSION['notified_log_ids'])) . ")
        ORDER BY created_at DESC
        LIMIT 5
    ";
    
    $stmt = $pdo->query($query);
    $alerts = [];
    
    while ($log = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Ajouter à la liste des logs notifiés
        $_SESSION['notified_log_ids'][] = $log['id'];
        
        // Limiter la liste à 100 entrées pour éviter qu'elle ne grandisse indéfiniment
        if (count($_SESSION['notified_log_ids']) > 100) {
            $_SESSION['notified_log_ids'] = array_slice($_SESSION['notified_log_ids'], -100);
        }
        
        // Préparation du message de notification
        $message = "Nouvel événement " . strtoupper($log['level']);
        $details = $log['details'];
        
        if ($log['entity_type']) {
            $details = "{$log['entity_type']} #{$log['entity_id']} - " . $details;
        }
        
        $alerts[] = [
            'id' => $log['id'],
            'level' => $log['level'],
            'message' => $message,
            'details' => $details,
            'time' => date('H:i:s', strtotime($log['created_at']))
        ];
    }
    
    // Retourner les alertes
    header('Content-Type: application/json');
    echo json_encode(['alerts' => $alerts]);
    
} catch (PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Erreur de base de données: ' . $e->getMessage()]);
}