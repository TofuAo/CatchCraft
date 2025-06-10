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
    // Get all content with their module information
    $stmt = $conn->query("
        SELECT 
            c.*,
            m.title as module_title,
            m.type as module_type
        FROM content c
        LEFT JOIN modules m ON c.module_id = m.id
        ORDER BY m.title, c.order_number
    ");
    
    $content = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'content' => $content
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?> 