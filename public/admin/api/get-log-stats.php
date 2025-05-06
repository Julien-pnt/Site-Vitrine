<?php
// filepath: c:\xampp\htdocs\Site-Vitrine\public\admin\api\get-log-stats.php
require_once '../../../php/config/database.php';
require_once '../../../php/utils/auth.php';

// Vérification de l'authentification
if (!isLoggedIn() || !isAdmin()) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Accès non autorisé']);
    exit;
}

// Initialiser la réponse
$response = [
    'levels' => [],
    'activity' => [],
    'categories' => [],
    'users' => []
];

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Distribution par niveau
    $stmt = $pdo->query("
        SELECT level, COUNT(*) as count 
        FROM system_logs 
        GROUP BY level 
        ORDER BY count DESC
    ");
    $response['levels'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Activité des 30 derniers jours
    $stmt = $pdo->query("
        SELECT 
            DATE(created_at) as date,
            COUNT(*) as count
        FROM system_logs
        WHERE created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 30 DAY)
        GROUP BY DATE(created_at)
        ORDER BY date
    ");
    $response['activity'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Top 5 des catégories
    $stmt = $pdo->query("
        SELECT category, COUNT(*) as count 
        FROM system_logs 
        GROUP BY category 
        ORDER BY count DESC 
        LIMIT 5
    ");
    $response['categories'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Top 5 des utilisateurs actifs
    $stmt = $pdo->query("
        SELECT 
            user_id,
            user_type,
            COUNT(*) as count
        FROM system_logs
        WHERE user_id IS NOT NULL
        GROUP BY user_id, user_type
        ORDER BY count DESC
        LIMIT 5
    ");
    $topUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les infos utilisateurs
    foreach ($topUsers as &$user) {
        if ($user['user_type'] === 'admin') {
            $stmt = $pdo->prepare("SELECT CONCAT(prenom, ' ', nom) as name FROM utilisateurs WHERE id = ?");
            $stmt->execute([$user['user_id']]);
            $userName = $stmt->fetchColumn();
            $user['name'] = $userName ?: 'Administrateur #' . $user['user_id'];
        } else {
            $stmt = $pdo->prepare("SELECT CONCAT(prenom, ' ', nom) as name FROM utilisateurs WHERE id = ?");
            $stmt->execute([$user['user_id']]);
            $userName = $stmt->fetchColumn();
            $user['name'] = $userName ?: 'Utilisateur #' . $user['user_id'];
        }
    }
    
    $response['users'] = $topUsers;
    
    // Retourner la réponse en JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    
} catch (PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Erreur de base de données: ' . $e->getMessage()]);
}