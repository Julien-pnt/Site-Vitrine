<?php
// Configuration
require_once __DIR__ . '../../../../config/config.php';
require_once __DIR__ . '/AuthService.php';

// Vérifier que les constantes de base de données sont définies
if (!defined('DB_HOST') || !defined('DB_NAME') || !defined('DB_USER') || !defined('DB_PASSWORD')) {
    http_response_code(500);
    exit(json_encode(['error' => 'Configuration de la base de données manquante']));
}

// Empêcher la mise en cache de cette réponse API
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');
header('Content-Type: application/json; charset=UTF-8');

// Démarrer la session si nécessaire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Préparer la réponse par défaut
$response = [
    'logged_in' => false,
    'username' => null,
    'profile_image' => null,
    'last_activity' => null
];

try {
    // Initialiser la connexion à la base de données
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    // Utiliser le service d'authentification
    $authService = new AuthService($pdo);
    
    // Vérifier si l'utilisateur est authentifié
    if ($authService->isAuthenticated()) {
        $currentUser = $authService->getCurrentUser();
        
        if ($currentUser) {
            $response['logged_in'] = true;
            $response['username'] = htmlspecialchars($currentUser['nom']);
            $response['last_activity'] = $_SESSION['last_activity'] ?? null;
            
            // Si l'utilisateur a une image de profil personnalisée
            if (isset($_SESSION['user_profile_image'])) {
                // Sanitize l'URL de l'image
                $response['profile_image'] = filter_var(
                    $_SESSION['user_profile_image'], 
                    FILTER_SANITIZE_URL
                );
            } else {
                // Image par défaut
                $response['profile_image'] = '/assets/img/default-profile.png';
            }
        }
    }
    
} catch (PDOException $e) {
    // Log l'erreur côté serveur
    error_log('Erreur PDO dans check.php: ' . $e->getMessage());
} catch (Exception $e) {
    // Log l'erreur côté serveur
    error_log('Erreur dans check.php: ' . $e->getMessage());
}

// Envoyer la réponse en JSON
echo json_encode($response);
exit;
