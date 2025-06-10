<?php
require_once 'Database Connection.php';
session_start();
header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$database = new Database();
$conn = $database->getConnection();

try {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'create_pathway':
            // Validate input
            if (empty($_POST['title']) || empty($_POST['description'])) {
                throw new Exception('Title and description are required');
            }

            $difficulty = $_POST['difficulty'] ?? 'beginner';
            $prerequisites = $_POST['prerequisites'] ?? null;

            // Insert pathway
            $stmt = $conn->prepare("
                INSERT INTO learning_pathways (title, description, difficulty_level, prerequisites) 
                VALUES (?, ?, ?, ?)
            ");
            
            if ($stmt->execute([$_POST['title'], $_POST['description'], $difficulty, $prerequisites])) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Learning pathway created successfully',
                    'pathway_id' => $conn->lastInsertId()
                ]);
            } else {
                throw new Exception('Failed to create learning pathway');
            }
            break;

        case 'create_module':
            // Validate input
            if (empty($_POST['pathway_id']) || empty($_POST['title']) || empty($_POST['content'])) {
                throw new Exception('Pathway ID, title and content are required');
            }

            // Get the next order number for this pathway
            $stmt = $conn->prepare("
                SELECT COALESCE(MAX(order_number), 0) + 1 
                FROM learning_modules 
                WHERE pathway_id = ?
            ");
            $stmt->execute([$_POST['pathway_id']]);
            $orderNumber = $stmt->fetchColumn();

            // Insert module
            $stmt = $conn->prepare("
                INSERT INTO learning_modules (
                    pathway_id, title, description, content_type, 
                    content, video_url, order_number, estimated_duration
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            if ($stmt->execute([
                $_POST['pathway_id'],
                $_POST['title'],
                $_POST['description'] ?? null,
                $_POST['content_type'] ?? 'text',
                $_POST['content'],
                $_POST['video_url'] ?? null,
                $orderNumber,
                $_POST['duration'] ?? 30
            ])) {
                echo json_encode(['success' => true, 'message' => 'Learning module created successfully']);
            } else {
                throw new Exception('Failed to create learning module');
            }
            break;

        case 'update_pathway':
            if (empty($_POST['pathway_id']) || empty($_POST['title']) || empty($_POST['description'])) {
                throw new Exception('Pathway ID, title and description are required');
            }

            $stmt = $conn->prepare("
                UPDATE learning_pathways 
                SET title = ?, description = ?, difficulty_level = ?, prerequisites = ?
                WHERE id = ?
            ");
            
            if ($stmt->execute([
                $_POST['title'],
                $_POST['description'],
                $_POST['difficulty'] ?? 'beginner',
                $_POST['prerequisites'] ?? null,
                $_POST['pathway_id']
            ])) {
                echo json_encode(['success' => true, 'message' => 'Learning pathway updated successfully']);
            } else {
                throw new Exception('Failed to update learning pathway');
            }
            break;

        case 'update_module':
            if (empty($_POST['module_id']) || empty($_POST['title']) || empty($_POST['content'])) {
                throw new Exception('Module ID, title and content are required');
            }

            $stmt = $conn->prepare("
                UPDATE learning_modules 
                SET title = ?, description = ?, content_type = ?, 
                    content = ?, video_url = ?, estimated_duration = ?
                WHERE id = ?
            ");
            
            if ($stmt->execute([
                $_POST['title'],
                $_POST['description'] ?? null,
                $_POST['content_type'] ?? 'text',
                $_POST['content'],
                $_POST['video_url'] ?? null,
                $_POST['duration'] ?? 30,
                $_POST['module_id']
            ])) {
                echo json_encode(['success' => true, 'message' => 'Learning module updated successfully']);
            } else {
                throw new Exception('Failed to update learning module');
            }
            break;

        case 'delete_pathway':
            if (empty($_POST['pathway_id'])) {
                throw new Exception('Pathway ID is required');
            }

            // This will also delete all associated modules due to ON DELETE CASCADE
            $stmt = $conn->prepare("DELETE FROM learning_pathways WHERE id = ?");
            if ($stmt->execute([$_POST['pathway_id']])) {
                echo json_encode(['success' => true, 'message' => 'Learning pathway deleted successfully']);
            } else {
                throw new Exception('Failed to delete learning pathway');
            }
            break;

        case 'delete_module':
            if (empty($_POST['module_id'])) {
                throw new Exception('Module ID is required');
            }

            $stmt = $conn->prepare("DELETE FROM learning_modules WHERE id = ?");
            if ($stmt->execute([$_POST['module_id']])) {
                echo json_encode(['success' => true, 'message' => 'Learning module deleted successfully']);
            } else {
                throw new Exception('Failed to delete learning module');
            }
            break;

        case 'reorder_modules':
            if (empty($_POST['modules'])) {
                throw new Exception('Modules array is required');
            }

            $modules = json_decode($_POST['modules'], true);
            if (!is_array($modules)) {
                throw new Exception('Invalid modules data');
            }

            $stmt = $conn->prepare("UPDATE learning_modules SET order_number = ? WHERE id = ?");
            
            foreach ($modules as $order => $moduleId) {
                $stmt->execute([$order + 1, $moduleId]);
            }

            echo json_encode(['success' => true, 'message' => 'Modules reordered successfully']);
            break;

        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 