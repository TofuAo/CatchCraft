<?php
require_once 'Database Connection.php';
session_start();
header('Content-Type: application/json');

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    // Get all users with their progress information
    $stmt = $conn->query("
        SELECT 
            u.id,
            u.username,
            u.email,
            u.created_at as join_date,
            u.is_admin
        FROM users u
        ORDER BY u.created_at DESC
    ");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'users' => $users
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?> 