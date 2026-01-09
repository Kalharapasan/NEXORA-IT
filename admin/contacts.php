<?php
$pageTitle = 'Contact Messages';
require_once __DIR__ . '/includes/header.php';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $messageId = $_POST['message_id'] ?? 0;
    $newStatus = $_POST['status'] ?? 'new';
    
    try {
        $db = getDBConnection();
        $stmt = $db->prepare("UPDATE contact_messages SET status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$newStatus, $messageId]);
        
        logAdminActivity($currentAdmin['id'], 'update_contact_status', "Updated message #$messageId status to $newStatus");
        setFlashMessage('Message status updated successfully');
        header('Location: contacts.php');
        exit();
    } catch (Exception $e) {
        $error = "Error updating status: " . $e->getMessage();
    }
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_message'])) {
    $messageId = $_POST['message_id'] ?? 0;
    
    try {
        $db = getDBConnection();
        $stmt = $db->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->execute([$messageId]);
        
        logAdminActivity($currentAdmin['id'], 'delete_contact', "Deleted message #$messageId");
        setFlashMessage('Message deleted successfully');
        header('Location: contacts.php');
        exit();
    } catch (Exception $e) {
        $error = "Error deleting message: " . $e->getMessage();
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
        $whereConditions[] = "(name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?)";
        $searchTerm = "%$searchQuery%";
        $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    }
    
    $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";
    
    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM contact_messages $whereClause";
    $countStmt = $db->prepare($countQuery);
    $countStmt->execute($params);
    $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalRecords / $perPage);
    
    // Get messages
    $query = "SELECT * FROM contact_messages $whereClause ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get status counts
    $countsQuery = "SELECT status, COUNT(*) as count FROM contact_messages GROUP BY status";
    $counts = $db->query($countsQuery)->fetchAll(PDO::FETCH_KEY_PAIR);
    
} catch (Exception $e) {
    error_log("Contacts page error: " . $e->getMessage());
    $messages = [];
    $totalRecords = 0;
    $totalPages = 0;
    $counts = [];
}
?>

<div class="page-header">
    <h2><i class="fas fa-envelope"></i> Contact Messages</h2>
    <div class="page-actions">
        <button onclick="exportData('contacts')" class="btn-secondary">
            <i class="fas fa-download"></i> Export CSV
        </button>
    </div>
</div>

<!-- Filters -->
<div class="filters-bar">
    <form method="GET" action="" class="filters-form">
        <div class="filter-group">
            <label>Status:</label>
            <select name="status" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="new" <?php echo $statusFilter === 'new' ? 'selected' : ''; ?>>New (<?php echo $counts['new'] ?? 0; ?>)</option>
                <option value="read" <?php echo $statusFilter === 'read' ? 'selected' : ''; ?>>Read (<?php echo $counts['read'] ?? 0; ?>)</option>
                <option value="replied" <?php echo $statusFilter === 'replied' ? 'selected' : ''; ?>>Replied (<?php echo $counts['replied'] ?? 0; ?>)</option>
                <option value="archived" <?php echo $statusFilter === 'archived' ? 'selected' : ''; ?>>Archived (<?php echo $counts['archived'] ?? 0; ?>)</option>
            </select>
        </div>
        
        <div class="filter-group search-group">
            <input 
                type="text" 
                name="search" 
                placeholder="Search name, email, subject..." 
                value="<?php echo htmlspecialchars($searchQuery); ?>"
            >
            <button type="submit" class="btn-search">
                <i class="fas fa-search"></i>
            </button>
        </div>
        
        <?php if ($statusFilter || $searchQuery): ?>
        <a href="contacts.php" class="btn-clear">
            <i class="fas fa-times"></i> Clear Filters
        </a>
        <?php endif; ?>
    </form>
</div>

<!-- Messages Table -->
<div class="data-card">
    <?php if (empty($messages)): ?>
        <p class="no-data">No contact messages found</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Subject</th>
                        <th>Message Preview</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $msg): ?>
                    <tr>
                        <td><?php echo $msg['id']; ?></td>
                        <td><?php echo htmlspecialchars($msg['name']); ?></td>
                        <td><a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>"><?php echo htmlspecialchars($msg['email']); ?></a></td>
                        <td><?php echo $msg['phone'] ? htmlspecialchars($msg['phone']) : '-'; ?></td>
                        <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                        <td class="message-preview"><?php echo htmlspecialchars(substr($msg['message'], 0, 50)) . '...'; ?></td>
                        <td>
                            <span class="badge badge-<?php echo $msg['status']; ?>"><?php echo $msg['status']; ?></span>
                        </td>
                        <td><?php echo date('M d, Y H:i', strtotime($msg['created_at'])); ?></td>
                        <td class="action-buttons">
                            <button onclick="viewMessage(<?php echo $msg['id']; ?>)" class="btn-sm btn-primary">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="updateStatus(<?php echo $msg['id']; ?>, '<?php echo $msg['status']; ?>')" class="btn-sm btn-info">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteMessage(<?php echo $msg['id']; ?>)" class="btn-sm btn-danger">
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
    <input type="hidden" name="message_id" id="statusMessageId">
    <input type="hidden" name="status" id="statusValue">
    <input type="hidden" name="update_status" value="1">
</form>

<form id="deleteForm" method="POST" action="" style="display:none;">
    <input type="hidden" name="message_id" id="deleteMessageId">
    <input type="hidden" name="delete_message" value="1">
</form>

<!-- View Message Modal -->
<div id="messageModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal()">&times;</span>
        <div id="messageDetails"></div>
    </div>
</div>

<script>
function viewMessage(id) {
    fetch(`ajax/get_message.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('messageDetails').innerHTML = `
                    <h2>Message Details</h2>
                    <div class="message-detail">
                        <p><strong>From:</strong> ${data.message.name}</p>
                        <p><strong>Email:</strong> <a href="mailto:${data.message.email}">${data.message.email}</a></p>
                        <p><strong>Phone:</strong> ${data.message.phone || 'Not provided'}</p>
                        <p><strong>Subject:</strong> ${data.message.subject}</p>
                        <p><strong>Status:</strong> <span class="badge badge-${data.message.status}">${data.message.status}</span></p>
                        <p><strong>Date:</strong> ${data.message.created_at}</p>
                        <p><strong>IP Address:</strong> ${data.message.ip_address || 'N/A'}</p>
                        <hr>
                        <h3>Message:</h3>
                        <p class="message-body">${data.message.message}</p>
                    </div>
                `;
                document.getElementById('messageModal').style.display = 'flex';
            }
        });
}

function updateStatus(id, currentStatus) {
    const statuses = ['new', 'read', 'replied', 'archived'];
    const status = prompt(`Update status for message #${id}\nCurrent: ${currentStatus}\nEnter new status (new/read/replied/archived):`, currentStatus);
    
    if (status && statuses.includes(status.toLowerCase())) {
        document.getElementById('statusMessageId').value = id;
        document.getElementById('statusValue').value = status.toLowerCase();
        document.getElementById('statusForm').submit();
    }
}

function deleteMessage(id) {
    if (confirm(`Are you sure you want to delete message #${id}? This action cannot be undone.`)) {
        document.getElementById('deleteMessageId').value = id;
        document.getElementById('deleteForm').submit();
    }
}

function closeModal() {
    document.getElementById('messageModal').style.display = 'none';
}

function exportData(type) {
    window.location.href = `ajax/export.php?type=${type}`;
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
