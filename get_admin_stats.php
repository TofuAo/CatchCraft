<?php
require_once 'db_connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}

try {
    // Get total users
    $stmt = $conn->query("SELECT COUNT(*) as total FROM users");
    $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get active users (users who logged in within last 30 days)
    $stmt = $conn->query("SELECT COUNT(*) as active FROM users WHERE last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $activeUsers = $stmt->fetch(PDO::FETCH_ASSOC)['active'];

    // Get total learning paths
    $stmt = $conn->query("SELECT COUNT(*) as total FROM learning_paths");
    $totalPaths = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get total modules (use 'learning_modules' table)
    $stmt = $conn->query("SELECT COUNT(*) as total FROM learning_modules");
    $totalModules = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    echo json_encode([
        'success' => true,
        'stats' => [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'total_paths' => $totalPaths,
            'total_modules' => $totalModules
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?> 