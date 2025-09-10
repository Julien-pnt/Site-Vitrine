<?php
require_once '../../../php/config/database.php';
require_once '../../../php/utils/auth.php';
require_once '../../../php/models/User.php';
require_once '../../../php/utils/UserValidator.php';
require_once '../../../php/utils/UserManager.php';
require_once '../../../php/utils/Logger.php';

// Vérification de l'authentification admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: /Site-Vitrine/public/pages/auth/login.php?redirect=admin/users/create');
    exit;
}

// Initialiser les objets
$userModel = new User($pdo);
$logger = new Logger($pdo);
$errors = [];

// Logger l'accès à la page
$logger->info('admin', 'access_user_create', [
    'details' => 'Accès au formulaire de création d\'utilisateur'
]);

// Créer un token CSRF pour le formulaire
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Invalid CSRF token');
    }
    
    // Récupérer les données du formulaire
    $userData = [
        'nom' => $_POST['nom'] ?? '',
        'prenom' => $_POST['prenom'] ?? '',
        'email' => $_POST['email'] ?? '',
        'mot_de_passe' => $_POST['mot_de_passe'] ?? '',
        'confirmer_mot_de_passe' => $_POST['confirmer_mot_de_passe'] ?? '',
        'telephone' => $_POST['telephone'] ?? '',
        'adresse' => $_POST['adresse'] ?? '',
        'code_postal' => $_POST['code_postal'] ?? '',
        'ville' => $_POST['ville'] ?? '',
        'pays' => $_POST['pays'] ?? 'France',
        'role' => $_POST['role'] ?? 'client',
        'actif' => isset($_POST['actif']) ? 1 : 0
    ];
    
    // Valider les données
    $validator = new UserValidator($userModel, $userData);
    
    if ($validator->validate()) {
        // Les données sont valides, créer l'utilisateur
        $userManager = new UserManager($pdo, $userModel);
        $userId = $userManager->createUser($userData);
        
        if ($userId) {
            // Rediriger vers la liste des utilisateurs avec un message de succès
            $_SESSION['message'] = "L'utilisateur a été créé avec succès.";
            $_SESSION['message_type'] = "success";
            header('Location: index.php');
            exit;
        } else {
            // Erreur lors de la création
            $errors['general'] = "Une erreur s'est produite lors de la création de l'utilisateur.";
        }
    } else {
        // Des erreurs de validation existent
        $errors = $validator->getErrors();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un utilisateur - Elixir du Temps</title>
    <link rel="icon" href="../../assets/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/users.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="../js/header.js" defer></script>
    
    <style>
        /* Styles améliorés pour le formulaire de création d'utilisateur */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"],
        .form-group input[type="tel"],
        .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            border-color: var(--gold-color, #d4af37);
            box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.15);
            outline: none;
        }
        
        .form-group.span-2 {
            grid-column: span 2;
        }
        
        .form-help {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        .form-error {
            color: #dc3545;
            font-size: 13px;
            margin-top: 5px;
        }
        
        .switch-container {
            display: flex;
            align-items: center;
            margin-top: 25px;
        }
        
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
            margin-right: 10px;
        }
        
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: var(--gold-color, #d4af37);
        }
        
        input:focus + .slider {
            box-shadow: 0 0 1px var(--gold-color, #d4af37);
        }
        
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        
        .form-actions {
            grid-column: span 2;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            border: none;
        }
        
        .btn-primary {
            background-color: var(--gold-color, #d4af37);
            color: #fff;
        }
        
        .btn-primary:hover {
            background-color: #c49e30;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: #fff;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }
        
        .btn-outline {
            background-color: transparent;
            color: #495057;
            border: 1px solid #ced4da;
        }

        .btn-outline:hover {
            background-color: #f8f9fa;
            color: var(--gold-color, #d4af37);
            border-color: var(--gold-color, #d4af37);
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            position: relative;
            animation: fadeIn 0.3s ease;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }
        
        .close-alert {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: inherit;
            opacity: 0.7;
        }
        
        .close-alert:hover {
            opacity: 1;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .card {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 25px;
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(to right, #f8f9fa, #ffffff);
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .card-header h2 {
            font-size: 1.5rem;
            margin: 0;
            font-weight: 600;
            color: #212529;
        }
        
        .card-body {
            padding: 25px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .page-header h1 {
            font-size: 1.8rem;
            color: #212529;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .page-header h1 i {
            color: var(--gold-color, #d4af37);
        }
        
        .page-actions {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Utiliser le template de sidebar -->
        <?php 
        // Définit la racine relative pour les liens dans la sidebar
        $admin_root = '../';
        include '../templates/sidebar.php'; 
        ?>

        <main class="main-content">
            <!-- Intégration du template header -->
            <?php 
            // Définir la racine relative pour les liens dans le header
            $admin_root = '../';
            
            // Personnaliser la recherche pour la page utilisateurs
            $search_placeholder = "Rechercher un utilisateur...";
            $search_action = "index.php";
            $search_param = "search";
            $search_value = isset($_GET['search']) ? $_GET['search'] : '';
            
            include '../templates/header.php'; 
            ?>

            <div class="content">
                <div class="page-header">
                    <h1><i class="fas fa-user-plus"></i> Créer un utilisateur</h1>
                    <div class="page-actions">
                        <a href="index.php" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                </div>
                
                <!-- Message d'erreur général -->
                <?php if (isset($errors['general'])): ?>
                    <div class="alert alert-danger">
                        <?= $errors['general'] ?>
                        <button type="button" class="close-alert">&times;</button>
                    </div>
                <?php endif; ?>
                
                <!-- Formulaire de création -->
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-id-card"></i> Informations de l'utilisateur</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" id="userForm">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <!-- Token CSRF -->
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            
                            <div class="form-grid">
                                <!-- Informations personnelles -->
                                <div class="form-group">
                                    <label for="prenom">Prénom <span class="text-danger">*</span></label>
                                    <input type="text" id="prenom" name="prenom" value="<?= isset($userData['prenom']) ? htmlspecialchars($userData['prenom']) : '' ?>" required>
                                    <?php if (isset($errors['prenom'])): ?>
                                        <div class="form-error"><?= $errors['prenom'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="nom">Nom <span class="text-danger">*</span></label>
                                    <input type="text" id="nom" name="nom" value="<?= isset($userData['nom']) ? htmlspecialchars($userData['nom']) : '' ?>" required>
                                    <?php if (isset($errors['nom'])): ?>
                                        <div class="form-error"><?= $errors['nom'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" id="email" name="email" value="<?= isset($userData['email']) ? htmlspecialchars($userData['email']) : '' ?>" required>
                                    <?php if (isset($errors['email'])): ?>
                                        <div class="form-error"><?= $errors['email'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="telephone">Téléphone</label>
                                    <input type="tel" id="telephone" name="telephone" value="<?= isset($userData['telephone']) ? htmlspecialchars($userData['telephone']) : '' ?>">
                                    <?php if (isset($errors['telephone'])): ?>
                                        <div class="form-error"><?= $errors['telephone'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Adresse -->
                                <div class="form-group span-2">
                                    <label for="adresse">Adresse</label>
                                    <input type="text" id="adresse" name="adresse" value="<?= isset($userData['adresse']) ? htmlspecialchars($userData['adresse']) : '' ?>">
                                    <?php if (isset($errors['adresse'])): ?>
                                        <div class="form-error"><?= $errors['adresse'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="code_postal">Code postal</label>
                                    <input type="text" id="code_postal" name="code_postal" value="<?= isset($userData['code_postal']) ? htmlspecialchars($userData['code_postal']) : '' ?>">
                                    <?php if (isset($errors['code_postal'])): ?>
                                        <div class="form-error"><?= $errors['code_postal'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="ville">Ville</label>
                                    <input type="text" id="ville" name="ville" value="<?= isset($userData['ville']) ? htmlspecialchars($userData['ville']) : '' ?>">
                                    <?php if (isset($errors['ville'])): ?>
                                        <div class="form-error"><?= $errors['ville'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="pays">Pays</label>
                                    <input type="text" id="pays" name="pays" value="<?= isset($userData['pays']) ? htmlspecialchars($userData['pays']) : 'France' ?>">
                                    <?php if (isset($errors['pays'])): ?>
                                        <div class="form-error"><?= $errors['pays'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="role">Rôle <span class="text-danger">*</span></label>
                                    <select id="role" name="role" required>
                                        <option value="client" <?= (isset($userData['role']) && $userData['role'] === 'client') ? 'selected' : '' ?>>Client</option>
                                        <option value="admin" <?= (isset($userData['role']) && $userData['role'] === 'admin') ? 'selected' : '' ?>>Administrateur</option>
                                    </select>
                                    <?php if (isset($errors['role'])): ?>
                                        <div class="form-error"><?= $errors['role'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Mot de passe -->
                                <div class="form-group">
                                    <label for="mot_de_passe">Mot de passe <span class="text-danger">*</span></label>
                                    <input type="password" id="mot_de_passe" name="mot_de_passe" required>
                                    <div class="form-help">8 caractères minimum, avec majuscule, minuscule et chiffre</div>
                                    <?php if (isset($errors['mot_de_passe'])): ?>
                                        <div class="form-error"><?= $errors['mot_de_passe'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="confirmer_mot_de_passe">Confirmer le mot de passe <span class="text-danger">*</span></label>
                                    <input type="password" id="confirmer_mot_de_passe" name="confirmer_mot_de_passe" required>
                                    <?php if (isset($errors['confirmer_mot_de_passe'])): ?>
                                        <div class="form-error"><?= $errors['confirmer_mot_de_passe'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group span-2">
                                    <div class="switch-container">
                                        <label class="switch">
                                            <input type="checkbox" name="actif" value="1" <?= (!isset($userData['actif']) || $userData['actif'] == 1) ? 'checked' : '' ?>>
                                            <span class="slider"></span>
                                        </label>
                                        <span>Utilisateur actif</span>
                                    </div>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Créer l'utilisateur
                                    </button>
                                    <a href="index.php" class="btn btn-outline">
                                        <i class="fas fa-times"></i> Annuler
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Script pour fermer les alertes
        document.addEventListener('DOMContentLoaded', function() {
            const closeButtons = document.querySelectorAll('.close-alert');
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const alert = this.closest('.alert');
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                });
            });
        });
    </script>
</body>
</html>