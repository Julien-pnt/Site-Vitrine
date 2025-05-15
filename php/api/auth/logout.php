<?php
// Démarrer la session
session_start();
require_once '../../../../php/config/database.php';

// Supprimer le jeton d'authentification si "Se souvenir de moi" était activé
if (isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        // Supprimer le jeton de la base de données
        $stmt = $conn->prepare("DELETE FROM auth_tokens WHERE token = ?");
        $stmt->execute([$token]);
        
        // Supprimer le cookie
        setcookie('remember_token', '', time() - 3600, '/', '', true, true);
    } catch (PDOException $e) {
        error_log('Erreur lors de la suppression du token: ' . $e->getMessage());
    }
}

// Détruire toutes les variables de session
$_SESSION = array();

// Si un cookie de session existe, le détruire
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// Détruire la session
session_destroy();

// Redirection vers la page d'accueil
$relativePath = "../../";
$returnUrl = isset($_GET['redirect']) ? $_GET['redirect'] : 'pages/Accueil.php';

// Vérifier et nettoyer l'URL de redirection pour éviter les redirections malveillantes
if (!filter_var($returnUrl, FILTER_VALIDATE_URL) && strpos($returnUrl, '../') === false) {
    // Si l'URL n'est pas complète et ne contient pas de tentative de directory traversal
    header('Location: ' . $relativePath . $returnUrl);
} else {
    // URL potentiellement dangereuse, rediriger vers l'accueil par défaut
    header('Location: ' . $relativePath . 'pages/Accueil.php');
}

// Arrêter l'exécution du script
exit;
?>