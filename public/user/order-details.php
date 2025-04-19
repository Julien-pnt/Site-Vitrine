<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/auth/login.html');
    exit;
}

// Vérifier si l'ID de commande est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: orders.php');
    exit;
}

// Connexion à la base de données
require_once '../../php/config/database.php';
$db = new Database();
$conn = $db->getConnection();

// Récupérer les informations de l'utilisateur
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupérer les informations de la commande
$orderId = (int)$_GET['id'];
$stmt = $conn->prepare("
    SELECT * FROM commandes 
    WHERE id = ? AND utilisateur_id = ?
");
$stmt->execute([$orderId, $userId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifier que la commande existe et appartient à l'utilisateur
if (!$order) {
    header('Location: orders.php');
    exit;
}

// Récupérer les produits de la commande (en supposant une table commande_produits)
$stmt = $conn->prepare("
    SELECT cp.*, p.nom, p.reference as product_ref, p.image_principale 
    FROM commande_produits cp
    JOIN produits p ON cp.produit_id = p.id
    WHERE cp.commande_id = ?
");
$stmt->execute([$orderId]);
$orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traduction des statuts
$statusTranslations = [
    'en_attente' => 'En attente',
    'payee' => 'Payée',
    'en_preparation' => 'En préparation',
    'expediee' => 'Expédiée',
    'livree' => 'Livrée',
    'annulee' => 'Annulée',
    'remboursee' => 'Remboursée'
];

// Titre de la page
$pageTitle = "Détails de la commande";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> | Elixir du Temps</title>
    
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/components/header.css">
    <link rel="stylesheet" href="../assets/css/components/footer.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Raleway:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- En-tête -->
    <?php include 'includes/header.php'; ?>
    
    <div class="dashboard-container">
        <!-- Barre latérale -->
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Contenu principal -->
        <main class="dashboard-content">
            <div class="dashboard-header">
                <div class="back-link">
                    <a href="orders.php"><i class="fas fa-arrow-left"></i> Retour aux commandes</a>
                </div>
                <h1>Commande #<?php echo htmlspecialchars($order['reference']); ?></h1>
                <p>Passée le <?php echo date('d/m/Y à H:i', strtotime($order['date_commande'])); ?></p>
            </div>
            
            <div class="order-status-bar">
                <div class="status-tracker">
                    <?php
                    $statusSteps = ['en_attente', 'payee', 'en_preparation', 'expediee', 'livree'];
                    $currentStatusIndex = array_search($order['statut'], $statusSteps);
                    
                    // Si la commande est annulée ou remboursée, on ajuste l'affichage
                    $isSpecialStatus = in_array($order['statut'], ['annulee', 'remboursee']);
                    ?>
                    
                    <?php if ($isSpecialStatus): ?>
                        <div class="status-step <?php echo 'status-' . $order['statut']; ?>">
                            <div class="status-icon">
                                <i class="fas <?php echo $order['statut'] === 'annulee' ? 'fa-times' : 'fa-undo'; ?>"></i>
                            </div>
                            <div class="status-text"><?php echo $statusTranslations[$order['statut']]; ?></div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($statusSteps as $index => $statusStep): ?>
                            <div class="status-step <?php 
                                if ($index < $currentStatusIndex || $order['statut'] === $statusStep) {
                                    echo 'completed';
                                } elseif ($index === $currentStatusIndex) {
                                    echo 'current';
                                }
                            ?>">
                                <div class="status-icon">
                                    <?php if ($index < $currentStatusIndex || $order['statut'] === $statusStep): ?>
                                        <i class="fas fa-check"></i>
                                    <?php else: ?>
                                        <i class="fas <?php 
                                            switch ($statusStep) {
                                                case 'en_attente': echo 'fa-clock';
                                                    break;
                                                case 'payee': echo 'fa-credit-card';
                                                    break;
                                                case 'en_preparation': echo 'fa-box';
                                                    break;
                                                case 'expediee': echo 'fa-truck';
                                                    break;
                                                case 'livree': echo 'fa-check-circle';
                                                    break;
                                                default: echo 'fa-circle';
                                            }
                                        ?>"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="status-text"><?php echo $statusTranslations[$statusStep]; ?></div>
                            </div>
                            <?php if ($index < count($statusSteps) - 1): ?>
                                <div class="status-line <?php echo $index < $currentStatusIndex ? 'completed' : ''; ?>"></div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="order-sections">
                <div class="order-section">
                    <h2>Détails de la commande</h2>
                    <div class="order-details-grid">
                        <div class="order-detail-item">
                            <div class="detail-label">Référence</div>
                            <div class="detail-value">#<?php echo htmlspecialchars($order['reference']); ?></div>
                        </div>
                        <div class="order-detail-item">
                            <div class="detail-label">Date</div>
                            <div class="detail-value"><?php echo date('d/m/Y à H:i', strtotime($order['date_commande'])); ?></div>
                        </div>
                        <div class="order-detail-item">
                            <div class="detail-label">Statut</div>
                            <div class="detail-value">
                                <span class="status-badge status-<?php echo $order['statut']; ?>">
                                    <?php echo $statusTranslations[$order['statut']]; ?>
                                </span>
                            </div>
                        </div>
                        <div class="order-detail-item">
                            <div class="detail-label">Mode de paiement</div>
                            <div class="detail-value"><?php echo htmlspecialchars($order['mode_paiement']); ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="order-section order-columns">
                    <div class="order-column">
                        <h2>Adresse de livraison</h2>
                        <div class="address-card">
                            <?php echo nl2br(htmlspecialchars($order['adresse_livraison'])); ?>
                        </div>
                    </div>
                    
                    <div class="order-column">
                        <h2>Adresse de facturation</h2>
                        <div class="address-card">
                            <?php echo nl2br(htmlspecialchars($order['adresse_facturation'])); ?>
                        </div>
                    </div>
                </div>
                
                <div class="order-section">
                    <h2>Produits commandés</h2>
                    <div class="order-products">
                        <?php foreach ($orderItems as $item): ?>
                            <div class="order-product">
                                <div class="product-image">
                                    <img src="../assets/img/products/<?php echo htmlspecialchars($item['image_principale']); ?>" alt="<?php echo htmlspecialchars($item['nom']); ?>">
                                </div>
                                <div class="product-details">
                                    <h3><?php echo htmlspecialchars($item['nom']); ?></h3>
                                    <p class="product-reference">Réf: <?php echo htmlspecialchars($item['product_ref']); ?></p>
                                    <?php if (!empty($item['options'])): ?>
                                        <p class="product-options"><?php echo htmlspecialchars($item['options']); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="product-quantity">
                                    <span>Quantité: <?php echo $item['quantite']; ?></span>
                                </div>
                                <div class="product-price">
                                    <span class="unit-price"><?php echo number_format($item['prix_unitaire'], 2, ',', ' '); ?> €</span>
                                    <span class="total-price"><?php echo number_format($item['prix_unitaire'] * $item['quantite'], 2, ',', ' '); ?> €</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="order-section">
                    <h2>Récapitulatif</h2>
                    <div class="order-summary">
                        <div class="summary-row">
                            <div class="summary-label">Total produits</div>
                            <div class="summary-value"><?php echo number_format($order['total_produits'], 2, ',', ' '); ?> €</div>
                        </div>
                        <div class="summary-row">
                            <div class="summary-label">Frais de livraison</div>
                            <div class="summary-value"><?php echo number_format($order['frais_livraison'], 2, ',', ' '); ?> €</div>
                        </div>
                        <div class="summary-row">
                            <div class="summary-label">Taxes</div>
                            <div class="summary-value"><?php echo number_format($order['total_taxe'], 2, ',', ' '); ?> €</div>
                        </div>
                        <div class="summary-row summary-total">
                            <div class="summary-label">Total</div>
                            <div class="summary-value"><?php echo number_format($order['total'], 2, ',', ' '); ?> €</div>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($order['notes'])): ?>
                <div class="order-section">
                    <h2>Notes</h2>
                    <div class="order-notes">
                        <?php echo nl2br(htmlspecialchars($order['notes'])); ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="order-actions">
                    <?php if ($order['statut'] === 'en_attente'): ?>
                        <a href="cancel-order.php?id=<?php echo $order['id']; ?>" class="btn-cancel" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette commande ?');">
                            Annuler la commande
                        </a>
                    <?php endif; ?>
                    
                    <a href="#" class="btn-contact" onclick="contactSupport('<?php echo $order['reference']; ?>'); return false;">
                        Contacter le service client
                    </a>
                    
                    <a href="orders.php" class="btn-secondary">
                        Retour aux commandes
                    </a>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Pied de page -->
    <?php include 'includes/footer.php'; ?>
    
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/video-background.js"></script>
    <script src="assets/js/dashboard.js"></script>
    <script>
        function contactSupport(orderRef) {
            // Rediriger vers un formulaire de contact prérempli
            window.location.href = 'contact.php?subject=Support commande ' + orderRef;
        }
    </script>
</body>
</html>