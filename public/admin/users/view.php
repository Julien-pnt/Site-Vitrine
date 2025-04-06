<?php
require_once '../../../php/config/database.php';
require_once '../../../php/utils/auth.php';
require_once '../../../php/models/User.php';
require_once '../../../php/utils/Logger.php';

// Créer le répertoire d'upload s'il n'existe pas
$upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/Site-Vitrine/public/uploads/users/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Vérification de l'authentification admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: /Site-Vitrine/public/pages/auth/login.php?redirect=admin/users');
    exit;
}

// Initialiser les objets
$userModel = new User($pdo);
$logger = new Logger($pdo);

// Vérifier l'ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = "ID d'utilisateur invalide";
    $_SESSION['message_type'] = "danger";
    header('Location: index.php');
    exit;
}

$userId = (int)$_GET['id'];

// Récupérer l'utilisateur
$user = $userModel->getUserById($userId);

if (!$user) {
    $_SESSION['message'] = "Utilisateur non trouvé";
    $_SESSION['message_type'] = "danger";
    header('Location: index.php');
    exit;
}

// Ajouter des valeurs par défaut pour les colonnes potentiellement manquantes
$user['photo'] = $user['photo'] ?? null;
$user['derniere_connexion'] = $user['derniere_connexion'] ?? null;

// S'assurer que les clés importantes existent toujours
$user['statut'] = $user['statut'] ?? 0; // 0 par défaut si non défini
$user['role'] = $user['role'] ?? 'client'; // client par défaut si non défini

// Logger l'accès
$logger->info('admin', 'view_user', [
    'user_id' => $userId,
    'details' => "Consultation des détails de l'utilisateur {$user['prenom']} {$user['nom']}"
]);

// Récupérer les logs d'activité de l'utilisateur
$userLogs = $logger->getUserActivity($userId);

// Formater les dates pour l'affichage
$dateCreation = new DateTime($user['date_creation']);
$dateDerniereConnexion = !empty($user['derniere_connexion']) ? new DateTime($user['derniere_connexion']) : null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails utilisateur - Admin</title>
    <link rel="stylesheet" href="../css/admin.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../css/users.css?v=<?= time() ?>">
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
                        <i class="fas fa-user"></i> Détails de l'utilisateur
                    </h1>
                    <div class="page-actions">
                        <a href="edit.php?id=<?= $userId ?>" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>

                <!-- Message d'alerte (succès/erreur) -->
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?= $_SESSION['message_type'] ?>">
                        <?= $_SESSION['message'] ?>
                        <button type="button" class="close-alert">&times;</button>
                    </div>
                    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
                <?php endif; ?>

                <div class="user-details-container">
                    <!-- Profil utilisateur -->
                    <div class="card user-profile-card">
                        <div class="card-header">
                            <h2>Profil</h2>
                        </div>
                        <div class="card-body">
                            <div class="user-profile">
                                <div class="user-avatar-large">
                                    <?php if(!empty($user['photo'])): ?>
                                        <?php 
                                        // Déterminer le bon chemin de l'image
                                        $imageSrc = '';
                                        if (strpos($user['photo'], '/') === 0) {
                                            // Chemin absolu commence par /
                                            $imageSrc = $user['photo'];
                                            // Vérifier si le fichier existe physiquement
                                            $physical_path = $_SERVER['DOCUMENT_ROOT'] . $imageSrc;
                                            $imageExists = file_exists($physical_path);
                                        } else {
                                            // Chemin relatif vers le dossier uploads/users
                                            $imageSrc = "/Site-Vitrine/public/uploads/users/" . $user['photo'];
                                            // Vérifier si le fichier existe physiquement
                                            $physical_path = $_SERVER['DOCUMENT_ROOT'] . $imageSrc;
                                            $imageExists = file_exists($physical_path);
                                        }
                                        
                                        // Si l'image existe physiquement, l'afficher, sinon montrer l'icône par défaut
                                        if ($imageExists):
                                        ?>
                                            <img src="<?= htmlspecialchars($imageSrc) ?>" alt="Photo de profil">
                                        <?php else: ?>
                                            <div class="avatar-placeholder">
                                                <i class="fas fa-user"></i>
                                                <small style="font-size: 10px; display: block">Image non trouvée</small>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="avatar-placeholder">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="user-info-large">
                                    <h2><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></h2>
                                    <span class="role-badge role-<?= $user['role'] ?>"><?= ucfirst(htmlspecialchars($user['role'])) ?></span>
                                    <span class="status-badge status-<?= $user['statut'] ? 'active' : 'inactive' ?>">
                                        <?= $user['statut'] ? 'Actif' : 'Inactif' ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations de contact -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Informations de contact</h2>
                        </div>
                        <div class="card-body">
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">Email</div>
                                    <div class="info-value">
                                        <a href="mailto:<?= htmlspecialchars($user['email']) ?>">
                                            <?= htmlspecialchars($user['email']) ?>
                                        </a>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Téléphone</div>
                                    <div class="info-value">
                                        <?php if(!empty($user['telephone'])): ?>
                                            <a href="tel:<?= htmlspecialchars($user['telephone']) ?>">
                                                <?= htmlspecialchars($user['telephone']) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="not-defined">Non défini</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations du compte -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Informations du compte</h2>
                        </div>
                        <div class="card-body">
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">Date de création</div>
                                    <div class="info-value">
                                        <?= $dateCreation->format('d/m/Y à H:i') ?>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Dernière connexion</div>
                                    <div class="info-value">
                                        <?= $dateDerniereConnexion ? $dateDerniereConnexion->format('d/m/Y à H:i') : '<span class="not-defined">Jamais connecté</span>' ?>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Statut</div>
                                    <div class="info-value">
                                        <span class="status-badge status-<?= $user['statut'] ? 'active' : 'inactive' ?>">
                                            <?= $user['statut'] ? 'Actif' : 'Inactif' ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Rôle</div>
                                    <div class="info-value">
                                        <span class="role-badge role-<?= $user['role'] ?>"><?= ucfirst(htmlspecialchars($user['role'])) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activité récente -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Activité récente</h2>
                        </div>
                        <div class="card-body">
                            <?php if (empty($userLogs)): ?>
                                <p class="text-center text-muted">Aucune activité enregistrée pour cet utilisateur</p>
                            <?php else: ?>
                                <div class="activity-timeline">
                                    <?php foreach ($userLogs as $log): ?>
                                        <?php 
                                            $logDate = new DateTime($log['date_creation']);
                                            $logDetails = is_string($log['details']) ? json_decode($log['details'], true) : $log['details'];
                                            
                                            // Déterminer l'icône basée sur le type et l'action
                                            $iconClass = 'fa-circle-info'; // Icône par défaut
                                            
                                            if ($log['type'] === 'auth') {
                                                $iconClass = $log['action'] === 'login' ? 'fa-sign-in-alt' : 'fa-sign-out-alt';
                                            } elseif ($log['type'] === 'user') {
                                                $iconClass = 'fa-user-edit';
                                            } elseif ($log['type'] === 'order') {
                                                $iconClass = 'fa-shopping-cart';
                                            } elseif ($log['type'] === 'admin') {
                                                $iconClass = 'fa-tools';
                                            }
                                            
                                            // Déterminer la classe de couleur basée sur le type
                                            $colorClass = 'info'; // Couleur par défaut
                                            
                                            if ($log['type'] === 'auth') {
                                                $colorClass = 'success';
                                            } elseif ($log['type'] === 'error') {
                                                $colorClass = 'danger';
                                            } elseif ($log['type'] === 'admin') {
                                                $colorClass = 'primary';
                                            } elseif ($log['type'] === 'order') {
                                                $colorClass = 'warning';
                                            }
                                        ?>
                                        <div class="activity-item">
                                            <div class="activity-icon bg-<?= $colorClass ?>">
                                                <i class="fas <?= $iconClass ?>"></i>
                                            </div>
                                            <div class="activity-content">
                                                <div class="activity-header">
                                                    <span class="activity-title"><?= ucfirst($log['action']) ?></span>
                                                    <span class="activity-time"><?= $logDate->format('d/m/Y à H:i') ?></span>
                                                </div>
                                                <div class="activity-description">
                                                    <?php 
                                                    // Afficher les détails de l'activité de manière lisible
                                                    if (is_array($logDetails)) {
                                                        if (isset($logDetails['details'])) {
                                                            echo htmlspecialchars($logDetails['details']);
                                                        } elseif (isset($logDetails['message'])) {
                                                            echo htmlspecialchars($logDetails['message']);
                                                        } else {
                                                            foreach ($logDetails as $key => $value) {
                                                                if ($key !== 'user_id' && $key !== 'ip' && !is_array($value)) {
                                                                    echo "<div><strong>".htmlspecialchars(ucfirst($key)).":</strong> ".htmlspecialchars($value)."</div>";
                                                                }
                                                            }
                                                        }
                                                    } else {
                                                        echo htmlspecialchars($log['details'] ?? 'Aucun détail disponible');
                                                    }
                                                    ?>
                                                </div>
                                                <div class="activity-meta">
                                                    <span class="activity-ip" title="Adresse IP">
                                                        <i class="fas fa-network-wired"></i> <?= htmlspecialchars($log['ip_address'] ?? 'Inconnue') ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <?php if (count($userLogs) >= 10): ?>
                                    <div class="text-center mt-3">
                                        <a href="user-activity.php?id=<?= $userId ?>" class="btn btn-sm btn-outline-secondary">
                                            Voir toutes les activités <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../js/admin.js"></script>
</body>
</html>