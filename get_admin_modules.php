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
    // Get all modules with their learning path information
    $stmt = $conn->query("
        SELECT 
            m.*,
            lp.title as path_title
        FROM modules m
        LEFT JOIN learning_paths lp ON m.path_id = lp.id
        ORDER BY lp.title, m.order_number
    ");
    
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'modules' => $modules
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?> 