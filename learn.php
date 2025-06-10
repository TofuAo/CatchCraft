<?php
include 'db.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ptalogin.html');
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM learn_content ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head><title>Learn Fishing</title></head>
<body>
<h2>Learn & Share Fishing Knowledge</h2>
<a href="add_learning.php">+ Share Video or Post</a>
<?php while($content = mysqli_fetch_assoc($result)): ?>
    <div>
        <h3><?= $content['title'] ?> (<?= $content['type'] ?>)</h3>
        <?php if ($content['type'] == 'video'): ?>
            <iframe width="300" height="200" src="<?= $content['video_url'] ?>" frameborder="0" allowfullscreen></iframe>
        <?php else: ?>
            <p><?= $content['content'] ?></p>
        <?php endif; ?>
    </div>
<?php endwhile; ?>
</body>
</html>
