<?php
session_start();
require_once 'db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Vérifier si l'ID de commande est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$commande_id = (int)$_GET['id'];

// Vérifier que la commande appartient à l'utilisateur
$stmt = $pdo->prepare("
    SELECT c.id, c.date_commande, c.total
    FROM commandes c
    WHERE c.id = ? AND c.utilisateur_id = ?
");
$stmt->execute([$commande_id, $_SESSION['user_id']]);
$commande = $stmt->fetch();

if (!$commande) {
    header('Location: dashboard.php');
    exit;
}

// Récupérer les articles de la commande
$stmt = $pdo->prepare("
    SELECT ac.*, p.nom, p.image
    FROM articles_commande ac
    JOIN produits p ON ac.produit_id = p.id
    WHERE ac.commande_id = ?
");
$stmt->execute([$commande_id]);
$articles = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de Commande - Elixir du Temps</title>
    <link rel="stylesheet" href="../css/Styles.css">
    <link rel="shortcut icon" href="../img/icon.png" type="image/x-icon">
    <style>
        .confirmation-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .confirmation-title {
            text-align: center;
            margin-bottom: 1rem;
            color: #ffd700;
            font-size: 2.5rem;
        }
        
        .confirmation-subtitle {
            text-align: center;
            margin-bottom: 2rem;
            color: #fff;
            opacity: 0.8;
        }
        
        .order-details {
            background: rgba(0, 0, 0, 0.5);
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .order-header h3 {
            margin: 0;
            color: #ffd700;
            font-size: 1.5rem;
        }
        
        .order-date {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .order-items {
            margin-bottom: 1.5rem;
        }
        
        .order-item {
            display: grid;
            grid-template-columns: 80px 1fr auto auto;
            gap: 1rem;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .order-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 0.5rem;
        }
        
        .order-item-details h4 {
            margin: 0 0 0.25rem 0;
            color: #fff;
        }
        
        .order-item-details p {
            margin: 0;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }
        
        .order-item-price, .order-item-quantity {
            color: #fff;
            text-align: right;
        }
        
        .order-summary {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            color: rgba(255, 255, 255, 0.85);
        }
        
        .summary-row.total {
            font-size: 1.2rem;
            font-weight: bold;
            color: #ffd700;
            margin-top: 1rem;
        }
        
        .delivery-info {
            background: rgba(0, 0, 0, 0.5);
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .delivery-info h3 {
            margin-top: 0;
            color: #ffd700;
            margin-bottom: 1rem;
        }
        
        .delivery-step {
            display: flex;
            margin-bottom: 1.5rem;
        }
        
        .step-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 215, 0, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: #ffd700;
            font-weight: bold;
        }
        
        .step-content h4 {
            margin: 0 0 0.5rem 0;
            color: #ffd700;
        }
        
        .step-content p {
            margin: 0;
            color: rgba(255, 255, 255, 0.7);
        }
        
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .action-button {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(90deg, #ffd700, #ff7f50);
            color: #000;
            font-weight: bold;
            text-decoration: none;
            border-radius: 9999px;
            transition: all 0.3s;
        }
        
        .action-button.secondary {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }
        
        .action-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.2);
        }
        
        .thank-you-message {
            text-align: center;
            margin-top: 2rem;
            color: #ffd700;
            font-style: italic;
            font-size: 1.1rem;
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

    <!-- Confirmation Section -->
    <div class="confirmation-container">
        <h1 class="confirmation-title">Commande confirmée</h1>
        <p class="confirmation-subtitle">Merci pour votre commande! Votre achat a été confirmé.</p>
        
        <div class="order-details">
            <div class="order-header">
                <h3>Commande #<?php echo $commande['id']; ?></h3>
                <span class="order-date"><?php echo date('d/m/Y à H:i', strtotime($commande['date_commande'])); ?></span>
            </div>
            
            <div class="order-items">
                <?php foreach ($articles as $article): ?>
                    <div class="order-item">
                        <img src="<?php echo htmlspecialchars($article['image']); ?>" alt="<?php echo htmlspecialchars($article['nom']); ?>">
                        <div class="order-item-details">
                            <h4><?php echo htmlspecialchars($article['nom']); ?></h4>
                            <p>Réf: ELX-<?php echo $article['produit_id']; ?></p>
                        </div>
                        <div class="order-item-quantity">Qté: <?php echo $article['quantite']; ?></div>
                        <div class="order-item-price"><?php echo number_format($article['prix'], 2, ',', ' '); ?> €</div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="order-summary">
                <div class="summary-row">
                    <span>Sous-total</span>
                    <span><?php echo number_format($commande['total'], 2, ',', ' '); ?> €</span>
                </div>
                <div class="summary-row">
                    <span>Livraison</span>
                    <span>Gratuite</span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span><?php echo number_format($commande['total'], 2, ',', ' '); ?> €</span>
                </div>
            </div>
        </div>
        
        <div class="delivery-info">
            <h3>Suivi de votre commande</h3>
            
            <div class="delivery-step">
                <div class="step-icon">1</div>
                <div class="step-content">
                    <h4>Commande confirmée</h4>
                    <p>Votre commande a été reçue et est en cours de traitement.</p>
                </div>
            </div>
            
            <div class="delivery-step">
                <div class="step-icon">2</div>
                <div class="step-content">
                    <h4>Préparation</h4>
                    <p>Nos horlogers préparent votre montre avec le plus grand soin.</p>
                </div>
            </div>
            
            <div class="delivery-step">
                <div class="step-icon">3</div>
                <div class="step-content">
                    <h4>Expédition</h4>
                    <p>Votre commande sera expédiée dans les 48 heures.</p>
                </div>
            </div>
            
            <div class="delivery-step">
                <div class="step-icon">4</div>
                <div class="step-content">
                    <h4>Livraison</h4>
                    <p>Votre montre sera livrée dans un écrin luxueux et sécurisé.</p>
                </div>
            </div>
        </div>
        
        <div class="action-buttons">
            <a href="mes-commandes.php" class="action-button">Voir mes commandes</a>
            <a href="../html/Collections.html" class="action-button secondary">Retour aux collections</a>
        </div>
        
        <div class="thank-you-message">
            <p>Nous vous remercions pour votre confiance. Bienvenue dans l'univers Elixir du Temps.</p>
        </div>
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
</body>
</html>
