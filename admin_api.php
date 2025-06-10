<?php
// Set session cookie parameters BEFORE session_start
session_set_cookie_params([
    'lifetime' => 86400, // 1 day in seconds
    'path' => '/',
    'domain' => '',
    'secure' => false, // Set to true if using HTTPS
    'httponly' => true,
    'samesite' => 'Lax'
]);

ini_set('session.cookie_lifetime', 86400); // 1 day in seconds
ini_set('session.gc_maxlifetime', 86400); // 1 day in seconds

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/DatabaseConnection.php';

// Check if user is logged in and is admin
function checkAdminAuth() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != true) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
        exit;
    }
}

// Set JSON header
header('Content-Type: application/json');

// Get the action from query parameter
$action = $_GET['action'] ?? '';

// Check admin authentication for all actions
checkAdminAuth();

$database = new Database();
$conn = $database->getConnection();

try {
    switch ($action) {
        case 'getUsers':
            $stmt = $conn->query("SELECT id, username, email, is_admin FROM users");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'users' => $users]);
            break;

        case 'getUser':
            $id = $_GET['id'] ?? 0;
            $stmt = $conn->prepare("SELECT id, username, email, is_admin FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'user' => $user]);
            break;

        case 'updateUser':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, is_admin = ? WHERE id = ?");
            $stmt->execute([
                $data['username'],
                $data['email'],
                $data['is_admin'] ? 1 : 0,
                $data['id']
            ]);
            echo json_encode(['success' => true, 'message' => 'User updated successfully']);
            break;

        case 'deleteUser':
            $id = $_GET['id'] ?? 0;
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
            break;

        case 'getPosts':
            try {
                // Try to select title, if it exists
                $stmt = $conn->query("
                    SELECT p.id, p.title, p.content, u.username as author, p.created_at 
                    FROM community_posts p 
                    JOIN users u ON p.user_id = u.id 
                    ORDER BY p.created_at DESC
                ");
            } catch (Exception $e) {
                // Fallback if title column does not exist
                $stmt = $conn->query("
                    SELECT p.id, p.content, u.username as author, p.created_at 
                    FROM community_posts p 
                    JOIN users u ON p.user_id = u.id 
                    ORDER BY p.created_at DESC
                ");
            }
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'posts' => $posts]);
            break;

        case 'getPost':
            $id = $_GET['id'] ?? 0;
            $stmt = $conn->prepare("
                SELECT p.*, u.username as author 
                FROM posts p 
                JOIN users u ON p.user_id = u.id 
                WHERE p.id = ?
            ");
            $stmt->execute([$id]);
            $post = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'post' => $post]);
            break;

        case 'deletePost':
            $id = $_GET['id'] ?? 0;
            $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Post deleted successfully']);
            break;

        case 'getLearningPaths':
            $stmt = $conn->query("
                SELECT p.*, COUNT(m.id) as total_modules 
                FROM learning_paths p 
                LEFT JOIN learning_modules m ON p.id = m.path_id 
                GROUP BY p.id
            ");
            $paths = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'paths' => $paths]);
            break;

        case 'getPath':
            $id = $_GET['id'] ?? 0;
            $stmt = $conn->prepare("SELECT * FROM learning_paths WHERE id = ?");
            $stmt->execute([$id]);
            $path = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'path' => $path]);
            break;

        case 'deletePath':
            $id = $_GET['id'] ?? 0;
            $conn->beginTransaction();
            try {
                // Delete associated modules first
                $stmt = $conn->prepare("DELETE FROM learning_modules WHERE path_id = ?");
                $stmt->execute([$id]);
                
                // Then delete the path
                $stmt = $conn->prepare("DELETE FROM learning_paths WHERE id = ?");
                $stmt->execute([$id]);
                
                $conn->commit();
                echo json_encode(['success' => true, 'message' => 'Learning path deleted successfully']);
            } catch (Exception $e) {
                $conn->rollBack();
                throw $e;
            }
            break;

        case 'getDashboardStats':
            $stats = [
                'users' => $conn->query("SELECT COUNT(*) FROM users")->fetchColumn(),
                'posts' => $conn->query("SELECT COUNT(*) FROM community_posts")->fetchColumn(),
                'paths' => $conn->query("SELECT COUNT(*) FROM learning_paths")->fetchColumn()
            ];
            echo json_encode(['success' => true, 'stats' => $stats]);
            break;

        case 'refreshSession':
            if (isset($_SESSION['user_id'])) {
                // Update session cookie lifetime
                session_set_cookie_params([
                    'lifetime' => 86400, // 1 day in seconds
                    'path' => '/',
                    'domain' => '',
                    'secure' => false,
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);
                
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);
                
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No active session']);
            }
            break;

        case 'checkSession':
            if (isset($_SESSION['user_id'])) {
                echo json_encode([
                    'success' => true,
                    'user' => [
                        'id' => $_SESSION['user_id'],
                        'is_admin' => $_SESSION['is_admin'] ?? false,
                        'username' => $_SESSION['username'] ?? ''
                    ]
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No active session']);
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?> 