<?php
include 'db.php';

// Set headers for JSON response
header('Content-Type: application/json');

try {
    // Fetch posts with user information
    $query = "SELECT p.*, u.username 
              FROM community_posts p 
              JOIN users u ON p.user_id = u.id 
              ORDER BY p.created_at DESC";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        throw new Exception(mysqli_error($conn));
    }
    
    $posts = array();
    while ($row = mysqli_fetch_assoc($result)) {
        // Format the post data
        $post = array(
            'id' => $row['id'],
            'username' => htmlspecialchars($row['username']),
            'content' => htmlspecialchars($row['content']),
            'created_at' => date('M j, Y g:i a', strtotime($row['created_at'])),
            'image_url' => $row['image_url'],
            'likes' => intval($row['likes']),
            'comments' => intval($row['comments'])
        );
        
        // Get likes count
        $likes_query = "SELECT COUNT(*) as count FROM post_likes WHERE post_id = " . $row['id'];
        $likes_result = mysqli_query($conn, $likes_query);
        $likes_row = mysqli_fetch_assoc($likes_result);
        $post['likes'] = intval($likes_row['count']);
        
        // Get comments count
        $comments_query = "SELECT COUNT(*) as count FROM post_comments WHERE post_id = " . $row['id'];
        $comments_result = mysqli_query($conn, $comments_query);
        $comments_row = mysqli_fetch_assoc($comments_result);
        $post['comments'] = intval($comments_row['count']);
        
        // Check if current user liked the post
        if (isset($_SESSION['user_id'])) {
            $liked_query = "SELECT 1 FROM post_likes WHERE post_id = " . $row['id'] . " AND user_id = " . $_SESSION['user_id'];
            $liked_result = mysqli_query($conn, $liked_query);
            $post['user_liked'] = (mysqli_num_rows($liked_result) > 0);
        } else {
            $post['user_liked'] = false;
        }
        
        $posts[] = $post;
    }
    
    echo json_encode(array(
        'success' => true,
        'posts' => $posts
    ));

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        'success' => false,
        'message' => $e->getMessage()
    ));
}

mysqli_close($conn);
?> 