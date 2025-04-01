<?php
session_start();
require_once 'db.php';

// Récupérer les IDs des montres à comparer
$montres_ids = isset($_GET['ids']) ? explode(',', $_GET['ids']) : [];

// Limiter à 3 produits maximum
$montres_ids = array_slice($montres_ids, 0, 3);

// Récupérer les données des produits
$montres = [];
if (!empty($montres_ids)) {
    $placeholders = implode(',', array_fill(0, count($montres_ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE id IN ($placeholders)");
    $stmt->execute($montres_ids);
    $montres = $stmt->fetchAll();
}

// Définir les caractéristiques à comparer
$caracteristiques = [
    'prix' => 'Prix',
    'boitier' => 'Boîtier',
    'bracelet' => 'Bracelet',
    'verre' => 'Verre',
    'mouvement' => 'Mouvement',
    'etancheite' => 'Étanchéité',
    'garantie' => 'Garantie'
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comparer - Elixir du Temps</title>
    <link rel="stylesheet" href="../css/Styles.css">
    <link rel="shortcut icon" href="../img/icon.png" type="image/x-icon">
    <style>
        .comparison-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .comparison-title {
            text-align: center;
            margin-bottom: 2rem;
            color: #ffd700;
            font-size: 2.5rem;
        }
        
        .comparison-subtitle {
            text-align: center;
            margin-bottom: 2rem;
            color: #fff;
            opacity: 0.8;
        }
        
        .comparison-table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 1rem;
            overflow: hidden;
        }
        
        .comparison-table img {
            width: 100%;
            max-width: 180px;
            height: auto;
            border-radius: 0.5rem;
            margin: 0 auto;
            display: block;
        }
        
        .comparison-table th {
            padding: 1.5rem;
            background: rgba(255, 215, 0, 0.2);
            text-align: left;
            color: #ffd700;
            font-size: 1.1rem;
            font-weight: 600;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .comparison-table td {
            padding: 1.5rem;
            vertical-align: middle;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            line-height: 1.4;
        }
        
        .comparison-table tr:last-child td {
            border-bottom: none;
        }
        
        .comparison-table .product-name {
            font-size: 1.2rem;
            font-weight: bold;
            color: #fff;
            margin: 1rem 0;
            text-align: center;
        }
        
        .comparison-table .product-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #ffd700;
            text-align: center;
        }
        
        .no-products {
            text-align: center;
            padding: 3rem;
            color: rgba(255, 255, 255, 0.7);
            background: rgba(0, 0, 0, 0.5);
            border-radius: 1rem;
        }
        
        .no-products h3 {
            margin-bottom: 1rem;
            color: #ffd700;
        }
        
        .back-to-products {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(90deg, #ffd700, #ff7f50);
            border: none;
            border-radius: 9999px;
            color: #000;
            font-weight: bold;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .back-to-products:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        .add-to-cart {
            display: block;
            width: 100%;
            padding: 0.75rem;
            margin-top: 1rem;
            background: linear-gradient(90deg, #ff7f50, #ff4500);
            border: none;
            border-radius: 0.5rem;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .add-to-cart:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 69, 0, 0.2);
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <header class="header">
        <div class="logo-container">
            <img src="../img/logo.png" alt="Elixir du Temps Logo" class="logo">
        </div>
        <nav>
            <ul class="menu-bar">
                <li><a href="../html/Accueil.html">Accueil</a></li>
                <li><a href="../html/Collections.html">Collections</a></li>
                <li><a href="../html/Montres.html">Montres</a></li>
                <li><a href="../html/DescriptionProduits.html">Description produits</a></li>
                <li><a href="../html/APropos.html">À propos</a></li>
                <li><a href="../html/Organigramme.html">Organigramme</a></li>
            </ul>
        </nav>
    </header>

    <!-- Video Background -->
    <div class="video-background">
        <video class="video-bg" autoplay muted loop playsinline>
            <source src="../video/background.mp4" type="video/mp4">
            Votre navigateur ne supporte pas la vidéo.
        </video>
        <div class="video-overlay"></div>
    </div>

    <!-- Comparison Section -->
    <div class="comparison-container">
        <h1 class="comparison-title">Comparaison de montres</h1>
        
        <?php if (empty($montres)): ?>
            <div class="no-products">
                <h3>Aucune montre sélectionnée pour la comparaison</h3>
                <p>Veuillez sélectionner des montres à comparer depuis notre catalogue.</p>
                <a href="../html/Montres.html" class="back-to-products">Voir toutes les montres</a>
            </div>
        <?php else: ?>
            <p class="comparison-subtitle">Comparez les caractéristiques de vos montres préférées pour faire le meilleur choix.</p>
            
            <table class="comparison-table">
                <tr>
                    <th>Caractéristique</th>
                    <?php foreach ($montres as $montre): ?>
                        <th><?php echo htmlspecialchars($montre['nom']); ?></th>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <td>Image</td>
                    <?php foreach ($montres as $montre): ?>
                        <td>
                            <img src="<?php echo htmlspecialchars($montre['image']); ?>" alt="<?php echo htmlspecialchars($montre['nom']); ?>">
                            <div class="product-name"><?php echo htmlspecialchars($montre['nom']); ?></div>
                            <div class="product-price"><?php echo number_format($montre['prix'], 2, ',', ' '); ?> €</div>
                            <form action="panier.php" method="post" class="add-to-cart-form">
                                <input type="hidden" name="produit_id" value="<?php echo $montre['id']; ?>">
                                <input type="hidden" name="quantite" value="1">
                                <input type="hidden" name="ajouter_panier" value="1">
                                <button type="submit" class="add-to-cart">Ajouter au panier</button>
                            </form>
                        </td>
                    <?php endforeach; ?>
                </tr>
                
                <?php foreach ($caracteristiques as $key => $label): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($label); ?></td>
                        <?php foreach ($montres as $montre): ?>
                            <td><?php echo isset($montre[$key]) ? htmlspecialchars($montre[$key]) : '—'; ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                
                <tr>
                    <td>Description</td>
                    <?php foreach ($montres as $montre): ?>
                        <td><?php echo htmlspecialchars($montre['description']); ?></td>
                    <?php endforeach; ?>
                </tr>
            </table>
            
            <div style="text-align: center; margin-top: 2rem;">
                <a href="../html/Montres.html" class="back-to-products">Voir toutes les montres</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer Section -->
    <footer class="footer">
        <div class="footer-content">
            <p>&copy; <?php echo date('Y'); ?> Elixir du Temps. Tous droits réservés.</p>
            <ul class="footer-links">
                <li><a href="mailto:contact@elixirdutemps.com" class="mon-email">Contact</a></li>
                <li><a href="privacy-policy.php">Politique de confidentialité</a></li>
            </ul>
        </div>
    </footer>
    
    <script src="../js/login.js"></script>
    <script>
        // Ajouter la gestion du panier en AJAX
        document.addEventListener('DOMContentLoaded', function() {
            const addToCartForms = document.querySelectorAll('.add-to-cart-form');
            
            addToCartForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    
                    // Ajouter le token CSRF si nécessaire
                    fetch('panier.php', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-Token': '<?php echo $_SESSION['csrf_token'] ?? ''; ?>'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast(data.message);
                        } else {
                            showToast(data.message, true);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        showToast('Une erreur est survenue', true);
                    });
                });
            });
            
            // Fonction pour afficher un message toast
            function showToast(message, isError = false) {
                let toast = document.querySelector('.toast');
                
                // Créer le toast s'il n'existe pas
                if (!toast) {
                    toast = document.createElement('div');
                    toast.className = 'toast';
                    document.body.appendChild(toast);
                }
                
                // Appliquer le style d'erreur si nécessaire
                if (isError) {
                    toast.classList.add('error');
                } else {
                    toast.classList.remove('error');
                }
                
                // Afficher le message
                toast.textContent = message;
                toast.classList.add('show');
                
                // Cacher après 3 secondes
                setTimeout(() => {
                    toast.classList.remove('show');
                }, 3000);
            }
        });
    </script>
</body>
</html>
