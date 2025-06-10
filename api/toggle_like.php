<?php
require_once __DIR__ . '/../DatabaseConnection.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Get post data from request body
    $data = json_decode(file_get_contents('php://input'), true);
    $postId = $data['post_id'] ?? null;
    $userId = $_SESSION['user_id'];

    if (!$postId) {
        echo json_encode(['success' => false, 'message' => 'Post ID is required']);
        exit;
    }

    // Check if user has already liked the post
    $stmt = $conn->prepare("SELECT id FROM forum_likes WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$postId, $userId]);
    $existingLike = $stmt->fetch();

    if ($existingLike) {
        // Unlike: Remove the like
        $stmt = $conn->prepare("DELETE FROM forum_likes WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$postId, $userId]);
        $message = 'Post unliked successfully';
    } else {
        // Like: Add new like
        $stmt = $conn->prepare("INSERT INTO forum_likes (post_id, user_id) VALUES (?, ?)");
        $stmt->execute([$postId, $userId]);
        $message = 'Post liked successfully';
    }

    echo json_encode(['success' => true, 'message' => $message]);

} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to update like']);
}
?> 