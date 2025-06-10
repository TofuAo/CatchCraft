<?php
require_once 'session_config.php';
header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Debug session data
error_log("Session data in check_session: " . print_r($_SESSION, true));
error_log("Session ID: " . session_id());

if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    // Allow admin users to access both admin and user pages
    $is_admin = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : false;
    $user_type = $is_admin ? 'admin' : 'user';
    
    // Debug user status
    error_log("User ID: {$_SESSION['user_id']}, Username: {$_SESSION['username']}, Is Admin: " . ($is_admin ? 'true' : 'false'));
    
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'is_admin' => $is_admin,
            'user_type' => $user_type,
            'can_access_user_pages' => true // Always allow user page access
        ]
    ]);
} else {
    error_log("No valid session found in check_session.php");
    echo json_encode([
        'success' => false,
        'message' => 'Not authenticated',
        'session_data' => $_SESSION // Include session data for debugging
    ]);
}
?> 