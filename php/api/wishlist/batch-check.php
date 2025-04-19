<?php
session_start();
header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit;
}

// Récupérer les IDs de produits
$product_ids_json = $_POST['product_ids'] ?? '';
$product_ids = json_decode($product_ids_json, true);

if (!is_array($product_ids) || empty($product_ids)) {
    echo json_encode(['success' => false, 'message' => 'Aucun produit spécifié']);
    exit;
}

// Récupérer l'ID de l'utilisateur
$userId = $_SESSION['user_id'];

// Connexion à la base de données
require_once '../../../php/config/database.php';
$db = new Database();
$conn = $db->getConnection();

// Préparer la liste de placeholders pour la requête SQL
$placeholders = implode(',', array_fill(0, count($product_ids), '?'));

// Préparer les paramètres pour la requête (ajouter l'ID utilisateur au début)
$params = [$userId];
foreach ($product_ids as $id) {
    $params[] = $id;
}

try {
    // Récupérer tous les produits favoris correspondant aux IDs fournis
    $stmt = $conn->prepare("
        SELECT produit_id 
        FROM favoris 
        WHERE utilisateur_id = ? 
        AND produit_id IN ($placeholders)
    ");
    
    $stmt->execute($params);
    $favorites = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    
    echo json_encode(['success' => true, 'favorites' => $favorites]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la vérification des favoris']);
}