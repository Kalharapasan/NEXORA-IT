<?php
// Security and performance headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Cache-Control: public, max-age=300'); // Cache for 5 minutes

require_once __DIR__ . '/config.php';

try {
    $db = getDBConnection();
    
    // Get active team members
    $stmt = $db->prepare("SELECT * FROM team_members WHERE status = 'active' ORDER BY display_order ASC, created_at ASC");
    $stmt->execute();
    $teamMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'members' => $teamMembers
    ]);
    
} catch (Exception $e) {
    error_log("Error fetching team members: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching team members',
        'members' => []
    ]);
}
?>
