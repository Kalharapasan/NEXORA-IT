<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

header('Content-Type: application/json');

$messageId = intval($_GET['id'] ?? 0);

if ($messageId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid message ID']);
    exit();
}

try {
    $db = getDBConnection();
    
    // Get message details
    $stmt = $db->prepare("SELECT * FROM contact_messages WHERE id = ?");
    $stmt->execute([$messageId]);
    $message = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$message) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Message not found']);
        exit();
    }
    
    // Mark as read if it's new
    if ($message['status'] === 'new') {
        $updateStmt = $db->prepare("UPDATE contact_messages SET status = 'read', updated_at = NOW() WHERE id = ?");
        $updateStmt->execute([$messageId]);
        
        $admin = getCurrentAdmin();
        logAdminActivity($admin['id'], 'view_contact', "Viewed message #$messageId");
    }
    
    // Format dates
    $message['created_at'] = date('F j, Y g:i A', strtotime($message['created_at']));
    if ($message['updated_at']) {
        $message['updated_at'] = date('F j, Y g:i A', strtotime($message['updated_at']));
    }
    
    // Sanitize for display
    $message['name'] = htmlspecialchars($message['name']);
    $message['email'] = htmlspecialchars($message['email']);
    $message['phone'] = htmlspecialchars($message['phone'] ?? '');
    $message['subject'] = htmlspecialchars($message['subject']);
    $message['message'] = nl2br(htmlspecialchars($message['message']));
    
    echo json_encode([
        'success' => true,
        'message' => $message
    ]);
    
} catch (Exception $e) {
    error_log("Get message error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred while retrieving the message']);
}
