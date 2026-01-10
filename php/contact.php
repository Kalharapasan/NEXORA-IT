<?php
// Security headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Only POST requests are accepted.'
    ]);
    exit;
}

require_once 'config.php';

$response = [
    'success' => false,
    'message' => '',
    'data' => []
];

try {
    // ==========================================
    // RATE LIMITING - Prevent spam
    // ==========================================
    function checkRateLimit($ip) {
        $db = getDBConnection();
        if (!$db) return true; // Allow if DB not available
        
        // Check submissions in last 5 minutes
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM contact_messages 
                             WHERE ip_address = ? AND created_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)");
        $stmt->execute([$ip]);
        $result = $stmt->fetch();
        
        // Allow max 3 submissions per 5 minutes
        return ($result['count'] < 3);
    }
    
    // ==========================================
    // SANITIZE INPUT FUNCTION
    // ==========================================
    function sanitizeInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }

    function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    function validatePhone($phone) {
        // Enhanced phone validation
        return preg_match('/^[\d\s\+\-\(\)]{10,}$/', $phone);
    }
    
    // ==========================================
    // CHECK RATE LIMIT
    // ==========================================
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    
    if (!checkRateLimit($ip_address)) {
        http_response_code(429);
        $response['message'] = 'Too many requests. Please try again in a few minutes.';
        echo json_encode($response);
        exit;
    }

    // ==========================================
    // GET AND SANITIZE INPUT
    // ==========================================
    $name = isset($_POST['name']) ? sanitizeInput($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitizeInput($_POST['phone']) : '';
    $subject = isset($_POST['subject']) ? sanitizeInput($_POST['subject']) : '';
    $message = isset($_POST['message']) ? sanitizeInput($_POST['message']) : '';

    $errors = [];

    if (empty($name)) {
        $errors[] = 'Name is required';
    } elseif (strlen($name) < 2) {
        $errors[] = 'Name must be at least 2 characters';
    } elseif (strlen($name) > 100) {
        $errors[] = 'Name must not exceed 100 characters';
    }

    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!validateEmail($email)) {
        $errors[] = 'Please enter a valid email address';
    }

    if (!empty($phone) && !validatePhone($phone)) {
        $errors[] = 'Please enter a valid phone number';
    }

    if (empty($subject)) {
        $errors[] = 'Subject is required';
    } elseif (strlen($subject) < 3) {
        $errors[] = 'Subject must be at least 3 characters';
    } elseif (strlen($subject) > 200) {
        $errors[] = 'Subject must not exceed 200 characters';
    }

    if (empty($message)) {
        $errors[] = 'Message is required';
    } elseif (strlen($message) < 10) {
        $errors[] = 'Message must be at least 10 characters';
    } elseif (strlen($message) > 5000) {
        $errors[] = 'Message must not exceed 5000 characters';
    }

    if (!empty($errors)) {
        $response['message'] = implode('. ', $errors);
        echo json_encode($response);
        exit;
    }

    $db = getDBConnection();
    
    if ($db) {
        try {
            $sql = "INSERT INTO contact_messages (name, email, phone, subject, message, ip_address, user_agent, created_at) 
                    VALUES (:name, :email, :phone, :subject, :message, :ip_address, :user_agent, NOW())";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':phone' => $phone,
                ':subject' => $subject,
                ':message' => $message,
                ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
                ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
            ]);

            $messageId = $db->lastInsertId();
            $response['data']['message_id'] = $messageId;
            
            // Create notification for admins
            try {
                require_once __DIR__ . '/../admin/includes/notifications.php';
                notifyNewContact([
                    'id' => $messageId,
                    'name' => $name,
                    'email' => $email,
                    'subject' => $subject
                ]);
            } catch (Exception $e) {
                error_log("Notification creation error: " . $e->getMessage());
            }
            
        } catch (PDOException $e) {
            
            error_log("Database Error: " . $e->getMessage());
        }
    }

 
    $emailSubject = "[Nexora Contact Form] " . $subject;
    
    $emailMessage = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #1e3c72, #2a5298); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .header h1 { margin: 0; font-size: 24px; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
            .field { margin-bottom: 20px; padding: 15px; background: white; border-radius: 5px; border-left: 4px solid #3d6cb9; }
            .field-label { font-weight: bold; color: #1e3c72; margin-bottom: 5px; }
            .field-value { color: #666; }
            .footer { text-align: center; padding: 20px; color: #999; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🔔 New Contact Form Submission</h1>
            </div>
            <div class='content'>
                <div class='field'>
                    <div class='field-label'>👤 Name:</div>
                    <div class='field-value'>{$name}</div>
                </div>
                <div class='field'>
                    <div class='field-label'>📧 Email:</div>
                    <div class='field-value'><a href='mailto:{$email}'>{$email}</a></div>
                </div>
                <div class='field'>
                    <div class='field-label'>📞 Phone:</div>
                    <div class='field-value'>{$phone}</div>
                </div>
                <div class='field'>
                    <div class='field-label'>📝 Subject:</div>
                    <div class='field-value'>{$subject}</div>
                </div>
                <div class='field'>
                    <div class='field-label'>💬 Message:</div>
                    <div class='field-value'>" . nl2br($message) . "</div>
                </div>
                <div class='field'>
                    <div class='field-label'>🕐 Submitted:</div>
                    <div class='field-value'>" . date('F j, Y, g:i a') . "</div>
                </div>
                <div class='field'>
                    <div class='field-label'>🌐 IP Address:</div>
                    <div class='field-value'>" . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . "</div>
                </div>
            </div>
            <div class='footer'>
                <p>This is an automated message from Nexora Website Contact Form</p>
                <p>&copy; " . date('Y') . " Nexora. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";

    $emailSent = sendEmail(MAIL_RECIPIENT, $emailSubject, $emailMessage, $email);


    $confirmSubject = "Thank you for contacting Nexora";
    $confirmMessage = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #1e3c72, #2a5298); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .header h1 { margin: 0; font-size: 24px; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
            .message-box { background: white; padding: 20px; border-radius: 5px; margin: 20px 0; }
            .button { display: inline-block; padding: 12px 30px; background: #3d6cb9; color: white; text-decoration: none; border-radius: 25px; margin: 20px 0; }
            .footer { text-align: center; padding: 20px; color: #999; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>✅ Message Received!</h1>
            </div>
            <div class='content'>
                <p>Dear {$name},</p>
                <p>Thank you for contacting <strong>Nexora</strong>. We have received your message and our team will get back to you as soon as possible.</p>
                
                <div class='message-box'>
                    <p><strong>Your Message Details:</strong></p>
                    <p><strong>Subject:</strong> {$subject}</p>
                    <p><strong>Message:</strong><br>" . nl2br($message) . "</p>
                </div>

                <p>We typically respond within 24 hours during business days.</p>
                <p>If you need immediate assistance, feel free to:</p>
                <ul>
                    <li>📞 Call us: " . CONTACT_PHONE_1 . "</li>
                    <li>💬 WhatsApp: " . CONTACT_WHATSAPP . "</li>
                </ul>

                <center>
                    <a href='" . SITE_URL . "' class='button'>Visit Our Website</a>
                </center>

                <p>Best regards,<br><strong>Nexora Team</strong></p>
            </div>
            <div class='footer'>
                <p>&copy; " . date('Y') . " Nexora. All rights reserved.</p>
                <p>" . CONTACT_ADDRESS . "</p>
            </div>
        </div>
    </body>
    </html>
    ";

    sendEmail($email, $confirmSubject, $confirmMessage);

  
    $response['success'] = true;
    $response['message'] = 'Thank you for your message! We\'ll get back to you soon.';
    $response['data']['email_sent'] = $emailSent;
    $response['data']['timestamp'] = date('Y-m-d H:i:s');


    error_log("Contact form submitted successfully - Name: {$name}, Email: {$email}");

} catch (Exception $e) {
   
    $response['success'] = false;
    $response['message'] = 'An error occurred. Please try again later or contact us directly.';
    error_log("Contact Form Error: " . $e->getMessage());
}


echo json_encode($response);
exit;
?>
