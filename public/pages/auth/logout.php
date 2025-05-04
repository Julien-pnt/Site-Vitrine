<?php
// Démarrer la session
session_start();

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
$returnUrl = isset($_GET['redirect']) ? $_GET['redirect'] : 'pages/Acceuil.php';

// Vérifier et nettoyer l'URL de redirection pour éviter les redirections malveillantes
if (!filter_var($returnUrl, FILTER_VALIDATE_URL) && strpos($returnUrl, '../') === false) {
    // Si l'URL n'est pas complète et ne contient pas de tentative de directory traversal
    header('Location: ' . $relativePath . $returnUrl);
} else {
    // URL potentiellement dangereuse, rediriger vers l'accueil par défaut
    header('Location: ' . $relativePath . 'pages/Acceuil.php');
}

// Arrêter l'exécution du script
exit;
?>