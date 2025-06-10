<?php
require_once '../session_config.php';
header('Content-Type: application/json');

if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    $is_admin = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : false;
    $user_type = $is_admin ? 'admin' : 'user';
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'is_admin' => $is_admin,
            'user_type' => $user_type
        ]
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Not authenticated',
        'session_data' => $_SESSION
    ]);
} 