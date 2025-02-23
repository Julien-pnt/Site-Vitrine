<?php
require_once 'db.php';
require_once 'AuthService.php';
session_start();

header('Content-Type: application/json');

/**
 * Vérifie si la requête est une requête POST.
 */
function checkRequestMethod() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit(json_encode(['success' => false, 'message' => 'Méthode non autorisée']));
    }
}

/**
 * Valide les données d'entrée.
 *
 * @param array $data Les données à valider.
 * @throws Exception Si les données sont invalides.
 */
function validateInputData($data) {
    if (!isset($data['email']) || !isset($data['password'])) {
        throw new Exception('Email et mot de passe requis');
    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Format d\'email invalide');
    }
}

/**
 * Gère la connexion de l'utilisateur.
 *
 * @param PDO $pdo La connexion à la base de données.
 * @param array $data Les données de connexion.
 * @return array Les informations de l'utilisateur.
 * @throws Exception Si la connexion échoue.
 */
function handleLogin(PDO $pdo, $data) {
    $auth = new AuthService($pdo);
    return $auth->login($data['email'], $data['password']);
}

try {
    checkRequestMethod();

    $data = json_decode(file_get_contents('php://input'), true);
    validateInputData($data);

    $user = handleLogin($pdo, $data);

    echo json_encode([
        'success' => true,
        'user' => $user
    ]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Échec de la connexion. Veuillez vérifier vos informations d\'identification.'
    ]);

    // Optionnel : Journaliser les tentatives de connexion échouées
    // error_log('Échec de la connexion pour l\'adresse IP ' . $_SERVER['REMOTE_ADDR']);
}
