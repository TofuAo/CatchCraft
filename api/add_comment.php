<?php
session_start();
require_once __DIR__ . '/../DatabaseConnection.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to comment']);
    exit;
}

// Check if required fields are present
if (!isset($_POST['post_id']) || !isset($_POST['content']) || empty(trim($_POST['content']))) {
    echo json_encode(['success' => false, 'message' => 'Post ID and content are required']);
    exit;
}

$userId = $_SESSION['user_id'];
$postId = intval($_POST['post_id']);
$content = trim($_POST['content']);

try {
    // First verify that the post exists
    $database = new Database();
    $conn = $database->getConnection();
    $checkStmt = $conn->prepare("SELECT 1 FROM forum_posts WHERE id = ?");
    $checkStmt->execute([$postId]);
    if (!$checkStmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Post not found']);
        exit;
    }

    // Add the comment
    $stmt = $conn->prepare("INSERT INTO forum_comments (post_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$postId, $userId, $content]);

    // Get the newly created comment with username
    $newCommentId = $conn->lastInsertId();
    $getStmt = $conn->prepare("SELECT c.*, u.username 
                              FROM forum_comments c
                              LEFT JOIN users u ON c.user_id = u.id
                              WHERE c.id = ?");
    $getStmt->execute([$newCommentId]);
    $comment = $getStmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'message' => 'Comment added successfully', 'comment' => $comment]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to add comment']);
}
?> 