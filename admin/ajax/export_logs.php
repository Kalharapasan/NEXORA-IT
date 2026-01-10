<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=activity_logs_' . date('Y-m-d_H-i-s') . '.csv');

try {
    $db = getDBConnection();
    $admin = getCurrentAdmin();
    
    // Build query with filters
    $whereConditions = [];
    $params = [];
    
    if (!empty($_GET['admin'])) {
        $whereConditions[] = "al.admin_id = ?";
        $params[] = $_GET['admin'];
    }
    
    if (!empty($_GET['action'])) {
        $whereConditions[] = "al.action = ?";
        $params[] = $_GET['action'];
    }
    
    if (!empty($_GET['date'])) {
        $whereConditions[] = "DATE(al.created_at) = ?";
        $params[] = $_GET['date'];
    }
    
    $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";
    
    $query = "SELECT al.id, al.created_at, au.username, au.full_name, al.action, al.description, al.ip_address 
              FROM admin_activity_logs al
              LEFT JOIN admin_users au ON al.admin_id = au.id
              $whereClause 
              ORDER BY al.created_at DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Log the export
    logAdminActivity($admin['id'], 'export_activity_logs', 'Exported activity logs');
    
    // Output CSV
    $output = fopen('php://output', 'w');
    
    // Add BOM for Excel UTF-8 support
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Write headers
    fputcsv($output, ['ID', 'Date & Time', 'Username', 'Full Name', 'Action', 'Description', 'IP Address']);
    
    // Write data
    foreach ($logs as $log) {
        fputcsv($output, [
            $log['id'],
            $log['created_at'],
            $log['username'] ?? 'Unknown',
            $log['full_name'] ?? '',
            $log['action'],
            $log['description'],
            $log['ip_address']
        ]);
    }
    
    fclose($output);
    exit();
    
} catch (Exception $e) {
    error_log("Export logs error: " . $e->getMessage());
    http_response_code(500);
    die('Error exporting logs');
}
