<?php
session_start();
require_once 'db_connect.php';

// Fetch featured content from database
try {
    // Get latest fishing tips
    $tipsStmt = $conn->query("SELECT * FROM fishing_tips ORDER BY created_at DESC LIMIT 3");
    $fishingTips = $tipsStmt->fetchAll();

    // Get popular fish species
    $fishStmt = $conn->query("SELECT * FROM fish_species ORDER BY RAND() LIMIT 4");
    $fishSpecies = $fishStmt->fetchAll();

    // Get latest forum posts
    $forumStmt = $conn->query("SELECT * FROM forum_posts ORDER BY created_at DESC LIMIT 2");
    $forumPosts = $forumStmt->fetchAll();
} catch(PDOException $e) {
    // Handle error gracefully
    $fishingTips = [];
    $fishSpecies = [];
    $forumPosts = [];
    $error = "Error loading content: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Fisher - Home</title>
    <link rel="stylesheet" href="ptastyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Mobile Header -->
    <div class="mobile-header">
        <button class="menu-toggle" aria-label="Menu">
            <i class="fas fa-bars"></i>
        </button>
        <div class="logo">Master Fisher</div>
        <div class="search-toggle" aria-label="Search">
            <i class="fas fa-search"></i>
        </div>
    </div>

    <!-- Mobile Menu (same as previous) -->
    <nav class="mobile-nav">
        <!-- ... (keep same menu structure as before) ... -->
    </nav>

    <!-- Mobile Search (same as previous) -->
    <div class="mobile-search">
        <!-- ... (keep same search structure as before) ... -->
    </div>

    <!-- Main Content -->
    <main class="mobile-content">
        <!-- Welcome Banner with User Greeting -->
        <section class="welcome-banner">
            <h1>
                <?php if (isset($_SESSION['username'])): ?>
                    Welcome Back, <?php echo htmlspecialchars($_SESSION['username']); ?>!
                <?php else: ?>
                    Welcome to Master Fisher!
                <?php endif; ?>
            </h1>
            <p>Your journey from novice to master starts here</p>
        </section>

        <!-- Quick Stats -->
        <section class="quick-stats">
            <div class="stat-card">
                <i class="fas fa-fish"></i>
                <span><?php echo count($fishSpecies); ?>+ Species</span>
            </div>
            <div class="stat-card">
                <i class="fas fa-graduation-cap"></i>
                <span><?php echo count($fishingTips); ?>+ Tips</span>
            </div>
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <span>Growing Community</span>
            </div>
        </section>

        <!-- Featured Fish Species -->
        <section class="featured-section">
            <h2><i class="fas fa-fish"></i> Featured Fish Species</h2>
            <div class="scroll-container">
                <?php foreach ($fishSpecies as $fish): ?>
                    <div class="fish-card">
                        <?php if ($fish['image_path']): ?>
                            <img src="<?php echo htmlspecialchars($fish['image_path']); ?>" alt="<?php echo htmlspecialchars($fish['name']); ?>">
                        <?php else: ?>
                            <div class="fish-placeholder">
                                <i class="fas fa-fish"></i>
                            </div>
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($fish['name']); ?></h3>
                        <a href="fish-details.php?id=<?php echo $fish['id']; ?>">View Details</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Latest Fishing Tips -->
        <section class="featured-section">
            <h2><i class="fas fa-lightbulb"></i> Latest Fishing Tips</h2>
            <div class="tips-list">
                <?php foreach ($fishingTips as $tip): ?>
                    <div class="tip-card">
                        <div class="difficulty-badge <?php echo htmlspecialchars($tip['difficulty']); ?>">
                            <?php echo ucfirst(htmlspecialchars($tip['difficulty'])); ?>
                        </div>
                        <h3><?php echo htmlspecialchars($tip['title']); ?></h3>
                        <p><?php echo substr(htmlspecialchars($tip['content']), 0, 100); ?>...</p>
                        <a href="tip-details.php?id=<?php echo $tip['id']; ?>">Read More</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Community Forum Highlights -->
        <section class="featured-section">
            <h2><i class="fas fa-comments"></i> Community Discussions</h2>
            <div class="forum-highlights">
                <?php foreach ($forumPosts as $post): ?>
                    <div class="forum-post">
                        <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                        <p><?php echo substr(htmlspecialchars($post['content']), 0, 150); ?>...</p>
                        <a href="forum-post.php?id=<?php echo $post['id']; ?>">Join Discussion</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="cta-section">
            <h2>Ready to Become a Master?</h2>
            <?php if (isset($_SESSION['username'])): ?>
                <a href="learn.php" class="btn btn-primary">Continue Learning</a>
            <?php else: ?>
                <a href="register.php" class="btn btn-primary">Join Now</a>
                <p>Already a member? <a href="login.php">Sign In</a></p>
            <?php endif; ?>
        </section>
    </main>

    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <a href="index.php" class="active">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="learn.php">
            <i class="fas fa-book-open"></i>
            <span>Learn</span>
        </a>
        <a href="fish.php">
            <i class="fas fa-fish"></i>
            <span>Fish</span>
        </a>
        <?php if (isset($_SESSION['username'])): ?>
            <a href="profile.php">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </a>
        <?php else: ?>
            <a href="login.php">
                <i class="fas fa-sign-in-alt"></i>
                <span>Login</span>
            </a>
        <?php endif; ?>
    </div>

    <script src="mobile.js"></script>
</body>
</html>