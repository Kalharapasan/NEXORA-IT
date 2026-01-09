
CREATE DATABASE IF NOT EXISTS nexora_db 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE nexora_db;


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
-- TEAM MEMBERS TABLE
-- ==========================================
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
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_display_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample team members
INSERT INTO team_members (name, position, bio, image_url, linkedin_url, twitter_url, github_url, display_order, status, created_at) VALUES
('John Anderson', 'Chief Technology Officer', 'Leading technology innovation with over 15 years of experience in software architecture and development.', 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop', '#', '#', '#', 1, 'active', NOW()),
('Sarah Mitchell', 'Lead Developer', 'Full-stack developer passionate about creating elegant solutions to complex problems.', 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400&h=400&fit=crop', '#', '#', '#', 2, 'active', NOW()),
('Michael Chen', 'UX/UI Designer', 'Crafting beautiful and intuitive user experiences that delight users and drive engagement.', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop', '#', '#', '#', 3, 'active', NOW()),
('Emily Roberts', 'Project Manager', 'Ensuring projects are delivered on time and exceed client expectations every time.', 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=400&h=400&fit=crop', '#', '#', '#', 4, 'active', NOW());

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


CREATE OR REPLACE VIEW newsletter_stats AS
SELECT 
    COUNT(*) AS total_subscribers,
    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active_subscribers,
    SUM(CASE WHEN status = 'unsubscribed' THEN 1 ELSE 0 END) AS unsubscribed,
    SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) AS today_subscriptions
FROM newsletter_subscribers;

DELIMITER $$
CREATE PROCEDURE IF NOT EXISTS GetContactMessagesByStatus(IN msg_status VARCHAR(20))
BEGIN
    SELECT * FROM contact_messages 
    WHERE status = msg_status 
    ORDER BY created_at DESC;
END$$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE IF NOT EXISTS GetActiveSubscribers()
BEGIN
    SELECT email, created_at 
    FROM newsletter_subscribers 
    WHERE status = 'active' 
    ORDER BY created_at DESC;
END$$
DELIMITER ;


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


SELECT 'Contact Messages' AS table_name, COUNT(*) AS row_count FROM contact_messages
UNION ALL
SELECT 'Newsletter Subscribers', COUNT(*) FROM newsletter_subscribers;


SELECT 'Database setup completed successfully!' AS status;
