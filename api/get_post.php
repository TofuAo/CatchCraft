<?php
session_start();
require_once __DIR__ . '/../DatabaseConnection.php';

$database = new Database();
$conn = $database->getConnection();

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Post ID is required']);
    exit;
}

$postId = intval($_GET['id']);

try {
    $stmt = $conn->prepare("SELECT p.*, u.username, 
                           (SELECT COUNT(*) FROM forum_likes WHERE post_id = p.id) as likes_count,
                           (SELECT COUNT(*) FROM forum_comments WHERE post_id = p.id) as comments_count
                           FROM forum_posts p 
                           LEFT JOIN users u ON p.user_id = u.id 
                           WHERE p.id = ?");
    $stmt->execute([$postId]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($post) {
        // Check if the current user has liked this post
        if (isset($_SESSION['user_id'])) {
            $likeStmt = $conn->prepare("SELECT 1 FROM forum_likes WHERE post_id = ? AND user_id = ?");
            $likeStmt->execute([$postId, $_SESSION['user_id']]);
            $post['user_liked'] = (bool)$likeStmt->fetch();
        } else {
            $post['user_liked'] = false;
        }

        echo json_encode(['success' => true, 'post' => $post]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Post not found']);
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to load post']);
}
?> 