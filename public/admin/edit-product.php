<?php
// Initialisation de la session et vérification d'authentification admin
session_start();
require_once '../../php/config/database.php';
require_once '../../php/utils/auth.php';

// Redirection si l'utilisateur n'est pas connecté en tant qu'admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../pages/auth/login.php?redirect=admin');
    exit;
}

// Vérifier si un ID de produit est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: products.php?message=ID+de+produit+invalide&status=error');
    exit;
}

$productId = (int)$_GET['id'];

// Connexion à la base de données
try {
    $db = new Database();
    $pdo = $db->getConnection();
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

// Variables pour stocker les messages et les données du formulaire
$success_message = '';
$error_message = '';
$formData = [];
$productPages = [];
$productAttributes = [];

// Récupérer les informations du produit existant
try {
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        header('Location: products.php?message=Produit+non+trouvé&status=error');
        exit;
    }
    
    // Initialiser formData avec les données du produit
    $formData = $product;
    
    // Récupérer les pages associées au produit
    $stmt = $pdo->prepare("SELECT page_code FROM produit_pages WHERE produit_id = ?");
    $stmt->execute([$productId]);
    $productPages = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Récupérer les attributs du produit
    $stmt = $pdo->prepare("SELECT attribut_id, valeur FROM produit_attributs WHERE produit_id = ?");
    $stmt->execute([$productId]);
    $productAttributes = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
} catch (PDOException $e) {
    $error_message = "Erreur lors de la récupération des données du produit: " . $e->getMessage();
}

// Récupérer les catégories pour le menu déroulant
try {
    $stmt = $pdo->query("SELECT id, nom FROM categories ORDER BY nom");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Erreur lors de la récupération des catégories: " . $e->getMessage();
    $categories = [];
}

// Récupérer les collections pour le menu déroulant
try {
    $stmt = $pdo->query("SELECT id, nom FROM collections WHERE active = 1 ORDER BY nom");
    $collections = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Erreur lors de la récupération des collections: " . $e->getMessage();
    $collections = [];
}

// Récupérer les attributs disponibles
try {
    $stmt = $pdo->query("SELECT id, nom, type FROM attributs ORDER BY nom");
    $attributs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Erreur lors de la récupération des attributs: " . $e->getMessage();
    $attributs = [];
}

// Traitement du formulaire d'édition
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $formData = [
        'nom' => $_POST['nom'] ?? '',
        'reference' => $_POST['reference'] ?? '',
        'description' => $_POST['description'] ?? '',
        'description_courte' => $_POST['description_courte'] ?? '',
        'prix' => $_POST['prix'] ?? '',
        'prix_promo' => $_POST['prix_promo'] ?? null,
        'stock' => $_POST['stock'] ?? 0,
        'stock_alerte' => $_POST['stock_alerte'] ?? 5,
        'poids' => $_POST['poids'] ?? null,
        'categorie_id' => $_POST['categorie_id'] ?? null,
        'collection_id' => $_POST['collection_id'] ?? null,
        'visible' => isset($_POST['visible']),
        'featured' => isset($_POST['featured']),
        'nouveaute' => isset($_POST['nouveaute']),
    ];
    
    // Validation des données
    $errors = [];
    
    if (empty($formData['nom'])) {
        $errors[] = "Le nom du produit est obligatoire";
    }
    
    if (empty($formData['reference'])) {
        $errors[] = "La référence du produit est obligatoire";
    } else {
        // Vérifier si la référence existe déjà (sauf pour ce produit)
        $stmt = $pdo->prepare("SELECT id FROM produits WHERE reference = ? AND id != ?");
        $stmt->execute([$formData['reference'], $productId]);
        if ($stmt->fetch()) {
            $errors[] = "Cette référence existe déjà";
        }
    }
    
    if (empty($formData['prix']) || !is_numeric($formData['prix']) || $formData['prix'] <= 0) {
        $errors[] = "Le prix doit être un nombre positif";
    }
    
    if (!empty($formData['prix_promo']) && (!is_numeric($formData['prix_promo']) || $formData['prix_promo'] <= 0)) {
        $errors[] = "Le prix promotionnel doit être un nombre positif";
    }
    
    if (!is_numeric($formData['stock']) || $formData['stock'] < 0) {
        $errors[] = "Le stock doit être un nombre positif ou nul";
    }
    
    // Génération du slug à partir du nom si modifié
    if ($formData['nom'] !== $product['nom']) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $formData['nom'])));
        
        // Vérifier si le slug existe déjà (sauf pour ce produit)
        $stmt = $pdo->prepare("SELECT id FROM produits WHERE slug = ? AND id != ?");
        $stmt->execute([$slug, $productId]);
        if ($stmt->fetch()) {
            // Ajouter un nombre aléatoire au slug
            $slug .= '-' . rand(100, 999);
        }
    } else {
        $slug = $product['slug'];
    }
    
    // Traitement de l'image principale
    $image_filename = $product['image']; // Garder l'image existante par défaut
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/products/';
        
        // Créer le répertoire s'il n'existe pas
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $tmp_name = $_FILES['image']['tmp_name'];
        $name = basename($_FILES['image']['name']);
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        
        // Vérifier le type de fichier
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($extension, $allowed_extensions)) {
            $errors[] = "Format d'image non autorisé. Formats acceptés: " . implode(', ', $allowed_extensions);
        } else {
            // Créer un nom de fichier unique
            $image_filename = $slug . '-' . uniqid() . '.' . $extension;
            $destination = $upload_dir . $image_filename;
            
            if (!move_uploaded_file($tmp_name, $destination)) {
                $errors[] = "Erreur lors de l'upload de l'image principale";
            } else {
                // Supprimer l'ancienne image si elle existe et si c'est une nouvelle image
                if (!empty($product['image']) && file_exists($upload_dir . $product['image']) && $product['image'] !== $image_filename) {
                    unlink($upload_dir . $product['image']);
                }
            }
        }
    }
    
    // Traitement des images supplémentaires
    $additional_images = [];
    $current_additional_images = !empty($product['images_supplementaires']) ? 
                                json_decode($product['images_supplementaires'], true) : [];
    
    // Conserver les images existantes sélectionnées
    if (isset($_POST['existing_images']) && is_array($_POST['existing_images'])) {
        foreach ($_POST['existing_images'] as $existing_image) {
            if (in_array($existing_image, $current_additional_images)) {
                $additional_images[] = $existing_image;
            }
        }
    }
    
    // Traiter les nouvelles images
    if (isset($_FILES['images_supplementaires']) && is_array($_FILES['images_supplementaires']['name'])) {
        $upload_dir = '../uploads/products/';
        
        // Créer le répertoire s'il n'existe pas
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_count = count($_FILES['images_supplementaires']['name']);
        
        for ($i = 0; $i < $file_count; $i++) {
            if ($_FILES['images_supplementaires']['error'][$i] === UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['images_supplementaires']['tmp_name'][$i];
                $name = basename($_FILES['images_supplementaires']['name'][$i]);
                $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                
                // Vérifier le type de fichier
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (!in_array($extension, $allowed_extensions)) {
                    $errors[] = "Format d'image supplémentaire non autorisé: $name";
                } else {
                    // Créer un nom de fichier unique
                    $add_image_filename = $slug . '-' . uniqid() . '.' . $extension;
                    $destination = $upload_dir . $add_image_filename;
                    
                    if (move_uploaded_file($tmp_name, $destination)) {
                        $additional_images[] = $add_image_filename;
                    } else {
                        $errors[] = "Erreur lors de l'upload de l'image supplémentaire: $name";
                    }
                }
            }
        }
    }
    
    // Supprimer les images qui ne sont plus utilisées
    foreach ($current_additional_images as $old_image) {
        if (!in_array($old_image, $additional_images)) {
            $image_path = '../uploads/products/' . $old_image;
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
    }
    
    // S'il n'y a pas d'erreurs, mettre à jour le produit
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            // Préparation de la requête de mise à jour
            $sql = "UPDATE produits SET 
                nom = :nom, 
                slug = :slug, 
                reference = :reference, 
                description = :description, 
                description_courte = :description_courte,
                prix = :prix, 
                prix_promo = :prix_promo, 
                image = :image, 
                images_supplementaires = :images_supplementaires, 
                stock = :stock, 
                stock_alerte = :stock_alerte, 
                poids = :poids, 
                categorie_id = :categorie_id, 
                collection_id = :collection_id, 
                visible = :visible, 
                featured = :featured, 
                nouveaute = :nouveaute,
                date_modification = NOW()
                WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            
            // Conversion des images supplémentaires en JSON
            $images_json = !empty($additional_images) ? json_encode($additional_images) : null;
            
            // Exécution de la requête avec les données
            $success = $stmt->execute([
                ':nom' => $formData['nom'],
                ':slug' => $slug,
                ':reference' => $formData['reference'],
                ':description' => $formData['description'],
                ':description_courte' => $formData['description_courte'],
                ':prix' => $formData['prix'],
                ':prix_promo' => $formData['prix_promo'] ?: null,
                ':image' => $image_filename,
                ':images_supplementaires' => $images_json,
                ':stock' => $formData['stock'],
                ':stock_alerte' => $formData['stock_alerte'],
                ':poids' => $formData['poids'] ?: null,
                ':categorie_id' => $formData['categorie_id'] ?: null,
                ':collection_id' => $formData['collection_id'] ?: null,
                ':visible' => $formData['visible'] ? 1 : 0,
                ':featured' => $formData['featured'] ? 1 : 0,
                ':nouveaute' => $formData['nouveaute'] ? 1 : 0,
                ':id' => $productId
            ]);
            
            if ($success) {
                // Supprimer les anciens attributs
                $stmt = $pdo->prepare("DELETE FROM produit_attributs WHERE produit_id = ?");
                $stmt->execute([$productId]);
                
                // Ajouter les nouveaux attributs
                if (isset($_POST['attributs']) && is_array($_POST['attributs'])) {
                    foreach ($_POST['attributs'] as $attribut_id => $valeur) {
                        if (!empty($valeur)) {
                            $stmt = $pdo->prepare("INSERT INTO produit_attributs (produit_id, attribut_id, valeur) VALUES (?, ?, ?)");
                            $stmt->execute([$productId, $attribut_id, $valeur]);
                        }
                    }
                }
                
                // Supprimer les anciennes associations de pages
                $stmt = $pdo->prepare("DELETE FROM produit_pages WHERE produit_id = ?");
                $stmt->execute([$productId]);
                
                // Ajouter les nouvelles associations de pages
                if (isset($_POST['pages']) && is_array($_POST['pages'])) {
                    foreach ($_POST['pages'] as $page_code) {
                        $stmt = $pdo->prepare("INSERT INTO produit_pages (produit_id, page_code) VALUES (?, ?)");
                        $stmt->execute([$productId, $page_code]);
                    }
                }
                
                // Confirmation et redirection
                $pdo->commit();
                $success_message = "Le produit a été mis à jour avec succès";
                
                // Journalisation de l'action
                $stmt = $pdo->prepare("INSERT INTO admin_logs (utilisateur_id, action, ip_address, details) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $_SESSION['user_id'], 
                    'update_product',
                    $_SERVER['REMOTE_ADDR'],
                    "Mise à jour du produit: " . $formData['nom'] . " (ID: " . $productId . ")"
                ]);
                
                // Mettre à jour les variables pour afficher les données à jour
                $stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ?");
                $stmt->execute([$productId]);
                $formData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Récupérer les pages mises à jour
                $stmt = $pdo->prepare("SELECT page_code FROM produit_pages WHERE produit_id = ?");
                $stmt->execute([$productId]);
                $productPages = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                // Récupérer les attributs mis à jour
                $stmt = $pdo->prepare("SELECT attribut_id, valeur FROM produit_attributs WHERE produit_id = ?");
                $stmt->execute([$productId]);
                $productAttributes = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            } else {
                try {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }
                } catch (Exception $rollbackException) {
                    // Ignorer les erreurs potentielles lors du rollback
                }
                $error_message = "Erreur lors de la mise à jour du produit";
            }
        } catch (PDOException $e) {
            try {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
            } catch (Exception $rollbackException) {
                // Ignorer les erreurs potentielles lors du rollback
            }
            $error_message = "Erreur de base de données: " . $e->getMessage();
        }
    } else {
        $error_message = "Veuillez corriger les erreurs suivantes:<br>" . implode("<br>", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un produit - Administration</title>
    <link rel="icon" href="../assets/img/layout/jb3.jpg" type="image/x-icon">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/header.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="js/header.js" defer></script>
    <!-- Editor WYSIWYG -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

    <style>
        .form-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .form-title {
            margin-top: 0;
            margin-bottom: 20px;
            color: #333;
            font-size: 1.5rem;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .required-field::after {
            content: ' *';
            color: #dc3545;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .checkbox-group input[type="checkbox"] {
            margin-right: 10px;
        }
        
        .image-preview {
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .preview-item {
            position: relative;
            width: 100px;
            height: 100px;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .remove-image {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            font-size: 12px;
            color: #dc3545;
        }
        
        .btn-submit {
            background-color: #d4af37;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .btn-submit:hover {
            background-color: #c09c2c;
        }
        
        .form-footer {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-col {
            flex: 1;
        }

        .editor-container {
            height: 300px;
            margin-bottom: 20px;
        }

        .ql-editor {
            min-height: 200px;
        }

        .attributes-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .attribute-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px dashed #eee;
        }

        .attribute-item:last-child {
            border-bottom: none;
        }
        
        .existing-images {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .existing-image-item {
            position: relative;
            width: 120px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
            background: #f9f9f9;
        }
        
        .existing-image-item img {
            width: 100%;
            height: 110px;
            object-fit: cover;
            border-radius: 3px;
            display: block;
        }
        
        .image-checkbox {
            margin-top: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .image-checkbox input {
            margin-right: 5px;
        }
        
        .btn-return {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-return:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <?php 
            // Définit la racine relative pour les liens dans la sidebar
            $admin_root = '';
            include 'templates/sidebar.php'; 
            ?>
        </aside>

        <main class="main-content">
            <!-- Intégration du template header -->
            <?php 
            // Définir la racine relative pour les liens dans le header
            $admin_root = '';
            
            // Personnaliser la recherche pour la page dashboard
            $search_placeholder = "Rechercher un produit...";
            $search_action = "products.php";
            $search_param = "search";
            $search_value = '';
            
            include 'templates/header.php'; 
            ?>

            <div class="dashboard">
                <h1>Modifier le produit: <?php echo htmlspecialchars($formData['nom']); ?></h1>
                
                <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
                <?php endif; ?>
                
                <form action="edit-product.php?id=<?php echo $productId; ?>" method="post" enctype="multipart/form-data">
                    <div class="form-container">
                        <h2 class="form-title">Informations générales</h2>
                        
                        <div class="form-row">
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="nom" class="required-field">Nom du produit</label>
                                    <input type="text" id="nom" name="nom" class="form-control" value="<?php echo htmlspecialchars($formData['nom']); ?>" required>
                                    <small class="form-text">Le nom complet du produit tel qu'il apparaîtra sur le site</small>
                                </div>
                            </div>
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="reference" class="required-field">Référence</label>
                                    <input type="text" id="reference" name="reference" class="form-control" value="<?php echo htmlspecialchars($formData['reference']); ?>" required>
                                    <small class="form-text">Code unique pour identifier le produit (ex: EDT-2023-001)</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="description_courte">Description courte</label>
                            <textarea id="description_courte" name="description_courte" class="form-control" rows="3"><?php echo htmlspecialchars($formData['description_courte']); ?></textarea>
                            <small class="form-text">Résumé concis du produit (max 500 caractères)</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description complète</label>
                            <div id="editor-container" class="editor-container"></div>
                            <input type="hidden" name="description" id="description" value="<?php echo htmlspecialchars($formData['description']); ?>">
                            <small class="form-text">Description détaillée avec formatage</small>
                        </div>
                    </div>
                    
                    <div class="form-container">
                        <h2 class="form-title">Prix et stock</h2>
                        
                        <div class="form-row">
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="prix" class="required-field">Prix (€)</label>
                                    <input type="number" id="prix" name="prix" class="form-control" step="0.01" min="0" value="<?php echo htmlspecialchars($formData['prix']); ?>" required>
                                </div>
                            </div>
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="prix_promo">Prix promotionnel (€)</label>
                                    <input type="number" id="prix_promo" name="prix_promo" class="form-control" step="0.01" min="0" value="<?php echo htmlspecialchars($formData['prix_promo'] ?? ''); ?>">
                                    <small class="form-text">Laissez vide si pas de promotion</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="stock">Stock disponible</label>
                                    <input type="number" id="stock" name="stock" class="form-control" min="0" value="<?php echo htmlspecialchars($formData['stock']); ?>">
                                </div>
                            </div>
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="stock_alerte">Seuil d'alerte stock</label>
                                    <input type="number" id="stock_alerte" name="stock_alerte" class="form-control" min="0" value="<?php echo htmlspecialchars($formData['stock_alerte']); ?>">
                                    <small class="form-text">Recevoir une alerte quand le stock passe sous ce seuil</small>
                                </div>
                            </div>
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="poids">Poids (g)</label>
                                    <input type="number" id="poids" name="poids" class="form-control" step="0.001" min="0" value="<?php echo htmlspecialchars($formData['poids'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-container">
                        <h2 class="form-title">Catégorisation</h2>
                        
                        <div class="form-row">
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="categorie_id">Catégorie</label>
                                    <select id="categorie_id" name="categorie_id" class="form-control">
                                        <option value="">-- Sélectionner une catégorie --</option>
                                        <?php foreach ($categories as $categorie): ?>
                                            <option value="<?php echo $categorie['id']; ?>" <?php echo ($formData['categorie_id'] == $categorie['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($categorie['nom']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="collection_id">Collection</label>
                                    <select id="collection_id" name="collection_id" class="form-control">
                                        <option value="">-- Sélectionner une collection --</option>
                                        <?php foreach ($collections as $collection): ?>
                                            <option value="<?php echo $collection['id']; ?>" <?php echo ($formData['collection_id'] == $collection['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($collection['nom']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="checkbox-group">
                                <input type="checkbox" id="visible" name="visible" <?php echo $formData['visible'] ? 'checked' : ''; ?>>
                                <label for="visible">Produit visible sur le site</label>
                            </div>
                            
                            <div class="checkbox-group">
                                <input type="checkbox" id="featured" name="featured" <?php echo $formData['featured'] ? 'checked' : ''; ?>>
                                <label for="featured">Produit mis en avant (featured)</label>
                            </div>
                            
                            <div class="checkbox-group">
                                <input type="checkbox" id="nouveaute" name="nouveaute" <?php echo $formData['nouveaute'] ? 'checked' : ''; ?>>
                                <label for="nouveaute">Marquer comme nouveauté</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-container">
                        <h2 class="form-title">Affichage du produit</h2>
                        
                        <p class="form-hint">Sélectionnez les pages où ce produit doit apparaître:</p>
                        
                        <div class="form-group display-options">
                            <div class="checkbox-group">
                                <input type="checkbox" id="display_home" name="pages[]" value="accueil" <?php echo in_array('accueil', $productPages) ? 'checked' : ''; ?>>
                                <label for="display_home">Page d'accueil</label>
                            </div>
                            
                            <div class="checkbox-group">
                                <input type="checkbox" id="display_classic" name="pages[]" value="collection_classic" <?php echo in_array('collection_classic', $productPages) ? 'checked' : ''; ?>>
                                <label for="display_classic">Collection Classic</label>
                            </div>
                            
                            <div class="checkbox-group">
                                <input type="checkbox" id="display_sport" name="pages[]" value="collection_sport" <?php echo in_array('collection_sport', $productPages) ? 'checked' : ''; ?>>
                                <label for="display_sport">Collection Sport</label>
                            </div>
                            
                            <div class="checkbox-group">
                                <input type="checkbox" id="display_prestige" name="pages[]" value="collection_prestige" <?php echo in_array('collection_prestige', $productPages) ? 'checked' : ''; ?>>
                                <label for="display_prestige">Collection Prestige</label>
                            </div>
                            
                            <div class="checkbox-group">
                                <input type="checkbox" id="display_limited" name="pages[]" value="collection_limited" <?php echo in_array('collection_limited', $productPages) ? 'checked' : ''; ?>>
                                <label for="display_limited">Édition Limitée</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-container">
                        <h2 class="form-title">Images du produit</h2>
                        
                        <div class="form-group">
                            <label for="image">Image principale</label>
                            <?php if(!empty($formData['image'])): ?>
                                <div class="current-main-image">
                                    <p>Image actuelle:</p>
                                    <div class="preview-item">
                                        <img src="../uploads/products/<?php echo htmlspecialchars($formData['image']); ?>" alt="Image principale">
                                    </div>
                                </div>
                            <?php endif; ?>
                            <p>Changer l'image principale (laissez vide pour conserver l'image actuelle):</p>
                            <input type="file" id="image" name="image" class="form-control" accept="image/*">
                            <div id="main-image-preview" class="image-preview"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="images_supplementaires">Images supplémentaires</label>
                            
                            <?php 
                            $additionalImages = !empty($formData['images_supplementaires']) ? 
                                              json_decode($formData['images_supplementaires'], true) : [];
                            if (!empty($additionalImages)): 
                            ?>
                                <div class="existing-images">
                                    <p>Images supplémentaires actuelles (décochez pour supprimer):</p>
                                    <?php foreach ($additionalImages as $index => $image): ?>
                                        <div class="existing-image-item">
                                            <img src="../uploads/products/<?php echo htmlspecialchars($image); ?>" alt="Image supplémentaire <?php echo $index + 1; ?>">
                                            <div class="image-checkbox">
                                                <input type="checkbox" id="existing_image_<?php echo $index; ?>" name="existing_images[]" value="<?php echo htmlspecialchars($image); ?>" checked>
                                                <label for="existing_image_<?php echo $index; ?>">Conserver</label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <p>Ajouter de nouvelles images supplémentaires:</p>
                            <input type="file" id="images_supplementaires" name="images_supplementaires[]" class="form-control" accept="image/*" multiple>
                            <small class="form-text">Vous pouvez sélectionner plusieurs images (max 5 au total)</small>
                            <div id="additional-images-preview" class="image-preview"></div>
                        </div>
                    </div>
                    
                    <?php if (!empty($attributs)): ?>
                    <div class="form-container attributes-section">
                        <h2 class="form-title">Attributs spécifiques</h2>
                        <p>Caractéristiques techniques spécifiques au produit</p>
                        
                        <?php foreach ($attributs as $attribut): ?>
                        <div class="attribute-item">
                            <div class="form-group">
                                <label for="attribut_<?php echo $attribut['id']; ?>"><?php echo htmlspecialchars($attribut['nom']); ?></label>
                                
                                <?php 
                                $currentValue = $productAttributes[$attribut['id']] ?? '';
                                
                                if ($attribut['type'] === 'texte'): ?>
                                <input type="text" id="attribut_<?php echo $attribut['id']; ?>" 
                                       name="attributs[<?php echo $attribut['id']; ?>]" 
                                       class="form-control" 
                                       value="<?php echo htmlspecialchars($currentValue); ?>">
                                
                                <?php elseif ($attribut['type'] === 'nombre'): ?>
                                <input type="number" id="attribut_<?php echo $attribut['id']; ?>" 
                                       name="attributs[<?php echo $attribut['id']; ?>]" 
                                       class="form-control" 
                                       step="any" 
                                       value="<?php echo htmlspecialchars($currentValue); ?>">
                                
                                <?php elseif ($attribut['type'] === 'booleen'): ?>
                                <select id="attribut_<?php echo $attribut['id']; ?>" 
                                        name="attributs[<?php echo $attribut['id']; ?>]" 
                                        class="form-control">
                                    <option value="">-- Sélectionner --</option>
                                    <option value="1" <?php echo $currentValue === '1' ? 'selected' : ''; ?>>Oui</option>
                                    <option value="0" <?php echo $currentValue === '0' ? 'selected' : ''; ?>>Non</option>
                                </select>
                                
                                <?php elseif ($attribut['type'] === 'liste'): ?>
                                <input type="text" id="attribut_<?php echo $attribut['id']; ?>" 
                                       name="attributs[<?php echo $attribut['id']; ?>]" 
                                       class="form-control" 
                                       placeholder="Valeurs séparées par des virgules" 
                                       value="<?php echo htmlspecialchars($currentValue); ?>">
                                
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-footer">
                        <a href="products.php" class="btn-return">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>
                        
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i> Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialisation de l'éditeur Quill
            var quill = new Quill('#editor-container', {
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'color': [] }, { 'background': [] }],
                        ['link', 'image'],
                        ['clean']
                    ]
                },
                placeholder: 'Saisissez une description détaillée du produit...',
                theme: 'snow'
            });
            
            // Charger le contenu initial s'il existe
            var initialContent = document.getElementById('description').value;
            if (initialContent) {
                quill.root.innerHTML = initialContent;
            }
            
            // Mise à jour du champ caché lors de la modification
            quill.on('text-change', function() {
                document.getElementById('description').value = quill.root.innerHTML;
            });
            
            // Prévisualisation de l'image principale
            var imageInput = document.getElementById('image');
            var mainImagePreview = document.getElementById('main-image-preview');
            
            imageInput.addEventListener('change', function() {
                mainImagePreview.innerHTML = '';
                
                if (this.files && this.files[0]) {
                    var reader = new FileReader();
                    
                    reader.onload = function(e) {
                        var previewItem = document.createElement('div');
                        previewItem.className = 'preview-item';
                        
                        var img = document.createElement('img');
                        img.src = e.target.result;
                        
                        var removeBtn = document.createElement('div');
                        removeBtn.className = 'remove-image';
                        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                        removeBtn.addEventListener('click', function() {
                            previewItem.remove();
                            imageInput.value = '';
                        });
                        
                        previewItem.appendChild(img);
                        previewItem.appendChild(removeBtn);
                        mainImagePreview.appendChild(previewItem);
                    };
                    
                    reader.readAsDataURL(this.files[0]);
                }
            });
            
            // Prévisualisation des images supplémentaires
            var additionalImagesInput = document.getElementById('images_supplementaires');
            var additionalImagesPreview = document.getElementById('additional-images-preview');
            
            additionalImagesInput.addEventListener('change', function() {
                additionalImagesPreview.innerHTML = '';
                
                if (this.files) {
                    var fileList = Array.from(this.files);
                    
                    // Limiter à 5 images max
                    if (fileList.length > 5) {
                        alert('Vous pouvez télécharger maximum 5 images supplémentaires.');
                        fileList = fileList.slice(0, 5);
                    }
                    
                    fileList.forEach(function(file) {
                        var reader = new FileReader();
                        
                        reader.onload = function(e) {
                            var previewItem = document.createElement('div');
                            previewItem.className = 'preview-item';
                            
                            var img = document.createElement('img');
                            img.src = e.target.result;
                            
                            previewItem.appendChild(img);
                            additionalImagesPreview.appendChild(previewItem);
                        };
                        
                        reader.readAsDataURL(file);
                    });
                }
            });
            
            // Animation de soumission du formulaire
            document.querySelector('form').addEventListener('submit', function() {
                document.querySelector('.btn-submit').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement en cours...';
                document.querySelector('.btn-submit').disabled = true;
                
                // Assurez-vous que le contenu de l'éditeur est bien enregistré
                document.getElementById('description').value = quill.root.innerHTML;
            });
        });
    </script>
</body>
</html>