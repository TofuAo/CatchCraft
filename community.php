<?php
include 'db.php';
$news = mysqli_query($conn, "SELECT * FROM community_links WHERE category='news'");
$tools = mysqli_query($conn, "SELECT * FROM community_links WHERE category='tools'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Community Links</title>
    <style>
        .slider { display: flex; overflow-x: auto; scroll-snap-type: x mandatory; }
        .slide { flex: 0 0 100%; scroll-snap-align: center; padding: 2rem; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<h2>Community Links</h2>
<div class="slider">
    <div class="slide">
        <h3>Fishing News</h3>
        <ul>
            <?php while($link = mysqli_fetch_assoc($news)): ?>
                <li><a href="<?= $link['url'] ?>" target="_blank"><?= $link['title'] ?></a></li>
            <?php endwhile; ?>
        </ul>
    </div>
    <div class="slide">
        <h3>Fishing Tools</h3>
        <ul>
            <?php while($link = mysqli_fetch_assoc($tools)): ?>
                <li><a href="<?= $link['url'] ?>" target="_blank"><?= $link['title'] ?></a></li>
            <?php endwhile; ?>
        </ul>
    </div>
</div>
</body>
</html>
