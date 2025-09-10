<?php
/**
 * Script de test pour vérifier les améliorations UX
 */

echo "🕰️ ELIXIR DU TEMPS - TEST DES AMÉLIORATIONS UX\n";
echo "===============================================\n\n";

$testResults = [];
$basePath = __DIR__ . '/../public';

// Test 1: Vérification des fichiers CSS modernes
echo "1. Vérification des fichiers CSS modernes...\n";
$cssFiles = [
    'assets/css/design-system.css',
    'assets/css/navigation.css',
    'assets/css/products.css'
];

foreach ($cssFiles as $file) {
    $fullPath = $basePath . '/' . $file;
    if (file_exists($fullPath)) {
        echo "   ✅ $file (Taille: " . round(filesize($fullPath) / 1024, 2) . " KB)\n";
        $testResults['css'][] = true;
    } else {
        echo "   ❌ $file manquant\n";
        $testResults['css'][] = false;
    }
}

// Test 2: Vérification des fichiers JavaScript
echo "\n2. Vérification des fichiers JavaScript...\n";
$jsFiles = [
    'assets/js/navigation.js',
    'assets/js/performance.js',
    'sw.js'
];

foreach ($jsFiles as $file) {
    $fullPath = $basePath . '/' . $file;
    if (file_exists($fullPath)) {
        echo "   ✅ $file (Taille: " . round(filesize($fullPath) / 1024, 2) . " KB)\n";
        $testResults['js'][] = true;
    } else {
        echo "   ❌ $file manquant\n";
        $testResults['js'][] = false;
    }
}

// Test 3: Vérification du header modernisé
echo "\n3. Vérification du header modernisé...\n";
$headerPath = $basePath . '/Includes/header.php';
if (file_exists($headerPath)) {
    $headerContent = file_get_contents($headerPath);
    
    $checks = [
        'main-header' => strpos($headerContent, 'main-header') !== false,
        'navbar' => strpos($headerContent, 'navbar') !== false,
        'mobile-menu' => strpos($headerContent, 'mobile-menu') !== false,
        'search-bar' => strpos($headerContent, 'search-bar') !== false,
        'dropdown' => strpos($headerContent, 'dropdown') !== false
    ];
    
    foreach ($checks as $feature => $exists) {
        if ($exists) {
            echo "   ✅ $feature implémenté\n";
            $testResults['header'][] = true;
        } else {
            echo "   ❌ $feature manquant\n";
            $testResults['header'][] = false;
        }
    }
} else {
    echo "   ❌ Header non trouvé\n";
    $testResults['header'] = [false];
}

// Test 4: Vérification de l'API de recherche
echo "\n4. Vérification de l'API de recherche...\n";
$searchApiPath = __DIR__ . '/../php/api/search.php';
if (file_exists($searchApiPath)) {
    echo "   ✅ API de recherche créée (Taille: " . round(filesize($searchApiPath) / 1024, 2) . " KB)\n";
    
    $apiContent = file_get_contents($searchApiPath);
    $apiChecks = [
        'CORS' => strpos($apiContent, 'Access-Control-Allow-Origin') !== false,
        'JSON Response' => strpos($apiContent, 'Content-Type: application/json') !== false,
        'Error Handling' => strpos($apiContent, 'try {') !== false,
        'Security' => strpos($apiContent, 'X-Requested-With') !== false
    ];
    
    foreach ($apiChecks as $feature => $exists) {
        if ($exists) {
            echo "   ✅ $feature\n";
            $testResults['api'][] = true;
        } else {
            echo "   ❌ $feature manquant\n";
            $testResults['api'][] = false;
        }
    }
} else {
    echo "   ❌ API de recherche non trouvée\n";
    $testResults['api'] = [false];
}

// Test 5: Vérification des fichiers de sécurité
echo "\n5. Vérification des améliorations de sécurité...\n";
$securityFiles = [
    '.htaccess',
    '../php/.htaccess',
    '../config/.htaccess',
    '../public/uploads/.htaccess'
];

foreach ($securityFiles as $file) {
    $fullPath = $basePath . '/' . $file;
    if (file_exists($fullPath)) {
        echo "   ✅ $file existe\n";
        $testResults['security'][] = true;
    } else {
        echo "   ⚠️  $file manquant\n";
        $testResults['security'][] = false;
    }
}

// Test 6: Vérification des variables CSS
echo "\n6. Vérification du système de design...\n";
$designSystemPath = $basePath . '/assets/css/design-system.css';
if (file_exists($designSystemPath)) {
    $designContent = file_get_contents($designSystemPath);
    
    $designChecks = [
        'CSS Variables' => strpos($designContent, ':root {') !== false,
        'Color Palette' => strpos($designContent, '--primary:') !== false,
        'Typography' => strpos($designContent, '--font-primary:') !== false,
        'Spacing' => strpos($designContent, '--space-') !== false,
        'Responsive' => strpos($designContent, '@media') !== false
    ];
    
    foreach ($designChecks as $feature => $exists) {
        if ($exists) {
            echo "   ✅ $feature\n";
            $testResults['design'][] = true;
        } else {
            echo "   ❌ $feature manquant\n";
            $testResults['design'][] = false;
        }
    }
} else {
    echo "   ❌ Système de design non trouvé\n";
    $testResults['design'] = [false];
}

// Résumé des tests
echo "\n" . str_repeat("=", 50) . "\n";
echo "RÉSUMÉ DES TESTS\n";
echo str_repeat("=", 50) . "\n";

$totalTests = 0;
$passedTests = 0;

foreach ($testResults as $category => $results) {
    $categoryPassed = array_sum($results);
    $categoryTotal = count($results);
    $totalTests += $categoryTotal;
    $passedTests += $categoryPassed;
    
    $percentage = $categoryTotal > 0 ? round(($categoryPassed / $categoryTotal) * 100, 1) : 0;
    $status = $percentage >= 80 ? '✅' : ($percentage >= 60 ? '⚠️' : '❌');
    
    echo sprintf("%-15s: %s %d/%d (%s%%)\n", 
        strtoupper($category), 
        $status, 
        $categoryPassed, 
        $categoryTotal, 
        $percentage
    );
}

$overallPercentage = $totalTests > 0 ? round(($passedTests / $totalTests) * 100, 1) : 0;
echo str_repeat("-", 50) . "\n";
echo sprintf("TOTAL           : %s %d/%d (%s%%)\n", 
    $overallPercentage >= 80 ? '✅' : ($overallPercentage >= 60 ? '⚠️' : '❌'),
    $passedTests, 
    $totalTests, 
    $overallPercentage
);

// Recommandations
echo "\n" . str_repeat("=", 50) . "\n";
echo "RECOMMANDATIONS\n";
echo str_repeat("=", 50) . "\n";

if ($overallPercentage >= 90) {
    echo "🎉 Excellent ! Toutes les améliorations UX sont correctement implémentées.\n";
    echo "   Le site offre maintenant une expérience utilisateur moderne et fluide.\n";
} elseif ($overallPercentage >= 80) {
    echo "👍 Très bien ! La plupart des améliorations sont en place.\n";
    echo "   Quelques ajustements mineurs pourraient encore améliorer l'expérience.\n";
} elseif ($overallPercentage >= 60) {
    echo "⚠️  Bon début ! Les bases sont là mais certains éléments nécessitent attention.\n";
    echo "   Continuez l'implémentation des améliorations manquantes.\n";
} else {
    echo "❌ Des améliorations importantes sont nécessaires.\n";
    echo "   Vérifiez que tous les fichiers ont été créés correctement.\n";
}

echo "\nProchaines étapes recommandées :\n";
echo "1. 🎨 Tester l'interface sur différentes tailles d'écran\n";
echo "2. ⚡ Vérifier les performances de chargement\n";
echo "3. 🔍 Tester la fonctionnalité de recherche\n";
echo "4. 📱 Valider la navigation mobile\n";
echo "5. ♿ Vérifier l'accessibilité\n";

echo "\n🕰️ Test terminé - " . date('Y-m-d H:i:s') . "\n";
?>
