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
    <title>Créer un utilisateur - Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/users.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <!-- Include sidebar -->
        <?php include '../includes/sidebar.php'; ?>

        <main class="main-content">
            <!-- Include header -->
            <?php include '../includes/header.php'; ?>

            <div class="content">
                <div class="page-header">
                    <h1><i class="fas fa-user-plus"></i> Créer un utilisateur</h1>
                    <div class="page-actions">
                        <a href="index.php" class="btn btn-secondary">
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
                        <h2>Informations de l'utilisateur</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" id="userForm">
                            <!-- Token CSRF -->
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            
                            <!-- Inclure le formulaire commun -->
                            <?php 
                                $formMode = 'create';
                                $userData = $_POST ?? [
                                    'nom' => '',
                                    'prenom' => '',
                                    'email' => '',
                                    'telephone' => '',
                                    'adresse' => '',
                                    'code_postal' => '',
                                    'ville' => '',
                                    'pays' => 'France',
                                    'role' => 'client',
                                    'actif' => 1
                                ];
                                include '../includes/user-form.php'; 
                            ?>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Créer l'utilisateur
                                </button>
                                <a href="index.php" class="btn btn-secondary">Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../js/users.js"></script>
</body>
</html>