<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/auth/login.html');
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

// Récupérer les commandes récentes
$stmtOrders = $conn->prepare("
    SELECT * FROM commandes 
    WHERE utilisateur_id = ? 
    ORDER BY date_commande DESC 
    LIMIT 5
");
$stmtOrders->execute([$userId]);
$recentOrders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);

// Titre de la page
$pageTitle = "Tableau de bord";
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
                <h1>Bienvenue, <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></h1>
                <p>Votre espace personnel Elixir du Temps</p>
            </div>
            
            <!-- Résumé -->
            <div class="summary-cards">
                <div class="summary-card">
                    <div class="summary-icon"><i class="fas fa-shopping-bag"></i></div>
                    <div class="summary-info">
                        <h3>Commandes</h3>
                        <p class="summary-count"><?php echo count($recentOrders); ?></p>
                    </div>
                    <a href="orders.php" class="summary-link">Voir tout</a>
                </div>
                
                <div class="summary-card">
                    <div class="summary-icon"><i class="fas fa-heart"></i></div>
                    <div class="summary-info">
                        <h3>Favoris</h3>
                        <p class="summary-count">0</p> <!-- À adapter avec votre système de favoris -->
                    </div>
                    <a href="wishlist.php" class="summary-link">Voir tout</a>
                </div>
                
                <div class="summary-card">
                    <div class="summary-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="summary-info">
                        <h3>Adresse</h3>
                        <p class="summary-count">1</p> <!-- Nombre d'adresses -->
                    </div>
                    <a href="addresses.php" class="summary-link">Gérer</a>
                </div>
            </div>
            
            <!-- Commandes récentes -->
            <section class="recent-orders">
                <div class="section-header">
                    <h2>Commandes récentes</h2>
                    <a href="orders.php" class="see-all">Voir tout</a>
                </div>
                
                <div class="orders-table-container">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentOrders)): ?>
                                <tr>
                                    <td colspan="5" class="empty-state">Vous n'avez pas encore passé de commande</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentOrders as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['reference']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($order['date_commande'])); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($order['statut']); ?>">
                                                <?php echo $order['statut']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo number_format($order['total'], 2, ',', ' '); ?> €</td>
                                        <td>
                                            <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn-view">
                                                Détails
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
            
            <!-- Résumé du profil -->
            <section class="profile-summary">
                <div class="section-header">
                    <h2>Mes informations</h2>
                    <a href="profile.php" class="see-all">Modifier</a>
                </div>
                
                <div class="profile-info-card">
                    <div class="info-group">
                        <div class="info-item">
                            <span class="info-label">Nom complet</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Email</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Téléphone</span>
                            <span class="info-value"><?php echo !empty($user['telephone']) ? htmlspecialchars($user['telephone']) : 'Non renseigné'; ?></span>
                        </div>
                    </div>
                    
                    <div class="info-group">
                        <div class="info-item">
                            <span class="info-label">Adresse</span>
                            <span class="info-value">
                                <?php if (!empty($user['adresse'])): ?>
                                    <?php echo htmlspecialchars($user['adresse']); ?><br>
                                    <?php echo htmlspecialchars($user['code_postal'] . ' ' . $user['ville']); ?><br>
                                    <?php echo htmlspecialchars($user['pays']); ?>
                                <?php else: ?>
                                    Aucune adresse enregistrée
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
    
    <!-- Pied de page -->
    <?php include 'includes/footer.php'; ?>
    
    <script src="../assets/js/video-background.js"></script>
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/gestion-cart.js"></script>
    <script src="assets/js/dashboard.js"></script>
    <script src="../assets/js/gestion-cart.js"></script>
    <script src="../assets/js/product-filters.js"></script>
    <script src="../assets/js/quick-view.js"></script>
</body>
</html>