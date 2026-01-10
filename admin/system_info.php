<?php
$pageTitle = 'System Info';
require_once __DIR__ . '/includes/header.php';

// Get system information
try {
    $db = getDBConnection();
    
    // Database statistics
    $dbStats = [
        'contacts' => $db->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn(),
        'contacts_new' => $db->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'")->fetchColumn(),
        'contacts_today' => $db->query("SELECT COUNT(*) FROM contact_messages WHERE DATE(created_at) = CURDATE()")->fetchColumn(),
        'contacts_week' => $db->query("SELECT COUNT(*) FROM contact_messages WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn(),
        'subscribers' => $db->query("SELECT COUNT(*) FROM newsletter_subscribers")->fetchColumn(),
        'subscribers_active' => $db->query("SELECT COUNT(*) FROM newsletter_subscribers WHERE status = 'active'")->fetchColumn(),
        'subscribers_today' => $db->query("SELECT COUNT(*) FROM newsletter_subscribers WHERE DATE(created_at) = CURDATE()")->fetchColumn(),
        'subscribers_week' => $db->query("SELECT COUNT(*) FROM newsletter_subscribers WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn(),
        'team_members' => $db->query("SELECT COUNT(*) FROM team_members")->fetchColumn(),
        'team_active' => $db->query("SELECT COUNT(*) FROM team_members WHERE status = 'active'")->fetchColumn(),
        'admin_users' => $db->query("SELECT COUNT(*) FROM admin_users")->fetchColumn(),
        'admin_active' => $db->query("SELECT COUNT(*) FROM admin_users WHERE is_active = 1")->fetchColumn(),
        'login_attempts' => $db->query("SELECT COUNT(*) FROM login_attempts WHERE attempted_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)")->fetchColumn(),
        'failed_logins' => $db->query("SELECT COUNT(*) FROM login_attempts WHERE success = 0 AND attempted_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)")->fetchColumn(),
        'activity_logs' => $db->query("SELECT COUNT(*) FROM admin_activity_logs")->fetchColumn(),
        'activity_today' => $db->query("SELECT COUNT(*) FROM admin_activity_logs WHERE DATE(created_at) = CURDATE()")->fetchColumn(),
    ];
    
    // Database size
    $dbName = DB_NAME;
    $dbSizeQuery = $db->query("SELECT SUM(data_length + index_length) / 1024 / 1024 AS size_mb 
                               FROM information_schema.TABLES 
                               WHERE table_schema = '$dbName'");
    $dbSize = round($dbSizeQuery->fetchColumn(), 2);
    
    // Recent activity
    $recentActivity = $db->query("SELECT al.*, au.username 
                                   FROM admin_activity_logs al
                                   LEFT JOIN admin_users au ON al.admin_id = au.id
                                   ORDER BY al.created_at DESC 
                                   LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("System info error: " . $e->getMessage());
    $dbStats = [];
    $dbSize = 0;
    $recentActivity = [];
}

// PHP Information
$phpInfo = [
    'version' => phpversion(),
    'memory_limit' => ini_get('memory_limit'),
    'max_execution_time' => ini_get('max_execution_time'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'timezone' => date_default_timezone_get(),
];

// Server Information
$serverInfo = [
    'software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'protocol' => $_SERVER['SERVER_PROTOCOL'] ?? 'Unknown',
    'os' => PHP_OS,
    'architecture' => php_uname('m'),
];
?>

<div class="page-header">
    <h2><i class="fas fa-info-circle"></i> System Information</h2>
</div>

<!-- Database Statistics -->
<div class="stats-section">
    <h3><i class="fas fa-database"></i> Database Statistics</h3>
    
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-icon"><i class="fas fa-envelope"></i></div>
            <div class="stat-content">
                <h3><?php echo number_format($dbStats['contacts']); ?></h3>
                <p>Total Contacts</p>
                <small><?php echo $dbStats['contacts_new']; ?> new | <?php echo $dbStats['contacts_today']; ?> today</small>
            </div>
        </div>
        
        <div class="stat-card green">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-content">
                <h3><?php echo number_format($dbStats['subscribers']); ?></h3>
                <p>Total Subscribers</p>
                <small><?php echo $dbStats['subscribers_active']; ?> active | <?php echo $dbStats['subscribers_today']; ?> today</small>
            </div>
        </div>
        
        <div class="stat-card orange">
            <div class="stat-icon"><i class="fas fa-user-friends"></i></div>
            <div class="stat-content">
                <h3><?php echo number_format($dbStats['team_members']); ?></h3>
                <p>Team Members</p>
                <small><?php echo $dbStats['team_active']; ?> active</small>
            </div>
        </div>
        
        <div class="stat-card purple">
            <div class="stat-icon"><i class="fas fa-user-shield"></i></div>
            <div class="stat-content">
                <h3><?php echo number_format($dbStats['admin_users']); ?></h3>
                <p>Admin Users</p>
                <small><?php echo $dbStats['admin_active']; ?> active</small>
            </div>
        </div>
        
        <div class="stat-card red">
            <div class="stat-icon"><i class="fas fa-shield-alt"></i></div>
            <div class="stat-content">
                <h3><?php echo number_format($dbStats['login_attempts']); ?></h3>
                <p>Login Attempts (24h)</p>
                <small><?php echo $dbStats['failed_logins']; ?> failed</small>
            </div>
        </div>
        
        <div class="stat-card teal">
            <div class="stat-icon"><i class="fas fa-history"></i></div>
            <div class="stat-content">
                <h3><?php echo number_format($dbStats['activity_logs']); ?></h3>
                <p>Activity Logs</p>
                <small><?php echo $dbStats['activity_today']; ?> today</small>
            </div>
        </div>
    </div>
</div>

<!-- System Information Grid -->
<div class="info-grid">
    <!-- Database Info -->
    <div class="info-card">
        <h3><i class="fas fa-database"></i> Database</h3>
        <table class="info-table">
            <tr>
                <td><strong>Database Name:</strong></td>
                <td><?php echo DB_NAME; ?></td>
            </tr>
            <tr>
                <td><strong>Database Host:</strong></td>
                <td><?php echo DB_HOST; ?></td>
            </tr>
            <tr>
                <td><strong>Database Size:</strong></td>
                <td><?php echo $dbSize; ?> MB</td>
            </tr>
            <tr>
                <td><strong>Charset:</strong></td>
                <td><?php echo DB_CHARSET; ?></td>
            </tr>
        </table>
    </div>
    
    <!-- PHP Info -->
    <div class="info-card">
        <h3><i class="fab fa-php"></i> PHP Configuration</h3>
        <table class="info-table">
            <tr>
                <td><strong>PHP Version:</strong></td>
                <td><?php echo $phpInfo['version']; ?></td>
            </tr>
            <tr>
                <td><strong>Memory Limit:</strong></td>
                <td><?php echo $phpInfo['memory_limit']; ?></td>
            </tr>
            <tr>
                <td><strong>Max Execution Time:</strong></td>
                <td><?php echo $phpInfo['max_execution_time']; ?>s</td>
            </tr>
            <tr>
                <td><strong>Upload Max Size:</strong></td>
                <td><?php echo $phpInfo['upload_max_filesize']; ?></td>
            </tr>
            <tr>
                <td><strong>Post Max Size:</strong></td>
                <td><?php echo $phpInfo['post_max_size']; ?></td>
            </tr>
            <tr>
                <td><strong>Timezone:</strong></td>
                <td><?php echo $phpInfo['timezone']; ?></td>
            </tr>
        </table>
    </div>
    
    <!-- Server Info -->
    <div class="info-card">
        <h3><i class="fas fa-server"></i> Server Information</h3>
        <table class="info-table">
            <tr>
                <td><strong>Server Software:</strong></td>
                <td><?php echo $serverInfo['software']; ?></td>
            </tr>
            <tr>
                <td><strong>Protocol:</strong></td>
                <td><?php echo $serverInfo['protocol']; ?></td>
            </tr>
            <tr>
                <td><strong>Operating System:</strong></td>
                <td><?php echo $serverInfo['os']; ?></td>
            </tr>
            <tr>
                <td><strong>Architecture:</strong></td>
                <td><?php echo $serverInfo['architecture']; ?></td>
            </tr>
            <tr>
                <td><strong>Document Root:</strong></td>
                <td><?php echo $_SERVER['DOCUMENT_ROOT']; ?></td>
            </tr>
        </table>
    </div>
    
    <!-- Application Info -->
    <div class="info-card">
        <h3><i class="fas fa-cog"></i> Application</h3>
        <table class="info-table">
            <tr>
                <td><strong>Company:</strong></td>
                <td><?php echo COMPANY_NAME; ?></td>
            </tr>
            <tr>
                <td><strong>Site URL:</strong></td>
                <td><?php echo SITE_URL; ?></td>
            </tr>
            <tr>
                <td><strong>Contact Email:</strong></td>
                <td><?php echo CONTACT_EMAIL; ?></td>
            </tr>
            <tr>
                <td><strong>Timezone:</strong></td>
                <td><?php echo TIMEZONE; ?></td>
            </tr>
            <tr>
                <td><strong>Current Time:</strong></td>
                <td><?php echo date('Y-m-d H:i:s'); ?></td>
            </tr>
        </table>
    </div>
</div>

<!-- Recent Activity -->
<div class="data-card mt-4">
    <h3><i class="fas fa-clock"></i> Recent Activity</h3>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Admin</th>
                    <th>Action</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentActivity as $activity): ?>
                <tr>
                    <td><?php echo date('M d, H:i', strtotime($activity['created_at'])); ?></td>
                    <td><?php echo htmlspecialchars($activity['username'] ?? 'Unknown'); ?></td>
                    <td><span class="badge badge-info"><?php echo htmlspecialchars($activity['action']); ?></span></td>
                    <td><?php echo htmlspecialchars($activity['description']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.stats-section {
    margin-bottom: 2rem;
}

.stats-section h3 {
    margin-bottom: 1.5rem;
    color: #1F2937;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card small {
    display: block;
    margin-top: 0.5rem;
    font-size: 0.875rem;
    opacity: 0.8;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.info-card {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.info-card h3 {
    margin-bottom: 1rem;
    color: #1F2937;
    font-size: 1.125rem;
}

.info-table {
    width: 100%;
    font-size: 0.875rem;
}

.info-table tr {
    border-bottom: 1px solid #E5E7EB;
}

.info-table tr:last-child {
    border-bottom: none;
}

.info-table td {
    padding: 0.75rem 0;
}

.info-table td:first-child {
    width: 50%;
}

.mt-4 {
    margin-top: 2rem;
}

@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
