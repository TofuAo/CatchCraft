<?php
session_start();
require_once __DIR__ . '/../DatabaseConnection.php';

$database = new Database();
$conn = $database->getConnection();

header('Content-Type: application/json');

if (!isset($_GET['post_id'])) {
    echo json_encode(['success' => false, 'message' => 'Post ID is required']);
    exit;
}

$postId = intval($_GET['post_id']);

try {
    $stmt = $conn->prepare("SELECT c.*, u.username 
                           FROM forum_comments c
                           LEFT JOIN users u ON c.user_id = u.id
                           WHERE c.post_id = ?
                           ORDER BY c.created_at ASC");
    $stmt->execute([$postId]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'comments' => $comments]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to load comments']);
}
?> 