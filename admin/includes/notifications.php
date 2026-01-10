<?php
/**
 * Notification Helper Functions
 * 
 * This file provides functions to create and manage admin notifications
 */

if (!function_exists('createNotification')) {
    /**
     * Create a new notification for an admin user
     * 
     * @param int|null $adminId Admin user ID (null for all admins)
     * @param string $title Notification title
     * @param string $message Notification message
     * @param string $type Notification type: info, success, warning, error
     * @param string|null $actionUrl Optional URL for action
     * @return bool Success status
     */
    function createNotification($adminId, $title, $message, $type = 'info', $actionUrl = null) {
        try {
            $db = getDBConnection();
            
            if ($adminId === null) {
                // Send to all admins
                $stmt = $db->query("SELECT id FROM admin_users WHERE is_active = 1");
                $adminIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                foreach ($adminIds as $id) {
                    $stmt = $db->prepare("INSERT INTO admin_notifications (admin_id, title, message, notification_type, action_url) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$id, $title, $message, $type, $actionUrl]);
                }
            } else {
                // Send to specific admin
                $stmt = $db->prepare("INSERT INTO admin_notifications (admin_id, title, message, notification_type, action_url) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$adminId, $title, $message, $type, $actionUrl]);
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Notification error: " . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('notifyNewContact')) {
    /**
     * Create notification for new contact message
     * 
     * @param array $contactData Contact message data
     * @return bool Success status
     */
    function notifyNewContact($contactData) {
        $title = "New Contact Message";
        $message = "From: {$contactData['name']} ({$contactData['email']})\nSubject: {$contactData['subject']}";
        $actionUrl = "contacts.php?id=" . $contactData['id'];
        
        return createNotification(null, $title, $message, 'info', $actionUrl);
    }
}

if (!function_exists('notifyNewSubscriber')) {
    /**
     * Create notification for new newsletter subscriber
     * 
     * @param array $subscriberData Subscriber data
     * @return bool Success status
     */
    function notifyNewSubscriber($subscriberData) {
        $title = "New Newsletter Subscriber";
        $message = "Email: {$subscriberData['email']}";
        $actionUrl = "subscribers.php";
        
        return createNotification(null, $title, $message, 'success', $actionUrl);
    }
}

if (!function_exists('notifyTeamUpdate')) {
    /**
     * Create notification for team member updates
     * 
     * @param string $action Action performed (added, updated, deleted)
     * @param string $memberName Team member name
     * @param int|null $adminId Admin to notify (null for all)
     * @return bool Success status
     */
    function notifyTeamUpdate($action, $memberName, $adminId = null) {
        $title = "Team Member " . ucfirst($action);
        $message = "Team member '$memberName' has been $action.";
        $actionUrl = "team.php";
        
        return createNotification($adminId, $title, $message, 'info', $actionUrl);
    }
}

if (!function_exists('notifyAdminAction')) {
    /**
     * Create notification for important admin actions
     * 
     * @param string $title Notification title
     * @param string $message Notification message
     * @param string $type Notification type
     * @param int|null $adminId Admin to notify (null for all admins)
     * @return bool Success status
     */
    function notifyAdminAction($title, $message, $type = 'info', $adminId = null) {
        return createNotification($adminId, $title, $message, $type);
    }
}

if (!function_exists('notifySystemEvent')) {
    /**
     * Create notification for system events
     * 
     * @param string $event Event description
     * @param string $type Event type: info, success, warning, error
     * @return bool Success status
     */
    function notifySystemEvent($event, $type = 'info') {
        $title = "System Event";
        $message = $event;
        
        // Notify super admins only for system events
        try {
            $db = getDBConnection();
            $stmt = $db->query("SELECT id FROM admin_users WHERE role = 'super_admin' AND is_active = 1");
            $superAdmins = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            foreach ($superAdmins as $adminId) {
                createNotification($adminId, $title, $message, $type);
            }
            
            return true;
        } catch (Exception $e) {
            error_log("System notification error: " . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('notifyBackupComplete')) {
    /**
     * Create notification for backup completion
     * 
     * @param bool $success Backup success status
     * @param string $details Backup details
     * @return bool Success status
     */
    function notifyBackupComplete($success, $details) {
        $title = $success ? "Backup Completed Successfully" : "Backup Failed";
        $type = $success ? "success" : "error";
        
        return notifySystemEvent($title . ": " . $details, $type);
    }
}

if (!function_exists('getUnreadNotificationCount')) {
    /**
     * Get count of unread notifications for an admin
     * 
     * @param int $adminId Admin user ID
     * @return int Count of unread notifications
     */
    function getUnreadNotificationCount($adminId) {
        try {
            $db = getDBConnection();
            $stmt = $db->prepare("SELECT COUNT(*) FROM admin_notifications WHERE admin_id = ? AND is_read = 0");
            $stmt->execute([$adminId]);
            return (int) $stmt->fetchColumn();
        } catch (Exception $e) {
            error_log("Get notification count error: " . $e->getMessage());
            return 0;
        }
    }
}

if (!function_exists('markNotificationRead')) {
    /**
     * Mark a notification as read
     * 
     * @param int $notificationId Notification ID
     * @param int $adminId Admin user ID
     * @return bool Success status
     */
    function markNotificationRead($notificationId, $adminId) {
        try {
            $db = getDBConnection();
            $stmt = $db->prepare("UPDATE admin_notifications SET is_read = 1, read_at = NOW() WHERE id = ? AND admin_id = ?");
            $stmt->execute([$notificationId, $adminId]);
            return true;
        } catch (Exception $e) {
            error_log("Mark notification read error: " . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('deleteOldNotifications')) {
    /**
     * Delete old read notifications (older than specified days)
     * 
     * @param int $days Days to keep notifications
     * @return int Number of deleted notifications
     */
    function deleteOldNotifications($days = 30) {
        try {
            $db = getDBConnection();
            $stmt = $db->prepare("DELETE FROM admin_notifications WHERE is_read = 1 AND read_at < DATE_SUB(NOW(), INTERVAL ? DAY)");
            $stmt->execute([$days]);
            return $stmt->rowCount();
        } catch (Exception $e) {
            error_log("Delete old notifications error: " . $e->getMessage());
            return 0;
        }
    }
}
