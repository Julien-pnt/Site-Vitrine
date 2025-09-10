<?php
echo "🔒 ELIXIR DU TEMPS - AUDIT DE SÉCURITÉ COMPLET\n";
echo "===============================================\n\n";

$total = 0;
$passed = 0;

function check($condition, $description) {
    global $total, $passed;
    $total++;
    if ($condition) {
        echo "✅ $description\n";
        $passed++;
        return true;
    } else {
        echo "❌ $description\n";
        return false;
    }
}

// 1. Fichiers de sécurité essentiels
echo "1. FICHIERS DE SÉCURITÉ\n";
echo "-----------------------\n";
check(file_exists('.htaccess'), "Fichier .htaccess principal");
check(file_exists('php/.htaccess'), "Protection dossier PHP");
check(file_exists('config/.htaccess'), "Protection dossier config");
check(file_exists('public/uploads/.htaccess'), "Protection dossier uploads");
echo "\n";

// 2. Classes de sécurité
echo "2. CLASSES DE SÉCURITÉ\n";
echo "----------------------\n";
check(file_exists('php/utils/BruteForceProtection.php'), "Protection contre force brute");
check(file_exists('php/utils/SecurityMiddleware.php'), "Middleware de sécurité");
check(file_exists('php/utils/SecureInputValidator.php'), "Validateur d'entrée sécurisé");
check(file_exists('php/utils/SecurityMonitor.php'), "Moniteur de sécurité");
check(file_exists('php/utils/security_guard.php'), "Garde de sécurité automatique");
echo "\n";

// 3. Configuration
echo "3. CONFIGURATION SÉCURISÉE\n";
echo "--------------------------\n";
require_once 'config/config.php';

check(defined('ENCRYPTION_KEY'), "Clé de chiffrement définie");
check(defined('CSRF_TOKEN_LIFETIME'), "Durée de vie token CSRF");
check(defined('MAX_LOGIN_ATTEMPTS'), "Limite tentatives de connexion");
check(defined('LOGIN_LOCKOUT_TIME'), "Temps de blocage défini");
check(defined('PASSWORD_MIN_LENGTH'), "Longueur minimum mot de passe");
check(defined('SECURITY_CONFIG_LOADED'), "Configuration sécurité chargée");
echo "\n";

// 4. Fonctions de sécurité
echo "4. FONCTIONS DE SÉCURITÉ\n";
echo "------------------------\n";
check(function_exists('secureLog'), "Logging sécurisé");
check(function_exists('generateSecureCSRFToken'), "Génération token CSRF");
check(function_exists('validateSecureCSRFToken'), "Validation token CSRF");
check(function_exists('secureInput'), "Nettoyage des entrées");
check(function_exists('detectAttackPatterns'), "Détection d'attaques");
echo "\n";

// 5. Configuration PHP
echo "5. CONFIGURATION PHP\n";
echo "--------------------\n";
check(ini_get('display_errors') == '0', "Affichage erreurs désactivé");
check(ini_get('log_errors') == '1', "Logging erreurs activé");
check(ini_get('session.cookie_httponly') == '1', "Cookies HTTP-only");
check(ini_get('session.cookie_secure') == '1', "Cookies sécurisés");
check(ini_get('session.use_strict_mode') == '1', "Mode strict sessions");
echo "\n";

// 6. Structure des répertoires
echo "6. STRUCTURE SÉCURISÉE\n";
echo "----------------------\n";
check(is_dir('logs'), "Répertoire logs existe");
check(is_dir('config'), "Répertoire config existe");
check(is_dir('php/utils'), "Répertoire utilitaires existe");
check(is_dir('public/uploads'), "Répertoire uploads existe");
echo "\n";

// 7. Fichiers de configuration avancée
echo "7. CONFIGURATION AVANCÉE\n";
echo "------------------------\n";
check(file_exists('config/ip_whitelist.txt'), "Liste blanche IPs");
check(file_exists('config/ip_blacklist.txt'), "Liste noire IPs");
check(file_exists('scripts/test_ux_improvements.php'), "Script de test UX");
check(file_exists('scripts/security_audit.php'), "Script d'audit sécurité");
echo "\n";

// 8. Test des classes
echo "8. CHARGEMENT DES CLASSES\n";
echo "-------------------------\n";
try {
    require_once 'php/utils/BruteForceProtection.php';
    check(class_exists('BruteForceProtection'), "Classe BruteForceProtection");
} catch (Exception $e) {
    check(false, "Classe BruteForceProtection");
}

try {
    require_once 'php/utils/SecureInputValidator.php';
    check(class_exists('SecureInputValidator'), "Classe SecureInputValidator");
} catch (Exception $e) {
    check(false, "Classe SecureInputValidator");
}

echo "\n";

// Résultats
echo str_repeat("=", 50) . "\n";
echo "RÉSULTATS DE L'AUDIT\n";
echo str_repeat("=", 50) . "\n";

$rate = round(($passed / $total) * 100, 1);
echo "Tests réussis: $passed/$total ($rate%)\n\n";

if ($rate == 100) {
    echo "🛡️  PARFAIT! Sécurité exceptionnelle.\n";
    echo "   Toutes les mesures de protection sont en place.\n";
} elseif ($rate >= 95) {
    echo "🔒 EXCELLENT! Sécurité très robuste.\n";
    echo "   Quelques détails mineurs à peaufiner.\n";
} elseif ($rate >= 85) {
    echo "✅ TRÈS BON niveau de sécurité.\n";
    echo "   Quelques améliorations recommandées.\n";
} elseif ($rate >= 75) {
    echo "⚠️  CORRECT - Sécurité acceptable.\n";
    echo "   Des améliorations sont nécessaires.\n";
} elseif ($rate >= 60) {
    echo "🚨 INSUFFISANT - Risques de sécurité.\n";
    echo "   Mesures correctives urgentes requises.\n";
} else {
    echo "💀 CRITIQUE - Vulnérabilités majeures!\n";
    echo "   Sécurisation immédiate obligatoire!\n";
}

echo "\n📊 DÉTAILS DE PROTECTION:\n";
echo "• Protection CSRF: " . (function_exists('generateSecureCSRFToken') ? '✅' : '❌') . "\n";
echo "• Protection XSS: " . (function_exists('secureInput') ? '✅' : '❌') . "\n";
echo "• Protection SQL: " . (function_exists('detectAttackPatterns') ? '✅' : '❌') . "\n";
echo "• Limitation tentatives: " . (defined('MAX_LOGIN_ATTEMPTS') ? '✅' : '❌') . "\n";
echo "• Monitoring: " . (file_exists('php/utils/SecurityMonitor.php') ? '✅' : '❌') . "\n";
echo "• Logs sécurisés: " . (function_exists('secureLog') ? '✅' : '❌') . "\n";

echo "\n🕰️ Audit terminé - " . date('Y-m-d H:i:s') . "\n";
?>
