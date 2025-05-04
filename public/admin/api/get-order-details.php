<?php
// Initialisation de la session et vérification d'authentification admin
session_start();
require_once '../../../php/config/database.php';
require_once '../../../php/utils/auth.php';
require_once '../../../php/models/Order.php';

// Vérification de l'authentification
if (!isLoggedIn() || !isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Accès non autorisé']);
    exit;
}

// Vérification de l'ID de la commande
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de commande invalide']);
    exit;
}

$orderId = (int)$_GET['id'];

// Initialiser le modèle de commande
$db = new Database();
$pdo = $db->getConnection();
$orderModel = new Order($pdo);

// Récupérer les détails de la commande
$orderDetails = $orderModel->getOrderDetails($orderId);

if (!$orderDetails) {
    http_response_code(404);
    echo json_encode(['error' => 'Commande non trouvée']);
    exit;
}

// Envoyer les détails de la commande au format JSON
header('Content-Type: application/json');
echo json_encode($orderDetails);