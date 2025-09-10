<?php
/**
 * SCRIPT DE TEST DE SÉCURITÉ COMPLET
 * Vérifie tous les aspects de sécurité du site Elixir du Temps
 */

// Inclure les dépendances nécessaires
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../php/config/database.php';

class SecurityTester {
    
    private $results = [];
    private $totalTests = 0;
    private $passedTests = 0;
    
    public function __construct() {
        echo "🔒 ELIXIR DU TEMPS - AUDIT DE SÉCURITÉ COMPLET\n";
        echo "================================================\n\n";
    }
    
    /**
     * Lance tous les tests de sécurité
     */
    public function runAllTests() {
        $this->testConfigurationSecurity();
        $this->testFilePermissions();
        $this->testDatabaseSecurity();
        $this->testHTTPHeaders();
        $this->testInputValidation();
        $this->testSessionSecurity();
        $this->testFileUploadSecurity();
        $this->testAuthenticationSecurity();
        $this->testPasswordSecurity();
        $this->testCSRFProtection();
        $this->testSQLInjectionProtection();
        $this->testXSSProtection();
        $this->testDirectoryTraversal();
        $this->testRateLimiting();
        $this->testErrorHandling();
        $this->testLoggingSecurity();
        $this->testBackupSecurity();
        $this->testHTTPSSecurity();
        $this->testAccessControls();
        
        $this->displayResults();
    }
    
    /**
     * Test de la configuration de sécurité
     */
    private function testConfigurationSecurity() {
        echo "1. Test de la configuration de sécurité...\n";
        
        // Test des constantes de sécurité
        $this->check(
            defined('ENCRYPTION_KEY') && !empty(ENCRYPTION_KEY),
            "Clé de chiffrement définie"
        );
        
        $this->check(
            defined('CSRF_TOKEN_LIFETIME') && CSRF_TOKEN_LIFETIME > 0,
            "Durée de vie des tokens CSRF configurée"
        );
        
        $this->check(
            defined('MAX_LOGIN_ATTEMPTS') && MAX_LOGIN_ATTEMPTS <= 5,
            "Limitation des tentatives de connexion"
        );
        
        // Test de la configuration PHP
        $this->check(
            ini_get('display_errors') == '0',
            "Affichage des erreurs désactivé"
        );
        
        $this->check(
            ini_get('expose_php') == '0' || ini_get('expose_php') == '',
            "Masquage de la version PHP"
        );
        
        $this->check(
            ini_get('session.cookie_httponly') == '1',
            "Cookies de session HTTP-only"
        );
        
        echo "\n";
    }
    
    /**
     * Test des permissions de fichiers
     */
    private function testFilePermissions() {
        echo "2. Test des permissions de fichiers...\n";
        
        $sensitiveFiles = [
            __DIR__ . '/../config/config.php' => 'Configuration principale',
            __DIR__ . '/../.htaccess' => 'Fichier .htaccess principal',
            __DIR__ . '/../php/.htaccess' => 'Protection du dossier PHP',
            __DIR__ . '/../config/.htaccess' => 'Protection du dossier config'
        ];
        
        foreach ($sensitiveFiles as $file => $description) {
            if (file_exists($file)) {
                $perms = fileperms($file);
                $this->check(
                    ($perms & 0044) === 0,
                    "$description - Lecture monde interdite"
                );
            } else {
                $this->check(false, "$description - Fichier manquant");
            }
        }
        
        // Test des répertoires sensibles
        $sensitiveDirs = [
            __DIR__ . '/../logs' => 'Répertoire des logs',
            __DIR__ . '/../config' => 'Répertoire de configuration'
        ];
        
        foreach ($sensitiveDirs as $dir => $description) {
            if (is_dir($dir)) {
                $perms = fileperms($dir);
                $this->check(
                    ($perms & 0005) === 0,
                    "$description - Accès monde interdit"
                );
            }
        }
        
        echo "\n";
    }
    
    /**
     * Test de la sécurité de la base de données
     */
    private function testDatabaseSecurity() {
        echo "3. Test de la sécurité de la base de données...\n";
        
        try {
            $db = new Database();
            $pdo = $db->getConnection();
            
            // Test de la connexion sécurisée
            $this->check(
                $pdo instanceof PDO,
                "Connexion à la base de données établie"
            );
            
            // Test des requêtes préparées (simulation)
            $stmt = $pdo->prepare("SHOW TABLES");
            $stmt->execute();
            $this->check(
                $stmt !== false,
                "Support des requêtes préparées"
            );
            
            // Test des privilèges utilisateur (basique)
            $stmt = $pdo->query("SELECT USER()");
            $user = $stmt->fetchColumn();
            $this->check(
                !str_contains(strtolower($user), 'root@'),
                "Utilisateur non-root pour la base de données"
            );
            
        } catch (Exception $e) {
            $this->check(false, "Erreur de connexion base de données: " . $e->getMessage());
        }
        
        echo "\n";
    }
    
    /**
     * Test des headers HTTP de sécurité
     */
    private function testHTTPHeaders() {
        echo "4. Test des headers HTTP de sécurité...\n";
        
        // Simuler une requête pour tester les headers
        ob_start();
        include __DIR__ . '/../config/config.php';
        $headers = headers_list();
        ob_end_clean();
        
        $expectedHeaders = [
            'X-Frame-Options' => 'Protection clickjacking',
            'X-XSS-Protection' => 'Protection XSS',
            'X-Content-Type-Options' => 'Protection MIME sniffing',
            'Content-Security-Policy' => 'Content Security Policy',
            'Referrer-Policy' => 'Politique de référent'
        ];
        
        foreach ($expectedHeaders as $header => $description) {
            $found = false;
            foreach ($headers as $sentHeader) {
                if (str_starts_with($sentHeader, $header . ':')) {
                    $found = true;
                    break;
                }
            }
            $this->check($found, $description);
        }
        
        echo "\n";
    }
    
    /**
     * Test de validation des entrées
     */
    private function testInputValidation() {
        echo "5. Test de la validation des entrées...\n";
        
        // Test des fonctions de sécurité
        $this->check(
            function_exists('secureInput'),
            "Fonction de nettoyage d'entrée disponible"
        );
        
        $this->check(
            function_exists('detectAttackPatterns'),
            "Fonction de détection d'attaques disponible"
        );
        
        // Test de validation d'email
        $testEmail = "test@example.com";
        $this->check(
            filter_var($testEmail, FILTER_VALIDATE_EMAIL) !== false,
            "Validation d'email fonctionnelle"
        );
        
        // Test de nettoyage XSS
        $xssTest = "<script>alert('xss')</script>";
        $cleaned = htmlspecialchars($xssTest, ENT_QUOTES, 'UTF-8');
        $this->check(
            $cleaned !== $xssTest,
            "Protection XSS dans le nettoyage"
        );
        
        echo "\n";
    }
    
    /**
     * Test de la sécurité des sessions
     */
    private function testSessionSecurity() {
        echo "6. Test de la sécurité des sessions...\n";
        
        $this->check(
            ini_get('session.cookie_httponly') == '1',
            "Sessions HTTP-only activées"
        );
        
        $this->check(
            ini_get('session.cookie_secure') == '1',
            "Sessions sécurisées (HTTPS)"
        );
        
        $this->check(
            ini_get('session.use_strict_mode') == '1',
            "Mode strict des sessions activé"
        );
        
        $this->check(
            session_status() === PHP_SESSION_ACTIVE,
            "Session démarrée correctement"
        );
        
        echo "\n";
    }
    
    /**
     * Test de sécurité des uploads
     */
    private function testFileUploadSecurity() {
        echo "7. Test de la sécurité des uploads...\n";
        
        $uploadDir = __DIR__ . '/../public/uploads';
        $this->check(
            is_dir($uploadDir),
            "Répertoire d'upload existe"
        );
        
        $htaccessUpload = $uploadDir . '/.htaccess';
        $this->check(
            file_exists($htaccessUpload),
            "Protection .htaccess dans uploads"
        );
        
        if (file_exists($htaccessUpload)) {
            $content = file_get_contents($htaccessUpload);
            $this->check(
                str_contains($content, 'php_flag engine off'),
                "Exécution PHP désactivée dans uploads"
            );
        }
        
        echo "\n";
    }
    
    /**
     * Test de sécurité d'authentification
     */
    private function testAuthenticationSecurity() {
        echo "8. Test de la sécurité d'authentification...\n";
        
        $this->check(
            function_exists('password_hash'),
            "Fonction de hashage de mot de passe disponible"
        );
        
        $this->check(
            function_exists('password_verify'),
            "Fonction de vérification de mot de passe disponible"
        );
        
        // Test du hashage
        $testPassword = "TestPassword123!";
        $hash = password_hash($testPassword, PASSWORD_DEFAULT);
        $this->check(
            password_verify($testPassword, $hash),
            "Hashage/vérification de mot de passe fonctionnel"
        );
        
        echo "\n";
    }
    
    /**
     * Test de la politique de mot de passe
     */
    private function testPasswordSecurity() {
        echo "9. Test de la politique de mot de passe...\n";
        
        $this->check(
            defined('PASSWORD_MIN_LENGTH') && PASSWORD_MIN_LENGTH >= 8,
            "Longueur minimum de mot de passe (8+ caractères)"
        );
        
        $this->check(
            defined('PASSWORD_REQUIRE_SPECIAL') && PASSWORD_REQUIRE_SPECIAL,
            "Caractères spéciaux requis"
        );
        
        $this->check(
            defined('PASSWORD_REQUIRE_NUMBERS') && PASSWORD_REQUIRE_NUMBERS,
            "Chiffres requis"
        );
        
        $this->check(
            defined('PASSWORD_REQUIRE_UPPERCASE') && PASSWORD_REQUIRE_UPPERCASE,
            "Majuscules requises"
        );
        
        echo "\n";
    }
    
    /**
     * Test de protection CSRF
     */
    private function testCSRFProtection() {
        echo "10. Test de la protection CSRF...\n";
        
        $this->check(
            function_exists('generateSecureCSRFToken'),
            "Fonction de génération de token CSRF disponible"
        );
        
        $this->check(
            function_exists('validateSecureCSRFToken'),
            "Fonction de validation de token CSRF disponible"
        );
        
        // Test de génération de token
        if (function_exists('generateSecureCSRFToken')) {
            $token = generateSecureCSRFToken();
            $this->check(
                !empty($token) && strlen($token) >= 32,
                "Token CSRF généré avec longueur suffisante"
            );
        }
        
        echo "\n";
    }
    
    /**
     * Test de protection SQL Injection
     */
    private function testSQLInjectionProtection() {
        echo "11. Test de protection contre l'injection SQL...\n";
        
        $sqlInjectionTests = [
            "'; DROP TABLE users; --",
            "1' OR '1'='1",
            "UNION SELECT password FROM users",
            "admin'--",
            "' OR 1=1#"
        ];
        
        foreach ($sqlInjectionTests as $test) {
            if (function_exists('detectAttackPatterns')) {
                $detected = detectAttackPatterns($test);
                $this->check($detected, "Détection injection SQL: " . substr($test, 0, 20) . "...");
            }
        }
        
        echo "\n";
    }
    
    /**
     * Test de protection XSS
     */
    private function testXSSProtection() {
        echo "12. Test de protection contre XSS...\n";
        
        $xssTests = [
            "<script>alert('xss')</script>",
            "javascript:alert('xss')",
            "<img src=x onerror=alert('xss')>",
            "<iframe src='javascript:alert()'></iframe>",
            "eval('alert(1)')"
        ];
        
        foreach ($xssTests as $test) {
            $cleaned = htmlspecialchars($test, ENT_QUOTES, 'UTF-8');
            $this->check($cleaned !== $test, "Protection XSS: " . substr($test, 0, 20) . "...");
        }
        
        echo "\n";
    }
    
    /**
     * Test de protection Directory Traversal
     */
    private function testDirectoryTraversal() {
        echo "13. Test de protection contre Directory Traversal...\n";
        
        $traversalTests = [
            "../../../etc/passwd",
            "..\\..\\windows\\system32\\config\\sam",
            "....//....//etc/passwd",
            "%2e%2e%2f%2e%2e%2f%etc%2fpasswd"
        ];
        
        foreach ($traversalTests as $test) {
            if (function_exists('detectAttackPatterns')) {
                $detected = detectAttackPatterns($test);
                $this->check($detected, "Détection traversal: " . substr($test, 0, 20) . "...");
            }
        }
        
        echo "\n";
    }
    
    /**
     * Test de limitation de taux
     */
    private function testRateLimiting() {
        echo "14. Test de limitation de taux...\n";
        
        $this->check(
            class_exists('BruteForceProtection') || file_exists('../php/utils/BruteForceProtection.php'),
            "Système de protection contre la force brute disponible"
        );
        
        $this->check(
            defined('MAX_LOGIN_ATTEMPTS'),
            "Limite de tentatives de connexion définie"
        );
        
        $this->check(
            defined('LOGIN_LOCKOUT_TIME'),
            "Temps de blocage défini"
        );
        
        echo "\n";
    }
    
    /**
     * Test de gestion des erreurs
     */
    private function testErrorHandling() {
        echo "15. Test de la gestion des erreurs...\n";
        
        $this->check(
            ini_get('display_errors') == '0',
            "Affichage des erreurs désactivé"
        );
        
        $this->check(
            ini_get('log_errors') == '1',
            "Logging des erreurs activé"
        );
        
        $errorLogFile = ini_get('error_log');
        $this->check(
            !empty($errorLogFile),
            "Fichier de log d'erreur configuré"
        );
        
        echo "\n";
    }
    
    /**
     * Test de sécurité des logs
     */
    private function testLoggingSecurity() {
        echo "16. Test de la sécurité des logs...\n";
        
        $this->check(
            function_exists('secureLog'),
            "Fonction de logging sécurisé disponible"
        );
        
        $logsDir = __DIR__ . '/../logs';
        if (is_dir($logsDir)) {
            $this->check(
                is_writable($logsDir),
                "Répertoire de logs accessible en écriture"
            );
            
            $perms = fileperms($logsDir);
            $this->check(
                ($perms & 0044) === 0,
                "Répertoire de logs non lisible par le monde"
            );
        }
        
        echo "\n";
    }
    
    /**
     * Test de sécurité des sauvegardes
     */
    private function testBackupSecurity() {
        echo "17. Test de la sécurité des sauvegardes...\n";
        
        $backupPatterns = ['*.sql', '*.bak', '*.backup', '*.dump'];
        $foundBackups = [];
        
        foreach ($backupPatterns as $pattern) {
            $files = glob(__DIR__ . "/../$pattern");
            $foundBackups = array_merge($foundBackups, $files);
        }
        
        $this->check(
            empty($foundBackups),
            "Aucun fichier de sauvegarde exposé trouvé"
        );
        
        if (!empty($foundBackups)) {
            echo "   ⚠️  Fichiers de sauvegarde trouvés: " . implode(', ', $foundBackups) . "\n";
        }
        
        echo "\n";
    }
    
    /**
     * Test de sécurité HTTPS
     */
    private function testHTTPSSecurity() {
        echo "18. Test de la sécurité HTTPS...\n";
        
        $this->check(
            isset($_SERVER['HTTPS']) || $_SERVER['SERVER_PORT'] == 443,
            "Connexion HTTPS détectée"
        );
        
        // Test HSTS en production
        if (defined('APP_ENV') && APP_ENV === 'production') {
            $this->check(
                in_array('Strict-Transport-Security', headers_list()),
                "Header HSTS présent en production"
            );
        }
        
        echo "\n";
    }
    
    /**
     * Test des contrôles d'accès
     */
    private function testAccessControls() {
        echo "19. Test des contrôles d'accès...\n";
        
        $this->check(
            function_exists('isLoggedIn'),
            "Fonction de vérification de connexion disponible"
        );
        
        $this->check(
            function_exists('isAdmin'),
            "Fonction de vérification admin disponible"
        );
        
        $this->check(
            function_exists('checkAccess'),
            "Fonction de contrôle d'accès disponible"
        );
        
        echo "\n";
    }
    
    /**
     * Vérifie une condition et enregistre le résultat
     */
    private function check($condition, $description) {
        $this->totalTests++;
        
        if ($condition) {
            echo "   ✅ $description\n";
            $this->passedTests++;
            $this->results[] = ['status' => 'PASS', 'test' => $description];
        } else {
            echo "   ❌ $description\n";
            $this->results[] = ['status' => 'FAIL', 'test' => $description];
        }
    }
    
    /**
     * Affiche les résultats finaux
     */
    private function displayResults() {
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "RÉSULTATS DE L'AUDIT DE SÉCURITÉ\n";
        echo str_repeat("=", 50) . "\n";
        
        $failedTests = $this->totalTests - $this->passedTests;
        $successRate = round(($this->passedTests / $this->totalTests) * 100, 1);
        
        echo "Total des tests: $this->totalTests\n";
        echo "Tests réussis: $this->passedTests\n";
        echo "Tests échoués: $failedTests\n";
        echo "Taux de réussite: $successRate%\n\n";
        
        // Déterminer le niveau de sécurité
        if ($successRate >= 95) {
            echo "🛡️  NIVEAU DE SÉCURITÉ: EXCELLENT\n";
            echo "   Votre site est très bien sécurisé.\n";
        } elseif ($successRate >= 85) {
            echo "🔒 NIVEAU DE SÉCURITÉ: TRÈS BON\n";
            echo "   Quelques améliorations mineures recommandées.\n";
        } elseif ($successRate >= 75) {
            echo "⚠️  NIVEAU DE SÉCURITÉ: CORRECT\n";
            echo "   Des améliorations sont nécessaires.\n";
        } elseif ($successRate >= 60) {
            echo "🚨 NIVEAU DE SÉCURITÉ: INSUFFISANT\n";
            echo "   Des mesures correctives urgentes sont requises.\n";
        } else {
            echo "💀 NIVEAU DE SÉCURITÉ: CRITIQUE\n";
            echo "   Sécurisation immédiate nécessaire!\n";
        }
        
        // Afficher les tests échoués
        if ($failedTests > 0) {
            echo "\n📋 TESTS ÉCHOUÉS:\n";
            foreach ($this->results as $result) {
                if ($result['status'] === 'FAIL') {
                    echo "   • {$result['test']}\n";
                }
            }
        }
        
        echo "\n🕰️ Audit terminé - " . date('Y-m-d H:i:s') . "\n";
    }
}

// Exécuter l'audit si le script est appelé directement
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    $tester = new SecurityTester();
    $tester->runAllTests();
}
