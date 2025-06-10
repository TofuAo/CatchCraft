USE fishing_master;

-- Add is_admin column to users table
ALTER TABLE users
ADD COLUMN is_admin BOOLEAN DEFAULT FALSE;

-- Create site settings table for admin configuration
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user
-- Password is 'admin123' (hashed)
INSERT INTO users (username, email, password, is_admin) VALUES
('admin', 'admin@fishingmaster.com', '$2y$10$8KzcO.v5e9zw5RqDB0Nqxe6AYEXrIdAzGHJ9P9q3L8JUlPHcN4n4W', TRUE);

-- Insert default site settings
INSERT INTO site_settings (setting_key, setting_value) VALUES
('support_whatsapp', '+1234567890'),
('support_email', 'support@fishingmaster.com'),
('site_name', 'Master Fisher'),
('maintenance_mode', 'false'),
('max_login_attempts', '5')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value); 