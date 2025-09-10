<?php
/**
 * Fonctions d'authentification pour le dashboard admin
 */

// S'assurer que la session est démarrée
function ensureSessionStarted() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Vérifie si un utilisateur est connecté
 * @return bool
 */
function isLoggedIn() {
    ensureSessionStarted();
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Vérifie si l'utilisateur connecté est un administrateur
 * @return bool
 */
function isAdmin() {
    ensureSessionStarted();
    
    if (!isLoggedIn()) {
        return false;
    }
    
    // Vérifier si la session contient le rôle d'admin
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        return true;
    }
    
    return false;
}

/**
 * Crée une session pour un utilisateur connecté
 * @param array $user Les données de l'utilisateur
 * @return void
 */
function createUserSession($user) {
    ensureSessionStarted();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['logged_in_time'] = time();
}

/**
 * Déconnecte l'utilisateur actuel
 * @return void
 */
function logout() {
    ensureSessionStarted();
    // Supprimer toutes les variables de session
    $_SESSION = array();
    
    // Si un cookie de session existe, le détruire
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Détruire la session
    session_destroy();
}