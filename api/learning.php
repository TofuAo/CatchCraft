<?php
require_once __DIR__ . '/../DatabaseConnection.php';
require_once __DIR__ . '/../session_config.php';
require_once __DIR__ . '/error_handler.php';

header('Content-Type: application/json');

$database = new Database();
$conn = $database->getConnection();

try {
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'get_paths':
            $stmt = $conn->query("
                SELECT p.*, COUNT(m.id) as total_modules 
                FROM learning_paths p 
                LEFT JOIN modules m ON p.id = m.path_id 
                GROUP BY p.id
                ORDER BY FIELD(p.difficulty_level, 'beginner', 'intermediate', 'advanced')
            ");
            $paths = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (isLoggedIn()) {
                foreach ($paths as &$path) {
                    $stmt = $conn->prepare("
                        SELECT COUNT(DISTINCT m.id) as total,
                               COUNT(DISTINCT CASE WHEN up.status = 'completed' THEN m.id END) as completed
                        FROM modules m
                        LEFT JOIN user_progress up ON m.id = up.module_id AND up.user_id = ?
                        WHERE m.path_id = ?
                    ");
                    $stmt->execute([$_SESSION['user_id'], $path['id']]);
                    $progress = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $path['progress'] = [
                        'total' => (int)$progress['total'],
                        'completed' => (int)$progress['completed'],
                        'percentage' => $progress['total'] > 0 ? round(($progress['completed'] / $progress['total']) * 100) : 0
                    ];
                }
            }
            
            sendSuccessResponse(['paths' => $paths]);
            break;

        case 'add_path':
            if (!isAdmin()) {
                throw new Exception('Unauthorized access');
            }

            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['title']) || empty($data['description'])) {
                throw new Exception('Title and description are required');
            }

            $stmt = $conn->prepare("
                INSERT INTO learning_paths (title, description, difficulty_level)
                VALUES (?, ?, ?)
            ");
            
            $stmt->execute([
                $data['title'],
                $data['description'],
                $data['difficulty_level'] ?? 'beginner'
            ]);
            
            sendSuccessResponse([
                'message' => 'Learning path created successfully',
                'path_id' => $conn->lastInsertId()
            ]);
            break;

        case 'get_modules':
            if (!isset($_GET['path_id'])) {
                sendErrorResponse('Path ID is required');
            }

            $stmt = $conn->prepare("
                SELECT m.*, 
                       COALESCE(up.status, 'not_started') as status,
                       up.completed_at,
                       up.score
                FROM modules m
                LEFT JOIN user_progress up ON m.id = up.module_id AND up.user_id = ?
                WHERE m.path_id = ?
                ORDER BY m.order_number
            ");
            
            $stmt->execute([isLoggedIn() ? $_SESSION['user_id'] : null, $_GET['path_id']]);
            $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            sendSuccessResponse(['modules' => $modules]);
            break;

        case 'add_module':
            if (!isAdmin()) {
                throw new Exception('Unauthorized access');
            }

            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['path_id']) || empty($data['title']) || empty($data['content'])) {
                throw new Exception('Path ID, title and content are required');
            }

            // Get next order number
            $stmt = $conn->prepare("
                SELECT COALESCE(MAX(order_number), 0) + 1 
                FROM modules 
                WHERE path_id = ?
            ");
            $stmt->execute([$data['path_id']]);
            $orderNumber = $stmt->fetchColumn();

            $stmt = $conn->prepare("
                INSERT INTO modules (
                    path_id, title, content, content_type, 
                    video_url, order_number, estimated_duration
                ) VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['path_id'],
                $data['title'],
                $data['content'],
                $data['content_type'] ?? 'text',
                $data['video_url'] ?? null,
                $orderNumber,
                $data['estimated_duration'] ?? 30
            ]);
            
            sendSuccessResponse([
                'message' => 'Module added successfully',
                'module_id' => $conn->lastInsertId()
            ]);
            break;

        case 'update_progress':
            if (!isLoggedIn()) {
                sendErrorResponse('User must be logged in', 401);
            }

            $data = json_decode(file_get_contents('php://input'), true);
            validateRequiredParams($data, ['module_id', 'status']);

            $stmt = $conn->prepare("
                INSERT INTO user_progress (user_id, module_id, status, completed_at)
                VALUES (?, ?, ?, CASE WHEN ? = 'completed' THEN CURRENT_TIMESTAMP ELSE NULL END)
                ON DUPLICATE KEY UPDATE 
                status = VALUES(status),
                completed_at = VALUES(completed_at)
            ");
            
            $stmt->execute([
                $_SESSION['user_id'],
                $data['module_id'],
                $data['status'],
                $data['status']
            ]);
            
            sendSuccessResponse([], 'Progress updated successfully');
            break;

        default:
            sendErrorResponse('Invalid action');
    }
} catch (Exception $e) {
    handleApiException($e);
}
?> 
