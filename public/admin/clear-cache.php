<!-- filepath: c:\xampp\htdocs\Site-Vitrine\public\admin\api\clear-cache.php -->
<?php
session_start();
require_once '../../../php/config/database.php';
require_once '../../../php/utils/auth.php';

// Vérifier l'authentification
if (!isLoggedIn() || !isAdmin()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

try {
    // Vider les fichiers de cache
    $cacheDir = $_SERVER['DOCUMENT_ROOT'] . '/Site-Vitrine/public/cache/';
    if (is_dir($cacheDir)) {
        $files = glob($cacheDir . '*.cache');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
    
    // Nettoyer les données de cache en base (si applicable)
    // $pdo->query("TRUNCATE TABLE cache");
    
    // Log de l'action
    $userId = $_SESSION['user_id'];
    $stmt = $pdo->prepare("INSERT INTO admin_logs (utilisateur_id, action, ip_address, date_action) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$userId, 'vidage_cache', $_SERVER['REMOTE_ADDR']]);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}