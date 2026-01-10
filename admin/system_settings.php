<?php
$pageTitle = 'System Settings';
require_once __DIR__ . '/includes/header.php';

// Check if user has permission (super_admin only)
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'super_admin') {
    header('Location: dashboard.php');
    exit;
}

try {
    $db = getDBConnection();
    
    // Handle settings update
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
        $updatedCount = 0;
        
        foreach ($_POST as $key => $value) {
            if ($key !== 'update_settings') {
                // Get setting type
                $stmt = $db->prepare("SELECT setting_type FROM system_settings WHERE setting_key = ?");
                $stmt->execute([$key]);
                $setting = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($setting) {
                    // Convert value based on type
                    $settingType = $setting['setting_type'];
                    if ($settingType === 'boolean') {
                        $value = isset($_POST[$key]) ? '1' : '0';
                    } elseif ($settingType === 'number') {
                        $value = intval($value);
                    } elseif ($settingType === 'json') {
                        // Validate JSON
                        json_decode($value);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            continue;
                        }
                    }
                    
                    // Update setting
                    $stmt = $db->prepare("UPDATE system_settings SET setting_value = ?, updated_by = ? WHERE setting_key = ?");
                    $stmt->execute([$value, $_SESSION['admin_id'], $key]);
                    $updatedCount++;
                }
            }
        }
        
        // Handle checkboxes that weren't posted (unchecked)
        $stmt = $db->query("SELECT setting_key FROM system_settings WHERE setting_type = 'boolean'");
        $boolSettings = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($boolSettings as $key) {
            if (!isset($_POST[$key])) {
                $stmt = $db->prepare("UPDATE system_settings SET setting_value = '0', updated_by = ? WHERE setting_key = ?");
                $stmt->execute([$_SESSION['admin_id'], $key]);
                $updatedCount++;
            }
        }
        
        if ($updatedCount > 0) {
            logAdminActivity($_SESSION['admin_id'], 'settings_update', "Updated $updatedCount settings");
            $message = "Settings updated successfully! ($updatedCount settings changed)";
        }
    }
    
    // Get all settings grouped by category
    $settingsQuery = "SELECT ss.*, au.username as updated_by_name 
                     FROM system_settings ss 
                     LEFT JOIN admin_users au ON ss.updated_by = au.id 
                     ORDER BY ss.category, ss.setting_key";
    $allSettings = $db->query($settingsQuery)->fetchAll(PDO::FETCH_ASSOC);
    
    // Group by category
    $settings = [];
    foreach ($allSettings as $setting) {
        $category = ucfirst($setting['category']);
        if (!isset($settings[$category])) {
            $settings[$category] = [];
        }
        $settings[$category][] = $setting;
    }
    
} catch (Exception $e) {
    error_log("System settings error: " . $e->getMessage());
    $error = "An error occurred. Please try again.";
    $settings = [];
}
?>

<div class="content-header">
    <h1><i class="fas fa-cogs"></i> System Settings</h1>
</div>

<?php if (isset($message)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="POST">
    <?php foreach ($settings as $category => $categorySettings): ?>
        <div class="card">
            <div class="card-header">
                <h2><?php echo htmlspecialchars($category); ?> Settings</h2>
            </div>
            
            <div class="card-body">
                <div class="settings-grid">
                    <?php foreach ($categorySettings as $setting): ?>
                        <div class="setting-item">
                            <div class="setting-info">
                                <label for="<?php echo htmlspecialchars($setting['setting_key']); ?>">
                                    <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $setting['setting_key']))); ?>
                                </label>
                                <?php if ($setting['description']): ?>
                                    <p class="setting-description"><?php echo htmlspecialchars($setting['description']); ?></p>
                                <?php endif; ?>
                                <?php if ($setting['updated_at']): ?>
                                    <small class="text-muted">
                                        Last updated: <?php echo date('M d, Y H:i', strtotime($setting['updated_at'])); ?>
                                        <?php if ($setting['updated_by_name']): ?>
                                            by <?php echo htmlspecialchars($setting['updated_by_name']); ?>
                                        <?php endif; ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                            
                            <div class="setting-input">
                                <?php if ($setting['setting_type'] === 'boolean'): ?>
                                    <label class="switch">
                                        <input type="checkbox" 
                                               name="<?php echo htmlspecialchars($setting['setting_key']); ?>" 
                                               <?php echo $setting['setting_value'] == '1' ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                    
                                <?php elseif ($setting['setting_type'] === 'number'): ?>
                                    <input type="number" 
                                           name="<?php echo htmlspecialchars($setting['setting_key']); ?>" 
                                           id="<?php echo htmlspecialchars($setting['setting_key']); ?>"
                                           value="<?php echo htmlspecialchars($setting['setting_value']); ?>" 
                                           class="form-control">
                                    
                                <?php elseif ($setting['setting_type'] === 'json'): ?>
                                    <textarea name="<?php echo htmlspecialchars($setting['setting_key']); ?>" 
                                              id="<?php echo htmlspecialchars($setting['setting_key']); ?>"
                                              class="form-control" 
                                              rows="3"><?php echo htmlspecialchars($setting['setting_value']); ?></textarea>
                                    
                                <?php else: ?>
                                    <input type="text" 
                                           name="<?php echo htmlspecialchars($setting['setting_key']); ?>" 
                                           id="<?php echo htmlspecialchars($setting['setting_key']); ?>"
                                           value="<?php echo htmlspecialchars($setting['setting_value']); ?>" 
                                           class="form-control">
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    
    <div class="form-actions">
        <button type="submit" name="update_settings" class="btn btn-primary btn-lg">
            <i class="fas fa-save"></i> Save All Settings
        </button>
        <button type="button" class="btn btn-secondary btn-lg" onclick="location.reload()">
            <i class="fas fa-undo"></i> Reset Changes
        </button>
    </div>
</form>

<style>
.settings-grid {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.setting-item {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 2rem;
    align-items: start;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.setting-info label {
    font-weight: 600;
    color: #333;
    margin-bottom: 0.25rem;
    display: block;
}

.setting-description {
    font-size: 0.875rem;
    color: #666;
    margin: 0.25rem 0;
}

.setting-input {
    min-width: 300px;
}

.setting-input .form-control {
    width: 100%;
}

/* Toggle Switch */
.switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #2196F3;
}

input:checked + .slider:before {
    transform: translateX(26px);
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 2rem;
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.btn-lg {
    padding: 0.75rem 2rem;
    font-size: 1.1rem;
}

.text-muted {
    color: #6c757d;
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .setting-item {
        grid-template-columns: 1fr;
    }
    
    .setting-input {
        min-width: 100%;
    }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
