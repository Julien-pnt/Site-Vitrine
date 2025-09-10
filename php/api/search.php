<?php
/**
 * API de recherche moderne pour Elixir du Temps
 * Endpoint: /php/api/search.php
 */

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

// CORS pour les requêtes AJAX
$allowedOrigins = [
    'http://localhost',
    'https://localhost',
    '127.0.0.1'
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
}

header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// Gestion des requêtes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // Vérification de la méthode
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Méthode non autorisée', 405);
    }
    
    // Protection CSRF basique
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
        // Pour les requêtes GET (recherche simple), on autorise
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            throw new Exception('Requête non autorisée', 403);
        }
    }
    
    // Récupération des paramètres
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON invalide', 400);
        }
        $query = $input['query'] ?? '';
        $limit = (int)($input['limit'] ?? 10);
        $category = $input['category'] ?? '';
    } else {
        $query = $_GET['q'] ?? '';
        $limit = (int)($_GET['limit'] ?? 10);
        $category = $_GET['category'] ?? '';
    }
    
    // Validation
    $query = trim($query);
    if (empty($query)) {
        throw new Exception('Terme de recherche requis', 400);
    }
    
    if (strlen($query) < 2) {
        throw new Exception('Le terme de recherche doit contenir au moins 2 caractères', 400);
    }
    
    if ($limit < 1 || $limit > 50) {
        $limit = 10;
    }
    
    // Nettoyage de la requête
    $query = htmlspecialchars($query, ENT_QUOTES, 'UTF-8');
    
    // Connexion à la base de données
    require_once __DIR__ . '/../config/database.php';
    
    if (!isset($pdo)) {
        throw new Exception('Erreur de connexion à la base de données', 500);
    }
    
    // Construction de la requête de recherche
    $searchTerms = explode(' ', $query);
    $searchConditions = [];
    $params = [];
    
    foreach ($searchTerms as $index => $term) {
        $term = trim($term);
        if (strlen($term) >= 2) {
            $paramName = "term{$index}";
            $searchConditions[] = "(
                p.nom LIKE :{$paramName}_nom OR 
                p.description LIKE :{$paramName}_desc OR 
                p.reference LIKE :{$paramName}_ref OR
                c.nom LIKE :{$paramName}_cat OR
                col.nom LIKE :{$paramName}_col
            )";
            $params["{$paramName}_nom"] = "%{$term}%";
            $params["{$paramName}_desc"] = "%{$term}%";
            $params["{$paramName}_ref"] = "%{$term}%";
            $params["{$paramName}_cat"] = "%{$term}%";
            $params["{$paramName}_col"] = "%{$term}%";
        }
    }
    
    if (empty($searchConditions)) {
        echo json_encode(['results' => [], 'total' => 0]);
        exit;
    }
    
    $whereClause = implode(' AND ', $searchConditions);
    
    // Ajouter filtre par catégorie si spécifié
    if (!empty($category)) {
        $whereClause .= " AND c.nom = :category";
        $params['category'] = $category;
    }
    
    // Requête principale
    $sql = "
        SELECT 
            p.id,
            p.nom,
            p.reference,
            p.description,
            p.prix,
            p.prix_promo,
            p.image,
            p.stock,
            p.note_moyenne,
            p.nb_avis,
            c.nom as categorie,
            col.nom as collection,
            CASE 
                WHEN p.prix_promo IS NOT NULL AND p.prix_promo > 0 THEN p.prix_promo
                ELSE p.prix
            END as prix_final,
            CASE 
                WHEN p.nom LIKE :exact_match THEN 100
                WHEN p.nom LIKE :start_match THEN 90
                WHEN p.reference LIKE :ref_match THEN 80
                WHEN p.description LIKE :desc_match THEN 70
                ELSE 60
            END as relevance_score
        FROM produits p
        LEFT JOIN categories c ON p.categorie_id = c.id
        LEFT JOIN collections col ON p.collection_id = col.id
        WHERE p.visible = 1 
        AND p.stock > 0
        AND ({$whereClause})
        ORDER BY relevance_score DESC, p.note_moyenne DESC, p.nb_avis DESC
        LIMIT :limit
    ";
    
    // Paramètres pour le scoring de pertinence
    $params['exact_match'] = "%{$query}%";
    $params['start_match'] = "{$query}%";
    $params['ref_match'] = "%{$query}%";
    $params['desc_match'] = "%{$query}%";
    $params['limit'] = $limit;
    
    $stmt = $pdo->prepare($sql);
    
    // Binding des paramètres
    foreach ($params as $key => $value) {
        if ($key === 'limit') {
            $stmt->bindValue(":$key", $value, PDO::PARAM_INT);
        } else {
            $stmt->bindValue(":$key", $value, PDO::PARAM_STR);
        }
    }
    
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatage des résultats
    $formattedResults = [];
    foreach ($results as $row) {
        // URL de l'image
        $imageUrl = '/public/uploads/products/default-watch.jpg';
        if (!empty($row['image'])) {
            $imageUrl = '/public/uploads/products/' . basename($row['image']);
        }
        
        // URL du produit
        $productUrl = "/public/pages/products/product-detail.php?id=" . $row['id'];
        
        // Prix formaté
        $prixFormate = number_format($row['prix_final'], 0, ',', ' ') . ' €';
        
        // Badge de promotion
        $hasPromo = !empty($row['prix_promo']) && $row['prix_promo'] < $row['prix'];
        $promoPercent = 0;
        if ($hasPromo) {
            $promoPercent = round((($row['prix'] - $row['prix_promo']) / $row['prix']) * 100);
        }
        
        // Note étoiles
        $stars = '';
        $note = (float)$row['note_moyenne'];
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $note) {
                $stars .= '★';
            } elseif ($i - 0.5 <= $note) {
                $stars .= '☆';
            } else {
                $stars .= '☆';
            }
        }
        
        $formattedResults[] = [
            'id' => (int)$row['id'],
            'title' => $row['nom'],
            'reference' => $row['reference'] ?: "ELX-{$row['id']}",
            'description' => substr(strip_tags($row['description']), 0, 100) . '...',
            'category' => $row['categorie'] ?: 'Montres',
            'collection' => $row['collection'],
            'price' => $prixFormate,
            'originalPrice' => $hasPromo ? number_format($row['prix'], 0, ',', ' ') . ' €' : null,
            'promoPercent' => $hasPromo ? "-{$promoPercent}%" : null,
            'image' => $imageUrl,
            'url' => $productUrl,
            'stock' => (int)$row['stock'],
            'rating' => round($note, 1),
            'ratingStars' => $stars,
            'reviewCount' => (int)$row['nb_avis'],
            'relevanceScore' => (int)$row['relevance_score']
        ];
    }
    
    // Compter le total pour la pagination (si nécessaire)
    $countSql = "
        SELECT COUNT(DISTINCT p.id) as total
        FROM produits p
        LEFT JOIN categories c ON p.categorie_id = c.id
        LEFT JOIN collections col ON p.collection_id = col.id
        WHERE p.visible = 1 
        AND p.stock > 0
        AND ({$whereClause})
    ";
    
    $countStmt = $pdo->prepare($countSql);
    foreach ($params as $key => $value) {
        if ($key !== 'limit') {
            $countStmt->bindValue(":$key", $value, PDO::PARAM_STR);
        }
    }
    $countStmt->execute();
    $totalResults = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Réponse JSON
    $response = [
        'success' => true,
        'results' => $formattedResults,
        'total' => (int)$totalResults,
        'query' => $query,
        'limit' => $limit,
        'hasMore' => $totalResults > count($formattedResults)
    ];
    
    // Suggestions de recherche si peu de résultats
    if (count($formattedResults) < 3) {
        $suggestions = generateSearchSuggestions($query, $pdo);
        if (!empty($suggestions)) {
            $response['suggestions'] = $suggestions;
        }
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    error_log("Erreur base de données recherche: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur de base de données',
        'message' => 'Une erreur est survenue lors de la recherche'
    ]);
} catch (Exception $e) {
    $code = $e->getCode() ?: 400;
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur de recherche',
        'message' => $e->getMessage()
    ]);
}

/**
 * Génère des suggestions de recherche basées sur le terme recherché
 */
function generateSearchSuggestions($query, $pdo) {
    try {
        $suggestions = [];
        
        // Rechercher des termes similaires dans les noms de produits
        $sql = "
            SELECT DISTINCT nom
            FROM produits 
            WHERE visible = 1 
            AND (
                nom LIKE :partial1 OR
                nom LIKE :partial2 OR
                description LIKE :partial3
            )
            LIMIT 5
        ";
        
        $stmt = $pdo->prepare($sql);
        $partial = substr($query, 0, -1) . '%';
        $stmt->execute([
            'partial1' => $partial,
            'partial2' => '%' . substr($query, 1) . '%',
            'partial3' => '%' . $query . '%'
        ]);
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $suggestions[] = $row['nom'];
        }
        
        // Ajouter des suggestions de catégories populaires
        if (empty($suggestions)) {
            $categoriesSql = "
                SELECT c.nom, COUNT(p.id) as product_count
                FROM categories c
                JOIN produits p ON c.id = p.categorie_id
                WHERE p.visible = 1 AND p.stock > 0
                GROUP BY c.id, c.nom
                ORDER BY product_count DESC
                LIMIT 3
            ";
            
            $catStmt = $pdo->prepare($categoriesSql);
            $catStmt->execute();
            
            while ($row = $catStmt->fetch(PDO::FETCH_ASSOC)) {
                $suggestions[] = $row['nom'];
            }
        }
        
        return array_unique($suggestions);
        
    } catch (Exception $e) {
        error_log("Erreur génération suggestions: " . $e->getMessage());
        return [];
    }
}
?>
