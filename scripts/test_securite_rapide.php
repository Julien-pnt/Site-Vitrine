<?php
/**
 * TEST DE SÉCURITÉ RAPIDE - ELIXIR DU TEMPS
 */

echo "🔒 ELIXIR DU TEMPS - TEST DE SÉCURITÉ RAPIDE\n";
echo "===========================================\n\n";

$tests = [];
$passed = 0;
$total = 0;

function test($condition, $description) {
    global $tests, $passed, $total;
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

// 1. Tests de fichiers de sécurité
echo "1. Vérification des fichiers de sécurité...\n";
test(file_exists('.htaccess'), "Fichier .htaccess principal");
test(file_exists('config/config.php'), "Configuration sécurisée");
test(file_exists('php/utils/BruteForceProtection.php'), "Protection force brute");
test(file_exists('php/utils/SecurityMiddleware.php'), "Middleware de sécurité");
test(file_exists('php/utils/SecureInputValidator.php'), "Validateur d'entrée");
test(file_exists('php/utils/SecurityMonitor.php'), "Moniteur de sécurité");
test(file_exists('php/utils/security_guard.php'), "Garde de sécurité");
echo "\n";

// 2. Tests de configuration
echo "2. Vérification de la configuration...\n";
require_once 'config/config.php';

test(defined('ENCRYPTION_KEY'), "Clé de chiffrement définie");
test(defined('CSRF_TOKEN_LIFETIME'), "Durée token CSRF définie");
test(defined('MAX_LOGIN_ATTEMPTS'), "Limite tentatives définie");
test(defined('LOGIN_LOCKOUT_TIME'), "Temps de blocage défini");
echo "\n";

// 3. Tests de session
echo "3. Vérification de la sécurité des sessions...\n";
test(ini_get('session.cookie_httponly') == '1', "Cookies HTTP-only");
test(ini_get('session.cookie_secure') == '1', "Cookies sécurisés");
test(ini_get('session.use_strict_mode') == '1', "Mode strict sessions");
echo "\n";

// 4. Tests PHP
echo "4. Vérification de la configuration PHP...\n";
test(ini_get('display_errors') == '0', "Erreurs masquées");
test(ini_get('log_errors') == '1', "Logs d'erreurs actifs");
test(function_exists('password_hash'), "Hashage mot de passe disponible");
echo "\n";

// 5. Tests de protection des fichiers
echo "5. Vérification de la protection des fichiers...\n";
test(file_exists('php/.htaccess'), "Protection dossier PHP");
test(file_exists('config/.htaccess'), "Protection dossier config");
test(file_exists('public/uploads/.htaccess'), "Protection dossier uploads");
echo "\n";

// 6. Tests de fonctions de sécurité
echo "6. Vérification des fonctions de sécurité...\n";
test(function_exists('secureLog'), "Fonction de log sécurisé");
test(function_exists('generateSecureCSRFToken'), "Génération token CSRF");
test(function_exists('validateSecureCSRFToken'), "Validation token CSRF");
test(function_exists('secureInput'), "Nettoyage entrées");
test(function_exists('detectAttackPatterns'), "Détection d'attaques");
echo "\n";

// 7. Tests des répertoires
echo "7. Vérification des répertoires...\n";
test(is_dir('logs'), "Répertoire logs existe");
test(is_writable('logs'), "Répertoire logs accessible");
test(is_dir('public/uploads'), "Répertoire uploads existe");
echo "\n";

// 8. Tests des classes de sécurité
echo "8. Vérification des classes de sécurité...\n";
require_once 'php/utils/BruteForceProtection.php';
require_once 'php/utils/SecureInputValidator.php';

test(class_exists('BruteForceProtection'), "Classe BruteForceProtection");
test(class_exists('SecureInputValidator'), "Classe SecureInputValidator");
echo "\n";

// Résultats
echo str_repeat("=", 50) . "\n";
echo "RÉSULTATS DU TEST DE SÉCURITÉ\n";
echo str_repeat("=", 50) . "\n";

$successRate = round(($passed / $total) * 100, 1);

echo "Tests réussis: $passed/$total ($successRate%)\n\n";

if ($successRate >= 95) {
    echo "🛡️  EXCELLENT - Sécurité très robuste!\n";
} elseif ($successRate >= 85) {
    echo "🔒 TRÈS BON - Quelques améliorations mineures possibles\n";
} elseif ($successRate >= 75) {
    echo "⚠️  CORRECT - Des améliorations sont recommandées\n";
} elseif ($successRate >= 60) {
    echo "🚨 INSUFFISANT - Mesures correctives nécessaires\n";
} else {
    echo "💀 CRITIQUE - Sécurisation urgente requise!\n";
}

echo "\n🕰️ Test terminé - " . date('Y-m-d H:i:s') . "\n";
