<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != true) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access',
        'session_data' => [
            'user_id' => isset($_SESSION['user_id']),
            'is_admin' => isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : null
        ]
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Admin authenticated',
    'user_id' => $_SESSION['user_id'],
    'username' => $_SESSION['username']
]);
?> 