<?php
require_once '../Database Connection.php';
$database = new Database();
$conn = $database->getConnection();
session_start();

header('Content-Type: application/json');

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

// Function to add new post
function addPost($data, $files) {
    if (!isAdmin()) {
        return ['success' => false, 'message' => 'Unauthorized'];
    }

    global $conn;
    try {
        // Handle image upload if present
        $image_url = null;
        if (isset($files['image']) && $files['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/posts/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_extension = strtolower(pathinfo($files['image']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($file_extension, $allowed_extensions)) {
                throw new Exception('Invalid file type. Only JPG, JPEG, PNG and GIF are allowed.');
            }

            $file_name = uniqid() . '.' . $file_extension;
            $target_path = $upload_dir . $file_name;

            if (move_uploaded_file($files['image']['tmp_name'], $target_path)) {
                $image_url = 'uploads/posts/' . $file_name;
            }
        }

        // Insert post into database
        $stmt = $conn->prepare("
            INSERT INTO posts (user_id, title, content, image_url, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $_SESSION['user_id'],
            $data['title'],
            $data['content'],
            $image_url
        ]);
        
        return [
            'success' => true,
            'post_id' => $conn->lastInsertId(),
            'message' => 'Post created successfully'
        ];
    } catch(Exception $e) {
        return ['success' => false, 'message' => 'Error creating post: ' . $e->getMessage()];
    }
}

// Function to get all posts
function getPosts() {
    global $conn;
    try {
        $stmt = $conn->query("
            SELECT p.*, u.username 
            FROM community_posts p 
            JOIN users u ON p.user_id = u.id 
            ORDER BY p.created_at DESC
        ");
        return ['success' => true, 'posts' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
    } catch(PDOException $e) {
        return ['success' => false, 'message' => 'Error fetching posts: ' . $e->getMessage()];
    }
}

// Function to delete post
function deletePost($postId) {
    if (!isAdmin()) {
        return ['success' => false, 'message' => 'Unauthorized'];
    }

    global $conn;
    try {
        // Get post image URL before deletion
        $stmt = $conn->prepare("SELECT image_url FROM community_posts WHERE id = ?");
        $stmt->execute([$postId]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        // Delete post
        $stmt = $conn->prepare("DELETE FROM community_posts WHERE id = ?");
        $stmt->execute([$postId]);

        // Delete associated image if exists
        if ($post && $post['image_url']) {
            $image_path = '../' . $post['image_url'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        return ['success' => true, 'message' => 'Post deleted successfully'];
    } catch(PDOException $e) {
        return ['success' => false, 'message' => 'Error deleting post: ' . $e->getMessage()];
    }
}

// Handle API requests
$action = $_GET['action'] ?? '';
$response = [];

switch ($action) {
    case 'add_post':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $response = addPost($_POST, $_FILES);
        } else {
            $response = ['success' => false, 'message' => 'Invalid request method'];
        }
        break;

    case 'get_posts':
        $response = getPosts();
        break;

    case 'delete_post':
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $postId = $_GET['post_id'] ?? null;
            if ($postId) {
                $response = deletePost($postId);
            } else {
                $response = ['success' => false, 'message' => 'Post ID is required'];
            }
        } else {
            $response = ['success' => false, 'message' => 'Invalid request method'];
        }
        break;

    default:
        $response = ['success' => false, 'message' => 'Invalid action'];
}

echo json_encode($response); 