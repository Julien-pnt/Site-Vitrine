<?php
// Initialisation de la session et vérification d'authentification admin
session_start();
require_once '../../php/config/database.php';
require_once '../../php/utils/auth.php';

// Redirection si l'utilisateur n'est pas connecté en tant qu'admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: /public/php/api/auth/login.php?redirect=admin');
    exit;
}

// Définir le type de contenu en fonction du format demandé
$format = $_GET['format'] ?? 'csv';
$type = $_GET['type'] ?? 'products';

// Générer un nom de fichier avec date
$date = date('Y-m-d-His');
$filename = "{$type}-export-{$date}";

// Définir les en-têtes pour chaque type d'exportation
$headers = [
    'products' => ['ID', 'Référence', 'Nom', 'Description', 'Prix', 'Stock', 'Collection', 'Catégorie', 'Date création'],
    'orders' => ['ID', 'Référence', 'Client', 'Email', 'Date', 'Statut', 'Total', 'Méthode paiement'],
    'users' => ['ID', 'Prénom', 'Nom', 'Email', 'Rôle', 'Date inscription', 'Actif']
];

// Requêtes SQL pour chaque type d'exportation
$queries = [
    'products' => "SELECT p.id, p.reference, p.nom, p.description, p.prix, p.stock, 
                  c.nom as collection, cat.nom as categorie, p.date_creation 
                  FROM produits p 
                  LEFT JOIN collections c ON p.collection_id = c.id 
                  LEFT JOIN categories cat ON p.categorie_id = cat.id 
                  ORDER BY p.id",
    
    'orders' => "SELECT c.id, c.reference, CONCAT(u.prenom, ' ', u.nom) as client_name, 
                u.email, c.date_commande, c.statut, c.total, c.methode_paiement 
                FROM commandes c 
                JOIN utilisateurs u ON c.utilisateur_id = u.id 
                ORDER BY c.date_commande DESC",
    
    'users' => "SELECT id, prenom, nom, email, role, date_creation, 
               IF(actif = 1, 'Oui', 'Non') as actif 
               FROM utilisateurs 
               ORDER BY date_creation DESC"
];

// Application de filtres si présents dans l'URL
$whereClause = '';
$params = [];

if ($type === 'products' && isset($_GET['collection'])) {
    $whereClause .= " WHERE c.id = ?";
    $params[] = $_GET['collection'];
}

if ($type === 'orders' && isset($_GET['status'])) {
    $whereClause .= " WHERE c.statut = ?";
    $params[] = $_GET['status'];
}

if ($type === 'orders' && isset($_GET['date_start']) && isset($_GET['date_end'])) {
    $operator = empty($whereClause) ? " WHERE" : " AND";
    $whereClause .= "$operator c.date_commande BETWEEN ? AND ?";
    $params[] = $_GET['date_start'] . ' 00:00:00';
    $params[] = $_GET['date_end'] . ' 23:59:59';
}

// Modifier la requête avec les filtres si nécessaire
if (!empty($whereClause)) {
    $queries[$type] = str_replace("ORDER BY", $whereClause . " ORDER BY", $queries[$type]);
}

try {
    // Exécuter la requête
    $stmt = $pdo->prepare($queries[$type]);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Si demande d'aperçu, retourner au formulaire avec les données
    if (isset($_GET['preview'])) {
        $_SESSION['export_preview'] = [
            'data' => array_slice($data, 0, 10),
            'total' => count($data),
            'type' => $type
        ];
        header('Location: export.php');
        exit;
    }
    
    // Générer le fichier d'export
    if ($format === 'csv') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, $headers[$type]);
        
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    } 
    elseif ($format === 'excel') {
        // Nécessite l'extension PHP PhpSpreadsheet, vérifier si elle est installée
        if (!class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            die("L'extension PhpSpreadsheet n'est pas installée. Utilisez le format CSV ou installez l'extension.");
        }
        
        require '../../vendor/autoload.php';
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Ajouter les en-têtes
        $column = 1;
        foreach ($headers[$type] as $header) {
            $sheet->setCellValueByColumnAndRow($column++, 1, $header);
        }
        
        // Ajouter les données
        $row = 2;
        foreach ($data as $item) {
            $column = 1;
            foreach ($item as $value) {
                $sheet->setCellValueByColumnAndRow($column++, $row, $value);
            }
            $row++;
        }
        
        // Télécharger le fichier
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
} catch (PDOException $e) {
    die("Erreur lors de l'exportation des données: " . $e->getMessage());
}

// Si aucune exportation n'est demandée, afficher le formulaire
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exportation de données - Administration</title>
    <link rel="icon" href="../assets/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/tables.css">
    <!-- FontAwesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .export-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
        }
        .export-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .export-form .form-group {
            margin-bottom: 20px;
        }
        .export-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        .export-form select, .export-form input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .buttons-container {
            grid-column: span 2;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        .btn-preview, .btn-export {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-preview {
            background-color: #f0f0f0;
            color: #333;
        }
        .btn-export {
            background-color: #d4af37;
            color: white;
        }
        .preview-container {
            margin-top: 30px;
            overflow-x: auto;
        }
        .filter-section {
            grid-column: span 2;
            border-top: 1px solid #eee;
            padding-top: 20px;
            margin-top: 10px;
        }
        .filter-title {
            margin-bottom: 15px;
            font-weight: 600;
        }
        .filter-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar de navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="../assets/img/layout/logo.png" alt="Elixir du Temps" class="logo">
                <h2>Administration</h2>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
                    <li><a href="products.php"><i class="fas fa-watch"></i> Produits</a></li>
                    <li><a href="categories.php"><i class="fas fa-tags"></i> Catégories</a></li>
                    <li><a href="collections.php"><i class="fas fa-layer-group"></i> Collections</a></li>
                    <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Commandes</a></li>
                    <li><a href="users.php"><i class="fas fa-users"></i> Utilisateurs</a></li>
                    <li><a href="reviews.php"><i class="fas fa-star"></i> Avis Clients</a></li>
                    <li><a href="pages.php"><i class="fas fa-file-alt"></i> Pages</a></li>
                    <li class="active"><a href="export.php"><i class="fas fa-file-export"></i> Exportation</a></li>
                    <li><a href="settings.php"><i class="fas fa-cog"></i> Paramètres</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="../pages/Accueil.html" target="_blank"><i class="fas fa-external-link-alt"></i> Voir le site</a>
                <a href="../../php/api/auth/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </div>
        </aside>

        <!-- Contenu principal -->
        <main class="main-content">
            <header class="main-header">
                <div class="header-search">
                    <input type="search" placeholder="Rechercher...">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </div>
                <div class="header-user">
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                </div>
            </header>

            <div class="dashboard">
                <h1><i class="fas fa-file-export"></i> Exportation de données</h1>
                
                <div class="export-container">
                    <form class="export-form" method="GET" action="export.php">
                        <div class="form-group">
                            <label for="type">Type de données</label>
                            <select id="type" name="type" onchange="updateFilters()">
                                <option value="products">Produits</option>
                                <option value="orders">Commandes</option>
                                <option value="users">Utilisateurs</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="format">Format d'exportation</label>
                            <select id="format" name="format">
                                <option value="csv">CSV</option>
                                <option value="excel">Excel</option>
                            </select>
                        </div>
                        
                        <!-- Filtres dynamiques selon le type sélectionné -->
                        <div id="filter-section" class="filter-section">
                            <h3 class="filter-title">Filtres</h3>
                            <div class="filter-content">
                                <!-- Filtres pour les produits -->
                                <div id="product-filters" class="filter-group">
                                    <div class="form-group">
                                        <label for="collection">Collection</label>
                                        <select id="collection" name="collection">
                                            <option value="">Toutes les collections</option>
                                            <?php
                                            $collections = $pdo->query("SELECT id, nom FROM collections ORDER BY nom")->fetchAll();
                                            foreach ($collections as $collection) {
                                                echo "<option value='{$collection['id']}'>{$collection['nom']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="category">Catégorie</label>
                                        <select id="category" name="category">
                                            <option value="">Toutes les catégories</option>
                                            <?php
                                            $categories = $pdo->query("SELECT id, nom FROM categories ORDER BY nom")->fetchAll();
                                            foreach ($categories as $category) {
                                                echo "<option value='{$category['id']}'>{$category['nom']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Filtres pour les commandes -->
                                <div id="order-filters" class="filter-group" style="display:none;">
                                    <div class="form-group">
                                        <label for="status">Statut</label>
                                        <select id="status" name="status">
                                            <option value="">Tous les statuts</option>
                                            <option value="en_attente">En attente</option>
                                            <option value="payee">Payée</option>
                                            <option value="en_preparation">En préparation</option>
                                            <option value="expediee">Expédiée</option>
                                            <option value="livree">Livrée</option>
                                            <option value="annulee">Annulée</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="date_start">Date début</label>
                                        <input type="date" id="date_start" name="date_start">
                                    </div>
                                    <div class="form-group">
                                        <label for="date_end">Date fin</label>
                                        <input type="date" id="date_end" name="date_end">
                                    </div>
                                </div>
                                
                                <!-- Filtres pour les utilisateurs -->
                                <div id="user-filters" class="filter-group" style="display:none;">
                                    <div class="form-group">
                                        <label for="role">Rôle</label>
                                        <select id="role" name="role">
                                            <option value="">Tous les rôles</option>
                                            <option value="client">Client</option>
                                            <option value="admin">Administrateur</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="active">Statut</label>
                                        <select id="active" name="active">
                                            <option value="">Tous</option>
                                            <option value="1">Actifs</option>
                                            <option value="0">Inactifs</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="buttons-container">
                            <button type="submit" name="preview" value="1" class="btn-preview">Aperçu</button>
                            <button type="submit" class="btn-export">Exporter</button>
                        </div>
                    </form>

                    <!-- Aperçu des données -->
                    <?php if (isset($_SESSION['export_preview'])): ?>
                        <div class="preview-container">
                            <h3>Aperçu (<?= count($_SESSION['export_preview']['data']) ?> sur <?= $_SESSION['export_preview']['total'] ?> enregistrements)</h3>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <?php foreach ($headers[$_SESSION['export_preview']['type']] as $header): ?>
                                            <th><?= htmlspecialchars($header) ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($_SESSION['export_preview']['data'] as $row): ?>
                                        <tr>
                                            <?php foreach ($row as $value): ?>
                                                <td><?= htmlspecialchars($value) ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php unset($_SESSION['export_preview']); ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        function updateFilters() {
            const typeSelect = document.getElementById('type');
            const productFilters = document.getElementById('product-filters');
            const orderFilters = document.getElementById('order-filters');
            const userFilters = document.getElementById('user-filters');
            
            // Masquer tous les filtres
            productFilters.style.display = 'none';
            orderFilters.style.display = 'none';
            userFilters.style.display = 'none';
            
            // Afficher les filtres correspondants au type sélectionné
            if (typeSelect.value === 'products') {
                productFilters.style.display = 'block';
            } else if (typeSelect.value === 'orders') {
                orderFilters.style.display = 'block';
            } else if (typeSelect.value === 'users') {
                userFilters.style.display = 'block';
            }
        }
        
        // Initialiser les filtres au chargement de la page
        document.addEventListener('DOMContentLoaded', updateFilters);
    </script>
</body>
</html>