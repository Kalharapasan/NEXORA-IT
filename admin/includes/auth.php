<?php
/**
 * Authentication Helper Functions
 * Handles session management and authentication checks
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database configuration
require_once __DIR__ . '/../../php/config.php';

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Require login - redirect to login page if not authenticated
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

/**
 * Get current admin user data
 * @return array|null
 */
function getCurrentAdmin() {
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'id' => $_SESSION['admin_id'] ?? null,
        'username' => $_SESSION['admin_username'] ?? null,
        'email' => $_SESSION['admin_email'] ?? null,
        'full_name' => $_SESSION['admin_full_name'] ?? null,
        'role' => $_SESSION['admin_role'] ?? null
    ];
}

/**
 * Login admin user
 * @param string $username
 * @param string $password
 * @return array ['success' => bool, 'message' => string]
 */
function loginAdmin($username, $password) {
    try {
        $db = getDBConnection();
        
        // Get user by username
        $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = ? AND is_active = 1");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Log login attempt
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        if ($admin && password_verify($password, $admin['password'])) {
            // Successful login
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_full_name'] = $admin['full_name'];
            $_SESSION['admin_role'] = $admin['role'];
            
            // Update last login
            $updateStmt = $db->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
            $updateStmt->execute([$admin['id']]);
            
            // Log successful attempt
            $logStmt = $db->prepare("INSERT INTO login_attempts (username, ip_address, user_agent, success, attempted_at) VALUES (?, ?, ?, 1, NOW())");
            $logStmt->execute([$username, $ip, $userAgent]);
            
            // Log activity
            logAdminActivity($admin['id'], 'login', 'Admin logged in successfully');
            
            return ['success' => true, 'message' => 'Login successful'];
        } else {
            // Failed login
            $logStmt = $db->prepare("INSERT INTO login_attempts (username, ip_address, user_agent, success, attempted_at) VALUES (?, ?, ?, 0, NOW())");
            $logStmt->execute([$username, $ip, $userAgent]);
            
            return ['success' => false, 'message' => 'Invalid username or password'];
        }
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        return ['success' => false, 'message' => 'An error occurred during login'];
    }
}

/**
 * Logout admin user
 */
function logoutAdmin() {
    $adminId = $_SESSION['admin_id'] ?? null;
    
    if ($adminId) {
        logAdminActivity($adminId, 'logout', 'Admin logged out');
    }
    
    // Destroy session
    session_unset();
    session_destroy();
    
    // Start new session for flash message
    session_start();
    $_SESSION['flash_message'] = 'You have been logged out successfully';
    
    header('Location: login.php');
    exit();
}

/**
 * Log admin activity
 * @param int $adminId
 * @param string $action
 * @param string $description
 */
function logAdminActivity($adminId, $action, $description = null) {
    try {
        $db = getDBConnection();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        
        $stmt = $db->prepare("INSERT INTO admin_activity_log (admin_id, action, description, ip_address, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$adminId, $action, $description, $ip]);
    } catch (Exception $e) {
        error_log("Activity log error: " . $e->getMessage());
    }
}

/**
 * Check if user has permission
 * @param string $requiredRole
 * @return bool
 */
function hasPermission($requiredRole) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $role = $_SESSION['admin_role'] ?? '';
    $roles = ['viewer' => 1, 'admin' => 2, 'super_admin' => 3];
    
    $userLevel = $roles[$role] ?? 0;
    $requiredLevel = $roles[$requiredRole] ?? 0;
    
    return $userLevel >= $requiredLevel;
}

/**
 * Get flash message and clear it
 * @return string|null
 */
function getFlashMessage() {
    $message = $_SESSION['flash_message'] ?? null;
    unset($_SESSION['flash_message']);
    return $message;
}

/**
 * Set flash message
 * @param string $message
 */
function setFlashMessage($message) {
    $_SESSION['flash_message'] = $message;
}
