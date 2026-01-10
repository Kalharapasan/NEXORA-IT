<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$action = $_POST['action'] ?? '';
$type = $_POST['type'] ?? ''; // 'contacts' or 'subscribers'
$ids = $_POST['ids'] ?? [];

if (empty($action) || empty($type) || empty($ids)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

try {
    $db = getDBConnection();
    $admin = getCurrentAdmin();
    $idsPlaceholder = implode(',', array_fill(0, count($ids), '?'));
    
    if ($type === 'contacts') {
        switch ($action) {
            case 'delete':
                $stmt = $db->prepare("DELETE FROM contact_messages WHERE id IN ($idsPlaceholder)");
                $stmt->execute($ids);
                $affected = $stmt->rowCount();
                logAdminActivity($admin['id'], 'bulk_delete_contacts', "Deleted $affected contact messages");
                echo json_encode(['success' => true, 'message' => "$affected contact(s) deleted successfully"]);
                break;
                
            case 'mark_read':
                $stmt = $db->prepare("UPDATE contact_messages SET status = 'read', updated_at = NOW() WHERE id IN ($idsPlaceholder)");
                $stmt->execute($ids);
                $affected = $stmt->rowCount();
                logAdminActivity($admin['id'], 'bulk_mark_read', "Marked $affected messages as read");
                echo json_encode(['success' => true, 'message' => "$affected contact(s) marked as read"]);
                break;
                
            case 'archive':
                $stmt = $db->prepare("UPDATE contact_messages SET status = 'archived', updated_at = NOW() WHERE id IN ($idsPlaceholder)");
                $stmt->execute($ids);
                $affected = $stmt->rowCount();
                logAdminActivity($admin['id'], 'bulk_archive', "Archived $affected messages");
                echo json_encode(['success' => true, 'message' => "$affected contact(s) archived"]);
                break;
                
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
    } elseif ($type === 'subscribers') {
        switch ($action) {
            case 'delete':
                $stmt = $db->prepare("DELETE FROM newsletter_subscribers WHERE id IN ($idsPlaceholder)");
                $stmt->execute($ids);
                $affected = $stmt->rowCount();
                logAdminActivity($admin['id'], 'bulk_delete_subscribers', "Deleted $affected subscribers");
                echo json_encode(['success' => true, 'message' => "$affected subscriber(s) deleted successfully"]);
                break;
                
            case 'activate':
                $stmt = $db->prepare("UPDATE newsletter_subscribers SET status = 'active', updated_at = NOW() WHERE id IN ($idsPlaceholder)");
                $stmt->execute($ids);
                $affected = $stmt->rowCount();
                logAdminActivity($admin['id'], 'bulk_activate_subscribers', "Activated $affected subscribers");
                echo json_encode(['success' => true, 'message' => "$affected subscriber(s) activated"]);
                break;
                
            case 'unsubscribe':
                $stmt = $db->prepare("UPDATE newsletter_subscribers SET status = 'unsubscribed', unsubscribed_at = NOW(), updated_at = NOW() WHERE id IN ($idsPlaceholder)");
                $stmt->execute($ids);
                $affected = $stmt->rowCount();
                logAdminActivity($admin['id'], 'bulk_unsubscribe', "Unsubscribed $affected subscribers");
                echo json_encode(['success' => true, 'message' => "$affected subscriber(s) unsubscribed"]);
                break;
                
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid type']);
    }
    
} catch (Exception $e) {
    error_log("Bulk operations error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred during bulk operation']);
}
