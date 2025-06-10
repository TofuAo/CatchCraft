<?php
require_once 'Database Connection.php';
session_start();
header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$database = new Database();
$conn = $database->getConnection();

// Handle file upload
function handleImageUpload() {
    if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $file = $_FILES['image'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    
    // Get file extension
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    // Allowed extensions
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    
    // Validate file
    if ($fileError !== 0) {
        throw new Exception('Error uploading file');
    }
    
    if (!in_array($fileExt, $allowed)) {
        throw new Exception('Invalid file type. Allowed types: ' . implode(', ', $allowed));
    }
    
    if ($fileSize > 5000000) { // 5MB max
        throw new Exception('File size too large. Maximum size: 5MB');
    }
    
    // Generate unique filename
    $newFileName = uniqid('post_', true) . '.' . $fileExt;
    $uploadPath = 'uploads/posts/' . $newFileName;
    
    // Create directory if it doesn't exist
    if (!file_exists('uploads/posts')) {
        mkdir('uploads/posts', 0777, true);
    }
    
    // Move uploaded file
    if (!move_uploaded_file($fileTmpName, $uploadPath)) {
        throw new Exception('Failed to save image');
    }
    
    return $uploadPath;
}

try {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'create':
            // Validate input
            if (empty($_POST['title']) || empty($_POST['content'])) {
                throw new Exception('Title and content are required');
            }

            $imageUrl = null;
            try {
                $imageUrl = handleImageUpload();
            } catch (Exception $e) {
                // Log the error but continue without image
                error_log("Image upload failed: " . $e->getMessage());
            }

            // Insert post
            $stmt = $conn->prepare("
                INSERT INTO posts (user_id, title, content, image_url) 
                VALUES (?, ?, ?, ?)
            ");
            
            if ($stmt->execute([$_SESSION['user_id'], $_POST['title'], $_POST['content'], $imageUrl])) {
                echo json_encode(['success' => true, 'message' => 'Post created successfully']);
            } else {
                throw new Exception('Failed to create post');
            }
            break;

        case 'update':
            if (empty($_POST['post_id']) || empty($_POST['title']) || empty($_POST['content'])) {
                throw new Exception('Post ID, title and content are required');
            }

            $imageUrl = null;
            try {
                $imageUrl = handleImageUpload();
            } catch (Exception $e) {
                // Log the error but continue without image
                error_log("Image upload failed: " . $e->getMessage());
            }

            // Update post
            $sql = "UPDATE posts SET title = ?, content = ?";
            $params = [$_POST['title'], $_POST['content']];

            if ($imageUrl) {
                $sql .= ", image_url = ?";
                $params[] = $imageUrl;
            }

            $sql .= " WHERE id = ? AND user_id = ?";
            $params[] = $_POST['post_id'];
            $params[] = $_SESSION['user_id'];

            $stmt = $conn->prepare($sql);
            if ($stmt->execute($params)) {
                echo json_encode(['success' => true, 'message' => 'Post updated successfully']);
            } else {
                throw new Exception('Failed to update post');
            }
            break;

        case 'delete':
            if (empty($_POST['post_id'])) {
                throw new Exception('Post ID is required');
            }

            $stmt = $conn->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
            if ($stmt->execute([$_POST['post_id'], $_SESSION['user_id']])) {
                echo json_encode(['success' => true, 'message' => 'Post deleted successfully']);
            } else {
                throw new Exception('Failed to delete post');
            }
            break;

        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 