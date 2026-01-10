-- ==========================================
-- ADMIN USERS TABLE FOR NEXORA ADMIN PANEL
-- ==========================================

USE nexora_db;

-- Create admin users table
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

-- ==========================================
-- INSERT DEFAULT ADMIN USER
-- ==========================================
-- Default credentials:
-- Username: admin
-- Password: admin123 (CHANGE THIS IMMEDIATELY!)
-- 
-- The password is hashed using PHP's password_hash() function
-- To create a new password hash, use: password_hash('your_password', PASSWORD_DEFAULT)

INSERT INTO admin_users (username, email, password, full_name, role, created_at)
VALUES (
    'admin',
    'nexorait@outlook.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- Password: admin123
    'Administrator',
    'super_admin',
    NOW()
) ON DUPLICATE KEY UPDATE username = username;

-- ==========================================
-- CREATE LOGIN ATTEMPTS TABLE (Security)
-- ==========================================

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

-- ==========================================
-- ADMIN ACTIVITY LOG TABLE
-- ==========================================

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
-- USEFUL VIEWS FOR ADMIN PANEL
-- ==========================================

-- View: Dashboard Statistics
CREATE OR REPLACE VIEW dashboard_stats AS
SELECT 
    (SELECT COUNT(*) FROM contact_messages WHERE status = 'new') AS new_messages,
    (SELECT COUNT(*) FROM contact_messages) AS total_messages,
    (SELECT COUNT(*) FROM newsletter_subscribers WHERE status = 'active') AS active_subscribers,
    (SELECT COUNT(*) FROM newsletter_subscribers) AS total_subscribers,
    (SELECT COUNT(*) FROM contact_messages WHERE DATE(created_at) = CURDATE()) AS today_messages,
    (SELECT COUNT(*) FROM newsletter_subscribers WHERE DATE(created_at) = CURDATE()) AS today_subscribers;

-- View: Recent Activity
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
-- COMPLETION MESSAGE
-- ==========================================

SELECT 'Admin setup completed successfully!' AS status;
SELECT 'Default admin credentials:' AS info;
SELECT 'Username: admin' AS username;
SELECT 'Password: admin123' AS password;
SELECT '⚠️  IMPORTANT: Change the default password immediately after first login!' AS warning;
