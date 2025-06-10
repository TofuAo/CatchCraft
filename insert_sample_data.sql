-- Insert learning paths
INSERT INTO learning_paths (title, description, difficulty_level) VALUES
('Fishing Basics', 'Learn the fundamentals of fishing including equipment, techniques, and safety.', 'beginner'),
('Intermediate Angling', 'Advanced techniques and strategies for experienced fishers.', 'intermediate'),
('Expert Fishing Mastery', 'Professional-level fishing techniques and advanced strategies.', 'advanced');

-- Insert modules for Fishing Basics
INSERT INTO modules (path_id, title, content, order_number) VALUES
(1, 'Introduction to Fishing', 'Basic introduction to fishing equipment and terminology.', 1),
(1, 'Fishing Safety', 'Essential safety guidelines for fishing.', 2),
(1, 'Basic Fishing Techniques', 'Learn fundamental fishing techniques and methods.', 3);

-- Insert modules for Intermediate Angling
INSERT INTO modules (path_id, title, content, order_number) VALUES
(2, 'Advanced Equipment Usage', 'Detailed guide on using advanced fishing equipment.', 1),
(2, 'Weather and Fish Behavior', 'Understanding how weather affects fish behavior.', 2),
(2, 'Specialized Fishing Methods', 'Learn specialized fishing techniques for different scenarios.', 3);

-- Insert modules for Expert Fishing Mastery
INSERT INTO modules (path_id, title, content, order_number) VALUES
(3, 'Professional Techniques', 'Master professional-level fishing techniques.', 1),
(3, 'Advanced Fish Finding', 'Advanced methods for locating specific fish species.', 2),
(3, 'Competition Strategies', 'Strategies used in professional fishing competitions.', 3); 