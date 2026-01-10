<?php
$pageTitle = 'Newsletter Subscribers';
require_once __DIR__ . '/includes/header.php';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $subscriberId = $_POST['subscriber_id'] ?? 0;
    $newStatus = $_POST['status'] ?? 'active';
    
    try {
        $db = getDBConnection();
        $updateFields = ['status = ?', 'updated_at = NOW()'];
        $params = [$newStatus];
        
        if ($newStatus === 'unsubscribed') {
            $updateFields[] = 'unsubscribed_at = NOW()';
        }
        
        $stmt = $db->prepare("UPDATE newsletter_subscribers SET " . implode(', ', $updateFields) . " WHERE id = ?");
        $params[] = $subscriberId;
        $stmt->execute($params);
        
        logAdminActivity($currentAdmin['id'], 'update_subscriber_status', "Updated subscriber #$subscriberId status to $newStatus");
        setFlashMessage('Subscriber status updated successfully');
        header('Location: subscribers.php');
        exit();
    } catch (Exception $e) {
        $error = "Error updating status: " . $e->getMessage();
    }
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_subscriber'])) {
    $subscriberId = $_POST['subscriber_id'] ?? 0;
    
    try {
        $db = getDBConnection();
        $stmt = $db->prepare("DELETE FROM newsletter_subscribers WHERE id = ?");
        $stmt->execute([$subscriberId]);
        
        logAdminActivity($currentAdmin['id'], 'delete_subscriber', "Deleted subscriber #$subscriberId");
        setFlashMessage('Subscriber deleted successfully');
        header('Location: subscribers.php');
        exit();
    } catch (Exception $e) {
        $error = "Error deleting subscriber: " . $e->getMessage();
    }
}

// Get filter parameters
$statusFilter = $_GET['status'] ?? '';
$searchQuery = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

try {
    $db = getDBConnection();
    
    // Build query
    $whereConditions = [];
    $params = [];
    
    if ($statusFilter) {
        $whereConditions[] = "status = ?";
        $params[] = $statusFilter;
    }
    
    if ($searchQuery) {
        $whereConditions[] = "email LIKE ?";
        $params[] = "%$searchQuery%";
    }
    
    $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";
    
    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM newsletter_subscribers $whereClause";
    $countStmt = $db->prepare($countQuery);
    $countStmt->execute($params);
    $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalRecords / $perPage);
    
    // Get subscribers
    $query = "SELECT * FROM newsletter_subscribers $whereClause ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get status counts
    $countsQuery = "SELECT status, COUNT(*) as count FROM newsletter_subscribers GROUP BY status";
    $counts = $db->query($countsQuery)->fetchAll(PDO::FETCH_KEY_PAIR);
    
} catch (Exception $e) {
    error_log("Subscribers page error: " . $e->getMessage());
    $subscribers = [];
    $totalRecords = 0;
    $totalPages = 0;
    $counts = [];
}
?>

<div class="page-header">
    <h2><i class="fas fa-users"></i> Newsletter Subscribers</h2>
    <div class="page-actions">
        <button onclick="showBulkActions()" class="btn-info" id="bulkActionsBtn" style="display: none;">
            <i class="fas fa-tasks"></i> Bulk Actions
        </button>
        <button onclick="exportData('subscribers')" class="btn-secondary">
            <i class="fas fa-download"></i> Export CSV
        </button>
    </div>
</div>

<!-- Bulk Actions Bar -->
<div id="bulkActionsBar" class="bulk-actions-bar" style="display: none;">
    <div class="bulk-actions-content">
        <span id="selectedCount">0 selected</span>
        <div class="bulk-buttons">
            <button onclick="bulkActivate()" class="btn-sm btn-success">
                <i class="fas fa-check-circle"></i> Activate
            </button>
            <button onclick="bulkUnsubscribe()" class="btn-sm btn-warning">
                <i class="fas fa-user-times"></i> Unsubscribe
            </button>
            <button onclick="bulkDelete()" class="btn-sm btn-danger">
                <i class="fas fa-trash"></i> Delete
            </button>
            <button onclick="clearSelection()" class="btn-sm btn-secondary">
                <i class="fas fa-times"></i> Clear
            </button>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filters-bar">
    <form method="GET" action="" class="filters-form">
        <div class="filter-group">
            <label>Status:</label>
            <select name="status" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="active" <?php echo $statusFilter === 'active' ? 'selected' : ''; ?>>Active (<?php echo $counts['active'] ?? 0; ?>)</option>
                <option value="unsubscribed" <?php echo $statusFilter === 'unsubscribed' ? 'selected' : ''; ?>>Unsubscribed (<?php echo $counts['unsubscribed'] ?? 0; ?>)</option>
                <option value="bounced" <?php echo $statusFilter === 'bounced' ? 'selected' : ''; ?>>Bounced (<?php echo $counts['bounced'] ?? 0; ?>)</option>
            </select>
        </div>
        
        <div class="filter-group search-group">
            <input 
                type="text" 
                name="search" 
                placeholder="Search email..." 
                value="<?php echo htmlspecialchars($searchQuery); ?>"
            >
            <button type="submit" class="btn-search">
                <i class="fas fa-search"></i>
            </button>
        </div>
        
        <?php if ($statusFilter || $searchQuery): ?>
        <a href="subscribers.php" class="btn-clear">
            <i class="fas fa-times"></i> Clear Filters
        </a>
        <?php endif; ?>
    </form>
</div>

<!-- Statistics Cards -->
<div class="stats-row-mini">
    <div class="stat-mini">
        <i class="fas fa-users"></i>
        <div>
            <strong><?php echo $counts['active'] ?? 0; ?></strong>
            <span>Active</span>
        </div>
    </div>
    <div class="stat-mini">
        <i class="fas fa-user-times"></i>
        <div>
            <strong><?php echo $counts['unsubscribed'] ?? 0; ?></strong>
            <span>Unsubscribed</span>
        </div>
    </div>
    <div class="stat-mini">
        <i class="fas fa-exclamation-triangle"></i>
        <div>
            <strong><?php echo $counts['bounced'] ?? 0; ?></strong>
            <span>Bounced</span>
        </div>
    </div>
    <div class="stat-mini">
        <i class="fas fa-envelope"></i>
        <div>
            <strong><?php echo array_sum($counts); ?></strong>
            <span>Total</span>
        </div>
    </div>
</div>

<!-- Subscribers Table -->
<div class="data-card">
    <?php if (empty($subscribers)): ?>
        <p class="no-data">No newsletter subscribers found</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)"></th>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Subscribed Date</th>
                        <th>Unsubscribed Date</th>
                        <th>IP Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subscribers as $sub): ?>
                    <tr>
                        <td><input type="checkbox" class="row-checkbox" value="<?php echo $sub['id']; ?>" onchange="updateBulkActions()"></td>
                        <td><?php echo $sub['id']; ?></td>
                        <td><a href="mailto:<?php echo htmlspecialchars($sub['email']); ?>"><?php echo htmlspecialchars($sub['email']); ?></a></td>
                        <td>
                            <span class="badge badge-<?php echo $sub['status']; ?>"><?php echo $sub['status']; ?></span>
                        </td>
                        <td><?php echo date('M d, Y H:i', strtotime($sub['created_at'])); ?></td>
                        <td><?php echo $sub['unsubscribed_at'] ? date('M d, Y H:i', strtotime($sub['unsubscribed_at'])) : '-'; ?></td>
                        <td><?php echo htmlspecialchars($sub['ip_address'] ?? 'N/A'); ?></td>
                        <td class="action-buttons">
                            <button onclick="updateStatus(<?php echo $sub['id']; ?>, '<?php echo $sub['status']; ?>')" class="btn-sm btn-info">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteSubscriber(<?php echo $sub['id']; ?>)" class="btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>&status=<?php echo $statusFilter; ?>&search=<?php echo urlencode($searchQuery); ?>" class="page-link">
                    <i class="fas fa-chevron-left"></i> Previous
                </a>
            <?php endif; ?>
            
            <span class="page-info">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
            
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>&status=<?php echo $statusFilter; ?>&search=<?php echo urlencode($searchQuery); ?>" class="page-link">
                    Next <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Hidden forms for actions -->
<form id="statusForm" method="POST" action="" style="display:none;">
    <input type="hidden" name="subscriber_id" id="statusSubscriberId">
    <input type="hidden" name="status" id="statusValue">
    <input type="hidden" name="update_status" value="1">
</form>

<form id="deleteForm" method="POST" action="" style="display:none;">
    <input type="hidden" name="subscriber_id" id="deleteSubscriberId">
    <input type="hidden" name="delete_subscriber" value="1">
</form>

<script>
function updateStatus(id, currentStatus) {
    const statuses = ['active', 'unsubscribed', 'bounced'];
    const status = prompt(`Update status for subscriber #${id}\nCurrent: ${currentStatus}\nEnter new status (active/unsubscribed/bounced):`, currentStatus);
    
    if (status && statuses.includes(status.toLowerCase())) {
        document.getElementById('statusSubscriberId').value = id;
        document.getElementById('statusValue').value = status.toLowerCase();
        document.getElementById('statusForm').submit();
    }
}

function deleteSubscriber(id) {
    if (confirm(`Are you sure you want to delete subscriber #${id}? This action cannot be undone.`)) {
        document.getElementById('deleteSubscriberId').value = id;
        document.getElementById('deleteForm').submit();
    }
}

function exportData(type) {
    window.location.href = `ajax/export.php?type=${type}`;
}

// Bulk Operations
let selectedIds = [];

function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
    updateBulkActions();
}

function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.row-checkbox:checked');
    selectedIds = Array.from(checkboxes).map(cb => cb.value);
    
    const bulkBar = document.getElementById('bulkActionsBar');
    const bulkBtn = document.getElementById('bulkActionsBtn');
    const countSpan = document.getElementById('selectedCount');
    
    if (selectedIds.length > 0) {
        bulkBar.style.display = 'block';
        bulkBtn.style.display = 'inline-block';
        countSpan.textContent = `${selectedIds.length} selected`;
    } else {
        bulkBar.style.display = 'none';
        bulkBtn.style.display = 'none';
    }
}

function clearSelection() {
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('selectAll').checked = false;
    updateBulkActions();
}

async function performBulkAction(action) {
    if (selectedIds.length === 0) {
        alert('Please select at least one subscriber');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', action);
    formData.append('type', 'subscribers');
    selectedIds.forEach(id => formData.append('ids[]', id));
    
    try {
        const response = await fetch('ajax/bulk_operations.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(result.message);
            location.reload();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        alert('An error occurred during bulk operation');
    }
}

function bulkActivate() {
    if (confirm(`Activate ${selectedIds.length} subscriber(s)?`)) {
        performBulkAction('activate');
    }
}

function bulkUnsubscribe() {
    if (confirm(`Unsubscribe ${selectedIds.length} subscriber(s)?`)) {
        performBulkAction('unsubscribe');
    }
}

function bulkDelete() {
    if (confirm(`Delete ${selectedIds.length} subscriber(s)? This cannot be undone!`)) {
        performBulkAction('delete');
    }
}
</script>

<style>
.bulk-actions-bar {
    background: #E3F2FD;
    border: 1px solid #2196F3;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1.5rem;
}

.bulk-actions-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.bulk-buttons {
    display: flex;
    gap: 0.5rem;
}

#selectedCount {
    font-weight: 600;
    color: #1976D2;
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
