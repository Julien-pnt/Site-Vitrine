<?php
// Démarrer la session avant tout
session_start();

header('Content-Type: application/json');

// Information sur le serveur
$serverInfo = [
    'php_version' => phpversion(),
    'server' => $_SERVER['SERVER_SOFTWARE'],
    'document_root' => $_SERVER['DOCUMENT_ROOT'],
    'script_filename' => $_SERVER['SCRIPT_FILENAME'],
    'request_uri' => $_SERVER['REQUEST_URI'],
    'session_status' => session_status(),
    'session_id' => session_id(),
    'session_data' => $_SESSION,
    'headers' => getallheaders()
];

echo json_encode([
    'success' => true,
    'message' => 'API de débogage fonctionnelle',
    'server_info' => $serverInfo,
    'timestamp' => date('Y-m-d H:i:s')
]);