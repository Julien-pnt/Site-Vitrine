<?php
session_start();
require_once '../../config/database.php';

header('Content-Type: text/html; charset=utf-8');

// Protection pour n'autoriser que l'environnement de développement
if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
    echo "Accès refusé - Outil réservé au développement local.";
    exit;
}

// Récupérer tous les éléments du panier
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$sessionId = session_id();

// Structure de la page HTML
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Débogage de la table panier</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        .actions { display: flex; gap: 10px; }
        .btn { padding: 5px 10px; border: none; border-radius: 3px; cursor: pointer; }
        .btn-delete { background-color: #ff4d4d; color: white; }
        .session-info { background: #f8f9fa; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        h1 { color: #333; }
    </style>
</head>
<body>
    <h1>Débogage de la table panier</h1>
    
    <div class="session-info">
        <p><strong>Session ID:</strong> <?= session_id() ?></p>
        <p><strong>User ID:</strong> <?= $userId ?? 'Non connecté' ?></p>
    </div>

    <h2>Contenu actuel de la table panier</h2>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Utilisateur ID</th>
                <th>Session ID</th>
                <th>Produit ID</th>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Date d'ajout</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            try {
                // Requête pour récupérer tous les éléments du panier avec les détails produit
                $query = "SELECT p.*, pr.nom, pr.prix, pr.prix_promo 
                          FROM panier p
                          LEFT JOIN produits pr ON p.produit_id = pr.id
                          ORDER BY p.date_ajout DESC";
                          
                $stmt = $pdo->prepare($query);
                $stmt->execute();
                $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (count($cartItems) === 0) {
                    echo "<tr><td colspan='9' style='text-align:center;'>Aucun article dans le panier</td></tr>";
                } else {
                    foreach ($cartItems as $item) {
                        $price = !empty($item['prix_promo']) ? $item['prix_promo'] : $item['prix'];
                        ?>
                        <tr>
                            <td><?= $item['id'] ?></td>
                            <td><?= $item['utilisateur_id'] ?? '<em>NULL</em>' ?></td>
                            <td><?= $item['session_id'] ?? '<em>NULL</em>' ?></td>
                            <td><?= $item['produit_id'] ?></td>
                            <td><?= $item['nom'] ?? '<em>Produit non trouvé</em>' ?></td>
                            <td><?= $item['quantite'] ?></td>
                            <td><?= number_format($price, 2, ',', ' ') ?> €</td>
                            <td><?= $item['date_ajout'] ?></td>
                            <td class="actions">
                                <form method="post" onsubmit="return confirm('Voulez-vous vraiment supprimer cet article?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                    <button type="submit" class="btn btn-delete">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                        <?php
                    }
                }
            } catch (Exception $e) {
                echo "<tr><td colspan='9' style='color:red'>Erreur: " . $e->getMessage() . "</td></tr>";
            }
            ?>
        </tbody>
    </table>
    
    <div style="margin-top: 20px;">
        <form method="post" onsubmit="return confirm('Voulez-vous vraiment vider le panier?');">
            <input type="hidden" name="action" value="clear">
            <button type="submit" class="btn btn-delete">Vider le panier</button>
        </form>
    </div>
    
    <?php
    // Traitement des actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            try {
                if ($_POST['action'] === 'delete' && isset($_POST['id'])) {
                    // Supprimer un article spécifique
                    $stmt = $pdo->prepare("DELETE FROM panier WHERE id = ?");
                    $stmt->execute([$_POST['id']]);
                    echo "<script>alert('Article supprimé!'); window.location.reload();</script>";
                }
                else if ($_POST['action'] === 'clear') {
                    // Vider tout le panier
                    $stmt = $pdo->prepare("DELETE FROM panier");
                    $stmt->execute();
                    echo "<script>alert('Panier vidé!'); window.location.reload();</script>";
                }
            } catch (Exception $e) {
                echo "<script>alert('Erreur: " . addslashes($e->getMessage()) . "');</script>";
            }
        }
    }
    ?>
</body>
</html>