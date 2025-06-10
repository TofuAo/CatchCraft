<?php
require_once 'Database Connection.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

// Set headers for JSON response
header('Content-Type: application/json');

try {
    // Get all posts with user info, likes, and comments
    $stmt = $conn->query("
        SELECT 
            p.*,
            u.username,
            u.fullname,
            (SELECT COUNT(*) FROM post_likes pl WHERE pl.post_id = p.id) as like_count,
            (SELECT COUNT(*) FROM post_comments pc WHERE pc.post_id = p.id) as comment_count,
            CASE WHEN EXISTS (
                SELECT 1 FROM post_likes pl 
                WHERE pl.post_id = p.id AND pl.user_id = " . $_SESSION['user_id'] . "
            ) THEN 1 ELSE 0 END as user_liked
        FROM community_posts p
        JOIN users u ON p.user_id = u.id
        ORDER BY p.created_at DESC
    ");
    
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get comments for each post
    foreach ($posts as &$post) {
        $stmt = $conn->prepare("
            SELECT 
                c.*,
                u.username,
                u.fullname
            FROM post_comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.post_id = ?
            ORDER BY c.created_at DESC
            LIMIT 3
        ");
        $stmt->execute([$post['id']]);
        $post['recent_comments'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format dates
        $post['created_at'] = date('M j, Y g:i a', strtotime($post['created_at']));
        
        // Ensure proper encoding of content
        $post['content'] = htmlspecialchars($post['content']);
        $post['username'] = htmlspecialchars($post['username']);
    }
    
    echo json_encode([
        'success' => true,
        'posts' => $posts
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching posts: ' . $e->getMessage()
    ]);
} 