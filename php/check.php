<?php
require_once 'db.php';
require_once 'AuthService.php';
session_start();

header('Content-Type: application/json');

try {
    $auth = new AuthService($pdo);
    $user = $auth->getCurrentUser();

    echo json_encode([
        'success' => true,
        'authenticated' => $auth->isAuthenticated(),
        'user' => $user
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la vérification'
    ]);
}