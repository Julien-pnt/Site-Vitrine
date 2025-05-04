<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/auth/login.php');
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

// Filtrage par statut
$statusFilter = '';
$params = [$userId];
if (isset($_GET['status']) && !empty($_GET['status'])) {
    $statusFilter = ' AND statut = ?';
    $params[] = $_GET['status'];
}

// Récupérer les commandes de l'utilisateur avec filtrage
$stmt = $conn->prepare("
    SELECT * FROM commandes 
    WHERE utilisateur_id = ? $statusFilter
    ORDER BY date_commande DESC
    LIMIT ? OFFSET ?
");
$params[] = $limit;
$params[] = $offset;
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Compter le nombre total de commandes pour la pagination (avec filtrage)
$stmt = $conn->prepare("SELECT COUNT(*) FROM commandes WHERE utilisateur_id = ? $statusFilter");
$stmt->execute(array_slice($params, 0, count($params) - 2));
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

// Pour le chemin relatif
$relativePath = "..";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes commandes | Elixir du Temps</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Raleway:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="../assets/img/layout/icon2.png" type="image/x-icon">
    
    <!-- Styles du tableau de bord -->
    <link rel="stylesheet" href="assets/css/dashboard-vars.css">
    <link rel="stylesheet" href="assets/css/sidebar.css">
    
    <style>
        /* Styles spécifiques à la page des commandes */
        .dashboard-header {
            margin-bottom: var(--spacing-xl);
            padding-bottom: var(--spacing-md);
            border-bottom: 1px solid var(--border-color);
        }
        
        .dashboard-header h1 {
            font-family: var(--font-primary);
            font-size: var(--font-size-xl);
            color: var(--secondary-color);
            margin-bottom: var(--spacing-xs);
            font-weight: 600;
        }
        
        .dashboard-header p {
            color: var(--medium-gray);
            font-size: var(--font-size-sm);
        }
        
        /* Filtres de commande */
        .orders-filter {
            margin-bottom: var(--spacing-lg);
            background-color: var(--light-color);
            padding: var(--spacing-lg);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
        }
        
        .filter-form {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            flex-wrap: wrap;
        }
        
        .form-group {
            flex: 1;
            min-width: 200px;
        }
        
        .form-select {
            width: 100%;
            padding: var(--spacing-md);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            background-color: var(--light-color);
            color: var(--dark-gray);
            font-family: var(--font-secondary);
            font-size: var(--font-size-sm);
            transition: var(--transition);
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23888' viewBox='0 0 16 16'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right var(--spacing-md) center;
            background-size: 12px;
        }
        
        .form-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.2);
        }
        
        .btn-filter {
            background-color: var(--primary-color);
            color: var(--light-color);
            border: none;
            padding: var(--spacing-md) var(--spacing-lg);
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
            min-width: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--spacing-xs);
        }
        
        .btn-filter:hover {
            background-color: var(--primary-dark);
            box-shadow: var(--shadow-sm);
        }
        
        /* Table des commandes */
        .orders-table-container {
            background-color: var(--light-color);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            border: 1px solid var(--border-color);
            margin-bottom: var(--spacing-xl);
        }
        
        .orders-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .orders-table th {
            background-color: var(--light-gray);
            text-align: left;
            padding: var(--spacing-md) var(--spacing-lg);
            color: var(--medium-gray);
            font-size: var(--font-size-sm);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border-color);
        }
        
        .orders-table tbody tr {
            transition: var(--transition);
        }
        
        .orders-table tbody tr:hover {
            background-color: var(--light-gray);
        }
        
        .orders-table td {
            padding: var(--spacing-lg);
            border-bottom: 1px solid var(--border-color);
            font-size: var(--font-size-sm);
        }
        
        .orders-table tr:last-child td {
            border-bottom: none;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: var(--spacing-xs) var(--spacing-md);
            border-radius: 30px;
            font-size: var(--font-size-xs);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .status-badge::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: var(--spacing-sm);
        }
        
        .status-en_attente {
            background-color: rgba(255, 152, 0, 0.1);
            color: var(--warning-color);
        }
        
        .status-en_attente::before {
            background-color: var(--warning-color);
        }
        
        .status-payee {
            background-color: rgba(33, 150, 243, 0.1);
            color: var(--info-color);
        }
        
        .status-payee::before {
            background-color: var(--info-color);
        }
        
        .status-en_preparation {
            background-color: rgba(156, 39, 176, 0.1);
            color: #9c27b0;
        }
        
        .status-en_preparation::before {
            background-color: #9c27b0;
        }
        
        .status-expediee {
            background-color: rgba(0, 150, 136, 0.1);
            color: #009688;
        }
        
        .status-expediee::before {
            background-color: #009688;
        }
        
        .status-livree {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
        }
        
        .status-livree::before {
            background-color: var(--success-color);
        }
        
        .status-annulee {
            background-color: rgba(244, 67, 54, 0.1);
            color: var(--danger-color);
        }
        
        .status-annulee::before {
            background-color: var(--danger-color);
        }
        
        .status-remboursee {
            background-color: rgba(96, 125, 139, 0.1);
            color: #607d8b;
        }
        
        .status-remboursee::before {
            background-color: #607d8b;
        }
        
        .btn-view {
            display: inline-flex;
            align-items: center;
            padding: var(--spacing-sm) var(--spacing-md);
            background-color: var(--light-gray);
            color: var(--dark-gray);
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-size: var(--font-size-xs);
            transition: var(--transition);
            border: 1px solid var(--border-color);
            font-weight: 500;
        }
        
        .btn-view i {
            margin-right: var(--spacing-xs);
        }
        
        .btn-view:hover {
            background-color: var(--primary-color);
            color: var(--light-color);
            border-color: var(--primary-color);
        }
        
        /* État vide */
        .empty-state-container {
            background-color: var(--light-color);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
        }
        
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: var(--spacing-xl) var(--spacing-lg);
            text-align: center;
        }
        
        .empty-icon {
            font-size: 4rem;
            color: var(--light-gray);
            margin-bottom: var(--spacing-lg);
            opacity: 0.5;
        }
        
        .empty-state h2 {
            font-family: var(--font-primary);
            font-size: var(--font-size-lg);
            color: var(--secondary-color);
            margin-bottom: var(--spacing-md);
        }
        
        .empty-state p {
            color: var(--medium-gray);
            margin-bottom: var(--spacing-lg);
        }
        
        .btn-primary {
            background: linear-gradient(to right, var(--primary-color), var(--primary-dark));
            color: var(--light-color);
            border: none;
            padding: var(--spacing-md) var(--spacing-xl);
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-size: var(--font-size-sm);
            transition: var(--transition);
            font-weight: 500;
            box-shadow: var(--shadow-sm);
            display: inline-flex;
            align-items: center;
        }
        
        .btn-primary:hover {
            background: linear-gradient(to right, var(--primary-dark), var(--secondary-color));
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }
        
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: var(--spacing-xs);
            margin-top: var(--spacing-xl);
        }
        
        .pagination-item {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: var(--light-color);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-size: var(--font-size-sm);
            color: var(--dark-gray);
            transition: var(--transition);
        }
        
        .pagination-item:hover {
            background-color: var(--light-gray);
            border-color: var(--medium-gray);
        }
        
        .pagination-item.active {
            background-color: var(--primary-color);
            color: var(--light-color);
            border-color: var(--primary-color);
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .orders-table {
                display: block;
                overflow-x: auto;
            }
        }
        
        @media (max-width: 768px) {
            .filter-form {
                flex-direction: column;
                align-items: stretch;
            }
            
            .form-group {
                width: 100%;
            }
            
            .btn-filter {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="dashboard-layout">
    <!-- Inclure la sidebar -->
    <?php include 'includes/sidebar.php'; ?>
    
    <!-- Main Content -->
    <main class="dashboard-content">
        <div class="dashboard-header">
            <h1>Mes commandes</h1>
            <p>Historique et suivi de vos commandes</p>
        </div>
        
        <?php if (empty($orders)): ?>
            <div class="empty-state-container">
                <div class="empty-state">
                    <i class="fas fa-shopping-bag empty-icon"></i>
                    <h2>Aucune commande trouvée</h2>
                    <p>Vous n'avez pas encore passé de commande<?php echo isset($_GET['status']) ? ' avec le statut sélectionné' : ''; ?>.</p>
                    <?php if (isset($_GET['status'])): ?>
                        <a href="orders.php" class="btn-primary">
                            <i class="fas fa-undo-alt"></i>&nbsp;Voir toutes mes commandes
                        </a>
                    <?php else: ?>
                        <a href="../pages/Montres.php" class="btn-primary">
                            <i class="fas fa-shopping-cart"></i>&nbsp;Découvrir nos montres
                        </a>
                    <?php endif; ?>
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
                    <button type="submit" class="btn-filter">
                        <i class="fas fa-filter"></i>
                        Filtrer
                    </button>
                    <?php if (isset($_GET['status']) && !empty($_GET['status'])): ?>
                        <a href="orders.php" class="btn-view">
                            <i class="fas fa-times"></i>
                            Réinitialiser
                        </a>
                    <?php endif; ?>
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
                                        <?php echo isset($statusTranslations[$order['statut']]) ? $statusTranslations[$order['statut']] : $order['statut']; ?>
                                    </span>
                                </td>
                                <td><strong><?php echo number_format($order['total'], 2, ',', ' '); ?> €</strong></td>
                                <td>
                                    <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn-view">
                                        <i class="fas fa-eye"></i>
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
                        <a href="?page=<?php echo $page - 1; ?><?php echo isset($_GET['status']) ? '&status='.$_GET['status'] : ''; ?>" class="pagination-item">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?php echo $i; ?><?php echo isset($_GET['status']) ? '&status='.$_GET['status'] : ''; ?>" class="pagination-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo isset($_GET['status']) ? '&status='.$_GET['status'] : ''; ?>" class="pagination-item">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Script pour faire fonctionner le filtre automatiquement
        const statusFilter = document.getElementById('status-filter');
        if (statusFilter) {
            statusFilter.addEventListener('change', function() {
                this.closest('form').submit();
            });
        }
    });
</script>

</body>
</html>