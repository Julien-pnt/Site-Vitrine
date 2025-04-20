<?php
session_start();
header('Content-Type: application/json');

// Vérifier l'état de connexion
$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

echo json_encode([
    'success' => true,
    'isLoggedIn' => $isLoggedIn,
    'isAdmin' => $isAdmin,
    'userId' => $isLoggedIn ? $_SESSION['user_id'] : null,
    'userEmail' => $isLoggedIn ? $_SESSION['user_email'] : null,
    'userRole' => $isLoggedIn ? $_SESSION['user_role'] : null
]);