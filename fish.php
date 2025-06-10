<?php
error_reporting(0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../session_config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Fetch all fish species from database
try {
    $stmt = $conn->query("SELECT * FROM fish_species ORDER BY name");
    $fishSpecies = $stmt->fetchAll();
} catch(PDOException $e) {
    die("Error fetching fish species: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Your existing mobile header/meta tags -->
    <title>Fish Species - Master Fisher</title>
</head>
<body>
    <!-- Your mobile menu/navigation -->
    
    <div class="mobile-content">
        <h1>Fish Species</h1>
        
        <div class="search-container">
            <input type="text" id="fish-search" placeholder="Search fish...">
        </div>
        
        <div class="fish-list">
            <?php foreach ($fishSpecies as $fish): ?>
                <div class="fish-card">
                    <?php if ($fish['image_path']): ?>
                        <img src="<?php echo htmlspecialchars($fish['image_path']); ?>" alt="<?php echo htmlspecialchars($fish['name']); ?>">
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($fish['name']); ?></h3>
                    <p><em><?php echo htmlspecialchars($fish['scientific_name']); ?></em></p>
                    <p><?php echo htmlspecialchars(substr($fish['description'], 0, 100)); ?>...</p>
                    <a href="fish-details.php?id=<?php echo $fish['id']; ?>">View Details</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Your bottom navigation -->
    
    <script>
        // Simple client-side search
        document.getElementById('fish-search').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const fishCards = document.querySelectorAll('.fish-card');
            
            fishCards.forEach(card => {
                const fishName = card.querySelector('h3').textContent.toLowerCase();
                if (fishName.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>