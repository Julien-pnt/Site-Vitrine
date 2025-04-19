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

$userId = $_SESSION['user_id'];
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Récupérer le mot de passe actuel de l'utilisateur
    $stmt = $conn->prepare("SELECT mot_de_passe FROM utilisateurs WHERE id = ?");
    $stmt->execute([$userId]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Vérification des entrées
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = "Tous les champs sont obligatoires.";
    } elseif ($newPassword !== $confirmPassword) {
        $error = "Les nouveaux mots de passe ne correspondent pas.";
    } elseif (!password_verify($currentPassword, $userData['mot_de_passe'])) {
        $error = "Le mot de passe actuel est incorrect.";
    } elseif (strlen($newPassword) < 8) {
        $error = "Le nouveau mot de passe doit contenir au moins 8 caractères.";
    } elseif (!preg_match('/[A-Z]/', $newPassword) || !preg_match('/[a-z]/', $newPassword) || !preg_match('/[0-9]/', $newPassword) || !preg_match('/[^A-Za-z0-9]/', $newPassword)) {
        $error = "Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial.";
    } else {
        // Tout est OK, on met à jour le mot de passe
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("UPDATE utilisateurs SET mot_de_passe = ?, date_modification = NOW() WHERE id = ?");
        $result = $stmt->execute([$hashedPassword, $userId]);
        
        if ($result) {
            $success = true;
        } else {
            $error = "Une erreur s'est produite lors de la mise à jour du mot de passe.";
        }
    }
}

// Pour afficher un message sur la page de profil après redirection
if ($success) {
    $_SESSION['password_updated'] = true;
    header('Location: profile.php');
    exit;
} else {
    $_SESSION['password_error'] = $error;
    header('Location: profile.php');
    exit;
}
?>