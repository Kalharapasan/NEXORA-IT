<?php



header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');


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

    $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';


    if (empty($email)) {
        $response['message'] = 'Email address is required';
        echo json_encode($response);
        exit;
    }

    if (!validateEmail($email)) {
        $response['message'] = 'Please enter a valid email address';
        echo json_encode($response);
        exit;
    }


    $db = getDBConnection();
    
    if ($db) {
        try {
         
            $checkSql = "SELECT id, status FROM newsletter_subscribers WHERE email = :email LIMIT 1";
            $checkStmt = $db->prepare($checkSql);
            $checkStmt->execute([':email' => $email]);
            $existingSubscriber = $checkStmt->fetch();

            if ($existingSubscriber) {
                if ($existingSubscriber['status'] === 'active') {
                    $response['message'] = 'This email is already subscribed to our newsletter';
                    echo json_encode($response);
                    exit;
                } else {
                   
                    $updateSql = "UPDATE newsletter_subscribers SET status = 'active', updated_at = NOW() WHERE email = :email";
                    $updateStmt = $db->prepare($updateSql);
                    $updateStmt->execute([':email' => $email]);
                    
                    $response['data']['action'] = 'reactivated';
                }
            } else {
                
                $insertSql = "INSERT INTO newsletter_subscribers (email, ip_address, user_agent, status, created_at) 
                             VALUES (:email, :ip_address, :user_agent, 'active', NOW())";
                
                $insertStmt = $db->prepare($insertSql);
                $insertStmt->execute([
                    ':email' => $email,
                    ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
                    ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
                ]);

                $subscriberId = $db->lastInsertId();
                $response['data']['subscriber_id'] = $subscriberId;
                $response['data']['action'] = 'subscribed';
            }

        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            $response['message'] = 'An error occurred. Please try again later.';
            echo json_encode($response);
            exit;
        }
    } else {
        $response['message'] = 'Database connection failed. Please try again later.';
        echo json_encode($response);
        exit;
    }

  
    $emailSubject = "Welcome to Nexora Newsletter! 🎉";
    
    $emailMessage = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #1e3c72, #2a5298); color: white; padding: 40px; text-align: center; border-radius: 10px 10px 0 0; }
            .header h1 { margin: 0; font-size: 28px; }
            .content { background: #f9f9f9; padding: 40px; border-radius: 0 0 10px 10px; }
            .welcome-box { background: white; padding: 30px; border-radius: 10px; margin: 20px 0; text-align: center; }
            .benefits { background: white; padding: 20px; border-radius: 10px; margin: 20px 0; }
            .benefit-item { padding: 15px; margin: 10px 0; border-left: 4px solid #3d6cb9; background: #f9f9f9; }
            .button { display: inline-block; padding: 15px 40px; background: #3d6cb9; color: white; text-decoration: none; border-radius: 25px; margin: 20px 0; font-weight: bold; }
            .footer { text-align: center; padding: 20px; color: #999; font-size: 12px; }
            .unsubscribe { color: #999; font-size: 11px; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🎉 Welcome to Nexora!</h1>
                <p style='margin: 10px 0 0; font-size: 18px;'>Thank you for subscribing to our newsletter</p>
            </div>
            <div class='content'>
                <div class='welcome-box'>
                    <h2 style='color: #1e3c72; margin-bottom: 15px;'>You're In! 🚀</h2>
                    <p style='font-size: 16px; color: #666;'>You've successfully subscribed to the Nexora newsletter. Get ready to receive exclusive updates, technology insights, and special offers!</p>
                </div>

                <div class='benefits'>
                    <h3 style='color: #1e3c72; text-align: center; margin-bottom: 20px;'>What to Expect:</h3>
                    
                    <div class='benefit-item'>
                        <strong>📰 Latest Technology Trends</strong>
                        <p style='margin: 5px 0 0; color: #666;'>Stay ahead with industry insights and innovations</p>
                    </div>
                    
                    <div class='benefit-item'>
                        <strong>💡 Expert Tips & Guides</strong>
                        <p style='margin: 5px 0 0; color: #666;'>Practical advice to optimize your business operations</p>
                    </div>
                    
                    <div class='benefit-item'>
                        <strong>🎁 Exclusive Offers</strong>
                        <p style='margin: 5px 0 0; color: #666;'>Special discounts and early access to new products</p>
                    </div>
                    
                    <div class='benefit-item'>
                        <strong>📢 Product Updates</strong>
                        <p style='margin: 5px 0 0; color: #666;'>Be the first to know about new features and releases</p>
                    </div>
                </div>

                <center>
                    <a href='" . SITE_URL . "' class='button'>Visit Our Website</a>
                </center>

                <p style='text-align: center; margin-top: 30px; color: #666;'>
                    Have questions? Contact us:<br>
                    📧 " . CONTACT_EMAIL . "<br>
                    📞 " . CONTACT_PHONE_1 . "<br>
                    💬 WhatsApp: " . CONTACT_WHATSAPP . "
                </p>

                <p class='unsubscribe'>
                    You received this email because you subscribed to Nexora newsletter.<br>
                    If you no longer wish to receive these emails, you can <a href='" . SITE_URL . "/unsubscribe?email=" . urlencode($email) . "'>unsubscribe here</a>.
                </p>
            </div>
            <div class='footer'>
                <p>&copy; " . date('Y') . " Nexora. All rights reserved.</p>
                <p>" . CONTACT_ADDRESS . "</p>
            </div>
        </div>
    </body>
    </html>
    ";

    $emailSent = sendEmail($email, $emailSubject, $emailMessage);

 
    $adminSubject = "[Nexora] New Newsletter Subscription";
    $adminMessage = "
    <!DOCTYPE html>
    <html>
    <body style='font-family: Arial, sans-serif;'>
        <h2>New Newsletter Subscriber</h2>
        <p><strong>Email:</strong> {$email}</p>
        <p><strong>Date:</strong> " . date('F j, Y, g:i a') . "</p>
        <p><strong>IP Address:</strong> " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . "</p>
        <p><strong>Action:</strong> " . ($response['data']['action'] ?? 'subscribed') . "</p>
    </body>
    </html>
    ";

    sendEmail(MAIL_RECIPIENT, $adminSubject, $adminMessage);

  
    $response['success'] = true;
    $response['message'] = 'Thank you for subscribing! Check your email for confirmation.';
    $response['data']['email_sent'] = $emailSent;
    $response['data']['timestamp'] = date('Y-m-d H:i:s');

   
    error_log("Newsletter subscription - Email: {$email}, Action: " . $response['data']['action']);

} catch (Exception $e) {
 
    $response['success'] = false;
    $response['message'] = 'An error occurred. Please try again later.';
    error_log("Newsletter Subscription Error: " . $e->getMessage());
}


echo json_encode($response);
exit;
?>
