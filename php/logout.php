<?php
session_start();

// Détruire toutes les données de session
session_unset();
session_destroy();

// Supprimer les cookies de session et de "Se souvenir de moi"
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time()-42000, '/');
}

// Renvoyer une réponse JSON
header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'Déconnexion réussie']);
exit;
