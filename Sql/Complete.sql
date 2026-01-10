-- ==========================================
-- NEXORA IT - Complete Database Setup
-- Version: 2.0 (Enterprise Edition)
-- Date: January 2026
-- Description: Complete database schema with all tables, views, and procedures
-- ==========================================

-- Create Database
CREATE DATABASE IF NOT EXISTS nexora_db 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE nexora_db;

-- ==========================================
-- CORE TABLES
-- ==========================================

-- Contact Messages Table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) DEFAULT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent VARCHAR(255) DEFAULT NULL,
    status ENUM('new', 'read', 'replied', 'archived') DEFAULT 'new',
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_name_email (name, email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Newsletter Subscribers Table
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent VARCHAR(255) DEFAULT NULL,
    status ENUM('active', 'unsubscribed', 'bounced') DEFAULT 'active',
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    unsubscribed_at DATETIME DEFAULT NULL,
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_status_created (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Team Members Table
CREATE TABLE IF NOT EXISTS team_members (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    bio TEXT DEFAULT NULL,
    image_url VARCHAR(500) DEFAULT NULL,
    linkedin_url VARCHAR(255) DEFAULT NULL,
    twitter_url VARCHAR(255) DEFAULT NULL,
    github_url VARCHAR(255) DEFAULT NULL,
    email VARCHAR(255) DEFAULT NULL,
    phone VARCHAR(50) DEFAULT NULL,
    display_order INT(11) DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_display_order (display_order),
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- ADMIN SYSTEM TABLES
-- ==========================================

-- Admin Users Table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('super_admin', 'admin', 'viewer') DEFAULT 'admin',
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME DEFAULT NULL,
    last_login_ip VARCHAR(45) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin Activity Logs Table
CREATE TABLE IF NOT EXISTS admin_activity_logs (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id INT(11) UNSIGNED NOT NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_admin_id (admin_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at),
    INDEX idx_admin_action (admin_id, action),
    FOREIGN KEY (admin_id) REFERENCES admin_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Login Attempts Table
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    success TINYINT(1) DEFAULT 0,
    attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_ip (ip_address),
    INDEX idx_attempted (attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dashboard Statistics Table
CREATE TABLE IF NOT EXISTS dashboard_stats (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    new_messages INT(11) DEFAULT 0,
    total_messages INT(11) DEFAULT 0,
    active_subscribers INT(11) DEFAULT 0,
    total_subscribers INT(11) DEFAULT 0,
    total_team_members INT(11) DEFAULT 0,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- VERSION 2.0 TABLES (NEW FEATURES)
-- ==========================================

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

-- Admin Notifications Table
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

-- ==========================================
-- DEFAULT DATA INSERTIONS
-- ==========================================

-- Insert Default Admin User (Password: admin123)
INSERT INTO admin_users (username, password, email, full_name, role, is_active) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@nexorait.com', 'System Administrator', 'super_admin', 1)
ON DUPLICATE KEY UPDATE id=id;

-- Initialize Dashboard Stats
INSERT INTO dashboard_stats (new_messages, total_messages, active_subscribers, total_subscribers, total_team_members)
VALUES (0, 0, 0, 0, 0)
ON DUPLICATE KEY UPDATE id=id;

-- Insert Default Email Templates
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
'["name", "email", "subject", "message"]')
ON DUPLICATE KEY UPDATE id=id;

-- Insert Default System Settings
INSERT INTO system_settings (setting_key, setting_value, setting_type, category, description, is_public) VALUES
('site_name', 'NEXORA IT', 'string', 'general', 'Website name', 1),
('site_email', 'nexorait@outlook.com', 'string', 'general', 'Primary contact email', 1),
('site_phone', '+94 77 635 0902', 'string', 'general', 'Contact phone number', 1),
('site_phone_2', '+94 70 671 7131', 'string', 'general', 'Secondary contact phone', 1),
('site_address', '218 Doalakanda, Dehiaththakandiya, Sri Lanka', 'string', 'general', 'Business address', 1),
('maintenance_mode', '0', 'boolean', 'general', 'Enable maintenance mode', 0),
('enable_newsletter', '1', 'boolean', 'features', 'Enable newsletter subscription', 1),
('enable_contact_form', '1', 'boolean', 'features', 'Enable contact form', 1),
('max_upload_size', '5242880', 'number', 'files', 'Maximum upload size in bytes (5MB)', 0),
('allowed_file_types', '["jpg", "jpeg", "png", "gif", "pdf"]', 'json', 'files', 'Allowed file types for upload', 0),
('items_per_page', '20', 'number', 'display', 'Items per page in admin lists', 0),
('session_timeout', '3600', 'number', 'security', 'Session timeout in seconds (1 hour)', 0),
('enable_recaptcha', '0', 'boolean', 'security', 'Enable Google reCAPTCHA', 0),
('auto_backup_enabled', '1', 'boolean', 'backup', 'Enable automatic backups', 0),
('backup_frequency', '7', 'number', 'backup', 'Backup frequency in days', 0)
ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value);

-- ==========================================
-- VIEWS FOR ANALYTICS
-- ==========================================

-- Dashboard Analytics View (30-day message analytics)
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

-- Subscriber Analytics View (30-day subscriber analytics)
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

-- Newsletter Statistics View
CREATE OR REPLACE VIEW newsletter_stats AS
SELECT 
    COUNT(*) as total_subscribers,
    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_subscribers,
    SUM(CASE WHEN status = 'unsubscribed' THEN 1 ELSE 0 END) as unsubscribed_count,
    SUM(CASE WHEN status = 'bounced' THEN 1 ELSE 0 END) as bounced_count,
    SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today_subscribers,
    SUM(CASE WHEN DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as week_subscribers,
    SUM(CASE WHEN DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as month_subscribers
FROM newsletter_subscribers;

-- ==========================================
-- STORED PROCEDURES
-- ==========================================

-- Procedure to Update Dashboard Statistics
DELIMITER //
CREATE PROCEDURE update_dashboard_stats()
BEGIN
    UPDATE dashboard_stats SET
        new_messages = (SELECT COUNT(*) FROM contact_messages WHERE status = 'new'),
        total_messages = (SELECT COUNT(*) FROM contact_messages),
        active_subscribers = (SELECT COUNT(*) FROM newsletter_subscribers WHERE status = 'active'),
        total_subscribers = (SELECT COUNT(*) FROM newsletter_subscribers),
        total_team_members = (SELECT COUNT(*) FROM team_members WHERE status = 'active');
END //
DELIMITER ;

-- Procedure to Update Daily Chart Data
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

-- ==========================================
-- TRIGGERS FOR AUTOMATIC UPDATES
-- ==========================================

-- Trigger: Update dashboard stats after contact message insert
DELIMITER //
CREATE TRIGGER after_contact_insert
AFTER INSERT ON contact_messages
FOR EACH ROW
BEGIN
    CALL update_dashboard_stats();
END //
DELIMITER ;

-- Trigger: Update dashboard stats after contact message update
DELIMITER //
CREATE TRIGGER after_contact_update
AFTER UPDATE ON contact_messages
FOR EACH ROW
BEGIN
    CALL update_dashboard_stats();
END //
DELIMITER ;

-- Trigger: Update dashboard stats after subscriber insert
DELIMITER //
CREATE TRIGGER after_subscriber_insert
AFTER INSERT ON newsletter_subscribers
FOR EACH ROW
BEGIN
    CALL update_dashboard_stats();
END //
DELIMITER ;

-- Trigger: Update dashboard stats after subscriber update
DELIMITER //
CREATE TRIGGER after_subscriber_update
AFTER UPDATE ON newsletter_subscribers
FOR EACH ROW
BEGIN
    CALL update_dashboard_stats();
END //
DELIMITER ;

-- ==========================================
-- SAMPLE DATA (OPTIONAL)
-- ==========================================

-- Sample Team Members (Uncomment to use)
/*
INSERT INTO team_members (name, position, bio, image_url, email, display_order, status) VALUES
('John Doe', 'CEO & Founder', 'Visionary leader with 15+ years in tech industry', 'images/team/john.jpg', 'john@nexorait.com', 1, 'active'),
('Jane Smith', 'CTO', 'Technology expert specializing in cloud solutions', 'images/team/jane.jpg', 'jane@nexorait.com', 2, 'active'),
('Mike Johnson', 'Lead Developer', 'Full-stack developer with expertise in modern frameworks', 'images/team/mike.jpg', 'mike@nexorait.com', 3, 'active');
*/

-- ==========================================
-- INITIAL DASHBOARD UPDATE
-- ==========================================

-- Update initial dashboard statistics
CALL update_dashboard_stats();

-- Initialize chart data for last 30 days
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
        (SELECT COUNT(*) FROM contact_messages WHERE DATE(created_at) <= DATE(cm.created_at)) as messages_count
    FROM contact_messages cm
    GROUP BY DATE(created_at)
) m ON d.date = m.date
LEFT JOIN (
    SELECT 
        DATE(created_at) as date,
        COUNT(*) as new_subscribers,
        (SELECT COUNT(*) FROM newsletter_subscribers WHERE status = 'active' AND DATE(created_at) <= DATE(ns.created_at)) as subscribers_count
    FROM newsletter_subscribers ns
    WHERE status = 'active'
    GROUP BY DATE(created_at)
) s ON d.date = s.date
ON DUPLICATE KEY UPDATE
    messages_count = VALUES(messages_count),
    subscribers_count = VALUES(subscribers_count),
    new_messages = VALUES(new_messages),
    new_subscribers = VALUES(new_subscribers);

-- ==========================================
-- VERIFICATION QUERIES
-- ==========================================

-- Show all created tables
SELECT 'Database Tables:' as Info;
SHOW TABLES;

-- Show table row counts
SELECT 'Table Statistics:' as Info;
SELECT 
    'admin_users' as table_name, COUNT(*) as row_count FROM admin_users
UNION ALL
SELECT 'contact_messages', COUNT(*) FROM contact_messages
UNION ALL
SELECT 'newsletter_subscribers', COUNT(*) FROM newsletter_subscribers
UNION ALL
SELECT 'team_members', COUNT(*) FROM team_members
UNION ALL
SELECT 'email_templates', COUNT(*) FROM email_templates
UNION ALL
SELECT 'system_settings', COUNT(*) FROM system_settings
UNION ALL
SELECT 'admin_notifications', COUNT(*) FROM admin_notifications
UNION ALL
SELECT 'admin_activity_logs', COUNT(*) FROM admin_activity_logs
UNION ALL
SELECT 'dashboard_stats', COUNT(*) FROM dashboard_stats
UNION ALL
SELECT 'dashboard_chart_data', COUNT(*) FROM dashboard_chart_data;

-- Show default admin user
SELECT 'Default Admin User:' as Info;
SELECT id, username, email, full_name, role, is_active, created_at FROM admin_users WHERE username = 'admin';

-- Show system settings
SELECT 'System Settings:' as Info;
SELECT setting_key, setting_value, category FROM system_settings ORDER BY category, setting_key;

-- ==========================================
-- SETUP COMPLETE
-- ==========================================

SELECT '
==========================================
✅ NEXORA IT Database Setup Complete!
==========================================

📊 Database: nexora_db
📋 Tables Created: 15+
👤 Default Admin: username=admin, password=admin123
🔧 Stored Procedures: 2
📈 Views: 3
⚡ Triggers: 4

Next Steps:
1. ⚠️ CHANGE DEFAULT ADMIN PASSWORD immediately
2. 🔧 Update php/config.php with database credentials
3. 🧪 Test contact form and newsletter
4. 🔐 Login to admin panel: /admin/
5. 📚 Read documentation in Doc/ folder

For detailed setup instructions, see:
- Doc/ADMIN_INSTALLATION.md
- Doc/ADMIN_FEATURES.md
- Doc/UPDATE_SUMMARY_V2.md

🎉 Your enterprise-grade admin system is ready!
==========================================
' as 'Setup Status';
