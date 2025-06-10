<?php
require_once __DIR__ . '/../DatabaseConnection.php';
$database = new Database();
$conn = $database->getConnection();
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Get all posts with user info, likes, and comments
function getPosts() {
    global $conn;
    try {
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
        $stmt = $conn->query("
            SELECT 
                p.*,
                u.username,
                u.fullname,
                (SELECT COUNT(*) FROM post_likes pl WHERE pl.post_id = p.id) as like_count,
                (SELECT COUNT(*) FROM post_comments pc WHERE pc.post_id = p.id) as comment_count,
                CASE WHEN EXISTS (
                    SELECT 1 FROM post_likes pl 
                    WHERE pl.post_id = p.id AND pl.user_id = " . $userId . "
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
        }
        
        return ['success' => true, 'posts' => $posts];
    } catch(PDOException $e) {
        return ['success' => false, 'message' => 'Error fetching posts: ' . $e->getMessage()];
    }
}

// Create a new post
function createPost($content, $imageUrl = null) {
    if (!isLoggedIn()) {
        return ['success' => false, 'message' => 'User must be logged in'];
    }
    
    global $conn;
    try {
        $stmt = $conn->prepare("
            INSERT INTO community_posts (user_id, content, image_url)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$_SESSION['user_id'], $content, $imageUrl]);
        
        // Get the newly created post with user info
        $postId = $conn->lastInsertId();
        $stmt = $conn->prepare("
            SELECT 
                p.*,
                u.username,
                u.fullname
            FROM community_posts p
            JOIN users u ON p.user_id = u.id
            WHERE p.id = ?
        ");
        $stmt->execute([$postId]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return ['success' => true, 'post' => $post];
    } catch(PDOException $e) {
        return ['success' => false, 'message' => 'Error creating post: ' . $e->getMessage()];
    }
}

// Add a comment to a post
function addComment($postId, $content) {
    if (!isLoggedIn()) {
        return ['success' => false, 'message' => 'User must be logged in'];
    }
    
    global $conn;
    try {
        $stmt = $conn->prepare("
            INSERT INTO post_comments (post_id, user_id, content)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$postId, $_SESSION['user_id'], $content]);
        
        // Get the newly created comment with user info
        $commentId = $conn->lastInsertId();
        $stmt = $conn->prepare("
            SELECT 
                c.*,
                u.username,
                u.fullname
            FROM post_comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.id = ?
        ");
        $stmt->execute([$commentId]);
        $comment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Update comment count
        $stmt = $conn->prepare("
            UPDATE community_posts 
            SET comments = (
                SELECT COUNT(*) FROM post_comments WHERE post_id = ?
            )
            WHERE id = ?
        ");
        $stmt->execute([$postId, $postId]);
        
        return ['success' => true, 'comment' => $comment];
    } 
    catch(PDOException $e) {
        return ['success' => false, 'message' => 'Error adding comment: ' . $e->getMessage()];
    }
}

// Toggle like on a post
function toggleLike($postId) {
    if (!isLoggedIn()) {
        return ['success' => false, 'message' => 'User must be logged in'];
    }
    
    global $conn;
    try {
        // Check if user already liked the post
        $stmt = $conn->prepare("
            SELECT id FROM post_likes 
            WHERE post_id = ? AND user_id = ?
        ");
        $stmt->execute([$postId, $_SESSION['user_id']]);
        $existingLike = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingLike) {
            // Unlike
            $stmt = $conn->prepare("
                DELETE FROM post_likes 
                WHERE post_id = ? AND user_id = ?
            ");
            $stmt->execute([$postId, $_SESSION['user_id']]);
            $liked = false;
        } else {
            // Like
            $stmt = $conn->prepare("
                INSERT INTO post_likes (post_id, user_id)
                VALUES (?, ?)
            ");
            $stmt->execute([$postId, $_SESSION['user_id']]);
            $liked = true;
        }
        
        // Update like count
        $stmt = $conn->prepare("
            UPDATE community_posts 
            SET likes = (
                SELECT COUNT(*) FROM post_likes WHERE post_id = ?
            )
            WHERE id = ?
        ");
        $stmt->execute([$postId, $postId]);
        
        // Get updated like count
        $stmt = $conn->prepare("
            SELECT likes FROM community_posts WHERE id = ?
        ");
        $stmt->execute([$postId]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'success' => true,
            'liked' => $liked,
            'likes' => $post['likes']
        ];
    } catch(PDOException $e) {
        return ['success' => false, 'message' => 'Error toggling like: ' . $e->getMessage()];
    }
}

// Get comments for a post
function getComments($postId) {
    global $conn;
    try {
        $stmt = $conn->prepare("
            SELECT 
                c.*,
                u.username,
                u.fullname
            FROM post_comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.post_id = ?
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([$postId]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return ['success' => true, 'comments' => $comments];
    } catch(PDOException $e) {
        return ['success' => false, 'message' => 'Error fetching comments: ' . $e->getMessage()];
    }
}

// Handle API requests
$action = $_GET['action'] ?? '';
$response = [];

switch ($action) {
    case 'get_posts':
        $response = getPosts();
        break;
        
    case 'create_post':
        if (!isset($_SESSION['user_id'])) {
            $response = ['success' => false, 'message' => 'User must be logged in'];
            break;
        }
        $content = $_POST['content'] ?? '';
        $imageUrl = $_POST['image_url'] ?? null;
        if ($content) {
            $response = createPost($content, $imageUrl);
        } else {
            $response = ['success' => false, 'message' => 'Content is required'];
        }
        break;
        
    case 'add_comment':
        if (!isset($_SESSION['user_id'])) {
            $response = ['success' => false, 'message' => 'User must be logged in'];
            break;
        }
        $postId = $_POST['post_id'] ?? null;
        $content = $_POST['content'] ?? '';
        if ($postId && $content) {
            $response = addComment($postId, $content);
        } else {
            $response = ['success' => false, 'message' => 'Post ID and content are required'];
        }
        break;
        
    case 'toggle_like':
        if (!isset($_SESSION['user_id'])) {
            $response = ['success' => false, 'message' => 'User must be logged in'];
            break;
        }
        $postId = $_POST['post_id'] ?? null;
        if ($postId) {
            $response = toggleLike($postId);
        } else {
            $response = ['success' => false, 'message' => 'Post ID is required'];
        }
        break;
        
    case 'get_comments':
        $postId = $_GET['post_id'] ?? null;
        if ($postId) {
            $response = getComments($postId);
        } else {
            $response = ['success' => false, 'message' => 'Post ID is required'];
        }
        break;
        
    default:
        $response = ['success' => false, 'message' => 'Invalid action'];
}

echo json_encode($response); 