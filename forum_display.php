<?php
include 'db.php';

// Fetch posts with user information
$query = "SELECT p.*, u.username 
          FROM community_posts p 
          JOIN users u ON p.user_id = u.id 
          ORDER BY p.created_at DESC";
$result = mysqli_query($conn, $query);

// Check if query was successful
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

// Display each post
while ($row = mysqli_fetch_assoc($result)) {
    echo '<div class="post">';
    echo '<div class="post-header">';
    echo '<span class="username">' . htmlspecialchars($row['username']) . '</span>';
    echo '</div>';
    echo '<div class="post-content">' . htmlspecialchars($row['content']) . '</div>';
    echo '<div class="post-footer">';
    echo '<span class="timestamp">' . date('M j, Y g:i a', strtotime($row['created_at'])) . '</span>';
    echo '</div>';
    echo '</div>';
}

mysqli_close($conn);
?> 