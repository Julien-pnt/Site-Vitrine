<?php
// Inclure les fichiers de configuration et services
require_once __DIR__ . '/../../../../config/config.php';
require_once __DIR__ . '/AuthService.php';

// Démarrer la session si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Préparer la réponse
$response = [
    'success' => true,
    'message' => 'Déconnexion réussie',
    'redirect' => '/public/pages/Accueil.html'
];

try {
    // Si l'utilisateur avait un token "Se souvenir de moi", le supprimer de la base de données
    if (isset($_COOKIE['remember_token'])) {
        $tokenParts = explode(':', $_COOKIE['remember_token']);
        if (count($tokenParts) === 2) {
            $userId = $tokenParts[0];
            
            // Initialiser la connexion à la base de données
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASSWORD,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            
            // Supprimer tous les tokens de l'utilisateur
            $stmt = $pdo->prepare("DELETE FROM auth_tokens WHERE user_id = ?");
            $stmt->execute([$userId]);
            
            // Journaliser la déconnexion pour des raisons de sécurité
            if (isset($_SESSION['user_id'])) {
                $stmt = $pdo->prepare("INSERT INTO connexions_log (user_id, ip_address, user_agent, action, date_connexion) 
                                      VALUES (?, ?, ?, 'logout', NOW())");
                $stmt->execute([
                    $_SESSION['user_id'],
                    $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                    $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
                ]);
            }
        }
    }
    
    // Utiliser le service d'authentification pour la déconnexion
    if (class_exists('AuthService')) {
        // Si nous avons chargé le service d'authentification, l'utiliser
        $pdo = $pdo ?? new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASSWORD,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        $authService = new AuthService($pdo);
        $authService->logout();
    } else {
        // Sinon, faire une déconnexion manuelle
        // Détruire toutes les données de session
        $_SESSION = [];
        session_destroy();
    }
    
    // Supprimer les cookies avec tous les paramètres de sécurité
    if (isset($_COOKIE[session_name()])) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    
    // Supprimer le cookie "remember_token"
    if (isset($_COOKIE['remember_token'])) {
        setcookie(
            'remember_token',
            '',
            time() - 42000,
            '/',
            '',
            true,  // Secure
            true   // HttpOnly
        );
    }
    
} catch (Exception $e) {
    // En cas d'erreur, enregistrer dans les logs mais ne pas exposer les détails
    error_log('Erreur lors de la déconnexion: ' . $e->getMessage());
    $response['success'] = false;
    $response['message'] = 'Une erreur est survenue lors de la déconnexion';
}

// Format de réponse selon le contexte
$acceptHeader = $_SERVER['HTTP_ACCEPT'] ?? '';
if (strpos($acceptHeader, 'application/json') !== false || isset($_GET['format']) && $_GET['format'] === 'json') {
    // Renvoyer une réponse JSON
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Rediriger vers la page d'accueil
    header('Location: ' . $response['redirect']);
}
exit;
