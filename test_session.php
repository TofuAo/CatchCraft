<?php
// Start session with explicit configuration
ini_set('session.gc_maxlifetime', 3600); // Set session timeout to 1 hour
ini_set('session.cookie_lifetime', 3600); // Set cookie timeout to 1 hour
session_start();

// Output session configuration
$sessionConfig = [
    'session.gc_maxlifetime' => ini_get('session.gc_maxlifetime'),
    'session.cookie_lifetime' => ini_get('session.cookie_lifetime'),
    'session.save_handler' => ini_get('session.save_handler'),
    'session.save_path' => ini_get('session.save_path'),
    'session.name' => session_name(),
    'session_id' => session_id()
];

header('Content-Type: application/json');
echo json_encode([
    'session_config' => $sessionConfig,
    'session_data' => $_SESSION,
    'cookies' => $_COOKIE
], JSON_PRETTY_PRINT);
?> 