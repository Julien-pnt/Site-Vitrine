<?php
require_once 'db.php';
require_once 'AuthService.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Vérification de la présence des champs requis
    if (empty($data['nom']) || empty($data['email']) || empty($data['password'])) {
        throw new Exception('Tous les champs sont requis');
    }
    
    $nom = trim($data['nom']);
    $email = trim($data['email']);
    $password = $data['password'];
    
    // Validation du nom
    if (strlen($nom) < 2 || strlen($nom) > 255) {
        throw new Exception('Le nom doit contenir entre 2 et 255 caractères');
    }
    
    // Validation de l'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Format d\'email invalide');
    }
    
    // Vérification si l'email existe déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        throw new Exception('Cet email est déjà utilisé');
    }
    
    // Validation du mot de passe
    if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/\d/', $password)) {
        throw new Exception('Le mot de passe doit contenir au moins 8 caractères, une majuscule et un chiffre');
    }
    
    $auth = new AuthService($pdo);
    $userId = $auth->register($nom, $email, $password);
    
    echo json_encode([
        'success' => true,
        'user_id' => $userId,
        'message' => 'Compte créé avec succès'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
