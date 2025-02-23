<?php
require_once '../db.php';
require_once 'AuthService.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['success' => false, 'message' => 'Méthode non autorisée']));
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['email']) || !isset($data['password'])) {
        throw new Exception('Email et mot de passe requis');
    }
    
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Format d\'email invalide');
    }
    
    $auth = new AuthService($pdo);
    $user = $auth->login($data['email'], $data['password']);
    
    echo json_encode([
        'success' => true,
        'user' => $user
    ]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}