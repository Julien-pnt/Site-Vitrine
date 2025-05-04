<?php
/**
 * Fonctions de sécurité pour l'authentification et l'autorisation
 * Adapté à la structure de base de données d'Elixir du Temps
 */

/**
 * Vérifie si un utilisateur est connecté
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Vérifie si l'utilisateur connecté est un administrateur
 * @return bool
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Vérifie si l'utilisateur connecté est un manager
 * @return bool
 */
function isManager() {
    return isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'manager' || $_SESSION['user_role'] === 'admin');
}

/**
 * Vérifie les droits d'accès et redirige si nécessaire
 * @param string $required_role Le rôle requis ('admin', 'manager', 'client')
 * @param string $redirect_url URL de redirection si l'accès est refusé
 * @return bool
 */
function checkAccess($required_role = 'client', $redirect_url = '../php/api/auth/login.php') {
    if (!isLoggedIn()) {
        header("Location: $redirect_url");
        exit();
    }
    
    if ($required_role === 'admin' && !isAdmin()) {
        header("Location: $redirect_url?error=unauthorized");
        exit();
    }
    
    if ($required_role === 'manager' && !isManager()) {
        header("Location: $redirect_url?error=unauthorized");
        exit();
    }
    
    return true;
}

/**
 * Génère un jeton CSRF
 * @return string
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie si un jeton CSRF est valide
 * @param string $token Le jeton à vérifier
 * @return bool
 */
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Filtre et nettoie une chaîne de caractères
 * @param string $input La chaîne à nettoyer
 * @return string
 */
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Génère un mot de passe aléatoire
 * @param int $length Longueur du mot de passe
 * @return string
 */
function generateRandomPassword($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+';
    return substr(str_shuffle(str_repeat($chars, ceil($length / strlen($chars)))), 0, $length);
}

/**
 * Vérifie la force d'un mot de passe
 * @param string $password Le mot de passe à vérifier
 * @return string 'weak', 'medium' ou 'strong'
 */
function checkPasswordStrength($password) {
    $strength = 0;
    
    // Longueur
    if (strlen($password) >= 8) $strength += 1;
    if (strlen($password) >= 12) $strength += 1;
    
    // Complexité
    if (preg_match('/[A-Z]/', $password)) $strength += 1;
    if (preg_match('/[a-z]/', $password)) $strength += 1;
    if (preg_match('/[0-9]/', $password)) $strength += 1;
    if (preg_match('/[^A-Za-z0-9]/', $password)) $strength += 1;
    
    if ($strength <= 2) return 'weak';
    if ($strength <= 4) return 'medium';
    return 'strong';
}

/**
 * Hache un mot de passe
 * @param string $password Mot de passe en clair
 * @return string Mot de passe haché
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Vérifie si un mot de passe correspond à son hash
 * @param string $password Mot de passe en clair
 * @param string $hash Hash stocké
 * @return bool
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Crée un jeton d'authentification "Se souvenir de moi"
 * @param int $user_id ID de l'utilisateur
 * @param int $expiry_days Nombre de jours avant expiration
 * @return string Le jeton généré
 */
function createAuthToken($user_id, $expiry_days = 30) {
    global $pdo;
    
    // Générer un jeton aléatoire
    $token = bin2hex(random_bytes(32));
    $expires_at = date('Y-m-d H:i:s', time() + $expiry_days * 86400);
    
    // Stocker dans la base de données
    $stmt = $pdo->prepare("INSERT INTO auth_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $token, $expires_at]);
    
    return $token;
}

/**
 * Vérifie et valide un jeton d'authentification
 * @param string $token Le jeton à vérifier
 * @return int|false L'ID utilisateur si valide, false sinon
 */
function validateAuthToken($token) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT user_id FROM auth_tokens WHERE token = ? AND expires_at > NOW()");
    $stmt->execute([$token]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result ? $result['user_id'] : false;
}

/**
 * Journalise une tentative de connexion
 * @param int $user_id ID de l'utilisateur
 * @param string $action Type d'action ('login', 'logout', 'failed_attempt')
 * @return bool
 */
function logConnection($user_id, $action = 'login') {
    global $pdo;
    
    $ip = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $stmt = $pdo->prepare("INSERT INTO connexions_log (user_id, ip_address, user_agent, action) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$user_id, $ip, $user_agent, $action]);
}

/**
 * Met à jour la date de dernière connexion d'un utilisateur
 * @param int $user_id ID de l'utilisateur
 * @return bool
 */
function updateLastLogin($user_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("UPDATE utilisateurs SET derniere_connexion = NOW() WHERE id = ?");
    return $stmt->execute([$user_id]);
}

/**
 * Crée un jeton de réinitialisation de mot de passe
 * @param int $user_id ID de l'utilisateur
 * @param int $expiry_hours Nombre d'heures avant expiration
 * @return string Le jeton généré
 */
function createPasswordResetToken($user_id, $expiry_hours = 24) {
    global $pdo;
    
    // Générer un jeton aléatoire
    $token = bin2hex(random_bytes(32));
    $expires_at = date('Y-m-d H:i:s', time() + $expiry_hours * 3600);
    
    // Supprimer les jetons existants pour cet utilisateur
    $stmt = $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?");
    $stmt->execute([$user_id]);
    
    // Stocker le nouveau jeton
    $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $token, $expires_at]);
    
    return $token;
}

/**
 * Vérifie et valide un jeton de réinitialisation de mot de passe
 * @param string $token Le jeton à vérifier
 * @return int|false L'ID utilisateur si valide, false sinon
 */
function validatePasswordResetToken($token) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT user_id FROM password_resets WHERE token = ? AND expires_at > NOW()");
    $stmt->execute([$token]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result ? $result['user_id'] : false;
}

/**
 * Log une action d'administration
 * @param int $user_id ID de l'utilisateur (admin)
 * @param string $action Description de l'action
 * @param array|string $details Détails supplémentaires (sera converti en JSON si array)
 * @return bool
 */
function logAdminAction($user_id, $action, $details = null) {
    global $pdo;
    
    $ip = $_SERVER['REMOTE_ADDR'];
    
    if (is_array($details)) {
        $details = json_encode($details, JSON_UNESCAPED_UNICODE);
    }
    
    $stmt = $pdo->prepare("INSERT INTO admin_logs (utilisateur_id, action, ip_address, date_action, details) 
                          VALUES (?, ?, ?, NOW(), ?)");
    return $stmt->execute([$user_id, $action, $ip, $details]);
}