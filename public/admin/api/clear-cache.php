<?php
session_start();
require_once '../../../php/config/database.php';
require_once '../../../php/utils/auth.php';

// Vérifier que l'utilisateur est admin
if (!isLoggedIn() || !isAdmin()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Accès refusé']);
    exit;
}

// Fonction pour vider les dossiers de cache
function clearCacheFolder($folderPath) {
    if (!is_dir($folderPath)) {
        return false;
    }
    
    $files = glob($folderPath . '/*');
    foreach ($files as $file) {
        if (is_file($file) && basename($file) != '.htaccess' && basename($file) != 'index.html') {
            unlink($file);
        }
    }
    
    return true;
}

// Définir les chemins de cache
$cacheFolders = [
    '../../../cache/images',
    '../../../cache/templates',
    '../../../cache/data'
];

$success = true;
$clearedFolders = [];

// Nettoyer chaque dossier de cache
foreach ($cacheFolders as $folder) {
    if (file_exists($folder) && is_dir($folder)) {
        if (clearCacheFolder($folder)) {
            $clearedFolders[] = basename($folder);
        } else {
            $success = false;
        }
    }
}

// Journaliser l'action
try {
    $stmt = $pdo->prepare("INSERT INTO admin_logs (utilisateur_id, action, details, ip_address, date_action) 
                           VALUES (?, 'vider_cache', ?, ?, NOW())");
    $stmt->execute([
        $_SESSION['user_id'], 
        'Dossiers: ' . implode(', ', $clearedFolders),
        $_SERVER['REMOTE_ADDR']
    ]);
} catch (PDOException $e) {
    // Ignorer l'erreur de journalisation
}

// Réponse JSON
header('Content-Type: application/json');
echo json_encode([
    'success' => $success, 
    'clearedFolders' => $clearedFolders,
    'timestamp' => date('Y-m-d H:i:s')
]);