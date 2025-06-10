<?php
require_once 'db.php';

// Set headers for JSON response
header('Content-Type: application/json');

try {
    // Check if learning_paths table exists and has data
    $check_paths = "SELECT COUNT(*) as count FROM learning_paths";
    $result = mysqli_query($conn, $check_paths);
    
    if (!$result) {
        throw new Exception("Learning paths table not found. Creating tables...");
    }
    
    $row = mysqli_fetch_assoc($result);
    
    if ($row['count'] == 0) {
        // Insert sample learning paths
        $insert_paths = "INSERT INTO learning_paths (title, description, difficulty_level) VALUES
            ('Fishing Basics', 'Learn the fundamentals of fishing including equipment, techniques, and safety.', 'beginner'),
            ('Intermediate Angling', 'Advanced techniques and strategies for experienced fishers.', 'intermediate'),
            ('Expert Fishing Mastery', 'Professional-level fishing techniques and advanced strategies.', 'advanced')";
        
        if (!mysqli_query($conn, $insert_paths)) {
            throw new Exception("Error inserting learning paths: " . mysqli_error($conn));
        }
        
        // Get the ID of the first path (Fishing Basics)
        $path_id = mysqli_insert_id($conn);
        
        // Insert sample modules for Fishing Basics
        $insert_modules = "INSERT INTO modules (path_id, title, content, order_number) VALUES
            ($path_id, 'Introduction to Fishing', 'Learn about basic fishing equipment:\n- Different types of rods\n- Various fishing reels\n- Essential fishing lines\n- Basic hooks and lures\n- Necessary accessories', 1),
            ($path_id, 'Fishing Safety', '- Water safety guidelines\n- Weather awareness\n- Safe handling of equipment\n- First aid basics\n- Environmental considerations', 2),
            ($path_id, 'Basic Fishing Techniques', '- Proper casting methods\n- Bait presentation\n- Reading water conditions\n- Basic knot tying\n- Fish handling techniques', 3)";
        
        if (!mysqli_query($conn, $insert_modules)) {
            throw new Exception("Error inserting modules: " . mysqli_error($conn));
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Learning paths and modules are set up correctly'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

mysqli_close($conn);
?> 