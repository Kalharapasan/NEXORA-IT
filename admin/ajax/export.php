<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$type = $_GET['type'] ?? 'contacts';
$admin = getCurrentAdmin();

try {
    $db = getDBConnection();
    
    if ($type === 'contacts') {
        // Export contact messages
        $query = "SELECT id, name, email, phone, subject, LEFT(message, 200) as message, status, ip_address, created_at 
                  FROM contact_messages 
                  ORDER BY created_at DESC";
        $stmt = $db->query($query);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $filename = "contact_messages_" . date('Y-m-d_H-i-s') . ".csv";
        $headers = ['ID', 'Name', 'Email', 'Phone', 'Subject', 'Message Preview', 'Status', 'IP Address', 'Date'];
        
    } elseif ($type === 'subscribers') {
        // Export newsletter subscribers
        $query = "SELECT id, email, status, ip_address, created_at, unsubscribed_at 
                  FROM newsletter_subscribers 
                  ORDER BY created_at DESC";
        $stmt = $db->query($query);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $filename = "newsletter_subscribers_" . date('Y-m-d_H-i-s') . ".csv";
        $headers = ['ID', 'Email', 'Status', 'IP Address', 'Subscribed Date', 'Unsubscribed Date'];
        
    } else {
        http_response_code(400);
        die('Invalid export type. Allowed types: contacts, subscribers');
    }
    
    // Log the export activity
    logAdminActivity($admin['id'], 'export_data', "Exported $type data");
    
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    
    // Output CSV
    $output = fopen('php://output', 'w');
    
    // Add BOM for Excel UTF-8 support
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Write headers
    fputcsv($output, $headers);
    
    // Write data
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit();
    
} catch (Exception $e) {
    error_log("Export error: " . $e->getMessage());
    http_response_code(500);
    die('An error occurred during export. Please try again or contact support.');
}
