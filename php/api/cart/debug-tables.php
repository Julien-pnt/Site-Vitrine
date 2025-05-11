<?php
session_start();
require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    // Tester la connexion à la base de données
    $tables = [];
    
    // Vérifier si la table panier existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'panier'");
    $panierExists = $stmt->rowCount() > 0;
    
    // Récupérer la structure de la table panier si elle existe
    $panierStructure = [];
    if ($panierExists) {
        $stmt = $pdo->query("DESCRIBE panier");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $panierStructure[] = $row;
        }
        
        // Compter les entrées
        $stmt = $pdo->query("SELECT COUNT(*) FROM panier");
        $panierCount = $stmt->fetchColumn();
    }
    
    // Récupérer des informations sur les autres tables importantes
    $tables['produits'] = [];
    $stmt = $pdo->query("SELECT COUNT(*) FROM produits");
    $tables['produits']['count'] = $stmt->fetchColumn();
    
    if (isset($_SESSION['user_id'])) {
        $tables['users'] = [];
        $stmt = $pdo->prepare("SELECT id, email, nom, prenom FROM utilisateurs WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $tables['users']['current_user'] = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Réponse finale
    echo json_encode([
        'success' => true,
        'message' => 'Informations sur les tables',
        'session' => [
            'id' => session_id(),
            'user_id' => $_SESSION['user_id'] ?? null
        ],
        'tables' => [
            'panier' => [
                'exists' => $panierExists,
                'structure' => $panierStructure ?? [],
                'count' => $panierCount ?? 0
            ],
            'other_tables' => $tables
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}