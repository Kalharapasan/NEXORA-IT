<?php
$pageTitle = 'Admin Users';
require_once __DIR__ . '/includes/header.php';

// Only super_admin can access this page
if ($currentAdmin['role'] !== 'super_admin') {
    header('Location: dashboard.php');
    exit();
}

$success = '';
$error = '';

// Handle add admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_admin'])) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $fullName = trim($_POST['full_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'admin';
    
    if (empty($username) || empty($email) || empty($fullName) || empty($password)) {
        $error = 'All fields are required';
    } elseif (strlen($username) < 3 || strlen($username) > 50) {
        $error = 'Username must be between 3 and 50 characters';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters';
    } else {
        try {
            $db = getDBConnection();
            
            // Check if username or email exists
            $checkStmt = $db->prepare("SELECT id FROM admin_users WHERE username = ? OR email = ?");
            $checkStmt->execute([$username, $email]);
            
            if ($checkStmt->fetch()) {
                $error = 'Username or email already exists';
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO admin_users (username, email, password, full_name, role, is_active, created_at) VALUES (?, ?, ?, ?, ?, 1, NOW())");
                $stmt->execute([$username, $email, $hashedPassword, $fullName, $role]);
                
                logAdminActivity($currentAdmin['id'], 'add_admin', "Added new admin user: $username");
                $success = 'Admin user added successfully';
            }
        } catch (Exception $e) {
            error_log("Add admin error: " . $e->getMessage());
            $error = 'An error occurred while adding admin user';
        }
    }
}

// Handle toggle status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status'])) {
    $adminId = intval($_POST['admin_id'] ?? 0);
    $newStatus = intval($_POST['new_status'] ?? 0);
    
    if ($adminId === $currentAdmin['id']) {
        $error = 'You cannot deactivate your own account';
    } else {
        try {
            $db = getDBConnection();
            $stmt = $db->prepare("UPDATE admin_users SET is_active = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$newStatus, $adminId]);
            
            logAdminActivity($currentAdmin['id'], 'toggle_admin_status', "Toggled admin #$adminId status to $newStatus");
            $success = 'Admin status updated successfully';
        } catch (Exception $e) {
            error_log("Toggle status error: " . $e->getMessage());
            $error = 'An error occurred while updating status';
        }
    }
}

// Handle delete admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_admin'])) {
    $adminId = intval($_POST['admin_id'] ?? 0);
    
    if ($adminId === $currentAdmin['id']) {
        $error = 'You cannot delete your own account';
    } else {
        try {
            $db = getDBConnection();
            $stmt = $db->prepare("DELETE FROM admin_users WHERE id = ?");
            $stmt->execute([$adminId]);
            
            logAdminActivity($currentAdmin['id'], 'delete_admin', "Deleted admin user #$adminId");
            $success = 'Admin user deleted successfully';
        } catch (Exception $e) {
            error_log("Delete admin error: " . $e->getMessage());
            $error = 'An error occurred while deleting admin user';
        }
    }
}

// Get all admin users
try {
    $db = getDBConnection();
    $stmt = $db->query("SELECT * FROM admin_users ORDER BY created_at DESC");
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Fetch admins error: " . $e->getMessage());
    $admins = [];
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

<div class="page-header">
    <h2><i class="fas fa-user-shield"></i> Admin Users Management</h2>
    <button onclick="openAddAdminModal()" class="btn-primary">
        <i class="fas fa-plus"></i> Add Admin User
    </button>
</div>

<div class="data-card">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($admins as $admin): ?>
                <tr>
                    <td><?php echo $admin['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($admin['username']); ?></strong></td>
                    <td><?php echo htmlspecialchars($admin['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($admin['email']); ?></td>
                    <td>
                        <span class="badge badge-<?php echo $admin['role'] === 'super_admin' ? 'success' : 'primary'; ?>">
                            <?php echo htmlspecialchars($admin['role']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-<?php echo $admin['is_active'] ? 'success' : 'danger'; ?>">
                            <?php echo $admin['is_active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </td>
                    <td><?php echo $admin['last_login'] ? date('M d, Y H:i', strtotime($admin['last_login'])) : 'Never'; ?></td>
                    <td><?php echo date('M d, Y', strtotime($admin['created_at'])); ?></td>
                    <td class="action-buttons">
                        <?php if ($admin['id'] !== $currentAdmin['id']): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                                <input type="hidden" name="new_status" value="<?php echo $admin['is_active'] ? 0 : 1; ?>">
                                <button type="submit" name="toggle_status" class="btn-sm btn-info" title="Toggle Status">
                                    <i class="fas fa-<?php echo $admin['is_active'] ? 'ban' : 'check'; ?>"></i>
                                </button>
                            </form>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this admin user?');">
                                <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                                <button type="submit" name="delete_admin" class="btn-sm btn-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        <?php else: ?>
                            <span class="text-muted">Current User</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Admin Modal -->
<div id="addAdminModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add New Admin User</h2>
            <button class="close-modal" onclick="closeAddAdminModal()">&times;</button>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Username *</label>
                <input type="text" id="username" name="username" required minlength="3" maxlength="50">
            </div>
            
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="full_name">Full Name *</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" required minlength="8">
                <small>Minimum 8 characters</small>
            </div>
            
            <div class="form-group">
                <label for="role">Role *</label>
                <select id="role" name="role" required>
                    <option value="admin">Admin</option>
                    <option value="super_admin">Super Admin</option>
                    <option value="viewer">Viewer</option>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeAddAdminModal()">Cancel</button>
                <button type="submit" name="add_admin" class="btn-save">
                    <i class="fas fa-save"></i> Add Admin
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddAdminModal() {
    document.getElementById('addAdminModal').style.display = 'block';
}

function closeAddAdminModal() {
    document.getElementById('addAdminModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('addAdminModal');
    if (event.target === modal) {
        closeAddAdminModal();
    }
}
</script>

<style>
.modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
}

.modal-content {
    background: white;
    margin: 5% auto;
    padding: 2rem;
    border-radius: 12px;
    width: 90%;
    max-width: 600px;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.close-modal {
    font-size: 2rem;
    color: #9CA3AF;
    cursor: pointer;
    border: none;
    background: none;
}

.close-modal:hover {
    color: #EF4444;
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
