<?php
session_start();
require_once 'db.php';

// Configuration et fonctions utilitaires
class CartException extends Exception {}

// Vérification CSRF
function verifyCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $headers = getallheaders();
        if (!isset($headers['X-CSRF-Token']) || $headers['X-CSRF-Token'] !== $_SESSION['csrf_token']) {
            throw new CartException('CSRF token invalide');
        }
    }
}

// Vérification de la connexion utilisateur
function checkAuth() {
    if (!isset($_SESSION['utilisateur_id'])) {
        throw new CartException('Utilisateur non connecté');
    }
    return $_SESSION['utilisateur_id'];
}

// Validation des entrées
function validateInput($value, $type = 'int') {
    $value = trim($value);
    switch ($type) {
        case 'int':
            $filtered = filter_var($value, FILTER_VALIDATE_INT);
            if ($filtered === false || $filtered <= 0) {
                throw new CartException('Valeur numérique invalide');
            }
            return $filtered;
        default:
            throw new CartException('Type de validation non supporté');
    }
}

// Gestionnaire principal des requêtes
try {
    verifyCsrfToken();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $utilisateur_id = checkAuth();

        if (isset($_POST['ajouter_panier'])) {
            handleAddToCart($pdo, $utilisateur_id);
        } elseif (isset($_POST['passer_commande'])) {
            handleCheckout($pdo, $utilisateur_id);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['recuperer_panier'])) {
        handleGetCart($pdo, checkAuth());
    }
} catch (CartException $e) {
    sendJsonResponse(false, $e->getMessage());
} catch (Exception $e) {
    error_log("Erreur panier: " . $e->getMessage());
    sendJsonResponse(false, 'Une erreur est survenue');
}

// Fonctions de gestion du panier

// Fonction pour ajouter un produit au panier
function handleAddToCart($pdo, $utilisateur_id) {
    $produit_id = validateInput($_POST['produit_id']);
    $quantite = validateInput($_POST['quantite']);

    try {
        $pdo->beginTransaction();

        // Vérifier le stock du produit
        $stmt = $pdo->prepare("SELECT stock, prix FROM produits WHERE id = ? FOR UPDATE");
        $stmt->execute([$produit_id]);
        $produit = $stmt->fetch();

        if (!$produit) {
            throw new CartException('Produit non trouvé');
        }

        if ($produit['stock'] < $quantite) {
            throw new CartException('Stock insuffisant');
        }

        // Mettre à jour ou insérer dans le panier
        $stmt = $pdo->prepare("
            INSERT INTO panier (utilisateur_id, produit_id, quantite) 
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE quantite = quantite + VALUES(quantite)
        ");
        $stmt->execute([$utilisateur_id, $produit_id, $quantite]);

        $pdo->commit();
        sendJsonResponse(true, 'Produit ajouté au panier');
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

// Fonction pour passer une commande
function handleCheckout($pdo, $utilisateur_id) {
    try {
        $pdo->beginTransaction();

        // Récupérer le panier de l'utilisateur
        $stmt = $pdo->prepare("
            SELECT p.id, p.prix, p.stock, panier.quantite 
            FROM panier 
            JOIN produits p ON panier.produit_id = p.id 
            WHERE panier.utilisateur_id = ?
            FOR UPDATE
        ");
        $stmt->execute([$utilisateur_id]);
        $items = $stmt->fetchAll();

        if (empty($items)) {
            throw new CartException('Le panier est vide');
        }

        // Vérifier le stock et calculer le total
        $total = 0;
        foreach ($items as $item) {
            if ($item['stock'] < $item['quantite']) {
                throw new CartException('Stock insuffisant pour certains produits');
            }
            $total += $item['prix'] * $item['quantite'];
        }

        // Créer la commande
        $stmt = $pdo->prepare("
            INSERT INTO commandes (utilisateur_id, total, date_creation) 
            VALUES (?, ?, NOW())
        ");
        $stmt->execute([$utilisateur_id, $total]);
        $commande_id = $pdo->lastInsertId();

        // Ajouter les articles à la commande et mettre à jour le stock
        foreach ($items as $item) {
            // Insérer l'article dans la commande
            $stmt = $pdo->prepare("
                INSERT INTO articles_commande (commande_id, produit_id, quantite, prix) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$commande_id, $item['id'], $item['quantite'], $item['prix']]);

            // Mettre à jour le stock du produit
            $stmt = $pdo->prepare("
                UPDATE produits 
                SET stock = stock - ? 
                WHERE id = ?
            ");
            $stmt->execute([$item['quantite'], $item['id']]);
        }

        // Vider le panier de l'utilisateur
        $stmt = $pdo->prepare("DELETE FROM panier WHERE utilisateur_id = ?");
        $stmt->execute([$utilisateur_id]);

        $pdo->commit();
        sendJsonResponse(true, 'Commande créée avec succès');
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

// Fonction pour récupérer le contenu du panier
function handleGetCart($pdo, $utilisateur_id) {
    $stmt = $pdo->prepare("
        SELECT 
            p.id,
            p.nom,
            p.prix,
            panier.quantite,
            p.stock
        FROM panier 
        JOIN produits p ON panier.produit_id = p.id 
        WHERE panier.utilisateur_id = ?
    ");
    $stmt->execute([$utilisateur_id]);
    $panier = $stmt->fetchAll();

    sendJsonResponse(true, '', ['items' => $panier]);
}

// Fonction utilitaire pour envoyer les réponses JSON
function sendJsonResponse($success, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode(array_merge(
        ['success' => $success, 'message' => $message],
        $data
    ));
    exit;
}