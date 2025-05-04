<?php
// Initialisation de la session et vérification d'authentification admin
session_start();

// Corriger les chemins d'inclusion pour pointer vers les bons dossiers
require_once '../../php/config/database.php';  // Remontez de deux niveaux
require_once '../../php/utils/auth.php';       // Remontez de deux niveaux
require_once '../php/models/Category.php';     // Ce fichier est dans public/php/models
require_once '../../php/utils/Logger.php';     // Remontez de deux niveaux

// Redirection si l'utilisateur n'est pas connecté en tant qu'admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: /Site-Vitrine/public/pages/auth/login.php?redirect=admin/categories');
    exit;
}

// Initialiser le modèle de catégorie et le logger
$categoryModel = new Category($pdo);
$logger = new Logger($pdo);

// Gestion de la pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Gestion de la recherche
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Récupérer le nombre total de catégories pour la pagination
$totalCategories = $categoryModel->getTotalCount($search);
$totalPages = ceil($totalCategories / $perPage);

// Récupérer les catégories avec pagination
$categories = $categoryModel->getAllCategories($perPage, $offset, $search);

// Récupération des catégories parentes pour le formulaire
$parentCategories = $categoryModel->getParentCategories();

// Initialiser les variables pour le formulaire
$editCategory = null;
$errors = [];

// Traitement de la suppression
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Vérifier s'il y a des produits associés à cette catégorie
    $productsCount = $categoryModel->getProductsCount($id);
    
    if ($productsCount > 0) {
        $_SESSION['message'] = "Impossible de supprimer cette catégorie car elle contient $productsCount produit(s)";
        $_SESSION['message_type'] = 'danger';
    } else {
        // Vérifier s'il y a des sous-catégories
        $subcategoriesCount = $categoryModel->getSubcategoriesCount($id);
        
        if ($subcategoriesCount > 0) {
            $_SESSION['message'] = "Impossible de supprimer cette catégorie car elle contient $subcategoriesCount sous-catégorie(s)";
            $_SESSION['message_type'] = 'danger';
        } else {
            // Récupérer le nom de la catégorie avant suppression pour le log
            $categoryInfo = $categoryModel->getCategoryById($id);
            
            $success = $categoryModel->deleteCategory($id);
            
            if ($success) {
                // Logger l'activité
                $logger->info('admin', 'delete_category', [
                    'category_id' => $id,
                    'details' => "Suppression de la catégorie: " . ($categoryInfo['nom'] ?? 'ID: '.$id)
                ]);
                
                $_SESSION['message'] = "Catégorie supprimée avec succès";
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = "Erreur lors de la suppression de la catégorie";
                $_SESSION['message_type'] = 'danger';
            }
        }
    }
    
    // Rediriger pour mettre à jour la liste
    header('Location: categories.php');
    exit;
}

// Récupérer une catégorie à éditer si demandé
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $editId = (int)$_GET['id'];
    $editCategory = $categoryModel->getCategoryById($editId);
    
    if (!$editCategory) {
        $_SESSION['message'] = "Catégorie non trouvée";
        $_SESSION['message_type'] = 'danger';
        header('Location: categories.php');
        exit;
    }
}

// Traitement du formulaire d'ajout/modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nom = trim($_POST['nom'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
    $position = isset($_POST['position']) ? (int)$_POST['position'] : 0;
    
    // Générer un slug à partir du nom si non fourni
    if (empty($slug)) {
        $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $nom), '-'));
    }
    
    // Valider les données
    if (empty($nom)) {
        $errors['nom'] = "Le nom de la catégorie est requis";
    }
    
    if (strlen($nom) > 100) {
        $errors['nom'] = "Le nom ne peut pas dépasser 100 caractères";
    }
    
    if (strlen($slug) > 100) {
        $errors['slug'] = "Le slug ne peut pas dépasser 100 caractères";
    }
    
    // Vérifier l'unicité du slug (sauf pour la même catégorie en mode édition)
    $slugExists = $categoryModel->slugExists($slug, $id);
    if ($slugExists) {
        $errors['slug'] = "Ce slug est déjà utilisé par une autre catégorie";
    }
    
    // Vérifier que parent_id n'est pas égal à l'id (une catégorie ne peut pas être son propre parent)
    if ($id > 0 && $parent_id === $id) {
        $errors['parent_id'] = "Une catégorie ne peut pas être son propre parent";
    }
    
    // Traitement de l'image si fournie
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/Site-Vitrine/public/uploads/categories/';
        
        // Créer le répertoire s'il n'existe pas
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            $errors['image'] = "Format d'image non supporté. Utilisez JPG, PNG, GIF ou WEBP.";
        } else {
            $newFileName = $slug . '-' . time() . '.' . $fileExtension;
            $uploadFile = $uploadDir . $newFileName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                $image = 'uploads/categories/' . $newFileName;
                
                // Si on édite une catégorie avec une image existante, supprimer l'ancienne image
                if ($id > 0 && !empty($editCategory['image'])) {
                    $oldImagePath = $_SERVER['DOCUMENT_ROOT'] . '/Site-Vitrine/public/' . $editCategory['image'];
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
            } else {
                $errors['image'] = "Erreur lors du téléchargement de l'image";
            }
        }
    } elseif ($id > 0 && isset($editCategory['image'])) {
        // Conserver l'image existante en mode édition
        $image = $editCategory['image'];
    }
    
    if (empty($errors)) {
        $categoryData = [
            'nom' => $nom,
            'slug' => $slug,
            'description' => $description,
            'parent_id' => $parent_id,
            'position' => $position
        ];
        
        if ($image !== null) {
            $categoryData['image'] = $image;
        }
        
        if ($id > 0) {
            // Mise à jour d'une catégorie existante
            $success = $categoryModel->updateCategory($id, $categoryData);
            if ($success) {
                // Logger l'activité
                $logger->info('admin', 'update_category', [
                    'category_id' => $id,
                    'details' => "Mise à jour de la catégorie: $nom"
                ]);
                
                $_SESSION['message'] = "Catégorie mise à jour avec succès";
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = "Erreur lors de la mise à jour de la catégorie";
                $_SESSION['message_type'] = 'danger';
            }
        } else {
            // Ajout d'une nouvelle catégorie
            $newId = $categoryModel->addCategory($categoryData);
            if ($newId) {
                // Logger l'activité
                $logger->info('admin', 'add_category', [
                    'category_id' => $newId,
                    'details' => "Ajout d'une nouvelle catégorie: $nom"
                ]);
                
                $_SESSION['message'] = "Catégorie ajoutée avec succès";
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = "Erreur lors de l'ajout de la catégorie";
                $_SESSION['message_type'] = 'danger';
            }
        }
        
        // Rediriger pour éviter la soumission multiple du formulaire
        header('Location: categories.php');
        exit;
    } else {
        // En cas d'erreur, récupérer la catégorie pour le formulaire
        if ($id > 0) {
            $editCategory = $categoryModel->getCategoryById($id);
        } else {
            // Conserver les valeurs saisies pour une nouvelle catégorie
            $editCategory = [
                'id' => 0,
                'nom' => $nom,
                'slug' => $slug,
                'description' => $description,
                'parent_id' => $parent_id,
                'position' => $position,
                'image' => $image
            ];
        }
    }
}

// Récupérer le message de session s'il existe
$message = $_SESSION['message'] ?? null;
$messageType = $_SESSION['message_type'] ?? 'info';

// Supprimer le message après l'avoir récupéré
if (isset($_SESSION['message'])) {
    unset($_SESSION['message'], $_SESSION['message_type']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des catégories - Administration</title>
    <link rel="shortcut icon" href="../assets/img/layout/icon2.png" type="image/x-icon">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="js/header.js" defer></script>
    
    <style>
        :root {
            --primary-color: #d4af37;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --primary-light: rgba(212, 175, 55, 0.1);
            --transition: all 0.3s ease;
        }
        
        /* Style pour la page de catégories */
        .categories-container {
            display: grid;
            grid-template-columns: 3fr 2fr;
            gap: 1.5rem;
        }
        
        @media (max-width: 1024px) {
            .categories-container {
                grid-template-columns: 1fr;
            }
        }
        
        .card {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background: linear-gradient(to right, #f8f9fa, #ffffff);
            padding: 1.25rem;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-header h2 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: #212529;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .card-header h2 i {
            color: var(--primary-color);
            font-size: 1.1rem;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .page-header h1 {
            margin: 0;
            font-size: 1.75rem;
            font-weight: 700;
            color: #212529;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .page-header h1 i {
            color: var(--primary-color);
            font-size: 1.5rem;
        }
        
        .search-form {
            margin-bottom: 1.5rem;
            display: flex;
            gap: 0.5rem;
        }
        
        .search-form input {
            flex-grow: 1;
            padding: 0.5rem 1rem;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            border: 1px solid transparent;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.9rem;
            line-height: 1.5;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            gap: 0.5rem;
            white-space: nowrap;
            text-decoration: none;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: #fff;
        }
        
        .btn-primary:hover {
            background-color: #c49e30;
            transform: translateY(-2px);
        }
        
        .btn-outline-secondary {
            background-color: transparent;
            border-color: #ced4da;
            color: var(--secondary-color);
        }
        
        .btn-outline-secondary:hover {
            background-color: #f8f9fa;
            border-color: var(--primary-color);
            color: var(--primary-color);
            transform: translateY(-2px);
        }
        
        .btn-outline-primary {
            background-color: transparent;
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-light);
            transform: translateY(-2px);
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            color: #fff;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            position: relative;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .alert-success {
            background-color: #d4edda;
            border-left: 4px solid var(--success-color);
            color: #155724;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border-left: 4px solid var(--danger-color);
            color: #721c24;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            border-left: 4px solid var(--info-color);
            color: #0c5460;
        }
        
        .alert-icon {
            font-size: 1.25rem;
        }
        
        .alert-content {
            flex: 1;
        }
        
        .alert-close {
            background: none;
            border: none;
            font-size: 1.25rem;
            cursor: pointer;
            color: inherit;
            opacity: 0.7;
            padding: 0;
            line-height: 1;
        }
        
        .alert-close:hover {
            opacity: 1;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th, 
        .data-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }
        
        .data-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        
        .data-table tbody tr:hover {
            background-color: var(--primary-light);
        }
        
        .data-table .image-cell {
            width: 60px;
        }
        
        .data-table .image-thumbnail {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .data-table .image-thumbnail img {
            max-width: 100%;
            max-height: 100%;
            border-radius: 4px;
        }
        
        .actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.25rem 0.5rem;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-success {
            background-color: rgba(40, 167, 69, 0.15);
            color: var(--success-color);
        }
        
        .badge-danger {
            background-color: rgba(220, 53, 69, 0.15);
            color: var(--danger-color);
        }
        
        .badge-info {
            background-color: rgba(23, 162, 184, 0.15);
            color: var(--info-color);
        }
        
        .badge-parent {
            background-color: rgba(108, 117, 125, 0.15);
            color: var(--secondary-color);
        }
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #495057;
        }
        
        .form-control {
            display: block;
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 1rem;
            line-height: 1.5;
            color: #495057;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }
        
        .form-text {
            display: block;
            margin-top: 0.25rem;
            font-size: 0.875rem;
            color: #6c757d;
        }
        
        .form-error {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875rem;
            color: var(--danger-color);
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .preview-image {
            max-width: 100%;
            max-height: 200px;
            margin-top: 0.5rem;
            border-radius: 4px;
            border: 1px solid #ced4da;
        }
        
        .current-image {
            display: flex;
            align-items: center;
            margin-top: 0.5rem;
            gap: 0.75rem;
        }
        
        .current-image img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ced4da;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }
        
        .pagination-item {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 0.5rem;
            border-radius: 4px;
            border: 1px solid #ced4da;
            background-color: #ffffff;
            color: #495057;
            text-decoration: none;
            transition: var(--transition);
        }
        
        .pagination-item:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            background-color: var(--primary-light);
        }
        
        .pagination-item.active {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: #ffffff;
        }
        
        .pagination-item.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        /* Ajout de style pour le formulaire d'upload d'image */
        .image-upload-container {
            margin-top: 0.5rem;
        }
        
        .form-file-input {
            position: relative;
        }
        
        .form-file-label {
            display: inline-block;
            padding: 0.5rem 1rem;
            border: 1px dashed #ced4da;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            background-color: #f8f9fa;
            transition: var(--transition);
            width: 100%;
        }
        
        .form-file-label:hover {
            border-color: var(--primary-color);
            background-color: var(--primary-light);
        }
        
        .form-file-label i {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }
        
        .form-file-input input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Utiliser le template de sidebar -->
        <?php 
        // Définit la racine relative pour les liens dans la sidebar
        $admin_root = '';
        include 'templates/sidebar.php'; 
        ?>

        <main class="main-content">
            <!-- Intégration du template header -->
            <?php 
            // Définir la racine relative pour les liens dans le header
            $admin_root = '';
            
            // Personnaliser la recherche pour la page catégories
            $search_placeholder = "Rechercher une catégorie...";
            $search_action = "categories.php";
            $search_param = "search";
            $search_value = $search;
            
            include 'templates/header.php'; 
            ?>

            <div class="content">
                <div class="page-header">
                    <h1><i class="fas fa-tags"></i> Gestion des catégories</h1>
                    <a href="categories.php" class="btn btn-outline-primary">
                        <i class="fas fa-plus"></i> Nouvelle catégorie
                    </a>
                </div>

                <!-- Message d'alerte (succès/erreur) -->
                <?php if ($message): ?>
                    <div class="alert alert-<?= $messageType ?>">
                        <div class="alert-icon">
                            <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : ($messageType === 'danger' ? 'exclamation-circle' : 'info-circle') ?>"></i>
                        </div>
                        <div class="alert-content"><?= $message ?></div>
                        <button type="button" class="alert-close">&times;</button>
                    </div>
                <?php endif; ?>

                <div class="categories-container">
                    <!-- Liste des catégories -->
                    <div class="card">
                        <div class="card-header">
                            <h2><i class="fas fa-list"></i> Liste des catégories</h2>
                        </div>
                        <div class="card-body">
                            <!-- Formulaire de recherche -->
                            <form action="categories.php" method="GET" class="search-form">
                                <input type="text" name="search" placeholder="Rechercher par nom ou description..." value="<?= htmlspecialchars($search) ?>">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Rechercher
                                </button>
                                <?php if (!empty($search)): ?>
                                    <a href="categories.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Effacer
                                    </a>
                                <?php endif; ?>
                            </form>

                            <!-- Tableau des catégories -->
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th class="image-cell">Image</th>
                                            <th>Nom</th>
                                            <th>Slug</th>
                                            <th>Parent</th>
                                            <th>Position</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($categories)): ?>
                                            <tr>
                                                <td colspan="6" class="text-center">Aucune catégorie trouvée</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($categories as $category): ?>
                                                <tr>
                                                    <td class="image-cell">
                                                        <div class="image-thumbnail">
                                                            <?php if (!empty($category['image'])): ?>
                                                                <img src="../<?= htmlspecialchars($category['image']) ?>" alt="<?= htmlspecialchars($category['nom']) ?>">
                                                            <?php else: ?>
                                                                <i class="fas fa-image text-muted"></i>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?= htmlspecialchars($category['nom']) ?>
                                                    </td>
                                                    <td>
                                                        <span class="text-muted"><?= htmlspecialchars($category['slug']) ?></span>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($category['parent_nom'])): ?>
                                                            <span class="badge badge-parent"><?= htmlspecialchars($category['parent_nom']) ?></span>
                                                        <?php else: ?>
                                                            <span class="badge badge-info">Principale</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?= $category['position'] ?>
                                                    </td>
                                                    <td>
                                                        <div class="actions">
                                                            <a href="categories.php?action=edit&id=<?= $category['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="#" class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $category['id'] ?>, '<?= htmlspecialchars(addslashes($category['nom'])) ?>')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if ($totalPages > 1): ?>
                                <div class="pagination">
                                    <?php if ($page > 1): ?>
                                        <a href="categories.php?page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="pagination-item">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="pagination-item disabled"><i class="fas fa-chevron-left"></i></span>
                                    <?php endif; ?>
                                    
                                    <?php 
                                    // Afficher un nombre limité de pages
                                    $startPage = max($page - 2, 1);
                                    $endPage = min($startPage + 4, $totalPages);
                                    
                                    // Ajuster startPage si nécessaire
                                    if ($endPage - $startPage < 4) {
                                        $startPage = max($endPage - 4, 1);
                                    }
                                    
                                    for ($i = $startPage; $i <= $endPage; $i++): 
                                    ?>
                                        <a href="categories.php?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                                           class="pagination-item <?= $i === $page ? 'active' : '' ?>">
                                            <?= $i ?>
                                        </a>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $totalPages): ?>
                                        <a href="categories.php?page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="pagination-item">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="pagination-item disabled"><i class="fas fa-chevron-right"></i></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Formulaire d'ajout/édition de catégorie -->
                    <div class="card">
                        <div class="card-header">
                            <h2>
                                <i class="fas fa-<?= $editCategory ? 'edit' : 'plus-circle' ?>"></i>
                                <?= $editCategory ? 'Modifier la catégorie' : 'Ajouter une catégorie' ?>
                            </h2>
                        </div>
                        <div class="card-body">
                            <form action="categories.php" method="POST" enctype="multipart/form-data">
                                <?php if ($editCategory): ?>
                                    <input type="hidden" name="id" value="<?= $editCategory['id'] ?>">
                                <?php endif; ?>
                                
                                <div class="form-group">
                                    <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                                    <input type="text" id="nom" name="nom" class="form-control" required 
                                           value="<?= htmlspecialchars($editCategory['nom'] ?? '') ?>">
                                    <?php if (isset($errors['nom'])): ?>
                                        <div class="form-error"><?= $errors['nom'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="slug" class="form-label">Slug</label>
                                    <input type="text" id="slug" name="slug" class="form-control" 
                                           value="<?= htmlspecialchars($editCategory['slug'] ?? '') ?>">
                                    <small class="form-text">Laissez vide pour générer automatiquement à partir du nom.</small>
                                    <?php if (isset($errors['slug'])): ?>
                                        <div class="form-error"><?= $errors['slug'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea id="description" name="description" class="form-control"><?= htmlspecialchars($editCategory['description'] ?? '') ?></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="parent_id" class="form-label">Catégorie parente</label>
                                    <select id="parent_id" name="parent_id" class="form-control">
                                        <option value="">-- Aucune (catégorie principale) --</option>
                                        <?php foreach ($parentCategories as $parent): ?>
                                            <?php if (!$editCategory || $parent['id'] !== $editCategory['id']): ?>
                                                <option value="<?= $parent['id'] ?>" <?= (isset($editCategory['parent_id']) && $editCategory['parent_id'] == $parent['id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($parent['nom']) ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['parent_id'])): ?>
                                        <div class="form-error"><?= $errors['parent_id'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="position" class="form-label">Position</label>
                                    <input type="number" id="position" name="position" class="form-control" min="0" 
                                           value="<?= htmlspecialchars($editCategory['position'] ?? '0') ?>">
                                    <small class="form-text">L'ordre d'affichage des catégories (0 = premier)</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="image" class="form-label">Image</label>
                                    
                                    <?php if (!empty($editCategory['image'])): ?>
                                        <div class="current-image">
                                            <img src="../<?= htmlspecialchars($editCategory['image']) ?>" alt="Image actuelle">
                                            <span>Image actuelle</span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="image-upload-container">
                                        <div class="form-file-input">
                                            <label for="image" class="form-file-label">
                                                <i class="fas fa-upload"></i> Choisir une image
                                            </label>
                                            <input type="file" id="image" name="image" accept="image/*">
                                        </div>
                                        <small class="form-text">Formats acceptés: JPG, JPEG, PNG, GIF, WEBP. Taille max: 2 Mo.</small>
                                    </div>
                                    
                                    <?php if (isset($errors['image'])): ?>
                                        <div class="form-error"><?= $errors['image'] ?></div>
                                    <?php endif; ?>
                                    
                                    <div id="image-preview-container" style="display: none;">
                                        <p>Aperçu:</p>
                                        <img id="image-preview" src="#" alt="Aperçu de l'image" class="preview-image">
                                    </div>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> <?= $editCategory ? 'Mettre à jour' : 'Ajouter' ?>
                                    </button>
                                    <a href="categories.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Annuler
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div class="modal" id="deleteModal" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Confirmer la suppression</h4>
                    <button type="button" class="close" onclick="closeModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer la catégorie <strong id="categoryName"></strong> ?</p>
                    <p class="text-danger">Cette action est irréversible.</p>
                </div>
                <div class="modal-footer">
                    <a href="#" id="confirmDelete" class="btn btn-danger">Supprimer</a>
                    <button type="button" class="btn btn-outline-secondary" onclick="closeModal()">Annuler</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Prévisualisation de l'image
        document.addEventListener('DOMContentLoaded', function() {
            // Prévisualisation de l'image
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('image-preview');
            const previewContainer = document.getElementById('image-preview-container');
            
            if (imageInput && imagePreview) {
                imageInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            imagePreview.src = e.target.result;
                            previewContainer.style.display = 'block';
                        };
                        
                        reader.readAsDataURL(this.files[0]);
                    } else {
                        previewContainer.style.display = 'none';
                    }
                });
            }
            
            // Auto-génération du slug à partir du nom
            const nomInput = document.getElementById('nom');
            const slugInput = document.getElementById('slug');
            
            if (nomInput && slugInput) {
                nomInput.addEventListener('input', function() {
                    if (!slugInput.value || slugInput.dataset.autogenerated === 'true') {
                        const slug = this.value
                            .normalize('NFD')
                            .replace(/[\u0300-\u036f]/g, '') // Supprime les accents
                            .toLowerCase()
                            .replace(/[^a-z0-9]+/g, '-')     // Remplace les caractères non alphanumériques par des tirets
                            .replace(/^-+|-+$/g, '');        // Supprime les tirets en début et fin
                        
                        slugInput.value = slug;
                        slugInput.dataset.autogenerated = 'true';
                    }
                });
                
                slugInput.addEventListener('input', function() {
                    // Si l'utilisateur modifie manuellement le slug, ne plus l'auto-générer
                    if (this.value) {
                        this.dataset.autogenerated = 'false';
                    } else {
                        this.dataset.autogenerated = 'true';
                    }
                });
            }
            
            // Fermeture des alertes
            const closeButtons = document.querySelectorAll('.alert-close');
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const alert = this.closest('.alert');
                    if (alert) {
                        alert.style.opacity = '0';
                        setTimeout(() => {
                            alert.remove();
                        }, 300);
                    }
                });
            });
        });
        
        // Affichage du modal de confirmation de suppression
        function confirmDelete(id, name) {
            const modal = document.getElementById('deleteModal');
            const categoryNameElement = document.getElementById('categoryName');
            const confirmDeleteButton = document.getElementById('confirmDelete');
            
            if (modal && categoryNameElement && confirmDeleteButton) {
                categoryNameElement.textContent = name;
                confirmDeleteButton.href = `categories.php?action=delete&id=${id}`;
                modal.style.display = 'block';
            }
        }
        
        // Fermeture du modal
        function closeModal() {
            const modal = document.getElementById('deleteModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }
        
        // Fermer le modal si on clique en dehors
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target === modal) {
                closeModal();
            }
        });
        
        // Style pour le modal
        document.head.insertAdjacentHTML('beforeend', `
            <style>
                .modal {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.5);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 1000;
                }
                
                .modal-dialog {
                    width: 100%;
                    max-width: 500px;
                    margin: 0 auto;
                }
                
                .modal-content {
                    background-color: #fff;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                    overflow: hidden;
                }
                
                .modal-header {
                    padding: 1rem 1.5rem;
                    border-bottom: 1px solid #e9ecef;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                }
                
                .modal-title {
                    margin: 0;
                    font-size: 1.25rem;
                    font-weight: 600;
                }
                
                .modal-body {
                    padding: 1.5rem;
                }
                
                .modal-footer {
                    padding: 1rem 1.5rem;
                    border-top: 1px solid #e9ecef;
                    display: flex;
                    justify-content: flex-end;
                    gap: 0.75rem;
                }
                
                .close {
                    background: none;
                    border: none;
                    font-size: 1.5rem;
                    cursor: pointer;
                    color: #6c757d;
                    transition: color 0.2s;
                }
                
                .close:hover {
                    color: #343a40;
                }
                
                .text-danger {
                    color: var(--danger-color);
                }
            </style>
        `);
    </script>
</body>
</html>