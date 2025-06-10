<?php
include 'db.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ptalogin.html');
    exit;
}

// Fetch forum posts
$sql = "SELECT forum_posts.*, users.username FROM forum_posts JOIN users ON forum_posts.user_id = users.id ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head><title>Forum</title></head>
<body>
<h2>Forum Topics</h2>
<a href="create_post.php">+ Create New Post</a>
<?php while($row = mysqli_fetch_assoc($result)): ?>
    <div>
        <h3><?= $row['title'] ?></h3>
        <p><?= $row['content'] ?></p>
        <small>Posted by <?= $row['username'] ?> on <?= $row['created_at'] ?></small>
    </div>
<?php endwhile; ?>
</body>
</html>
