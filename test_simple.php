<?php
echo "=== ELIXIR DU TEMPS - VALIDATION SÉCURITÉ ===\n";

$checks = 0;
$passed = 0;

// Test 1: Fichier .htaccess principal
$checks++; if (file_exists('.htaccess')) { $passed++; echo "OK"; } else { echo "FAIL"; } echo " - .htaccess principal\n";

// Test 2: Configuration sécurisée
$checks++; if (file_exists('config/config.php')) { $passed++; echo "OK"; } else { echo "FAIL"; } echo " - Configuration\n";

// Test 3: Protection force brute
$checks++; if (file_exists('php/utils/BruteForceProtection.php')) { $passed++; echo "OK"; } else { echo "FAIL"; } echo " - Protection force brute\n";

// Test 4: Middleware sécurité
$checks++; if (file_exists('php/utils/SecurityMiddleware.php')) { $passed++; echo "OK"; } else { echo "FAIL"; } echo " - Middleware sécurité\n";

// Test 5: Validateur entrée
$checks++; if (file_exists('php/utils/SecureInputValidator.php')) { $passed++; echo "OK"; } else { echo "FAIL"; } echo " - Validateur entrée\n";

// Test 6: Moniteur sécurité
$checks++; if (file_exists('php/utils/SecurityMonitor.php')) { $passed++; echo "OK"; } else { echo "FAIL"; } echo " - Moniteur sécurité\n";

// Test 7: Garde sécurité
$checks++; if (file_exists('php/utils/security_guard.php')) { $passed++; echo "OK"; } else { echo "FAIL"; } echo " - Garde sécurité\n";

// Test 8: Protection PHP
$checks++; if (file_exists('php/.htaccess')) { $passed++; echo "OK"; } else { echo "FAIL"; } echo " - Protection dossier PHP\n";

// Test 9: Protection config
$checks++; if (file_exists('config/.htaccess')) { $passed++; echo "OK"; } else { echo "FAIL"; } echo " - Protection dossier config\n";

// Test 10: Protection uploads
$checks++; if (file_exists('public/uploads/.htaccess')) { $passed++; echo "OK"; } else { echo "FAIL"; } echo " - Protection uploads\n";

$rate = round(($passed / $checks) * 100);
echo "\nRESULTAT: $passed/$checks tests passés ($rate%)\n";

if ($rate >= 90) echo "NIVEAU: EXCELLENT\n";
elseif ($rate >= 80) echo "NIVEAU: TRES BON\n";
elseif ($rate >= 70) echo "NIVEAU: BON\n";
elseif ($rate >= 60) echo "NIVEAU: CORRECT\n";
else echo "NIVEAU: INSUFFISANT\n";

echo "Audit terminé.\n";
?>
