<?php
session_start();
require 'db.php';

// Ajouter un produit au panier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_panier'])) {
    $produit_id = $_POST['produit_id'];
    $quantite = $_POST['quantite'];

    // Vérifier si l'utilisateur est connecté
    if (isset($_SESSION['utilisateur_id'])) {
        $utilisateur_id = $_SESSION['utilisateur_id'];

        // Vérifier si le produit est déjà dans le panier
        $stmt = $pdo->prepare("SELECT * FROM panier WHERE utilisateur_id = ? AND produit_id = ?");
        $stmt->execute([$utilisateur_id, $produit_id]);
        $produit = $stmt->fetch();

        if ($produit) {
            // Mettre à jour la quantité
            $stmt = $pdo->prepare("UPDATE panier SET quantite = quantite + ? WHERE id = ?");
            $stmt->execute([$quantite, $produit['id']]);
        } else {
            // Ajouter le produit au panier
            $stmt = $pdo->prepare("INSERT INTO panier (utilisateur_id, produit_id, quantite) VALUES (?, ?, ?)");
            $stmt->execute([$utilisateur_id, $produit_id, $quantite]);
        }

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    }
}

// Récupérer le panier de l'utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['recuperer_panier'])) {
    if (isset($_SESSION['utilisateur_id'])) {
        $utilisateur_id = $_SESSION['utilisateur_id'];

        $stmt = $pdo->prepare("SELECT p.*, panier.quantite FROM panier JOIN produits p ON panier.produit_id = p.id WHERE panier.utilisateur_id = ?");
        $stmt->execute([$utilisateur_id]);
        $panier = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'panier' => $panier]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    }
}
?>