<?php
require_once '../../../php/config/database.php';
require_once '../../../php/utils/auth.php';
require_once '../../../php/models/User.php';
require_once '../../../php/utils/UserValidator.php';
require_once '../../../php/utils/UserManager.php';
require_once '../../../php/utils/Logger.php';

// Vérification de l'authentification admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: /Site-Vitrine/public/pages/auth/login.php?redirect=admin/users/edit');
    exit;
}

// Initialiser les objets
$userModel = new User($pdo);
$logger = new Logger($pdo);
$errors = [];

// Vérifier si l'ID est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = "ID utilisateur non valide.";
    $_SESSION['message_type'] = "error";
    header('Location: index.php');
    exit;
}

$userId = (int)$_GET['id'];

// Récupérer les données de l'utilisateur
$userData = $userModel->getUserById($userId);
if (!$userData) {
    $_SESSION['message'] = "Utilisateur non trouvé.";
    $_SESSION['message_type'] = "error";
    header('Location: index.php');
    exit;
}

// Logger l'accès à la page
$logger->info('admin', 'access_user_edit', [
    'details' => "Accès à l'édition de l'utilisateur #{$userId}",
    'entity_type' => 'user',
    'entity_id' => $userId
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
    
    // Récupération des données avant modification
    $oldUserData = $userModel->getUserById($userId);
    
    // Récupérer les données du formulaire
    $updatedUserData = [
        'id' => $userId,
        'nom' => $_POST['nom'] ?? '',
        'prenom' => $_POST['prenom'] ?? '',
        'email' => $_POST['email'] ?? '',
        'telephone' => $_POST['telephone'] ?? '',
        'adresse' => $_POST['adresse'] ?? '',
        'code_postal' => $_POST['code_postal'] ?? '',
        'ville' => $_POST['ville'] ?? '',
        'pays' => $_POST['pays'] ?? 'France',
        'role' => $_POST['role'] ?? 'client',
        'actif' => isset($_POST['actif']) ? 1 : 0
    ];
    
    // Gérer les mots de passe
    if (!empty($_POST['mot_de_passe'])) {
        $updatedUserData['mot_de_passe'] = $_POST['mot_de_passe'];
        $updatedUserData['confirmer_mot_de_passe'] = $_POST['confirmer_mot_de_passe'] ?? '';
    }
    
    // Valider les données
    $validator = new UserValidator($userModel, $updatedUserData, true); // true = mode édition
    
    if ($validator->validate()) {
        // Les données sont valides, mettre à jour l'utilisateur
        $userManager = new UserManager($pdo, $userModel);
        $success = $userManager->updateUser($updatedUserData);
        
        if ($success) {
            // Logger la modification
            $logDetails = [];
            foreach ($updatedUserData as $key => $value) {
                if ($key === 'mot_de_passe' || $key === 'confirmer_mot_de_passe') continue;
                
                if (isset($oldUserData[$key]) && $oldUserData[$key] != $value) {
                    $logDetails[$key] = [
                        'old' => $oldUserData[$key],
                        'new' => $value
                    ];
                }
            }
            
            $logger->logChanges(
                'user',
                $userId,
                $oldUserData,
                $updatedUserData,
                'update',
                "Utilisateur #{$userId} modifié par l'admin #{$_SESSION['user_id']}"
            );
            
            // Rediriger vers la liste des utilisateurs avec un message de succès
            $_SESSION['message'] = "L'utilisateur a été modifié avec succès.";
            $_SESSION['message_type'] = "success";
            header('Location: index.php');
            exit;
        } else {
            // Erreur lors de la mise à jour
            $errors['general'] = "Une erreur s'est produite lors de la mise à jour de l'utilisateur.";
        }
    } else {
        // Des erreurs de validation existent
        $errors = $validator->getErrors();
    }
    
    // En cas d'erreur, conserver les données du formulaire
    $userData = $updatedUserData;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un utilisateur - Admin</title>
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
                    <h1>
                        <i class="fas fa-user-edit"></i> 
                        Modifier l'utilisateur: <?= htmlspecialchars($userData['prenom'] . ' ' . $userData['nom']) ?>
                    </h1>
                    <div class="page-actions">
                        <a href="view.php?id=<?= $userId ?>" class="btn btn-info">
                            <i class="fas fa-eye"></i> Voir le profil
                        </a>
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
                
                <!-- Formulaire de modification -->
                <div class="card">
                    <div class="card-header">
                        <h2>Informations de l'utilisateur</h2>
                        <?php if ($userData['date_modification']): ?>
                            <span class="text-muted">
                                Dernière modification: <?= date('d/m/Y à H:i', strtotime($userData['date_modification'])) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" id="userForm">
                            <!-- Token CSRF -->
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            
                            <!-- Inclure le formulaire commun -->
                            <?php 
                                $formMode = 'edit';
                                include '../includes/user-form.php'; 
                            ?>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Enregistrer les modifications
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