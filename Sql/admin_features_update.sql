-- Additional admin features tables
USE nexora_db;

-- Email Templates Table
CREATE TABLE IF NOT EXISTS email_templates (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    template_type ENUM('contact_reply', 'newsletter', 'welcome', 'notification', 'custom') DEFAULT 'custom',
    variables TEXT COMMENT 'JSON array of available variables',
    is_active TINYINT(1) DEFAULT 1,
    created_by INT(11) UNSIGNED DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_type (template_type),
    INDEX idx_active (is_active),
    FOREIGN KEY (created_by) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- System Settings Table
CREATE TABLE IF NOT EXISTS system_settings (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    category VARCHAR(50) DEFAULT 'general',
    description TEXT,
    is_public TINYINT(1) DEFAULT 0 COMMENT 'Can be accessed from frontend',
    updated_by INT(11) UNSIGNED DEFAULT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_public (is_public),
    FOREIGN KEY (updated_by) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Backup History Table
CREATE TABLE IF NOT EXISTS backup_history (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    backup_name VARCHAR(255) NOT NULL,
    backup_path VARCHAR(500) NOT NULL,
    backup_type ENUM('full', 'database', 'files') DEFAULT 'full',
    file_size BIGINT UNSIGNED DEFAULT NULL,
    status ENUM('success', 'failed', 'in_progress') DEFAULT 'in_progress',
    error_message TEXT DEFAULT NULL,
    created_by INT(11) UNSIGNED DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_type (backup_type),
    INDEX idx_status (status),
    INDEX idx_created (created_at),
    FOREIGN KEY (created_by) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notifications Table
CREATE TABLE IF NOT EXISTS admin_notifications (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id INT(11) UNSIGNED DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    notification_type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
    is_read TINYINT(1) DEFAULT 0,
    action_url VARCHAR(500) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    read_at DATETIME DEFAULT NULL,
    INDEX idx_admin (admin_id),
    INDEX idx_read (is_read),
    INDEX idx_type (notification_type),
    INDEX idx_created (created_at),
    FOREIGN KEY (admin_id) REFERENCES admin_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dashboard Chart Data Table
CREATE TABLE IF NOT EXISTS dashboard_chart_data (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    messages_count INT(11) DEFAULT 0,
    subscribers_count INT(11) DEFAULT 0,
    new_messages INT(11) DEFAULT 0,
    new_subscribers INT(11) DEFAULT 0,
    UNIQUE KEY idx_date (date),
    INDEX idx_date_desc (date DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default email templates
INSERT INTO email_templates (name, subject, body, template_type, variables) VALUES
('Contact Reply Template', 'Re: {{subject}}', 
'Dear {{name}},\n\nThank you for contacting us.\n\n{{custom_message}}\n\nBest regards,\nNEXORA IT Team', 
'contact_reply', 
'["name", "email", "subject", "custom_message"]'),

('Newsletter Welcome', 'Welcome to NEXORA IT Newsletter', 
'Dear Subscriber,\n\nThank you for subscribing to our newsletter!\n\nYou will receive updates about our latest services, technology trends, and exclusive offers.\n\nBest regards,\nNEXORA IT Team', 
'newsletter', 
'["email"]'),

('New Message Notification', 'New Contact Message Received', 
'A new contact message has been received:\n\nName: {{name}}\nEmail: {{email}}\nSubject: {{subject}}\n\nPlease check the admin panel for details.', 
'notification', 
'["name", "email", "subject", "message"]');

-- Insert default system settings
INSERT INTO system_settings (setting_key, setting_value, setting_type, category, description, is_public) VALUES
('site_name', 'NEXORA IT', 'string', 'general', 'Website name', 1),
('site_email', 'info@nexorait.com', 'string', 'general', 'Primary contact email', 1),
('site_phone', '+1-555-0123', 'string', 'general', 'Contact phone number', 1),
('maintenance_mode', '0', 'boolean', 'general', 'Enable maintenance mode', 0),
('enable_newsletter', '1', 'boolean', 'features', 'Enable newsletter subscription', 1),
('enable_contact_form', '1', 'boolean', 'features', 'Enable contact form', 1),
('max_upload_size', '5242880', 'number', 'files', 'Maximum upload size in bytes (5MB)', 0),
('allowed_file_types', '["jpg", "jpeg", "png", "gif", "pdf"]', 'json', 'files', 'Allowed file types for upload', 0),
('items_per_page', '20', 'number', 'display', 'Items per page in admin lists', 0),
('session_timeout', '3600', 'number', 'security', 'Session timeout in seconds (1 hour)', 0),
('enable_recaptcha', '0', 'boolean', 'security', 'Enable Google reCAPTCHA', 0),
('auto_backup_enabled', '1', 'boolean', 'backup', 'Enable automatic backups', 0),
('backup_frequency', '7', 'number', 'backup', 'Backup frequency in days', 0);

-- Create view for dashboard analytics
CREATE OR REPLACE VIEW dashboard_analytics AS
SELECT 
    DATE(created_at) as date,
    COUNT(CASE WHEN status = 'new' THEN 1 END) as new_messages,
    COUNT(CASE WHEN status = 'read' THEN 1 END) as read_messages,
    COUNT(CASE WHEN status = 'replied' THEN 1 END) as replied_messages,
    COUNT(*) as total_messages
FROM contact_messages
WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
GROUP BY DATE(created_at)
ORDER BY date DESC;

-- Create view for subscriber analytics
CREATE OR REPLACE VIEW subscriber_analytics AS
SELECT 
    DATE(created_at) as date,
    COUNT(CASE WHEN status = 'active' THEN 1 END) as active_subscribers,
    COUNT(CASE WHEN status = 'unsubscribed' THEN 1 END) as unsubscribed,
    COUNT(*) as total_subscribers
FROM newsletter_subscribers
WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
GROUP BY DATE(created_at)
ORDER BY date DESC;

-- Procedure to update chart data daily
DELIMITER //
CREATE PROCEDURE update_dashboard_chart_data()
BEGIN
    INSERT INTO dashboard_chart_data (date, messages_count, subscribers_count, new_messages, new_subscribers)
    SELECT 
        CURDATE(),
        (SELECT COUNT(*) FROM contact_messages),
        (SELECT COUNT(*) FROM newsletter_subscribers WHERE status = 'active'),
        (SELECT COUNT(*) FROM contact_messages WHERE DATE(created_at) = CURDATE()),
        (SELECT COUNT(*) FROM newsletter_subscribers WHERE DATE(created_at) = CURDATE())
    ON DUPLICATE KEY UPDATE
        messages_count = VALUES(messages_count),
        subscribers_count = VALUES(subscribers_count),
        new_messages = VALUES(new_messages),
        new_subscribers = VALUES(new_subscribers);
END //
DELIMITER ;

-- Initial data population for last 30 days
INSERT INTO dashboard_chart_data (date, messages_count, subscribers_count, new_messages, new_subscribers)
SELECT 
    d.date,
    COALESCE(m.messages_count, 0),
    COALESCE(s.subscribers_count, 0),
    COALESCE(m.new_messages, 0),
    COALESCE(s.new_subscribers, 0)
FROM (
    SELECT CURDATE() - INTERVAL (a.a + (10 * b.a) + (100 * c.a)) DAY as date
    FROM (SELECT 0 as a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) as a
    CROSS JOIN (SELECT 0 as a UNION ALL SELECT 1 UNION ALL SELECT 2) as b
    CROSS JOIN (SELECT 0 as a) as c
    WHERE CURDATE() - INTERVAL (a.a + (10 * b.a) + (100 * c.a)) DAY >= CURDATE() - INTERVAL 30 DAY
) d
LEFT JOIN (
    SELECT 
        DATE(created_at) as date,
        COUNT(*) as new_messages,
        (SELECT COUNT(*) FROM contact_messages WHERE DATE(created_at) <= d2.date) as messages_count
    FROM contact_messages, (SELECT DISTINCT DATE(created_at) as date FROM contact_messages) d2
    GROUP BY DATE(created_at)
) m ON d.date = m.date
LEFT JOIN (
    SELECT 
        DATE(created_at) as date,
        COUNT(*) as new_subscribers,
        (SELECT COUNT(*) FROM newsletter_subscribers WHERE status = 'active' AND DATE(created_at) <= d2.date) as subscribers_count
    FROM newsletter_subscribers, (SELECT DISTINCT DATE(created_at) as date FROM newsletter_subscribers) d2
    WHERE status = 'active'
    GROUP BY DATE(created_at)
) s ON d.date = s.date
ON DUPLICATE KEY UPDATE
    messages_count = VALUES(messages_count),
    subscribers_count = VALUES(subscribers_count),
    new_messages = VALUES(new_messages),
    new_subscribers = VALUES(new_subscribers);
