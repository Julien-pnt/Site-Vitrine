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

        // Vérifier le stock du produit
        $stmt = $pdo->prepare("SELECT stock FROM produits WHERE id = ?");
        $stmt->execute([$produit_id]);
        $produit = $stmt->fetch();

        if ($produit && $produit['stock'] >= $quantite) {
            // Vérifier si le produit est déjà dans le panier
            $stmt = $pdo->prepare("SELECT * FROM panier WHERE utilisateur_id = ? AND produit_id = ?");
            $stmt->execute([$utilisateur_id, $produit_id]);
            $panier_item = $stmt->fetch();

            if ($panier_item) {
                // Mettre à jour la quantité
                $stmt = $pdo->prepare("UPDATE panier SET quantite = quantite + ? WHERE id = ?");
                $stmt->execute([$quantite, $panier_item['id']]);
            } else {
                // Ajouter le produit au panier
                $stmt = $pdo->prepare("INSERT INTO panier (utilisateur_id, produit_id, quantite) VALUES (?, ?, ?)");
                $stmt->execute([$utilisateur_id, $produit_id, $quantite]);
            }

            echo json_encode(['success' => true, 'message' => 'Produit ajouté au panier !']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Stock insuffisant']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    }
}

// Passer à la caisse
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['passer_commande'])) {
    if (isset($_SESSION['utilisateur_id'])) {
        $utilisateur_id = $_SESSION['utilisateur_id'];

        // Récupérer le panier de l'utilisateur
        $stmt = $pdo->prepare("SELECT p.*, panier.quantite FROM panier JOIN produits p ON panier.produit_id = p.id WHERE panier.utilisateur_id = ?");
        $stmt->execute([$utilisateur_id]);
        $panier = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($panier)) {
            echo json_encode(['success' => false, 'message' => 'Votre panier est vide']);
            exit;
        }

        // Calculer le total de la commande
        $total = array_reduce($panier, function ($sum, $item) {
            return $sum + ($item['prix'] * $item['quantite']);
        }, 0);

        // Créer la commande
        $stmt = $pdo->prepare("INSERT INTO commandes (utilisateur_id, total) VALUES (?, ?)");
        $stmt->execute([$utilisateur_id, $total]);
        $commande_id = $pdo->lastInsertId();

        // Ajouter les articles de la commande
        foreach ($panier as $item) {
            $stmt = $pdo->prepare("INSERT INTO articles_commande (commande_id, produit_id, quantite, prix) VALUES (?, ?, ?, ?)");
            $stmt->execute([$commande_id, $item['id'], $item['quantite'], $item['prix']]);

            // Mettre à jour le stock
            $stmt = $pdo->prepare("UPDATE produits SET stock = stock - ? WHERE id = ?");
            $stmt->execute([$item['quantite'], $item['id']]);
        }

        // Vider le panier
        $stmt = $pdo->prepare("DELETE FROM panier WHERE utilisateur_id = ?");
        $stmt->execute([$utilisateur_id]);

        echo json_encode(['success' => true, 'message' => 'Commande passée avec succès !']);
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