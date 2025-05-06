<?php
// Initialisation de la session et vérification d'authentification admin
session_start();
require_once '../../../php/config/database.php';
require_once '../../../php/utils/auth.php';

// Vérifier si l'utilisateur est authentifié et admin
if (!isLoggedIn() || !isAdmin()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit;
}

// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Récupérer et décoder les données JSON
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

// Vérifier si l'ID du produit est présent
if (!isset($data['id']) || !is_numeric($data['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'ID de produit invalide']);
    exit;
}

$productId = (int)$data['id'];

// Connexion à la base de données
try {
    $db = new Database();
    $pdo = $db->getConnection();
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données']);
    exit;
}

try {
    // Commencer une transaction
    $pdo->beginTransaction();
    
    // Supprimer d'abord les associations pages/produit
    $stmt = $pdo->prepare("DELETE FROM produit_pages WHERE produit_id = ?");
    $stmt->execute([$productId]);
    
    // Supprimer les attributs du produit
    $stmt = $pdo->prepare("DELETE FROM produit_attributs WHERE produit_id = ?");
    $stmt->execute([$productId]);
    
    // Récupérer les infos du produit pour le log et les images
    $stmt = $pdo->prepare("SELECT nom, image, images_supplementaires FROM produits WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        // Le produit n'existe pas
        $pdo->rollBack();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Produit non trouvé']);
        exit;
    }
    
    // Supprimer le produit lui-même
    $stmt = $pdo->prepare("DELETE FROM produits WHERE id = ?");
    $result = $stmt->execute([$productId]);
    
    if ($result) {
        // Journaliser l'action
        $logStmt = $pdo->prepare("INSERT INTO admin_logs (utilisateur_id, action, ip_address, details) VALUES (?, ?, ?, ?)");
        $logStmt->execute([
            $_SESSION['user_id'],
            'delete_product',
            $_SERVER['REMOTE_ADDR'],
            "Suppression du produit: " . $product['nom'] . " (ID: " . $productId . ")"
        ]);
        
        // Supprimer les fichiers d'images si nécessaire
        if (!empty($product['image'])) {
            $imagePath = __DIR__ . '/../../uploads/products/' . $product['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        // Supprimer les images supplémentaires
        if (!empty($product['images_supplementaires'])) {
            $additionalImages = json_decode($product['images_supplementaires'], true);
            if (is_array($additionalImages)) {
                foreach ($additionalImages as $image) {
                    $imagePath = __DIR__ . '/../../uploads/products/' . $image;
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
            }
        }
        
        $pdo->commit();
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Produit supprimé avec succès']);
    } else {
        $pdo->rollBack();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression du produit']);
    }
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}
?>