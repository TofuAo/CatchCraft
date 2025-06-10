<?php
session_start();
require_once '../Database Connection.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Get JSON data from request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    if (empty($data['title']) || empty($data['description']) || empty($data['difficulty']) || 
        empty($data['type']) || empty($data['estimated_time']) || empty($data['modules'])) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }

    // Start transaction
    $conn->beginTransaction();

    // Insert learning path
    $stmt = $conn->prepare("INSERT INTO learning_paths (title, description, difficulty, type, estimated_time, created_at) 
                           VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([
        $data['title'],
        $data['description'],
        $data['difficulty'],
        $data['type'],
        $data['estimated_time']
    ]);

    $pathId = $conn->lastInsertId();

    // Insert modules
    $moduleStmt = $conn->prepare("INSERT INTO learning_modules (path_id, title, description, type, content_url, order_index) 
                                 VALUES (?, ?, ?, ?, ?, ?)");

    foreach ($data['modules'] as $index => $module) {
        $moduleStmt->execute([
            $pathId,
            $module['title'],
            $module['description'],
            $module['type'],
            $module['content_url'],
            $index + 1
        ]);
    }

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Learning path created successfully']);

} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn) {
        $conn->rollBack();
    }
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to create learning path']);
}
?> 