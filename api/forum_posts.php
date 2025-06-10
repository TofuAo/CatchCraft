<?php
require_once __DIR__ . '/../DatabaseConnection.php';
session_start();

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user is logged in for creating posts
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'User not logged in']);
        exit;
    }
    // Handle post creation
    $content = $_POST['postContent'] ?? '';
    $userId = $_SESSION['user_id'];

    if (empty($content)) {
        echo json_encode(['success' => false, 'message' => 'Post content is required']);
        exit;
    }

    try {
        // First get the username from the users table
        $userStmt = $conn->prepare("SELECT username, is_admin FROM users WHERE id = ?");
        $userStmt->execute([$userId]);
        $userData = $userStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$userData) {
            throw new Exception('User not found');
        }

        $username = $userData['username'];
        $isAdmin = $userData['is_admin'];

        // Then create the post
        $stmt = $conn->prepare("INSERT INTO forum_posts (user_id, username, content, created_at, is_admin_post) VALUES (?, ?, ?, NOW(), ?)");
        $stmt->execute([$userId, $username, $content, $isAdmin ? 1 : 0]);
        
        echo json_encode(['success' => true, 'message' => 'Post created successfully']);
    } catch (Exception $e) {
        error_log($e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to create post: ' . $e->getMessage()]);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Allow public access to fetch posts
    try {
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
        $stmt = $conn->prepare("
            SELECT 
                fp.id,
                fp.content,
                fp.created_at,
                u.username,
                u.is_admin,
                COUNT(DISTINCT fl.id) as likes_count,
                COUNT(DISTINCT fc.id) as comments_count,
                EXISTS(SELECT 1 FROM forum_likes fl2 WHERE fl2.post_id = fp.id AND fl2.user_id = ?) as user_liked
            FROM forum_posts fp
            LEFT JOIN users u ON fp.user_id = u.id
            LEFT JOIN forum_likes fl ON fp.id = fl.post_id
            LEFT JOIN forum_comments fc ON fp.id = fc.post_id
            GROUP BY fp.id, fp.content, fp.created_at, u.username, u.is_admin
            ORDER BY fp.created_at DESC
        ");
        $stmt->execute([$userId]);
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'posts' => $posts]);
    } catch (Exception $e) {
        error_log($e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error loading posts: ' . $e->getMessage()]);
    }
}
?> 