<?php 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 403 Forbidden');
    exit('Direct access not permitted');
}

header('Content-Type: application/json');

$config = [
    'recipient_email' => 'nexorait@outlook.com',
    'cc_emails' => [], 
    'subject_prefix' => '[Nexora Contact Form]',
    'from_email' => 'noreply@nexora.com',
    'from_name' => 'Nexora Website',
];
$response = [
    'success' => false,
    'message' => ''
];

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

$name = isset($_POST['name']) ? sanitize_input($_POST['name']) : '';
$email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
$phone = isset($_POST['phone']) ? sanitize_input($_POST['phone']) : '';
$subject = isset($_POST['subject']) ? sanitize_input($_POST['subject']) : '';
$message = isset($_POST['message']) ? sanitize_input($_POST['message']) : '';

$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required';
}

if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!validate_email($email)) {
    $errors[] = 'Invalid email format';
}

if (empty($subject)) {
    $errors[] = 'Subject is required';
}

if (empty($message)) {
    $errors[] = 'Message is required';
}

if (isset($_POST['website']) && !empty($_POST['website'])) {
    $errors[] = 'Spam detected';
}

if (!empty($errors)) {
    $response['message'] = implode(', ', $errors);
    echo json_encode($response);
    exit;
}

$email_subject = $config['subject_prefix'] . ' ' . $subject;


$email_body = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: white;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .field {
            margin-bottom: 20px;
        }
        .label {
            font-weight: bold;
            color: #1e3c72;
            margin-bottom: 5px;
        }
        .value {
            padding: 10px;
            background: #f5f5f5;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>New Contact Form Submission</h1>
            <p>Nexora Website</p>
        </div>
        <div class='content'>
            <div class='field'>
                <div class='label'>Name:</div>
                <div class='value'>" . htmlspecialchars($name) . "</div>
            </div>
            <div class='field'>
                <div class='label'>Email:</div>
                <div class='value'>" . htmlspecialchars($email) . "</div>
            </div>
            <div class='field'>
                <div class='label'>Phone:</div>
                <div class='value'>" . (!empty($phone) ? htmlspecialchars($phone) : 'Not provided') . "</div>
            </div>
            <div class='field'>
                <div class='label'>Subject:</div>
                <div class='value'>" . htmlspecialchars($subject) . "</div>
            </div>
            <div class='field'>
                <div class='label'>Message:</div>
                <div class='value'>" . nl2br(htmlspecialchars($message)) . "</div>
            </div>
        </div>
        <div class='footer'>
            <p>This email was sent from the Nexora website contact form</p>
            <p>Timestamp: " . date('Y-m-d H:i:s') . "</p>
        </div>
    </div>
</body>
</html>
";

$email_body_plain = "
New Contact Form Submission - Nexora Website

Name: $name
Email: $email
Phone: " . (!empty($phone) ? $phone : 'Not provided') . "
Subject: $subject

Message:
$message

---
Timestamp: " . date('Y-m-d H:i:s') . "
";

$headers = [
    'MIME-Version: 1.0',
    'Content-Type: text/html; charset=UTF-8',
    'From: ' . $config['from_name'] . ' <' . $config['from_email'] . '>',
    'Reply-To: ' . $name . ' <' . $email . '>',
    'X-Mailer: PHP/' . phpversion()
];

if (!empty($config['cc_emails'])) {
    $headers[] = 'Cc: ' . implode(', ', $config['cc_emails']);
}

$mail_sent = mail(
    $config['recipient_email'],
    $email_subject,
    $email_body,
    implode("\r\n", $headers)
);

if ($mail_sent) {
   
    $log_entry = date('Y-m-d H:i:s') . " - Contact form submission from: $name ($email)\n";
    @file_put_contents(__DIR__ . '/../logs/contact_submissions.log', $log_entry, FILE_APPEND);
    
    $response['success'] = true;
    $response['message'] = 'Thank you for contacting us! We will get back to you soon.';
} else {
    $response['message'] = 'Sorry, there was an error sending your message. Please try again or contact us directly at ' . $config['recipient_email'];
}

if ($mail_sent) {
    $auto_reply_subject = 'Thank you for contacting Nexora';
    $auto_reply_body = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: white; padding: 30px; border: 1px solid #e5e5e5; border-top: none; border-radius: 0 0 10px 10px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Thank You for Contacting Nexora</h1>
            </div>
            <div class='content'>
                <p>Dear " . htmlspecialchars($name) . ",</p>
                <p>Thank you for reaching out to us. We have received your message and will get back to you as soon as possible.</p>
                <p><strong>Your message:</strong></p>
                <p style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>" . nl2br(htmlspecialchars($message)) . "</p>
                <p>If you have any urgent questions, please feel free to contact us directly:</p>
                <ul>
                    <li>Email: nexorait@outlook.com</li>
                    <li>Phone: +94 77 635 0902 / +94 70 671 7131</li>
                    <li>WhatsApp: +94 70 671 7131</li>
                </ul>
                <p>Best regards,<br>The Nexora Team</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $auto_reply_headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        'From: Nexora <' . $config['from_email'] . '>',
        'X-Mailer: PHP/' . phpversion()
    ];
    
    @mail($email, $auto_reply_subject, $auto_reply_body, implode("\r\n", $auto_reply_headers));
}

?>