<?php
session_start();
require_once '../../config/database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Vous devez être connecté pour laisser un avis']);
    exit;
}

// Vérifier les données requises
if (!isset($_POST['produit_id']) || !isset($_POST['note']) || !isset($_POST['commentaire'])) {
    echo json_encode(['success' => false, 'error' => 'Données manquantes']);
    exit;
}

$produitId = intval($_POST['produit_id']);
$utilisateurId = $_SESSION['user_id'];
$note = intval($_POST['note']);
$commentaire = trim($_POST['commentaire']);

// Validation
if ($note < 1 || $note > 5) {
    echo json_encode(['success' => false, 'error' => 'La note doit être comprise entre 1 et 5']);
    exit;
}

if (empty($commentaire)) {
    echo json_encode(['success' => false, 'error' => 'Le commentaire ne peut pas être vide']);
    exit;
}

// Vérifier si l'utilisateur a déjà laissé un avis pour ce produit
try {
    $checkStmt = $pdo->prepare("SELECT id FROM avis WHERE utilisateur_id = ? AND produit_id = ?");
    $checkStmt->execute([$utilisateurId, $produitId]);
    
    if ($checkStmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'error' => 'Vous avez déjà laissé un avis pour ce produit']);
        exit;
    }
    
    // Insérer l'avis
    $stmt = $pdo->prepare("
        INSERT INTO avis (utilisateur_id, produit_id, note, commentaire, statut, date_creation)
        VALUES (?, ?, ?, ?, 'en_attente', NOW())
    ");
    
    $stmt->execute([$utilisateurId, $produitId, $note, $commentaire]);
    
    echo json_encode(['success' => true, 'message' => 'Votre avis a été enregistré et sera publié après modération']);
    
} catch (PDOException $e) {
    error_log('Erreur lors de l\'ajout d\'un avis: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Une erreur est survenue lors de l\'enregistrement de votre avis']);
}
?>