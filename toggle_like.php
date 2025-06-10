<?php
require_once 'Database Connection.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

if (!isset($_POST['post_id'])) {
    echo json_encode(['success' => false, 'message' => 'Post ID is required']);
    exit;
}

$database = new Database();
$conn = $database->getConnection();

try {
    $user_id = $_SESSION['user_id'];
    $post_id = $_POST['post_id'];

    // Check if user already liked the post
    $check_query = "SELECT id FROM post_likes WHERE post_id = :post_id AND user_id = :user_id";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bindParam(':post_id', $post_id);
    $check_stmt->bindParam(':user_id', $user_id);
    $check_stmt->execute();

    if ($check_stmt->rowCount() > 0) {
        // Unlike: Remove the like
        $delete_query = "DELETE FROM post_likes WHERE post_id = :post_id AND user_id = :user_id";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bindParam(':post_id', $post_id);
        $delete_stmt->bindParam(':user_id', $user_id);
        $delete_stmt->execute();
        
        $action = 'unliked';
    } else {
        // Like: Add new like
        $insert_query = "INSERT INTO post_likes (post_id, user_id) VALUES (:post_id, :user_id)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bindParam(':post_id', $post_id);
        $insert_stmt->bindParam(':user_id', $user_id);
        $insert_stmt->execute();
        
        $action = 'liked';
    }

    // Get updated like count
    $count_query = "SELECT COUNT(*) as like_count FROM post_likes WHERE post_id = :post_id";
    $count_stmt = $conn->prepare($count_query);
    $count_stmt->bindParam(':post_id', $post_id);
    $count_stmt->execute();
    $result = $count_stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'action' => $action,
        'like_count' => $result['like_count']
    ]);

} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error updating like: ' . $e->getMessage()
    ]);
}
?> 