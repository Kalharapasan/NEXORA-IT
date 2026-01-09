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

?>