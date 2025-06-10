USE fishing_master;

-- Create site settings table
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default contact information
INSERT INTO site_settings (setting_key, setting_value) VALUES
('support_whatsapp', '+1234567890'),
('support_email', 'support@fishingmaster.com')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value); 