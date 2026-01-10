<?php
/**
 * Fix Login Script - Diagnose and fix login issues
 */

require_once 'php/config.php';

echo "=== NEXORA Login Fix Script ===\n\n";

try {
    $db = getDBConnection();
    echo "✓ Database connection successful\n\n";
    
    // Check if admin_users table exists
    $tables = $db->query("SHOW TABLES LIKE 'admin_users'")->fetchAll();
    if (empty($tables)) {
        echo "✗ admin_users table not found!\n";
        echo "Please run the database setup SQL file first.\n";
        exit(1);
    }
    echo "✓ admin_users table exists\n\n";
    
    // Check for admin users
    $stmt = $db->query("SELECT id, username, email, role, is_active FROM admin_users");
    $users = $stmt->fetchAll();
    
    if (empty($users)) {
        echo "✗ No admin users found in database\n";
        echo "Creating default admin user...\n\n";
        
        $username = 'admin';
        $email = 'admin@nexorait.com';
        $password = 'admin123';
        $fullName = 'System Administrator';
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        $createStmt = $db->prepare("INSERT INTO admin_users (username, email, password, full_name, role, is_active, created_at) VALUES (?, ?, ?, ?, 'super_admin', 1, NOW())");
        $createStmt->execute([$username, $email, $passwordHash, $fullName]);
        
        echo "✓ Default admin user created successfully!\n";
        echo "  Username: $username\n";
        echo "  Password: $password\n\n";
    } else {
        echo "✓ Admin users found:\n";
        foreach ($users as $user) {
            echo "  - Username: {$user['username']}, Email: {$user['email']}, Role: {$user['role']}, Active: {$user['is_active']}\n";
        }
        echo "\n";
    }
    
    // Test password for admin user
    echo "Testing admin user password...\n";
    $stmt = $db->prepare("SELECT id, username, password FROM admin_users WHERE username = ?");
    $stmt->execute(['admin']);
    $adminUser = $stmt->fetch();
    
    if (!$adminUser) {
        echo "✗ Admin user not found\n";
        echo "Creating admin user...\n";
        
        $username = 'admin';
        $email = 'admin@nexorait.com';
        $password = 'admin123';
        $fullName = 'System Administrator';
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        $createStmt = $db->prepare("INSERT INTO admin_users (username, email, password, full_name, role, is_active, created_at) VALUES (?, ?, ?, ?, 'super_admin', 1, NOW())");
        $createStmt->execute([$username, $email, $passwordHash, $fullName]);
        
        echo "✓ Admin user created successfully!\n";
        echo "  Username: $username\n";
        echo "  Password: $password\n\n";
    } else {
        $testPassword = 'admin123';
        if (password_verify($testPassword, $adminUser['password'])) {
            echo "✓ Password verification successful for 'admin' user with password 'admin123'\n\n";
        } else {
            echo "✗ Password verification failed\n";
            echo "Resetting password to 'admin123'...\n";
            
            $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
            $updateStmt = $db->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
            $updateStmt->execute([$newHash, $adminUser['id']]);
            
            echo "✓ Password reset successfully!\n";
            echo "  Username: admin\n";
            echo "  Password: admin123\n\n";
        }
    }
    
    // Check required tables for login functionality
    echo "Checking required tables...\n";
    $requiredTables = ['admin_users', 'login_attempts', 'admin_activity_log'];
    foreach ($requiredTables as $table) {
        $result = $db->query("SHOW TABLES LIKE '$table'")->fetchAll();
        if (empty($result)) {
            echo "  ✗ Table '$table' is missing\n";
        } else {
            echo "  ✓ Table '$table' exists\n";
        }
    }
    
    echo "\n=== Fix Complete ===\n";
    echo "You can now login with:\n";
    echo "Username: admin\n";
    echo "Password: admin123\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
