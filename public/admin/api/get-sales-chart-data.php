<?php
// Initialisation de la session et connexion à la base de données
session_start();
require_once '../../../php/config/database.php';
require_once '../../../php/utils/auth.php';

// Vérification des droits d'administrateur
if (!isLoggedIn() || !isAdmin()) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Accès non autorisé']);
    exit;
}

// Obtenir les données de vente des 30 derniers jours
$days = 30;
$labels = [];
$salesData = [];

try {
    // Préparer la requête pour obtenir les ventes par jour
    $stmt = $pdo->prepare("
        SELECT 
            DATE(date_commande) as date,
            SUM(total) as total_sales
        FROM 
            commandes
        WHERE 
            date_commande >= DATE_SUB(CURRENT_DATE, INTERVAL :days DAY)
            AND statut IN ('payee', 'en_preparation', 'expediee', 'livree')
        GROUP BY 
            DATE(date_commande)
        ORDER BY 
            date ASC
    ");
    
    $stmt->bindParam(':days', $days, PDO::PARAM_INT);
    $stmt->execute();
    
    // Initialiser un tableau avec les 30 derniers jours à zéro
    $salesByDate = [];
    for ($i = $days - 1; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $salesByDate[$date] = 0;
        $labels[] = date('d/m', strtotime($date));
    }
    
    // Remplir avec les données réelles
    while ($row = $stmt->fetch()) {
        $salesByDate[$row['date']] = (float)$row['total_sales'];
    }
    
    // Convertir en tableau simple pour Chart.js
    $salesData = array_values($salesByDate);
    
    // Retourner les données au format JSON
    header('Content-Type: application/json');
    echo json_encode([
        'labels' => $labels,
        'sales' => $salesData
    ]);
    
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Erreur lors de la récupération des données']);
    error_log('Erreur API get-sales-chart-data: ' . $e->getMessage());
}