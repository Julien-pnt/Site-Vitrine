<?php
session_start();

// Préparer la réponse
$response = [
    'logged_in' => false,
    'username' => null,
    'profile_image' => null
];

// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['user_id']) && isset($_SESSION['user_nom'])) {
    $response['logged_in'] = true;
    $response['username'] = $_SESSION['user_nom'];
    
    // Si l'utilisateur a une image de profil personnalisée
    if (isset($_SESSION['user_profile_image'])) {
        $response['profile_image'] = $_SESSION['user_profile_image'];
    }
}

// Envoyer la réponse en JSON
header('Content-Type: application/json');
echo json_encode($response);
exit;
