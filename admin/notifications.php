<?php
$pageTitle = 'Notifications';
require_once __DIR__ . '/includes/header.php';

try {
    $db = getDBConnection();
    $adminId = $_SESSION['admin_id'];
    
    // Handle mark as read
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['mark_read'])) {
            $notificationId = $_POST['notification_id'];
            $stmt = $db->prepare("UPDATE admin_notifications SET is_read = 1, read_at = NOW() WHERE id = ? AND admin_id = ?");
            $stmt->execute([$notificationId, $adminId]);
        }
        
        if (isset($_POST['mark_all_read'])) {
            $stmt = $db->prepare("UPDATE admin_notifications SET is_read = 1, read_at = NOW() WHERE admin_id = ? AND is_read = 0");
            $stmt->execute([$adminId]);
            $message = "All notifications marked as read!";
        }
        
        if (isset($_POST['delete'])) {
            $notificationId = $_POST['notification_id'];
            $stmt = $db->prepare("DELETE FROM admin_notifications WHERE id = ? AND admin_id = ?");
            $stmt->execute([$notificationId, $adminId]);
        }
        
        if (isset($_POST['delete_read'])) {
            $stmt = $db->prepare("DELETE FROM admin_notifications WHERE admin_id = ? AND is_read = 1");
            $stmt->execute([$adminId]);
            $message = "All read notifications deleted!";
        }
    }
    
    // Get filter
    $filter = $_GET['filter'] ?? 'all';
    
    // Build query
    $whereClause = "admin_id = ?";
    $params = [$adminId];
    
    if ($filter === 'unread') {
        $whereClause .= " AND is_read = 0";
    } elseif ($filter === 'read') {
        $whereClause .= " AND is_read = 1";
    } elseif ($filter !== 'all') {
        $whereClause .= " AND notification_type = ?";
        $params[] = $filter;
    }
    
    // Get notifications
    $stmt = $db->prepare("SELECT * FROM admin_notifications WHERE $whereClause ORDER BY created_at DESC");
    $stmt->execute($params);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get counts
    $countsStmt = $db->prepare("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread,
        SUM(CASE WHEN is_read = 1 THEN 1 ELSE 0 END) as read
        FROM admin_notifications 
        WHERE admin_id = ?");
    $countsStmt->execute([$adminId]);
    $counts = $countsStmt->fetch(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Notifications error: " . $e->getMessage());
    $error = "An error occurred loading notifications.";
    $notifications = [];
    $counts = ['total' => 0, 'unread' => 0, 'read' => 0];
}
?>

<div class="content-header">
    <h1><i class="fas fa-bell"></i> Notifications</h1>
    <div class="header-actions">
        <?php if ($counts['unread'] > 0): ?>
            <form method="POST" style="display: inline;">
                <button type="submit" name="mark_all_read" class="btn btn-secondary">
                    <i class="fas fa-check-double"></i> Mark All Read
                </button>
            </form>
        <?php endif; ?>
        <?php if ($counts['read'] > 0): ?>
            <form method="POST" style="display: inline;" onsubmit="return confirm('Delete all read notifications?');">
                <button type="submit" name="delete_read" class="btn btn-secondary">
                    <i class="fas fa-trash"></i> Clear Read
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($message)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<!-- Filter Tabs -->
<div class="filter-tabs">
    <a href="?filter=all" class="filter-tab <?php echo $filter === 'all' ? 'active' : ''; ?>">
        All <span class="badge"><?php echo $counts['total']; ?></span>
    </a>
    <a href="?filter=unread" class="filter-tab <?php echo $filter === 'unread' ? 'active' : ''; ?>">
        Unread <span class="badge badge-primary"><?php echo $counts['unread']; ?></span>
    </a>
    <a href="?filter=read" class="filter-tab <?php echo $filter === 'read' ? 'active' : ''; ?>">
        Read <span class="badge"><?php echo $counts['read']; ?></span>
    </a>
    <div class="filter-divider"></div>
    <a href="?filter=info" class="filter-tab <?php echo $filter === 'info' ? 'active' : ''; ?>">
        <i class="fas fa-info-circle text-info"></i> Info
    </a>
    <a href="?filter=success" class="filter-tab <?php echo $filter === 'success' ? 'active' : ''; ?>">
        <i class="fas fa-check-circle text-success"></i> Success
    </a>
    <a href="?filter=warning" class="filter-tab <?php echo $filter === 'warning' ? 'active' : ''; ?>">
        <i class="fas fa-exclamation-triangle text-warning"></i> Warning
    </a>
    <a href="?filter=error" class="filter-tab <?php echo $filter === 'error' ? 'active' : ''; ?>">
        <i class="fas fa-times-circle text-danger"></i> Error
    </a>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($notifications)): ?>
            <div class="empty-state">
                <i class="fas fa-bell-slash"></i>
                <p>No notifications found</p>
            </div>
        <?php else: ?>
            <div class="notifications-list">
                <?php foreach ($notifications as $notification): ?>
                    <div class="notification-item <?php echo $notification['is_read'] ? 'read' : 'unread'; ?> notification-<?php echo $notification['notification_type']; ?>">
                        <div class="notification-icon">
                            <?php
                            $icons = [
                                'info' => 'fa-info-circle',
                                'success' => 'fa-check-circle',
                                'warning' => 'fa-exclamation-triangle',
                                'error' => 'fa-times-circle'
                            ];
                            $icon = $icons[$notification['notification_type']] ?? 'fa-bell';
                            ?>
                            <i class="fas <?php echo $icon; ?>"></i>
                        </div>
                        
                        <div class="notification-content">
                            <h4><?php echo htmlspecialchars($notification['title']); ?></h4>
                            <p><?php echo nl2br(htmlspecialchars($notification['message'])); ?></p>
                            <div class="notification-meta">
                                <span class="notification-time">
                                    <i class="fas fa-clock"></i>
                                    <?php echo timeAgo($notification['created_at']); ?>
                                </span>
                                <?php if ($notification['is_read']): ?>
                                    <span class="notification-read-time">
                                        Read <?php echo timeAgo($notification['read_at']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="notification-actions">
                            <?php if (!$notification['is_read']): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="notification_id" value="<?php echo $notification['id']; ?>">
                                    <button type="submit" name="mark_read" class="btn-icon" title="Mark as read">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <?php if ($notification['action_url']): ?>
                                <a href="<?php echo htmlspecialchars($notification['action_url']); ?>" class="btn-icon" title="View">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            <?php endif; ?>
                            
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this notification?');">
                                <input type="hidden" name="notification_id" value="<?php echo $notification['id']; ?>">
                                <button type="submit" name="delete" class="btn-icon btn-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    
    return date('M d, Y', $time);
}
?>

<style>
.filter-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    background: white;
    padding: 1rem;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    flex-wrap: wrap;
    align-items: center;
}

.filter-tab {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    text-decoration: none;
    color: #666;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-tab:hover {
    background: #f0f0f0;
    color: #333;
}

.filter-tab.active {
    background: #2196F3;
    color: white;
}

.filter-tab .badge {
    background: rgba(0,0,0,0.1);
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
}

.filter-tab.active .badge {
    background: rgba(255,255,255,0.3);
}

.filter-divider {
    width: 1px;
    height: 24px;
    background: #ddd;
    margin: 0 0.5rem;
}

.notifications-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.notification-item {
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 1rem;
    padding: 1rem;
    border-radius: 8px;
    border-left: 4px solid;
    transition: all 0.3s;
}

.notification-item.unread {
    background: #E3F2FD;
}

.notification-item.read {
    background: #f8f9fa;
    opacity: 0.8;
}

.notification-item.notification-info {
    border-left-color: #2196F3;
}

.notification-item.notification-success {
    border-left-color: #4CAF50;
}

.notification-item.notification-warning {
    border-left-color: #FF9800;
}

.notification-item.notification-error {
    border-left-color: #F44336;
}

.notification-icon {
    font-size: 1.5rem;
}

.notification-item.notification-info .notification-icon {
    color: #2196F3;
}

.notification-item.notification-success .notification-icon {
    color: #4CAF50;
}

.notification-item.notification-warning .notification-icon {
    color: #FF9800;
}

.notification-item.notification-error .notification-icon {
    color: #F44336;
}

.notification-content h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1rem;
    color: #333;
}

.notification-content p {
    margin: 0 0 0.5rem 0;
    color: #666;
    font-size: 0.9rem;
}

.notification-meta {
    display: flex;
    gap: 1rem;
    font-size: 0.8rem;
    color: #999;
}

.notification-actions {
    display: flex;
    gap: 0.5rem;
    align-items: flex-start;
}

.header-actions {
    display: flex;
    gap: 0.5rem;
}

.text-info { color: #2196F3; }
.text-success { color: #4CAF50; }
.text-warning { color: #FF9800; }
.text-danger { color: #F44336; }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
