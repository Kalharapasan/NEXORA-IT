<?php
$pageTitle = 'Settings';
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = 'All fields are required';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'New passwords do not match';
    } elseif (strlen($newPassword) < 6) {
        $error = 'Password must be at least 6 characters long';
    } else {
        try {
            $db = getDBConnection();
            $adminId = $currentAdmin['id'];
            
            // Verify current password
            $stmt = $db->prepare("SELECT password FROM admin_users WHERE id = ?");
            $stmt->execute([$adminId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!password_verify($currentPassword, $user['password'])) {
                $error = 'Current password is incorrect';
            } else {
                // Update password
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateStmt = $db->prepare("UPDATE admin_users SET password = ?, updated_at = NOW() WHERE id = ?");
                $updateStmt->execute([$hashedPassword, $adminId]);
                
                logAdminActivity($adminId, 'change_password', 'Password changed successfully');
                $success = 'Password changed successfully';
            }
        } catch (Exception $e) {
            error_log("Password change error: " . $e->getMessage());
            $error = 'An error occurred while changing password';
        }
    }
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    if (empty($fullName) || empty($email)) {
        $error = 'Full name and email are required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } else {
        try {
            $db = getDBConnection();
            $adminId = $currentAdmin['id'];
            
            // Check if email already exists (for other users)
            $checkStmt = $db->prepare("SELECT id FROM admin_users WHERE email = ? AND id != ?");
            $checkStmt->execute([$email, $adminId]);
            
            if ($checkStmt->fetch()) {
                $error = 'Email already in use by another admin';
            } else {
                // Update profile
                $updateStmt = $db->prepare("UPDATE admin_users SET full_name = ?, email = ?, updated_at = NOW() WHERE id = ?");
                $updateStmt->execute([$fullName, $email, $adminId]);
                
                // Update session
                $_SESSION['admin_full_name'] = $fullName;
                $_SESSION['admin_email'] = $email;
                
                logAdminActivity($adminId, 'update_profile', 'Profile updated successfully');
                $success = 'Profile updated successfully';
                
                // Refresh current admin data
                $currentAdmin = getCurrentAdmin();
            }
        } catch (Exception $e) {
            error_log("Profile update error: " . $e->getMessage());
            $error = 'An error occurred while updating profile';
        }
    }
}
?>

<?php if ($success): ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i>
    <?php echo htmlspecialchars($success); ?>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-error">
    <i class="fas fa-exclamation-circle"></i>
    <?php echo htmlspecialchars($error); ?>
</div>
<?php endif; ?>

<div class="settings-grid">
    <!-- Profile Settings -->
    <div class="dashboard-card">
        <div class="card-header">
            <h2><i class="fas fa-user"></i> Profile Settings</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input 
                        type="text" 
                        id="username" 
                        value="<?php echo htmlspecialchars($currentAdmin['username']); ?>" 
                        disabled
                        class="form-control"
                    >
                    <small>Username cannot be changed</small>
                </div>
                
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input 
                        type="text" 
                        id="full_name" 
                        name="full_name" 
                        value="<?php echo htmlspecialchars($currentAdmin['full_name']); ?>" 
                        required
                        class="form-control"
                    >
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="<?php echo htmlspecialchars($currentAdmin['email']); ?>" 
                        required
                        class="form-control"
                    >
                </div>
                
                <div class="form-group">
                    <label for="role">Role</label>
                    <input 
                        type="text" 
                        id="role" 
                        value="<?php echo htmlspecialchars($currentAdmin['role']); ?>" 
                        disabled
                        class="form-control"
                    >
                </div>
                
                <button type="submit" name="update_profile" class="btn-primary">
                    <i class="fas fa-save"></i> Update Profile
                </button>
            </form>
        </div>
    </div>
    
    <!-- Change Password -->
    <div class="dashboard-card">
        <div class="card-header">
            <h2><i class="fas fa-lock"></i> Change Password</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input 
                        type="password" 
                        id="current_password" 
                        name="current_password" 
                        required
                        class="form-control"
                    >
                </div>
                
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input 
                        type="password" 
                        id="new_password" 
                        name="new_password" 
                        required
                        minlength="6"
                        class="form-control"
                    >
                    <small>Minimum 6 characters</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        required
                        minlength="6"
                        class="form-control"
                    >
                </div>
                
                <button type="submit" name="change_password" class="btn-primary">
                    <i class="fas fa-key"></i> Change Password
                </button>
            </form>
        </div>
    </div>
    
    <!-- System Information -->
    <div class="dashboard-card">
        <div class="card-header">
            <h2><i class="fas fa-info-circle"></i> System Information</h2>
        </div>
        <div class="card-body">
            <div class="info-grid">
                <div class="info-item">
                    <strong>Admin Panel Version:</strong>
                    <span>1.0</span>
                </div>
                <div class="info-item">
                    <strong>PHP Version:</strong>
                    <span><?php echo phpversion(); ?></span>
                </div>
                <div class="info-item">
                    <strong>Database:</strong>
                    <span>MySQL/MariaDB</span>
                </div>
                <div class="info-item">
                    <strong>Server:</strong>
                    <span><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.settings-grid {
    display: grid;
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--text-primary);
}

.form-control {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid var(--border-color);
    border-radius: 5px;
    font-size: 1rem;
}

.form-control:disabled {
    background: #f8f9fa;
    cursor: not-allowed;
}

.form-group small {
    display: block;
    margin-top: 5px;
    color: var(--text-secondary);
    font-size: 0.85rem;
}

.info-grid {
    display: grid;
    gap: 15px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    padding: 15px;
    background: var(--light-color);
    border-radius: 5px;
}

.info-item strong {
    color: var(--text-primary);
}

.info-item span {
    color: var(--text-secondary);
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
