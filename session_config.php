<?php
// Set session cookie parameters BEFORE session_start
session_set_cookie_params([
    'lifetime' => 86400, // 1 day in seconds
    'path' => '/',
    'domain' => '',
    'secure' => false, // Set to true if using HTTPS
    'httponly' => true,
    'samesite' => 'Lax'
]);

ini_set('session.cookie_lifetime', 86400); // 1 day in seconds
ini_set('session.gc_maxlifetime', 86400); // 1 day in seconds

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug session configuration
error_log("Session configuration:");
error_log("Session name: " . session_name());
error_log("Session ID: " . session_id());
error_log("Session cookie params: " . print_r(session_get_cookie_params(), true));
// Removed database connection from session_config.php
?> 