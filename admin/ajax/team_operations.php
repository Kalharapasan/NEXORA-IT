<?php
session_start();
require_once __DIR__ . '/../../php/config.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');

try {
    $db = getDBConnection();
    
    // Get the action from GET or POST
    $action = $_GET['action'] ?? $_POST['action'] ?? null;
    
    // If POST with JSON body
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST)) {
        $jsonInput = file_get_contents('php://input');
        $data = json_decode($jsonInput, true);
        if ($data) {
            $action = $data['action'] ?? null;
        }
    } else {
        $data = $_POST;
    }
    
    switch ($action) {
        case 'get':
            // Get single team member
            $id = $_GET['id'] ?? null;
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Member ID required']);
                exit();
            }
            
            $stmt = $db->prepare("SELECT * FROM team_members WHERE id = ?");
            $stmt->execute([$id]);
            $member = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($member) {
                echo json_encode(['success' => true, 'member' => $member]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Member not found']);
            }
            break;
            
        case 'add':
            // Add new team member
            $name = $data['name'] ?? '';
            $position = $data['position'] ?? '';
            $bio = $data['bio'] ?? null;
            $imageUrl = $data['imageUrl'] ?? null;
            $email = $data['email'] ?? null;
            $phone = $data['phone'] ?? null;
            $linkedinUrl = $data['linkedinUrl'] ?? null;
            $twitterUrl = $data['twitterUrl'] ?? null;
            $githubUrl = $data['githubUrl'] ?? null;
            $displayOrder = $data['displayOrder'] ?? 0;
            $status = $data['status'] ?? 'active';
            
            // Enhanced validation
            if (empty($name) || empty($position)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Name and position are required']);
                exit();
            }
            
            if (strlen($name) < 2 || strlen($name) > 100) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Name must be between 2 and 100 characters']);
                exit();
            }
            
            if (strlen($position) < 2 || strlen($position) > 100) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Position must be between 2 and 100 characters']);
                exit();
            }
            
            if ($imageUrl && !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid image URL format']);
                exit();
            }
            
            $stmt = $db->prepare("INSERT INTO team_members 
                (name, position, bio, image_url, email, phone, linkedin_url, twitter_url, github_url, display_order, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            
            $result = $stmt->execute([
                $name, $position, $bio, $imageUrl, $email, $phone, 
                $linkedinUrl, $twitterUrl, $githubUrl, $displayOrder, $status
            ]);
            
            if ($result) {
                http_response_code(201); // Created
                echo json_encode(['success' => true, 'message' => 'Team member added successfully', 'id' => $db->lastInsertId()]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error adding team member']);
            }
            break;
            
        case 'update':
            // Update existing team member
            $id = $data['id'] ?? null;
            $name = $data['name'] ?? '';
            $position = $data['position'] ?? '';
            $bio = $data['bio'] ?? null;
            $imageUrl = $data['imageUrl'] ?? null;
            $email = $data['email'] ?? null;
            $phone = $data['phone'] ?? null;
            $linkedinUrl = $data['linkedinUrl'] ?? null;
            $twitterUrl = $data['twitterUrl'] ?? null;
            $githubUrl = $data['githubUrl'] ?? null;
            $displayOrder = $data['displayOrder'] ?? 0;
            $status = $data['status'] ?? 'active';
            
            if (!$id || empty($name) || empty($position)) {
                echo json_encode(['success' => false, 'message' => 'ID, name, and position are required']);
                exit();
            }
            
            $stmt = $db->prepare("UPDATE team_members SET 
                name = ?, position = ?, bio = ?, image_url = ?, email = ?, phone = ?,
                linkedin_url = ?, twitter_url = ?, github_url = ?, display_order = ?, status = ?
                WHERE id = ?");
            
            $result = $stmt->execute([
                $name, $position, $bio, $imageUrl, $email, $phone,
                $linkedinUrl, $twitterUrl, $githubUrl, $displayOrder, $status, $id
            ]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Team member updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error updating team member']);
            }
            break;
            
        case 'delete':
            // Delete team member
            $id = $data['id'] ?? null;
            
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Member ID required']);
                exit();
            }
            
            $stmt = $db->prepare("DELETE FROM team_members WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Team member deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error deleting team member']);
            }
            break;
            
        case 'list':
            // Get all team members
            $status = $_GET['status'] ?? 'all';
            
            if ($status === 'all') {
                $stmt = $db->query("SELECT * FROM team_members ORDER BY display_order ASC, created_at DESC");
            } else {
                $stmt = $db->prepare("SELECT * FROM team_members WHERE status = ? ORDER BY display_order ASC, created_at DESC");
                $stmt->execute([$status]);
            }
            
            $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'members' => $members]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
    
} catch (Exception $e) {
    error_log("Team operations error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
