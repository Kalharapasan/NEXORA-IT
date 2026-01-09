-- ==========================================
-- NEXORA DATABASE SETUP
-- Create database and tables for contact form and newsletter
-- ==========================================

-- Create Database
CREATE DATABASE IF NOT EXISTS nexora_db 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE nexora_db;

-- ==========================================
-- CONTACT MESSAGES TABLE
-- ==========================================
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
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- NEWSLETTER SUBSCRIBERS TABLE
-- ==========================================
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
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- SAMPLE DATA (Optional - for testing)
-- ==========================================

-- Insert sample contact message (for testing)
-- INSERT INTO contact_messages (name, email, phone, subject, message, ip_address, created_at)
-- VALUES ('Test User', 'test@example.com', '+1234567890', 'Test Subject', 'This is a test message', '127.0.0.1', NOW());

-- Insert sample newsletter subscriber (for testing)
-- INSERT INTO newsletter_subscribers (email, ip_address, status, created_at)
-- VALUES ('subscriber@example.com', '127.0.0.1', 'active', NOW());

-- ==========================================
-- VIEWS FOR EASY REPORTING
-- ==========================================

-- View: Recent Contact Messages
CREATE OR REPLACE VIEW recent_contact_messages AS
SELECT 
    id,
    name,
    email,
    phone,
    subject,
    LEFT(message, 100) AS message_preview,
    status,
    created_at
FROM contact_messages
ORDER BY created_at DESC
LIMIT 50;

-- View: Active Newsletter Subscribers Count
CREATE OR REPLACE VIEW newsletter_stats AS
SELECT 
    COUNT(*) AS total_subscribers,
    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active_subscribers,
    SUM(CASE WHEN status = 'unsubscribed' THEN 1 ELSE 0 END) AS unsubscribed,
    SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) AS today_subscriptions
FROM newsletter_subscribers;

-- ==========================================
-- STORED PROCEDURES (Optional)
-- ==========================================

-- Procedure: Get Contact Messages by Status
DELIMITER $$
CREATE PROCEDURE IF NOT EXISTS GetContactMessagesByStatus(IN msg_status VARCHAR(20))
BEGIN
    SELECT * FROM contact_messages 
    WHERE status = msg_status 
    ORDER BY created_at DESC;
END$$
DELIMITER ;

-- Procedure: Get Active Subscribers
DELIMITER $$
CREATE PROCEDURE IF NOT EXISTS GetActiveSubscribers()
BEGIN
    SELECT email, created_at 
    FROM newsletter_subscribers 
    WHERE status = 'active' 
    ORDER BY created_at DESC;
END$$
DELIMITER ;

-- ==========================================
-- TRIGGERS FOR AUDIT
-- ==========================================

-- Trigger: Before update contact message
DELIMITER $$
CREATE TRIGGER IF NOT EXISTS before_contact_message_update
BEFORE UPDATE ON contact_messages
FOR EACH ROW
BEGIN
    IF NEW.status != OLD.status THEN
        SET NEW.updated_at = NOW();
    END IF;
END$$
DELIMITER ;

-- ==========================================
-- INDEXES FOR PERFORMANCE
-- ==========================================

-- Additional indexes for better query performance
ALTER TABLE contact_messages ADD INDEX idx_name_email (name, email);
ALTER TABLE newsletter_subscribers ADD INDEX idx_status_created (status, created_at);

-- ==========================================
-- GRANT PERMISSIONS (Optional - adjust as needed)
-- ==========================================

-- For production, create a specific user with limited privileges
-- CREATE USER 'nexora_user'@'localhost' IDENTIFIED BY 'your_secure_password';
-- GRANT SELECT, INSERT, UPDATE ON nexora_db.* TO 'nexora_user'@'localhost';
-- FLUSH PRIVILEGES;

-- ==========================================
-- DISPLAY TABLES AND STRUCTURE
-- ==========================================

SHOW TABLES;

DESCRIBE contact_messages;
DESCRIBE newsletter_subscribers;

-- Display initial counts
SELECT 'Contact Messages' AS table_name, COUNT(*) AS row_count FROM contact_messages
UNION ALL
SELECT 'Newsletter Subscribers', COUNT(*) FROM newsletter_subscribers;

-- Success message
SELECT 'Database setup completed successfully!' AS status;
