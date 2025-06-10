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
    // Get all learning paths with module counts
    $stmt = $conn->query("
        SELECT 
            lp.*,
            COUNT(m.id) as module_count
        FROM learning_paths lp
        LEFT JOIN modules m ON lp.id = m.path_id
        GROUP BY lp.id
        ORDER BY lp.title
    ");
    
    $paths = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'paths' => $paths
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?> 