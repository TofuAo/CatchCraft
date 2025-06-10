<?php
require_once __DIR__ . '/../db_connect.php';
header('Content-Type: application/json');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Module ID is required.']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT * FROM learning_modules WHERE id = ?");
    $stmt->execute([$id]);
    $module = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($module) {
        echo json_encode(['success' => true, 'module' => $module]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Module not found.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} 