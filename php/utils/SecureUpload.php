<?php
/**
 * Validation sécurisée des uploads - Générée automatiquement
 */

class SecureUpload {
    
    private static $allowedMimes = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp'
    ];
    
    private static $allowedExtensions = [
        'jpg', 'jpeg', 'png', 'gif', 'webp'
    ];
    
    private static $maxSize = 2 * 1024 * 1024; // 2MB
    
    public static function validate($file) {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['valid' => false, 'error' => 'Fichier non valide'];
        }
        
        // Vérifier la taille
        if ($file['size'] > self::$maxSize) {
            return ['valid' => false, 'error' => 'Fichier trop volumineux (max 2MB)'];
        }
        
        // Vérifier le MIME type réel
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, self::$allowedMimes)) {
            return ['valid' => false, 'error' => 'Type de fichier non autorisé'];
        }
        
        // Vérifier l'extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, self::$allowedExtensions)) {
            return ['valid' => false, 'error' => 'Extension non autorisée'];
        }
        
        // Vérifier que c'est vraiment une image
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return ['valid' => false, 'error' => 'Fichier image corrompu'];
        }
        
        return ['valid' => true];
    }
    
    public static function generateSecureName($originalName) {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        return bin2hex(random_bytes(16)) . '.' . $extension;
    }
}
