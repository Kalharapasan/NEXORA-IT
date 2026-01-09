<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';

try {
    $db = getDBConnection();
    
    // Get dashboard statistics
    $statsQuery = "SELECT * FROM dashboard_stats";
    $stats = $db->query($statsQuery)->fetch(PDO::FETCH_ASSOC);
    
    // Get recent messages
    $recentMessagesQuery = "SELECT id, name, email, subject, status, created_at 
                           FROM contact_messages 
                           ORDER BY created_at DESC 
                           LIMIT 5";
    $recentMessages = $db->query($recentMessagesQuery)->fetchAll(PDO::FETCH_ASSOC);
    
    // Get recent subscribers
    $recentSubsQuery = "SELECT id, email, status, created_at 
                       FROM newsletter_subscribers 
                       ORDER BY created_at DESC 
                       LIMIT 5";
    $recentSubscribers = $db->query($recentSubsQuery)->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
    $stats = [];
    $recentMessages = [];
    $recentSubscribers = [];
}
?>

<div class="dashboard-grid">
    <!-- Stats Cards -->
    <div class="stats-row">
        <div class="stat-card blue">
            <div class="stat-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $stats['new_messages'] ?? 0; ?></h3>
                <p>New Messages</p>
            </div>
        </div>
        
        <div class="stat-card green">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $stats['active_subscribers'] ?? 0; ?></h3>
                <p>Active Subscribers</p>
            </div>
        </div>
        
        <div class="stat-card orange">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $stats['today_messages'] ?? 0; ?></h3>
                <p>Today's Messages</p>
            </div>
        </div>
        
        <div class="stat-card purple">
            <div class="stat-icon">
                <i class="fas fa-mail-bulk"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $stats['today_subscribers'] ?? 0; ?></h3>
                <p>Today's Subscribers</p>
            </div>
        </div>
    </div>
    
    <!-- Recent Messages -->
    <div class="dashboard-card">
        <div class="card-header">
            <h2><i class="fas fa-envelope"></i> Recent Contact Messages</h2>
            <a href="contacts.php" class="btn-link">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="card-body">
            <?php if (empty($recentMessages)): ?>
                <p class="no-data">No messages yet</p>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentMessages as $msg): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($msg['name']); ?></td>
                            <td><?php echo htmlspecialchars($msg['email']); ?></td>
                            <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                            <td><span class="badge badge-<?php echo $msg['status']; ?>"><?php echo $msg['status']; ?></span></td>
                            <td><?php echo date('M d, Y', strtotime($msg['created_at'])); ?></td>
                            <td>
                                <a href="contacts.php?id=<?php echo $msg['id']; ?>" class="btn-sm btn-primary">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Recent Subscribers -->
    <div class="dashboard-card">
        <div class="card-header">
            <h2><i class="fas fa-users"></i> Recent Newsletter Subscribers</h2>
            <a href="subscribers.php" class="btn-link">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="card-body">
            <?php if (empty($recentSubscribers)): ?>
                <p class="no-data">No subscribers yet</p>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Subscribed Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentSubscribers as $sub): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($sub['email']); ?></td>
                            <td><span class="badge badge-<?php echo $sub['status']; ?>"><?php echo $sub['status']; ?></span></td>
                            <td><?php echo date('M d, Y', strtotime($sub['created_at'])); ?></td>
                            <td>
                                <a href="subscribers.php?id=<?php echo $sub['id']; ?>" class="btn-sm btn-primary">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="dashboard-card">
        <div class="card-header">
            <h2><i class="fas fa-chart-bar"></i> Quick Statistics</h2>
        </div>
        <div class="card-body">
            <div class="quick-stats">
                <div class="quick-stat-item">
                    <span class="label">Total Messages:</span>
                    <span class="value"><?php echo $stats['total_messages'] ?? 0; ?></span>
                </div>
                <div class="quick-stat-item">
                    <span class="label">Total Subscribers:</span>
                    <span class="value"><?php echo $stats['total_subscribers'] ?? 0; ?></span>
                </div>
                <div class="quick-stat-item">
                    <span class="label">New Messages:</span>
                    <span class="value"><?php echo $stats['new_messages'] ?? 0; ?></span>
                </div>
                <div class="quick-stat-item">
                    <span class="label">Active Subscribers:</span>
                    <span class="value"><?php echo $stats['active_subscribers'] ?? 0; ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
