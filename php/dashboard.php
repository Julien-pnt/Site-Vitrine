<?php
session_start();
require_once 'db.php';
require_once 'AuthService.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Récupérer les informations de l'utilisateur
$authService = new AuthService($pdo);
$user = $authService->getCurrentUser();

// Récupérer l'historique des commandes de l'utilisateur
$stmt = $pdo->prepare("
    SELECT c.id, c.date_commande, c.total, COUNT(ac.id) as nombre_articles
    FROM commandes c
    LEFT JOIN articles_commande ac ON c.id = ac.commande_id
    WHERE c.utilisateur_id = ?
    GROUP BY c.id
    ORDER BY c.date_commande DESC
");
$stmt->execute([$_SESSION['user_id']]);
$commandes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Compte - Elixir du Temps</title>
    <link rel="stylesheet" href="../css/Styles.css">
    <link rel="shortcut icon" href="../img/icon.png" type="image/x-icon">
    <style>
        .dashboard-container {
            display: flex;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: 2rem auto;
            gap: 2rem;
        }
        
        .dashboard-sidebar {
            flex: 1;
            min-width: 250px;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 1rem;
            padding: 1.5rem;
        }
        
        .dashboard-content {
            flex: 3;
            min-width: 300px;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 1rem;
            padding: 1.5rem;
        }
        
        .dashboard-sidebar h3 {
            margin-top: 0;
            color: #ffd700;
            border-bottom: 1px solid rgba(255, 215, 0, 0.3);
            padding-bottom: 0.5rem;
        }
        
        .dashboard-nav {
            list-style: none;
            padding: 0;
        }
        
        .dashboard-nav li {
            margin-bottom: 0.5rem;
        }
        
        .dashboard-nav a {
            display: block;
            padding: 0.75rem;
            color: #fff;
            text-decoration: none;
            border-radius: 0.5rem;
            transition: all 0.3s;
        }
        
        .dashboard-nav a:hover, .dashboard-nav a.active {
            background: rgba(255, 127, 80, 0.3);
            transform: translateX(5px);
        }
        
        .dashboard-nav a.active {
            background: rgba(255, 215, 0, 0.2);
            color: #ffd700;
        }
        
        .dashboard-nav a i {
            margin-right: 0.5rem;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .user-info img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-right: 1rem;
            object-fit: cover;
            border: 3px solid #ffd700;
        }
        
        .user-details h2 {
            margin: 0 0 0.25rem 0;
            color: #ffd700;
        }
        
        .user-details p {
            margin: 0;
            font-style: italic;
            opacity: 0.8;
        }
        
        .dashboard-section {
            margin-bottom: 2rem;
        }
        
        .dashboard-section h2 {
            color: #ffd700;
            border-bottom: 1px solid rgba(255, 215, 0, 0.3);
            padding-bottom: 0.5rem;
        }
        
        .commandes-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .commandes-table th {
            text-align: left;
            padding: 0.75rem;
            background: rgba(255, 127, 80, 0.3);
        }
        
        .commandes-table td {
            padding: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .commandes-table tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        
        .commandes-table .btn-details {
            background: rgba(255, 215, 0, 0.3);
            color: #ffd700;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .commandes-table .btn-details:hover {
            background: rgba(255, 215, 0, 0.5);
        }
        
        .empty-message {
            text-align: center;
            padding: 2rem;
            color: rgba(255, 255, 255, 0.6);
            font-style: italic;
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
        
        <!-- User Icon -->
        <div class="user-icon logged-in">
            <img id="profile-image" src="../img/user-logged.png" alt="Utilisateur">
            <div class="dropdown-menu">
                <a href="dashboard.php" class="active">Mon Compte</a>
                <a href="mes-commandes.php" id="orders-link">Mes Commandes</a>
                <a href="mes-favoris.php">Mes Favoris</a>
                <a href="mes-parametres.php">Paramètres</a>
                <a href="logout.php" id="logout-link" class="logout">Déconnexion</a>
            </div>
        </div>
    </header>

    <!-- Video Background -->
    <div class="video-background">
        <video class="video-bg" autoplay muted loop playsinline>
            <source src="../video/background.mp4" type="video/mp4">
            Votre navigateur ne supporte pas la vidéo.
        </video>
        <div class="video-overlay"></div>
    </div>

    <!-- Dashboard Section -->
    <div class="dashboard-container">
        <div class="dashboard-sidebar">
            <div class="user-info">
                <img src="../img/user-logged.png" alt="Photo de profil">
                <div class="user-details">
                    <h2><?php echo htmlspecialchars($user['nom']); ?></h2>
                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
            </div>
            
            <h3>Navigation</h3>
            <ul class="dashboard-nav">
                <li><a href="dashboard.php" class="active">Tableau de bord</a></li>
                <li><a href="mes-commandes.php">Mes commandes</a></li>
                <li><a href="mes-favoris.php">Mes favoris</a></li>
                <li><a href="modifier-profil.php">Modifier mon profil</a></li>
                <li><a href="changer-mot-de-passe.php">Changer de mot de passe</a></li>
                <li><a href="logout.php" class="logout-link">Déconnexion</a></li>
            </ul>
        </div>
        
        <div class="dashboard-content">
            <div class="dashboard-section">
                <h2>Tableau de bord</h2>
                <p>Bienvenue sur votre espace personnel, <?php echo htmlspecialchars($user['nom']); ?>!</p>
                <p>Depuis cet espace, vous pouvez suivre vos commandes, gérer vos favoris et modifier vos informations personnelles.</p>
            </div>
            
            <div class="dashboard-section">
                <h2>Vos commandes récentes</h2>
                <?php if (empty($commandes)): ?>
                    <div class="empty-message">Vous n'avez pas encore effectué de commande.</div>
                <?php else: ?>
                    <table class="commandes-table">
                        <thead>
                            <tr>
                                <th>N° de commande</th>
                                <th>Date</th>
                                <th>Articles</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($commandes as $commande): ?>
                            <tr>
                                <td>#<?php echo $commande['id']; ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($commande['date_commande'])); ?></td>
                                <td><?php echo $commande['nombre_articles']; ?> article(s)</td>
                                <td><?php echo number_format($commande['total'], 2, ',', ' '); ?> €</td>
                                <td><a href="detail-commande.php?id=<?php echo $commande['id']; ?>" class="btn-details">Détails</a></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            
            <div class="dashboard-section">
                <h2>Vos montres préférées</h2>
                <p>Découvrez nos dernières collections et ajoutez vos montres préférées à vos favoris pour les retrouver facilement.</p>
                <a href="../html/Collections.html" class="primary-btn">Voir les collections</a>
            </div>
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

            
