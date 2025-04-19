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

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Nombre de commandes par page
$offset = ($page - 1) * $limit;

// Récupérer les commandes de l'utilisateur
$stmt = $conn->prepare("
    SELECT * FROM commandes 
    WHERE utilisateur_id = ? 
    ORDER BY date_commande DESC
    LIMIT ? OFFSET ?
");
$stmt->execute([$userId, $limit, $offset]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Compter le nombre total de commandes pour la pagination
$stmt = $conn->prepare("SELECT COUNT(*) FROM commandes WHERE utilisateur_id = ?");
$stmt->execute([$userId]);
$totalOrders = $stmt->fetchColumn();
$totalPages = ceil($totalOrders / $limit);

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
$pageTitle = "Mes commandes";
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
                <h1>Mes commandes</h1>
                <p>Historique et suivi de vos commandes</p>
            </div>
            
            <?php if (empty($orders)): ?>
                <div class="empty-state-container">
                    <div class="empty-state">
                        <i class="fas fa-shopping-bag empty-icon"></i>
                        <h2>Aucune commande</h2>
                        <p>Vous n'avez pas encore passé de commande.</p>
                        <a href="../pages/products/Montres.html" class="btn-primary">Découvrir nos montres</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="orders-filter">
                    <form action="" method="GET" class="filter-form">
                        <div class="form-group">
                            <select name="status" id="status-filter" class="form-select">
                                <option value="">Tous les statuts</option>
                                <?php foreach ($statusTranslations as $key => $value): ?>
                                    <option value="<?php echo $key; ?>" <?php echo isset($_GET['status']) && $_GET['status'] === $key ? 'selected' : ''; ?>>
                                        <?php echo $value; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn-filter">Filtrer</button>
                    </form>
                </div>
                
                <div class="orders-table-container">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><strong>#<?php echo htmlspecialchars($order['reference']); ?></strong></td>
                                    <td><?php echo date('d/m/Y à H:i', strtotime($order['date_commande'])); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $order['statut']; ?>">
                                            <?php echo $statusTranslations[$order['statut']]; ?>
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
                        </tbody>
                    </table>
                </div>
                
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>" class="pagination-item">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>" class="pagination-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?>" class="pagination-item">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>
    
    <!-- Pied de page -->
    <?php include 'includes/footer.php'; ?>
    
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/video-background.js"></script>
    <script src="assets/js/dashboard.js"></script>
</body>
</html>