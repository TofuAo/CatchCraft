<?php
require_once '../session_config.php';
require_once '../DatabaseConnection.php';

header('Content-Type: application/json');

// Database connection
$database = new Database();
$conn = $database->getConnection();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

// Get user profile
function getProfile() {
    global $conn;
    try {
        $stmt = $conn->prepare("
            SELECT u.*, 
                   up.bio, 
                   up.location, 
                   up.experience,
                   up.avatar_url
            FROM users u
            LEFT JOIN user_profiles up ON u.id = up.user_id
            WHERE u.id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Remove sensitive information
            unset($user['password']);
            return ['success' => true, 'user' => $user];
        } else {
            return ['success' => false, 'message' => 'User not found'];
        }
    } catch(PDOException $e) {
        return ['success' => false, 'message' => 'Error fetching profile: ' . $e->getMessage()];
    }
}

// Get user activity
function getActivity() {
    global $conn;
    try {
        $stmt = $conn->prepare("
            (SELECT 
                'post' as type,
                'Created a new post' as description,
                created_at
            FROM community_posts
            WHERE user_id = ?)
            UNION ALL
            (SELECT 
                'comment' as type,
                'Commented on a post' as description,
                created_at
            FROM post_comments
            WHERE user_id = ?)
            UNION ALL
            (SELECT 
                'like' as type,
                'Liked a post' as description,
                created_at
            FROM post_likes
            WHERE user_id = ?)
            UNION ALL
            (SELECT 
                'learning' as type,
                CONCAT('Completed module: ', m.title) as description,
                up.completed_at as created_at
            FROM user_progress up
            JOIN learning_modules m ON up.module_id = m.id
            WHERE up.user_id = ? AND up.status = 'completed')
            ORDER BY created_at DESC
            LIMIT 10
        ");
        
        $stmt->execute([
            $_SESSION['user_id'],
            $_SESSION['user_id'],
            $_SESSION['user_id'],
            $_SESSION['user_id']
        ]);
        
        $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ['success' => true, 'activities' => $activities];
    } catch(PDOException $e) {
        return ['success' => false, 'message' => 'Error fetching activity: ' . $e->getMessage()];
    }
}

// Get user achievements
function getAchievements() {
    global $conn;
    try {
        $stmt = $conn->prepare("
            SELECT * FROM user_achievements
            WHERE user_id = ?
            ORDER BY achieved_at DESC
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $achievements = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return ['success' => true, 'achievements' => $achievements];
    } catch(PDOException $e) {
        return ['success' => false, 'message' => 'Error fetching achievements: ' . $e->getMessage()];
    }
}

// Update user profile
function updateProfile($data) {
    global $conn;
    try {
        // Begin transaction
        $conn->beginTransaction();
        
        // Update users table
        $stmt = $conn->prepare("
            UPDATE users 
            SET fullname = ?
            WHERE id = ?
        ");
        $stmt->execute([$data['fullName'], $_SESSION['user_id']]);
        
        // Update or insert user_profiles
        $stmt = $conn->prepare("
            INSERT INTO user_profiles (user_id, bio, location, experience)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            bio = VALUES(bio),
            location = VALUES(location),
            experience = VALUES(experience)
        ");
        $stmt->execute([
            $_SESSION['user_id'],
            $data['bio'],
            $data['location'],
            $data['experience']
        ]);
        
        $conn->commit();
        return ['success' => true, 'message' => 'Profile updated successfully'];
    } catch(PDOException $e) {
        $conn->rollBack();
        return ['success' => false, 'message' => 'Error updating profile: ' . $e->getMessage()];
    }
}

// Update password
function updatePassword($currentPassword, $newPassword) {
    global $conn;
    try {
        // Verify current password
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user || !password_verify($currentPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }
        
        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $_SESSION['user_id']]);
        
        return ['success' => true, 'message' => 'Password updated successfully'];
    } catch(PDOException $e) {
        return ['success' => false, 'message' => 'Error updating password: ' . $e->getMessage()];
    }
}

// Handle API requests
$action = $_GET['action'] ?? '';
$response = [];

switch ($action) {
    case 'get_profile':
        $response = getProfile();
        break;
        
    case 'get_activity':
        $response = getActivity();
        break;
        
    case 'get_achievements':
        $response = getAchievements();
        break;
        
    case 'update_profile':
        $data = json_decode(file_get_contents('php://input'), true);
        if ($data) {
            $response = updateProfile($data);
        } else {
            $response = ['success' => false, 'message' => 'Invalid data'];
        }
        break;
        
    case 'update_password':
        $data = json_decode(file_get_contents('php://input'), true);
        if ($data && isset($data['currentPassword']) && isset($data['newPassword'])) {
            $response = updatePassword($data['currentPassword'], $data['newPassword']);
        } else {
            $response = ['success' => false, 'message' => 'Invalid data'];
        }
        break;
        
    default:
        $response = ['success' => false, 'message' => 'Invalid action'];
}

echo json_encode($response); 