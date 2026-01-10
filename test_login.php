<?php
/**
 * Test Login Functionality
 */

require_once 'admin/includes/auth.php';

echo "=== Testing Login Functionality ===\n\n";

// Test 1: Database Connection
echo "Test 1: Database Connection\n";
try {
    $db = getDBConnection();
    echo "✓ PASSED: Database connected successfully\n\n";
} catch (Exception $e) {
    echo "✗ FAILED: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 2: Admin User Exists
echo "Test 2: Admin User Exists\n";
$stmt = $db->prepare("SELECT * FROM admin_users WHERE username = ?");
$stmt->execute(['admin']);
$user = $stmt->fetch();
if ($user) {
    echo "✓ PASSED: Admin user found\n";
    echo "  - Username: {$user['username']}\n";
    echo "  - Email: {$user['email']}\n";
    echo "  - Role: {$user['role']}\n";
    echo "  - Active: " . ($user['is_active'] ? 'Yes' : 'No') . "\n\n";
} else {
    echo "✗ FAILED: Admin user not found\n\n";
    exit(1);
}

// Test 3: Password Verification
echo "Test 3: Password Verification\n";
if (password_verify('admin123', $user['password'])) {
    echo "✓ PASSED: Password 'admin123' verified successfully\n\n";
} else {
    echo "✗ FAILED: Password verification failed\n\n";
    exit(1);
}

// Test 4: Login Function
echo "Test 4: Login Function\n";
$result = loginAdmin('admin', 'admin123');
if ($result['success']) {
    echo "✓ PASSED: Login function successful\n";
    echo "  - Message: {$result['message']}\n\n";
} else {
    echo "✗ FAILED: {$result['message']}\n\n";
    exit(1);
}

// Test 5: Session Check
echo "Test 5: Session Variables\n";
if (isLoggedIn()) {
    echo "✓ PASSED: User is logged in\n";
    $admin = getCurrentAdmin();
    echo "  - Admin ID: {$admin['id']}\n";
    echo "  - Username: {$admin['username']}\n";
    echo "  - Email: {$admin['email']}\n";
    echo "  - Full Name: {$admin['full_name']}\n";
    echo "  - Role: {$admin['role']}\n\n";
} else {
    echo "✗ FAILED: User is not logged in\n\n";
    exit(1);
}

// Test 6: Required Tables
echo "Test 6: Required Database Tables\n";
$tables = ['admin_users', 'login_attempts'];
$allExist = true;
foreach ($tables as $table) {
    $result = $db->query("SHOW TABLES LIKE '$table'")->fetchAll();
    if (!empty($result)) {
        echo "  ✓ $table - exists\n";
    } else {
        echo "  ✗ $table - missing\n";
        $allExist = false;
    }
}
if ($allExist) {
    echo "✓ PASSED: All required tables exist\n\n";
} else {
    echo "✗ FAILED: Some tables are missing\n\n";
}

echo "=== All Tests Passed! ===\n";
echo "\nYou can now login at: http://localhost/NEXORA_IT/admin/login.php\n";
echo "Username: admin\n";
echo "Password: admin123\n";
