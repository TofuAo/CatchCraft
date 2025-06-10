<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../session_config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

try {
    // Get all learning paths with completion status
    $paths_query = "
        SELECT 
            lp.id,
            lp.title,
            lp.description,
            lp.difficulty_level,
            lp.created_at,
            lp.is_active,
            COUNT(DISTINCT m.id) as total_modules,
            COUNT(DISTINCT CASE WHEN up.completed = 1 THEN m.id END) as completed_modules
        FROM learning_paths lp
        LEFT JOIN learning_modules m ON lp.id = m.path_id
        LEFT JOIN user_progress up ON m.id = up.module_id AND up.user_id = :user_id
        GROUP BY lp.id
        ORDER BY FIELD(lp.difficulty_level, 'beginner', 'intermediate', 'advanced')
    ";

    $paths_stmt = $conn->prepare($paths_query);
    $paths_stmt->bindParam(':user_id', $user_id);
    $paths_stmt->execute();
    $paths = $paths_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get modules for each path
    foreach ($paths as &$path) {
        $modules_query = "
            SELECT 
                m.id,
                m.title,
                m.description,
                m.content_type,
                m.content,
                m.video_url,
                m.order_number,
                m.estimated_duration,
                m.created_at,
                CASE WHEN up.completed = 1 THEN 1 ELSE 0 END as is_completed
            FROM learning_modules m
            LEFT JOIN user_progress up ON m.id = up.module_id AND up.user_id = :user_id
            WHERE m.path_id = :path_id
            ORDER BY m.order_number
        ";

        $modules_stmt = $conn->prepare($modules_query);
        $modules_stmt->bindParam(':user_id', $user_id);
        $modules_stmt->bindParam(':path_id', $path['id']);
        $modules_stmt->execute();
        $path['modules'] = $modules_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate completion percentage
        $path['completion_percentage'] = $path['total_modules'] > 0 
            ? round(($path['completed_modules'] / $path['total_modules']) * 100) 
            : 0;
    }

    echo json_encode([
        'success' => true,
        'paths' => $paths
    ]);

} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching learning paths: ' . $e->getMessage()
    ]);
}
?> 