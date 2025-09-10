<?php
/**
 * VALIDATEUR D'ENTRÉE SÉCURISÉ
 * Protection avancée contre les injections et attaques
 */

class SecureInputValidator {
    
    private static $instance = null;
    private $sanitizationRules = [];
    private $validationRules = [];
    private $errors = [];
    
    // Patterns d'attaques communes
    private $attackPatterns = [
        'xss' => [
            '/(\<script|\<\/script\>)/i',
            '/(javascript:|vbscript:|onload=|onerror=|onclick=)/i',
            '/(\<iframe|\<object|\<embed)/i',
            '/(eval\(|expression\()/i'
        ],
        'sql_injection' => [
            '/(union\s+select|select\s+from)/i',
            '/(insert\s+into|update\s+set|delete\s+from)/i',
            '/(drop\s+table|create\s+table|alter\s+table)/i',
            '/(exec\s+|sp_|xp_)/i',
            '/(\|\||&&|\-\-|\/\*|\*\/)/i'
        ],
        'directory_traversal' => [
            '/(\.\.\/)/',
            '/(\.\.\\\\)/',
            '/(\/etc\/|\/proc\/|\/sys\/|\/var\/)/i'
        ],
        'code_injection' => [
            '/(eval\(|exec\(|system\(|passthru\()/i',
            '/(base64_decode|gzinflate|str_rot13)/i',
            '/(include\s*\(|require\s*\()/i',
            '/(file_get_contents|fopen|fwrite)/i'
        ],
        'nosql_injection' => [
            '/(\$where|\$ne|\$gt|\$lt)/i',
            '/(\$regex|\$exists|\$type)/i'
        ]
    ];
    
    private function __construct() {
        $this->initializeDefaultRules();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Initialise les règles par défaut
     */
    private function initializeDefaultRules() {
        // Règles de validation par défaut
        $this->validationRules = [
            'email' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            'phone' => '/^(\+33|0)[1-9](\d{8})$/',
            'url' => '/^https?:\/\/[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/',
            'alphanumeric' => '/^[a-zA-Z0-9]+$/',
            'text' => '/^[a-zA-Z0-9\s\-_.,:;!?\'"()]+$/u',
            'name' => '/^[a-zA-ZÀ-ÿ\s\-\'\.]+$/u',
            'password' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            'zipcode' => '/^\d{5}$/',
            'integer' => '/^\d+$/',
            'float' => '/^\d+(\.\d+)?$/',
            'date' => '/^\d{4}-\d{2}-\d{2}$/',
            'time' => '/^\d{2}:\d{2}:\d{2}$/',
            'datetime' => '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/'
        ];
    }
    
    /**
     * Valide et nettoie un ensemble de données
     */
    public function validateAndSanitize($data, $rules) {
        $this->errors = [];
        $sanitizedData = [];
        
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            
            // Traitement selon les règles
            $processedValue = $this->processField($field, $value, $fieldRules);
            
            if ($processedValue !== false) {
                $sanitizedData[$field] = $processedValue;
            }
        }
        
        return [
            'data' => $sanitizedData,
            'errors' => $this->errors,
            'valid' => empty($this->errors)
        ];
    }
    
    /**
     * Traite un champ selon ses règles
     */
    private function processField($fieldName, $value, $rules) {
        // Règles par défaut
        $defaultRules = [
            'required' => false,
            'type' => 'string',
            'min_length' => null,
            'max_length' => null,
            'pattern' => null,
            'sanitize' => true,
            'allow_html' => false,
            'custom_validator' => null
        ];
        
        // Fusionner avec les règles fournies
        if (is_string($rules)) {
            $rules = ['type' => $rules];
        }
        $rules = array_merge($defaultRules, $rules);
        
        // Vérifier si requis
        if ($rules['required'] && (is_null($value) || $value === '')) {
            $this->addError($fieldName, "Le champ $fieldName est requis");
            return false;
        }
        
        // Si optionnel et vide, retourner null
        if (!$rules['required'] && (is_null($value) || $value === '')) {
            return null;
        }
        
        // Convertir en chaîne pour traitement
        $value = (string)$value;
        
        // Détecter les attaques
        if ($this->detectAttacks($value, $fieldName)) {
            return false;
        }
        
        // Sanitisation
        if ($rules['sanitize']) {
            $value = $this->sanitizeValue($value, $rules);
        }
        
        // Validation de type
        if (!$this->validateType($value, $rules['type'], $fieldName)) {
            return false;
        }
        
        // Validation de longueur
        if (!$this->validateLength($value, $rules, $fieldName)) {
            return false;
        }
        
        // Validation de pattern
        if ($rules['pattern'] && !$this->validatePattern($value, $rules['pattern'], $fieldName)) {
            return false;
        }
        
        // Validateur personnalisé
        if ($rules['custom_validator'] && is_callable($rules['custom_validator'])) {
            $customResult = $rules['custom_validator']($value);
            if ($customResult !== true) {
                $this->addError($fieldName, $customResult ?: "Validation personnalisée échouée pour $fieldName");
                return false;
            }
        }
        
        return $value;
    }
    
    /**
     * Détecte les patterns d'attaque
     */
    private function detectAttacks($value, $fieldName) {
        foreach ($this->attackPatterns as $attackType => $patterns) {
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $value)) {
                    $this->logSecurityThreat($attackType, $fieldName, $value);
                    $this->addError($fieldName, "Contenu suspect détecté dans $fieldName");
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Sanitise une valeur
     */
    private function sanitizeValue($value, $rules) {
        // Supprimer les espaces en début/fin
        $value = trim($value);
        
        // Gestion HTML
        if (!$rules['allow_html']) {
            $value = strip_tags($value);
        } else {
            // Permettre seulement certaines balises sûres
            $allowedTags = '<p><br><strong><em><u><a><ul><ol><li>';
            $value = strip_tags($value, $allowedTags);
        }
        
        // Échapper les caractères spéciaux
        $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Normaliser les espaces
        $value = preg_replace('/\s+/', ' ', $value);
        
        return $value;
    }
    
    /**
     * Valide le type
     */
    private function validateType($value, $type, $fieldName) {
        switch ($type) {
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($fieldName, "Format d'email invalide pour $fieldName");
                    return false;
                }
                break;
                
            case 'url':
                if (!filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->addError($fieldName, "Format d'URL invalide pour $fieldName");
                    return false;
                }
                break;
                
            case 'integer':
                if (!filter_var($value, FILTER_VALIDATE_INT)) {
                    $this->addError($fieldName, "$fieldName doit être un nombre entier");
                    return false;
                }
                break;
                
            case 'float':
                if (!filter_var($value, FILTER_VALIDATE_FLOAT)) {
                    $this->addError($fieldName, "$fieldName doit être un nombre décimal");
                    return false;
                }
                break;
                
            case 'boolean':
                if (!in_array(strtolower($value), ['true', 'false', '1', '0', 'on', 'off', 'yes', 'no'])) {
                    $this->addError($fieldName, "$fieldName doit être un booléen");
                    return false;
                }
                break;
                
            case 'date':
                if (!$this->validateDate($value)) {
                    $this->addError($fieldName, "Format de date invalide pour $fieldName (YYYY-MM-DD attendu)");
                    return false;
                }
                break;
                
            case 'phone':
                if (!$this->validatePhone($value)) {
                    $this->addError($fieldName, "Format de téléphone invalide pour $fieldName");
                    return false;
                }
                break;
                
            case 'password':
                if (!$this->validatePassword($value)) {
                    $this->addError($fieldName, "Le mot de passe doit contenir au moins 8 caractères avec majuscules, minuscules, chiffres et caractères spéciaux");
                    return false;
                }
                break;
        }
        
        return true;
    }
    
    /**
     * Valide la longueur
     */
    private function validateLength($value, $rules, $fieldName) {
        $length = mb_strlen($value, 'UTF-8');
        
        if ($rules['min_length'] && $length < $rules['min_length']) {
            $this->addError($fieldName, "$fieldName doit contenir au moins {$rules['min_length']} caractères");
            return false;
        }
        
        if ($rules['max_length'] && $length > $rules['max_length']) {
            $this->addError($fieldName, "$fieldName ne peut pas dépasser {$rules['max_length']} caractères");
            return false;
        }
        
        return true;
    }
    
    /**
     * Valide selon un pattern
     */
    private function validatePattern($value, $pattern, $fieldName) {
        // Si c'est une chaîne, chercher dans les patterns prédéfinis
        if (is_string($pattern) && isset($this->validationRules[$pattern])) {
            $pattern = $this->validationRules[$pattern];
        }
        
        if (!preg_match($pattern, $value)) {
            $this->addError($fieldName, "Format invalide pour $fieldName");
            return false;
        }
        
        return true;
    }
    
    /**
     * Valide une date
     */
    private function validateDate($date) {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
    
    /**
     * Valide un numéro de téléphone français
     */
    private function validatePhone($phone) {
        // Nettoyer le numéro
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Formats acceptés
        $patterns = [
            '/^0[1-9]\d{8}$/',           // 0123456789
            '/^\+33[1-9]\d{8}$/',        // +33123456789
            '/^33[1-9]\d{8}$/'           // 33123456789
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $phone)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Valide la force d'un mot de passe
     */
    private function validatePassword($password) {
        // Critères minimum
        $criteria = [
            '/[a-z]/',      // Minuscule
            '/[A-Z]/',      // Majuscule
            '/\d/',         // Chiffre
            '/[@$!%*?&]/'   // Caractère spécial
        ];
        
        if (strlen($password) < 8) {
            return false;
        }
        
        foreach ($criteria as $criterion) {
            if (!preg_match($criterion, $password)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Ajoute une erreur
     */
    private function addError($field, $message) {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }
    
    /**
     * Log des menaces de sécurité
     */
    private function logSecurityThreat($attackType, $fieldName, $value) {
        if (function_exists('secureLog')) {
            secureLog('security', "Attack detected in input validation", [
                'attack_type' => $attackType,
                'field' => $fieldName,
                'value_preview' => substr($value, 0, 100),
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                'url' => $_SERVER['REQUEST_URI'] ?? 'unknown'
            ]);
        }
    }
    
    /**
     * Validation rapide d'un email
     */
    public static function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validation rapide d'une URL
     */
    public static function isValidURL($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * Nettoyage rapide d'une chaîne
     */
    public static function quickSanitize($input, $allowHTML = false) {
        $input = trim($input);
        
        if (!$allowHTML) {
            $input = strip_tags($input);
        }
        
        return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    /**
     * Validation de fichier uploadé
     */
    public function validateFile($file, $rules = []) {
        $defaultRules = [
            'max_size' => 5242880, // 5MB
            'allowed_types' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'require_image' => true
        ];
        
        $rules = array_merge($defaultRules, $rules);
        
        // Vérifier les erreurs d'upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'error' => 'Erreur lors de l\'upload du fichier'];
        }
        
        // Vérifier la taille
        if ($file['size'] > $rules['max_size']) {
            $maxMB = round($rules['max_size'] / 1024 / 1024, 1);
            return ['valid' => false, 'error' => "Le fichier est trop volumineux (max: {$maxMB}MB)"];
        }
        
        // Vérifier le type MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $rules['allowed_types'])) {
            return ['valid' => false, 'error' => 'Type de fichier non autorisé'];
        }
        
        // Vérifier l'extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $rules['allowed_extensions'])) {
            return ['valid' => false, 'error' => 'Extension de fichier non autorisée'];
        }
        
        // Vérifier si c'est vraiment une image
        if ($rules['require_image']) {
            $imageInfo = getimagesize($file['tmp_name']);
            if ($imageInfo === false) {
                return ['valid' => false, 'error' => 'Le fichier n\'est pas une image valide'];
            }
        }
        
        return ['valid' => true, 'mime_type' => $mimeType, 'extension' => $extension];
    }
    
    /**
     * Obtient toutes les erreurs
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Vérifie s'il y a des erreurs
     */
    public function hasErrors() {
        return !empty($this->errors);
    }
    
    /**
     * Réinitialise les erreurs
     */
    public function clearErrors() {
        $this->errors = [];
    }
}

/**
 * Fonction utilitaire pour validation rapide
 */
function validateInput($data, $rules) {
    $validator = SecureInputValidator::getInstance();
    return $validator->validateAndSanitize($data, $rules);
}

/**
 * Fonction de nettoyage rapide
 */
function sanitize($input, $allowHTML = false) {
    return SecureInputValidator::quickSanitize($input, $allowHTML);
}
