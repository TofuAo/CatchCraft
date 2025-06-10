<?php
include 'db.php';
$result = mysqli_query($conn, "SELECT * FROM fish_species");
?>

<!DOCTYPE html>
<html>
<head><title>Fish Species</title></head>
<body>
<h2>Fish Species</h2>
<?php while($fish = mysqli_fetch_assoc($result)): ?>
    <div>
        <img src="<?= $fish['image_url'] ?>" alt="<?= $fish['name'] ?>" width="200">
        <h3><?= $fish['name'] ?></h3>
        <p><strong>Habitat:</strong> <?= $fish['habitat'] ?></p>
        <p><?= $fish['description'] ?></p>
    </div>
<?php endwhile; ?>
</body>
</html>
