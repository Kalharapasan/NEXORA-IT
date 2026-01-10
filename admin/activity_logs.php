<?php
$pageTitle = 'Activity Logs';
require_once __DIR__ . '/includes/header.php';

// Get filter parameters
$adminFilter = $_GET['admin'] ?? '';
$actionFilter = $_GET['action'] ?? '';
$dateFilter = $_GET['date'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 50;
$offset = ($page - 1) * $perPage;

try {
    $db = getDBConnection();
    
    // Build query
    $whereConditions = [];
    $params = [];
    
    if ($adminFilter) {
        $whereConditions[] = "admin_id = ?";
        $params[] = $adminFilter;
    }
    
    if ($actionFilter) {
        $whereConditions[] = "action = ?";
        $params[] = $actionFilter;
    }
    
    if ($dateFilter) {
        $whereConditions[] = "DATE(created_at) = ?";
        $params[] = $dateFilter;
    }
    
    $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";
    
    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM admin_activity_logs $whereClause";
    $countStmt = $db->prepare($countQuery);
    $countStmt->execute($params);
    $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalRecords / $perPage);
    
    // Get activity logs with admin info
    $query = "SELECT al.*, au.username, au.full_name 
              FROM admin_activity_logs al
              LEFT JOIN admin_users au ON al.admin_id = au.id
              $whereClause 
              ORDER BY al.created_at DESC 
              LIMIT $perPage OFFSET $offset";
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get admin list for filter
    $adminsStmt = $db->query("SELECT id, username, full_name FROM admin_users ORDER BY username");
    $adminsList = $adminsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get unique actions for filter
    $actionsStmt = $db->query("SELECT DISTINCT action FROM admin_activity_logs ORDER BY action");
    $actionsList = $actionsStmt->fetchAll(PDO::FETCH_COLUMN);
    
} catch (Exception $e) {
    error_log("Activity logs error: " . $e->getMessage());
    $logs = [];
    $adminsList = [];
    $actionsList = [];
    $totalRecords = 0;
    $totalPages = 0;
}
?>

<div class="page-header">
    <h2><i class="fas fa-history"></i> Admin Activity Logs</h2>
    <div class="page-actions">
        <button onclick="exportLogs()" class="btn-secondary">
            <i class="fas fa-download"></i> Export Logs
        </button>
    </div>
</div>

<!-- Filters -->
<div class="filters-bar">
    <form method="GET" action="" class="filters-form">
        <div class="filter-group">
            <label>Admin:</label>
            <select name="admin" onchange="this.form.submit()">
                <option value="">All Admins</option>
                <?php foreach ($adminsList as $admin): ?>
                    <option value="<?php echo $admin['id']; ?>" <?php echo $adminFilter == $admin['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($admin['username']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="filter-group">
            <label>Action:</label>
            <select name="action" onchange="this.form.submit()">
                <option value="">All Actions</option>
                <?php foreach ($actionsList as $action): ?>
                    <option value="<?php echo htmlspecialchars($action); ?>" <?php echo $actionFilter === $action ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($action); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="filter-group">
            <label>Date:</label>
            <input type="date" name="date" value="<?php echo htmlspecialchars($dateFilter); ?>" onchange="this.form.submit()">
        </div>
        
        <?php if ($adminFilter || $actionFilter || $dateFilter): ?>
        <a href="activity_logs.php" class="btn-clear">
            <i class="fas fa-times"></i> Clear Filters
        </a>
        <?php endif; ?>
    </form>
</div>

<!-- Activity Logs Table -->
<div class="data-card">
    <p class="text-muted mb-3">Total Records: <?php echo number_format($totalRecords); ?></p>
    
    <?php if (empty($logs)): ?>
        <p class="no-data">No activity logs found</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date & Time</th>
                        <th>Admin</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?php echo $log['id']; ?></td>
                        <td><?php echo date('M d, Y H:i:s', strtotime($log['created_at'])); ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($log['username'] ?? 'Unknown'); ?></strong><br>
                            <small class="text-muted"><?php echo htmlspecialchars($log['full_name'] ?? ''); ?></small>
                        </td>
                        <td>
                            <span class="badge badge-info">
                                <?php echo htmlspecialchars($log['action']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($log['description']); ?></td>
                        <td><code><?php echo htmlspecialchars($log['ip_address']); ?></code></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>&admin=<?php echo $adminFilter; ?>&action=<?php echo $actionFilter; ?>&date=<?php echo $dateFilter; ?>" class="page-link">
                    <i class="fas fa-chevron-left"></i> Previous
                </a>
            <?php endif; ?>
            
            <span class="page-info">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
            
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>&admin=<?php echo $adminFilter; ?>&action=<?php echo $actionFilter; ?>&date=<?php echo $dateFilter; ?>" class="page-link">
                    Next <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
function exportLogs() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', '1');
    window.location.href = 'ajax/export_logs.php?' + params.toString();
}
</script>

<style>
.mb-3 {
    margin-bottom: 1rem;
}

.text-muted {
    color: #6B7280;
}

code {
    background: #F3F4F6;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
