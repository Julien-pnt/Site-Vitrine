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
    if (!isset($_SESSION['user_id'])) {
        throw new CartException('Utilisateur non connecté');
    }
    return $_SESSION['user_id'];
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

// Fonction pour envoyer une réponse JSON
function sendJsonResponse($success, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'csrf_token' => $_SESSION['csrf_token'] ?? ''
    ]);
    exit;
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
        } elseif (isset($_POST['supprimer_article'])) {
            handleRemoveFromCart($pdo, $utilisateur_id);
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
        
        // Vérifier si l'article existe déjà dans le panier
        $stmt = $pdo->prepare("SELECT quantite FROM panier WHERE utilisateur_id = ? AND produit_id = ?");
        $stmt->execute([$utilisateur_id, $produit_id]);
        $article_panier = $stmt->fetch();
        
        if ($article_panier) {
            // Mettre à jour la quantité
            $nouvelle_quantite = $article_panier['quantite'] + $quantite;
            
            // Vérifier à nouveau avec la nouvelle quantité
            if ($produit['stock'] < $nouvelle_quantite) {
                throw new CartException('Stock insuffisant pour ajouter cette quantité');
            }
            
            $stmt = $pdo->prepare("UPDATE panier SET quantite = ? WHERE utilisateur_id = ? AND produit_id = ?");
            $stmt->execute([$nouvelle_quantite, $utilisateur_id, $produit_id]);
        } else {
            // Ajouter un nouvel article au panier
            $stmt = $pdo->prepare("INSERT INTO panier (utilisateur_id, produit_id, quantite) VALUES (?, ?, ?)");
            $stmt->execute([$utilisateur_id, $produit_id, $quantite]);
        }
        
        $pdo->commit();
        sendJsonResponse(true, 'Produit ajouté au panier');
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

// Fonction pour récupérer le contenu du panier
function handleGetCart($pdo, $utilisateur_id) {
    $stmt = $pdo->prepare("
        SELECT p.id, p.nom, p.prix, pa.quantite, p.image 
        FROM panier pa
        JOIN produits p ON pa.produit_id = p.id
        WHERE pa.utilisateur_id = ?
    ");
    $stmt->execute([$utilisateur_id]);
    $articles = $stmt->fetchAll();
    
    $total = 0;
    foreach ($articles as $article) {
        $total += $article['prix'] * $article['quantite'];
    }
    
    sendJsonResponse(true, 'Panier récupéré', [
        'articles' => $articles,
        'total' => $total,
        'nombre_articles' => count($articles)
    ]);
}

// Fonction pour supprimer un article du panier
function handleRemoveFromCart($pdo, $utilisateur_id) {
    $produit_id = validateInput($_POST['produit_id']);
    
    $stmt = $pdo->prepare("DELETE FROM panier WHERE utilisateur_id = ? AND produit_id = ?");
    $stmt->execute([$utilisateur_id, $produit_id]);
    
    sendJsonResponse(true, 'Article supprimé du panier');
}

// Fonction pour passer une commande
function handleCheckout($pdo, $utilisateur_id) {
    try {
        $pdo->beginTransaction();
        
        // Récupérer le contenu du panier
        $stmt = $pdo->prepare("
            SELECT pa.produit_id, pa.quantite, p.prix, p.stock
            FROM panier pa
            JOIN produits p ON pa.produit_id = p.id
            WHERE pa.utilisateur_id = ?
        ");
        $stmt->execute([$utilisateur_id]);
        $articles = $stmt->fetchAll();
        
        if (empty($articles)) {
            throw new CartException('Votre panier est vide');
        }
        
        // Calculer le total et vérifier le stock
        $total = 0;
        foreach ($articles as $article) {
            if ($article['stock'] < $article['quantite']) {
                throw new CartException('Stock insuffisant pour ' . $article['nom']);
            }
            $total += $article['prix'] * $article['quantite'];
        }
        
        // Créer la commande
        $stmt = $pdo->prepare("INSERT INTO commandes (utilisateur_id, total) VALUES (?, ?)");
        $stmt->execute([$utilisateur_id, $total]);
        $commande_id = $pdo->lastInsertId();
        
        // Ajouter les articles à la commande et mettre à jour le stock
        foreach ($articles as $article) {
            // Ajouter l'article à la commande
            $stmt = $pdo->prepare("
                INSERT INTO articles_commande (commande_id, produit_id, quantite, prix)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $commande_id,
                $article['produit_id'],
                $article['quantite'],
                $article['prix']
            ]);
            
            // Mettre à jour le stock
            $nouveau_stock = $article['stock'] - $article['quantite'];
            $stmt = $pdo->prepare("UPDATE produits SET stock = ? WHERE id = ?");
            $stmt->execute([$nouveau_stock, $article['produit_id']]);
        }
        
        // Vider le panier
        $stmt = $pdo->prepare("DELETE FROM panier WHERE utilisateur_id = ?");
        $stmt->execute([$utilisateur_id]);
        
        $pdo->commit();
        sendJsonResponse(true, 'Commande effectuée avec succès', ['commande_id' => $commande_id]);
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

// Fonction pour mettre à jour la quantité d'un article dans le panier
function handleUpdateCart($pdo, $utilisateur_id) {
    $produit_id = validateInput($_POST['produit_id']);
    $quantite = validateInput($_POST['quantite']);

    try {
        $pdo->beginTransaction();

        // Vérifier le stock du produit
        $stmt = $pdo->prepare("SELECT stock FROM produits WHERE id = ? FOR UPDATE");
        $stmt->execute([$produit_id]);
        $produit = $stmt->fetch();

        if (!$produit) {
            throw new CartException('Produit non trouvé');
        }

        if ($produit['stock'] < $quantite) {
            throw new CartException('Stock insuffisant');
        }

        // Mettre à jour la quantité
        $stmt = $pdo->prepare("UPDATE panier SET quantite = ? WHERE utilisateur_id = ? AND produit_id = ?");
        $stmt->execute([$quantite, $utilisateur_id, $produit_id]);

        $pdo->commit();
        sendJsonResponse(true, 'Panier mis à jour');
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}
