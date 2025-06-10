<?php
require_once 'Database Connection.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

$database = new Database();
$conn = $database->getConnection();

try {
    // Query to get posts from admin users, ordered by most recent first
    $query = "
        SELECT 
            p.id,
            p.title,
            p.content,
            p.image_url,
            p.created_at,
            u.username as author,
            (SELECT COUNT(*) FROM post_likes pl WHERE pl.post_id = p.id) as like_count,
            (SELECT COUNT(*) FROM post_comments pc WHERE pc.post_id = p.id) as comment_count,
            EXISTS(SELECT 1 FROM post_likes ul WHERE ul.post_id = p.id AND ul.user_id = :user_id) as user_has_liked
        FROM posts p
        LEFT JOIN users u ON p.user_id = u.id
        WHERE u.is_admin = TRUE
        ORDER BY p.created_at DESC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();

    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($posts)) {
        echo json_encode([
            'success' => true,
            'posts' => [],
            'message' => 'No admin posts found'
        ]);
        exit;
    }

    // Format dates and process content
    foreach ($posts as &$post) {
        // Format the date
        $post['created_at'] = date('M j, Y', strtotime($post['created_at']));
        
        // Convert numeric strings to integers
        $post['like_count'] = intval($post['like_count']);
        $post['comment_count'] = intval($post['comment_count']);
        $post['user_has_liked'] = (bool)$post['user_has_liked'];
        
        // Add line breaks for better formatting
        $post['content'] = nl2br(htmlspecialchars($post['content']));
    }

    echo json_encode([
        'success' => true,
        'posts' => $posts
    ]);

} catch(PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching admin posts. Please try again later.'
    ]);
}
?> 