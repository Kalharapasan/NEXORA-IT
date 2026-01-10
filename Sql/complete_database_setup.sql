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
    INDEX idx_created_at (created_at),
    INDEX idx_name_email (name, email)
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
    INDEX idx_created_at (created_at),
    INDEX idx_status_created (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



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



CREATE TABLE IF NOT EXISTS admin_users (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('super_admin', 'admin', 'viewer') DEFAULT 'admin',
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS login_attempts (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(255) DEFAULT NULL,
    success TINYINT(1) DEFAULT 0,
    attempted_at DATETIME NOT NULL,
    INDEX idx_username (username),
    INDEX idx_ip_address (ip_address),
    INDEX idx_attempted_at (attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS admin_activity_log (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id INT(11) UNSIGNED NOT NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_admin_id (admin_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (admin_id) REFERENCES admin_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- INSERT DEFAULT DATA
-- ==========================================

-- Insert default admin user
-- Username: admin
-- Password: admin123 (CHANGE THIS IMMEDIATELY!)
INSERT INTO admin_users (username, email, password, full_name, role, created_at)
VALUES (
    'admin',
    'nexorait@outlook.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- Password: admin123
    'Administrator',
    'super_admin',
    NOW()
) ON DUPLICATE KEY UPDATE username = username;

-- Insert sample team members
INSERT INTO team_members (name, position, bio, image_url, linkedin_url, twitter_url, github_url, display_order, status, created_at) VALUES
('John Anderson', 'Chief Technology Officer', 'Leading technology innovation with over 15 years of experience in software architecture and development.', 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop', '#', '#', '#', 1, 'active', NOW()),
('Sarah Mitchell', 'Lead Developer', 'Full-stack developer passionate about creating elegant solutions to complex problems.', 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400&h=400&fit=crop', '#', '#', '#', 2, 'active', NOW()),
('Michael Chen', 'UX/UI Designer', 'Crafting beautiful and intuitive user experiences that delight users and drive engagement.', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop', '#', '#', '#', 3, 'active', NOW()),
('Emily Roberts', 'Project Manager', 'Ensuring projects are delivered on time and exceed client expectations every time.', 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=400&h=400&fit=crop', '#', '#', '#', 4, 'active', NOW())
ON DUPLICATE KEY UPDATE name = name;

-- ==========================================
-- CREATE VIEWS FOR REPORTING
-- ==========================================

-- View: Dashboard Statistics
CREATE OR REPLACE VIEW dashboard_stats AS
SELECT 
    (SELECT COUNT(*) FROM contact_messages WHERE status = 'new') AS new_messages,
    (SELECT COUNT(*) FROM contact_messages) AS total_messages,
    (SELECT COUNT(*) FROM newsletter_subscribers WHERE status = 'active') AS active_subscribers,
    (SELECT COUNT(*) FROM newsletter_subscribers) AS total_subscribers,
    (SELECT COUNT(*) FROM contact_messages WHERE DATE(created_at) = CURDATE()) AS today_messages,
    (SELECT COUNT(*) FROM newsletter_subscribers WHERE DATE(created_at) = CURDATE()) AS today_subscribers,
    (SELECT COUNT(*) FROM team_members WHERE status = 'active') AS active_team_members;

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

-- View: Newsletter Statistics
CREATE OR REPLACE VIEW newsletter_stats AS
SELECT 
    COUNT(*) AS total_subscribers,
    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active_subscribers,
    SUM(CASE WHEN status = 'unsubscribed' THEN 1 ELSE 0 END) AS unsubscribed,
    SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) AS today_subscriptions
FROM newsletter_subscribers;

-- View: Team Statistics
CREATE OR REPLACE VIEW team_stats AS
SELECT 
    COUNT(*) AS total_members,
    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active_members,
    SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) AS inactive_members
FROM team_members;

-- View: Recent Activity (Last 7 days)
CREATE OR REPLACE VIEW recent_activity AS
SELECT 
    'contact' AS type,
    id,
    name AS title,
    email,
    created_at
FROM contact_messages
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
UNION ALL
SELECT 
    'newsletter' AS type,
    id,
    'Newsletter Subscription' AS title,
    email,
    created_at
FROM newsletter_subscribers
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
ORDER BY created_at DESC
LIMIT 20;

-- ==========================================
-- CREATE STORED PROCEDURES
-- ==========================================

DELIMITER $$

-- Procedure: Get Contact Messages by Status
CREATE PROCEDURE IF NOT EXISTS GetContactMessagesByStatus(IN msg_status VARCHAR(20))
BEGIN
    SELECT * FROM contact_messages 
    WHERE status = msg_status 
    ORDER BY created_at DESC;
END$$

-- Procedure: Get Active Subscribers
CREATE PROCEDURE IF NOT EXISTS GetActiveSubscribers()
BEGIN
    SELECT email, created_at 
    FROM newsletter_subscribers 
    WHERE status = 'active' 
    ORDER BY created_at DESC;
END$$

-- Procedure: Get Dashboard Summary
CREATE PROCEDURE IF NOT EXISTS GetDashboardSummary()
BEGIN
    SELECT * FROM dashboard_stats;
END$$

DELIMITER ;

-- ==========================================
-- CREATE TRIGGERS
-- ==========================================

DELIMITER $$

-- Trigger: Update timestamp when contact message status changes
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
-- DISPLAY SETUP INFORMATION
-- ==========================================

SELECT '✅ Database created successfully!' AS status;
SELECT 'nexora_db' AS database_name;

SELECT 'Tables created:' AS info;
SHOW TABLES;

SELECT 'Default Admin Credentials:' AS info;
SELECT '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' AS separator;
SELECT 'Username: admin' AS username;
SELECT 'Password: admin123' AS password;
SELECT '⚠️  CHANGE DEFAULT PASSWORD IMMEDIATELY!' AS warning;
SELECT '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' AS separator;

SELECT 'Database Statistics:' AS info;
SELECT 
    (SELECT COUNT(*) FROM contact_messages) AS contact_messages,
    (SELECT COUNT(*) FROM newsletter_subscribers) AS newsletter_subscribers,
    (SELECT COUNT(*) FROM team_members) AS team_members,
    (SELECT COUNT(*) FROM admin_users) AS admin_users;

SELECT '✅ Setup completed successfully! You can now access the admin panel.' AS status;

-- ==========================================
-- NOTES FOR PRODUCTION
-- ==========================================
/*
IMPORTANT SECURITY NOTES:

1. Change the default admin password immediately after first login!
   
2. For production, create a specific database user with limited privileges:
   
   CREATE USER 'nexora_user'@'localhost' IDENTIFIED BY 'your_secure_password';
   GRANT SELECT, INSERT, UPDATE, DELETE ON nexora_db.* TO 'nexora_user'@'localhost';
   FLUSH PRIVILEGES;

3. Update the config.php file with your database credentials

4. Ensure your web server has proper security headers configured

5. Enable HTTPS and update .htaccess to force SSL connections

6. Regular backups are essential - set up automated backups

7. Monitor the login_attempts table for suspicious activity

8. Review admin_activity_log regularly for audit purposes
*/
