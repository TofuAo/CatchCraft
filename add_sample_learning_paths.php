<?php
require_once 'Database Connection.php';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Add sample learning paths
    $paths = [
        [
            'title' => 'Fishing Basics',
            'description' => 'Learn the fundamentals of fishing, from equipment to basic techniques.',
            'difficulty_level' => 'beginner'
        ],
        [
            'title' => 'Advanced Techniques',
            'description' => 'Master advanced fishing techniques and strategies.',
            'difficulty_level' => 'intermediate'
        ],
        [
            'title' => 'Professional Fishing',
            'description' => 'Become a professional angler with expert-level skills.',
            'difficulty_level' => 'advanced'
        ]
    ];

    foreach ($paths as $path) {
        $stmt = $conn->prepare("INSERT INTO learning_paths (title, description, difficulty_level) VALUES (?, ?, ?)");
        $stmt->execute([$path['title'], $path['description'], $path['difficulty_level']]);
        $path_id = $conn->lastInsertId();

        // Add modules for each path
        $modules = [
            [
                'title' => 'Introduction to Fishing Equipment',
                'content' => 'Learn about essential fishing gear and how to choose the right equipment.',
                'order_number' => 1
            ],
            [
                'title' => 'Basic Casting Techniques',
                'content' => 'Master the fundamental casting techniques for different fishing scenarios.',
                'order_number' => 2
            ],
            [
                'title' => 'Understanding Fish Behavior',
                'content' => 'Learn how to read water conditions and understand fish behavior.',
                'order_number' => 3
            ]
        ];

        foreach ($modules as $module) {
            $stmt = $conn->prepare("INSERT INTO modules (path_id, title, content, order_number) VALUES (?, ?, ?, ?)");
            $stmt->execute([$path_id, $module['title'], $module['content'], $module['order_number']]);
        }
    }

    echo "Sample learning paths and modules added successfully!";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 