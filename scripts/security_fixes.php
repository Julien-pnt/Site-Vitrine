#!/usr/bin/env php
<?php
/**
 * Script d'automatisation des corrections de sécurité
 * Usage: php scripts/security_fixes.php
 */

class SecurityFixesAutomator {
    
    private $rootPath;
    private $backupPath;
    private $logFile;
    
    public function __construct() {
        $this->rootPath = dirname(__DIR__);
        $this->backupPath = $this->rootPath . '/backups/' . date('Y-m-d_H-i-s');
        $this->logFile = $this->rootPath . '/logs/security_fixes.log';
        
        // Créer les répertoires nécessaires
        $this->ensureDirectories();
    }
    
    public function run() {
        $this->log("=== Début des corrections de sécurité ===");
        
        try {
            $this->createBackup();
            $this->fixConfigurationSecurity();
            $this->addCSRFProtection();
            $this->fixSessionSecurity();
            $this->improveUploadValidation();
            $this->addSecurityHeaders();
            $this->createLogDirectories();
            
            $this->log("✅ Toutes les corrections ont été appliquées avec succès");
            echo "Corrections de sécurité terminées avec succès!\n";
            echo "Vérifiez le fichier de log: {$this->logFile}\n";
            echo "Sauvegarde créée dans: {$this->backupPath}\n";
            
        } catch (Exception $e) {
            $this->log("❌ Erreur: " . $e->getMessage());
            echo "Erreur lors des corrections: " . $e->getMessage() . "\n";
            echo "Restaurez la sauvegarde si nécessaire: {$this->backupPath}\n";
        }
    }
    
    private function ensureDirectories() {
        $dirs = [
            dirname($this->backupPath),
            dirname($this->logFile),
            $this->rootPath . '/logs',
            $this->rootPath . '/cache'
        ];
        
        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }
    
    private function createBackup() {
        $this->log("Création de la sauvegarde...");
        
        if (!is_dir($this->backupPath)) {
            mkdir($this->backupPath, 0755, true);
        }
        
        // Sauvegarder les fichiers critiques
        $filesToBackup = [
            'config/config.php',
            'php/utils/auth.php',
            'public/admin/index.php'
        ];
        
        foreach ($filesToBackup as $file) {
            $sourcePath = $this->rootPath . '/' . $file;
            $backupFile = $this->backupPath . '/' . basename($file);
            
            if (file_exists($sourcePath)) {
                copy($sourcePath, $backupFile);
                $this->log("Sauvegardé: $file");
            }
        }
    }
    
    private function fixConfigurationSecurity() {
        $this->log("Correction de la configuration de sécurité...");
        
        $configFile = $this->rootPath . '/config/config.php';
        
        if (!file_exists($configFile)) {
            throw new Exception("Fichier config.php non trouvé");
        }
        
        $content = file_get_contents($configFile);
        
        // Remplacer la configuration d'erreurs non sécurisée
        $content = preg_replace(
            "/ini_set\('display_errors',\s*1\);/",
            "ini_set('display_errors', 0); // Sécurisé automatiquement",
            $content
        );
        
        $content = preg_replace(
            "/error_reporting\(E_ALL\);/",
            "error_reporting(0); // Sécurisé automatiquement",
            $content
        );
        
        // Ajouter la configuration de logs
        if (strpos($content, 'error_log') === false) {
            $logConfig = "\n// Configuration de logs sécurisée\n";
            $logConfig .= "ini_set('log_errors', 1);\n";
            $logConfig .= "ini_set('error_log', ROOT_PATH . '/logs/php_errors.log');\n";
            
            $content = str_replace('date_default_timezone_set', $logConfig . 'date_default_timezone_set', $content);
        }
        
        file_put_contents($configFile, $content);
        $this->log("✅ Configuration sécurisée");
    }
    
    private function addCSRFProtection() {
        $this->log("Ajout de la protection CSRF...");
        
        // Trouver tous les fichiers PHP avec des formulaires
        $phpFiles = $this->findFilesWithForms();
        
        foreach ($phpFiles as $file) {
            $this->addCSRFToFile($file);
        }
        
        $this->log("✅ Protection CSRF ajoutée à " . count($phpFiles) . " fichiers");
    }
    
    private function findFilesWithForms() {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->rootPath . '/public')
        );
        
        foreach ($iterator as $file) {
            if ($file->getExtension() === 'php') {
                $content = file_get_contents($file->getPathname());
                if (preg_match('/<form[^>]*method=["\']post["\'][^>]*>/i', $content)) {
                    $files[] = $file->getPathname();
                }
            }
        }
        
        return $files;
    }
    
    private function addCSRFToFile($filePath) {
        $content = file_get_contents($filePath);
        
        // Ajouter la génération du token CSRF au début du fichier
        if (strpos($content, 'csrf_token') === false) {
            $csrfInit = "<?php\n";
            $csrfInit .= "// Protection CSRF ajoutée automatiquement\n";
            $csrfInit .= "if (session_status() === PHP_SESSION_NONE) session_start();\n";
            $csrfInit .= "if (!isset(\$_SESSION['csrf_token'])) {\n";
            $csrfInit .= "    \$_SESSION['csrf_token'] = bin2hex(random_bytes(32));\n";
            $csrfInit .= "}\n\n";
            
            // Insérer après le premier <?php
            $content = preg_replace('/^<\?php\s*/', $csrfInit, $content, 1);
        }
        
        // Ajouter le champ CSRF aux formulaires POST
        $content = preg_replace(
            '/(<form[^>]*method=["\']post["\'][^>]*>)/i',
            '$1' . "\n" . '    <input type="hidden" name="csrf_token" value="<?= $_SESSION[\'csrf_token\'] ?>">',
            $content
        );
        
        file_put_contents($filePath, $content);
    }
    
    private function fixSessionSecurity() {
        $this->log("Amélioration de la sécurité des sessions...");
        
        $authFile = $this->rootPath . '/php/utils/auth.php';
        
        if (!file_exists($authFile)) {
            $this->log("⚠️ Fichier auth.php non trouvé, création...");
            $this->createSecureAuthFile();
            return;
        }
        
        $content = file_get_contents($authFile);
        
        // Ajouter la configuration de session sécurisée
        if (strpos($content, 'session_regenerate_id') === false) {
            $sessionConfig = "\n    // Configuration de session sécurisée\n";
            $sessionConfig .= "    ini_set('session.cookie_httponly', 1);\n";
            $sessionConfig .= "    ini_set('session.cookie_secure', 1);\n";
            $sessionConfig .= "    ini_set('session.use_strict_mode', 1);\n";
            $sessionConfig .= "    session_regenerate_id(true);\n";
            
            $content = str_replace('session_start();', 'session_start();' . $sessionConfig, $content);
        }
        
        file_put_contents($authFile, $content);
        $this->log("✅ Sécurité des sessions améliorée");
    }
    
    private function createSecureAuthFile() {
        $authContent = '<?php
/**
 * Fonctions d\'authentification sécurisées - Générées automatiquement
 */

function ensureSessionStarted() {
    if (session_status() === PHP_SESSION_NONE) {
        // Configuration de session sécurisée
        ini_set(\'session.cookie_httponly\', 1);
        ini_set(\'session.cookie_secure\', 1);
        ini_set(\'session.use_strict_mode\', 1);
        ini_set(\'session.cookie_samesite\', \'Strict\');
        
        session_start();
        session_regenerate_id(true);
    }
}

function isLoggedIn() {
    ensureSessionStarted();
    return isset($_SESSION[\'user_id\']) && !empty($_SESSION[\'user_id\']);
}

function isAdmin() {
    ensureSessionStarted();
    return isLoggedIn() && isset($_SESSION[\'user_role\']) && $_SESSION[\'user_role\'] === \'admin\';
}

function logout() {
    ensureSessionStarted();
    $_SESSION = array();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), \'\', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
}
';
        
        file_put_contents($this->rootPath . '/php/utils/auth.php', $authContent);
        $this->log("✅ Fichier auth.php sécurisé créé");
    }
    
    private function improveUploadValidation() {
        $this->log("Amélioration de la validation des uploads...");
        
        // Créer une fonction de validation sécurisée
        $validationFile = $this->rootPath . '/php/utils/SecureUpload.php';
        
        $validationContent = '<?php
/**
 * Validation sécurisée des uploads - Générée automatiquement
 */

class SecureUpload {
    
    private static $allowedMimes = [
        \'image/jpeg\', \'image/png\', \'image/gif\', \'image/webp\'
    ];
    
    private static $allowedExtensions = [
        \'jpg\', \'jpeg\', \'png\', \'gif\', \'webp\'
    ];
    
    private static $maxSize = 2 * 1024 * 1024; // 2MB
    
    public static function validate($file) {
        if (!isset($file[\'tmp_name\']) || !is_uploaded_file($file[\'tmp_name\'])) {
            return [\'valid\' => false, \'error\' => \'Fichier non valide\'];
        }
        
        // Vérifier la taille
        if ($file[\'size\'] > self::$maxSize) {
            return [\'valid\' => false, \'error\' => \'Fichier trop volumineux (max 2MB)\'];
        }
        
        // Vérifier le MIME type réel
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file[\'tmp_name\']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, self::$allowedMimes)) {
            return [\'valid\' => false, \'error\' => \'Type de fichier non autorisé\'];
        }
        
        // Vérifier l\'extension
        $extension = strtolower(pathinfo($file[\'name\'], PATHINFO_EXTENSION));
        if (!in_array($extension, self::$allowedExtensions)) {
            return [\'valid\' => false, \'error\' => \'Extension non autorisée\'];
        }
        
        // Vérifier que c\'est vraiment une image
        $imageInfo = getimagesize($file[\'tmp_name\']);
        if ($imageInfo === false) {
            return [\'valid\' => false, \'error\' => \'Fichier image corrompu\'];
        }
        
        return [\'valid\' => true];
    }
    
    public static function generateSecureName($originalName) {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        return bin2hex(random_bytes(16)) . \'.\' . $extension;
    }
}
';
        
        file_put_contents($validationFile, $validationContent);
        $this->log("✅ Validation sécurisée des uploads créée");
    }
    
    private function addSecurityHeaders() {
        $this->log("Ajout des en-têtes de sécurité...");
        
        $htaccessContent = '# En-têtes de sécurité ajoutés automatiquement
<IfModule mod_headers.c>
    # Protection XSS
    Header always set X-XSS-Protection "1; mode=block"
    
    # Empêcher le MIME sniffing
    Header always set X-Content-Type-Options "nosniff"
    
    # Protection clickjacking
    Header always set X-Frame-Options "SAMEORIGIN"
    
    # Politique de référent
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    
    # Content Security Policy basique
    Header always set Content-Security-Policy "default-src \'self\'; script-src \'self\' \'unsafe-inline\' cdnjs.cloudflare.com; style-src \'self\' \'unsafe-inline\' cdnjs.cloudflare.com fonts.googleapis.com; font-src \'self\' fonts.gstatic.com; img-src \'self\' data:;"
</IfModule>

# Protection contre les injections
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{QUERY_STRING} (\<|%3C).*script.*(\>|%3E) [NC,OR]
    RewriteCond %{QUERY_STRING} GLOBALS(=|\[|\%[0-9A-Z]{0,2}) [OR]
    RewriteCond %{QUERY_STRING} _REQUEST(=|\[|\%[0-9A-Z]{0,2}) [OR]
    RewriteCond %{QUERY_STRING} \.\./\.\./\.\./\.\./\.\./\.\./\.\./\.\./\.\./\.\./
    RewriteRule ^(.*)$ index.php [F,L]
</IfModule>

';
        
        $publicHtaccess = $this->rootPath . '/public/.htaccess';
        
        if (file_exists($publicHtaccess)) {
            $content = file_get_contents($publicHtaccess);
            if (strpos($content, 'X-XSS-Protection') === false) {
                file_put_contents($publicHtaccess, $htaccessContent . "\n" . $content);
            }
        } else {
            file_put_contents($publicHtaccess, $htaccessContent);
        }
        
        $this->log("✅ En-têtes de sécurité ajoutés");
    }
    
    private function createLogDirectories() {
        $this->log("Création des répertoires de logs...");
        
        $logDirs = [
            $this->rootPath . '/logs',
            $this->rootPath . '/logs/security',
            $this->rootPath . '/logs/access',
            $this->rootPath . '/logs/errors'
        ];
        
        foreach ($logDirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
                
                // Créer un .htaccess pour protéger les logs
                $htaccessContent = "Order Deny,Allow\nDeny from all\n";
                file_put_contents($dir . '/.htaccess', $htaccessContent);
                
                $this->log("✅ Répertoire créé et protégé: $dir");
            }
        }
    }
    
    private function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message\n";
        
        file_put_contents($this->logFile, $logMessage, FILE_APPEND | LOCK_EX);
        echo $logMessage;
    }
}

// Exécution du script
if (php_sapi_name() === 'cli') {
    $automator = new SecurityFixesAutomator();
    $automator->run();
} else {
    echo "Ce script doit être exécuté en ligne de commande.\n";
    exit(1);
}
