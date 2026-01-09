# NEXORA PHP Backend Setup Guide

## 📋 Overview

This folder contains the PHP backend files for handling contact form submissions and newsletter subscriptions with database storage and email notifications.

## 🗂️ Files Included

### 1. **config.php**
- Database configuration
- Company information constants
- Email settings
- Database connection function
- Email sending function

### 2. **contact.php**
- Handles contact form submissions
- Validates form data
- Stores messages in database
- Sends email to admin
- Sends confirmation email to user

### 3. **newsletter.php**
- Handles newsletter subscriptions
- Prevents duplicate subscriptions
- Stores subscribers in database
- Sends welcome email to subscriber
- Notifies admin of new subscriptions

### 4. **database_setup.sql**
- SQL script to create database and tables
- Creates views for reporting
- Includes sample stored procedures
- Sets up indexes for performance

## 🚀 Installation Steps

### Step 1: Database Setup

1. Open **phpMyAdmin** or **MySQL Command Line**
2. Import the `database_setup.sql` file:
   ```sql
   mysql -u root -p < database_setup.sql
   ```
   Or simply copy and paste the SQL content into phpMyAdmin SQL tab

3. This will create:
   - Database: `nexora_db`
   - Table: `contact_messages`
   - Table: `newsletter_subscribers`
   - Views and procedures

### Step 2: Configure Database Connection

1. Open `config.php`
2. Update the database credentials:
   ```php
   define('DB_HOST', 'localhost');      // Your database host
   define('DB_NAME', 'nexora_db');      // Database name
   define('DB_USER', 'root');           // Your database username
   define('DB_PASS', '');               // Your database password
   ```

### Step 3: Configure Email Settings

1. In `config.php`, update email settings:
   ```php
   define('CONTACT_EMAIL', 'your-email@outlook.com');
   define('MAIL_RECIPIENT', 'your-email@outlook.com');
   ```

2. For production, consider using SMTP (PHPMailer):
   - Download PHPMailer: https://github.com/PHPMailer/PHPMailer
   - Configure SMTP settings in config.php

### Step 4: Test the Forms

1. **Test Contact Form:**
   - Fill out the contact form on your website
   - Check if data appears in `contact_messages` table
   - Verify emails are received

2. **Test Newsletter:**
   - Subscribe using the newsletter form
   - Check if email appears in `newsletter_subscribers` table
   - Verify welcome email is received

## 📊 Database Structure

### Contact Messages Table
```sql
- id (Primary Key)
- name (VARCHAR 100)
- email (VARCHAR 255)
- phone (VARCHAR 50)
- subject (VARCHAR 200)
- message (TEXT)
- ip_address (VARCHAR 45)
- user_agent (VARCHAR 255)
- status (ENUM: new, read, replied, archived)
- created_at (DATETIME)
- updated_at (DATETIME)
```

### Newsletter Subscribers Table
```sql
- id (Primary Key)
- email (VARCHAR 255, UNIQUE)
- ip_address (VARCHAR 45)
- user_agent (VARCHAR 255)
- status (ENUM: active, unsubscribed, bounced)
- created_at (DATETIME)
- updated_at (DATETIME)
- unsubscribed_at (DATETIME)
```

## 🔐 Security Features

1. **Input Sanitization:** All inputs are sanitized using `htmlspecialchars()`
2. **Email Validation:** Validates email format using `filter_var()`
3. **SQL Injection Prevention:** Uses PDO prepared statements
4. **XSS Protection:** Escapes all output
5. **CSRF Protection:** Can be added using tokens
6. **Rate Limiting:** Should be implemented for production

## 📧 Email Configuration

### Using PHP Mail (Default)
The default setup uses PHP's `mail()` function. This works on most shared hosting.

### Using SMTP (Recommended for Production)

Install PHPMailer:
```bash
composer require phpmailer/phpmailer
```

Update `config.php`:
```php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmail($to, $subject, $message, $from = null) {
    $mail = new PHPMailer(true);
    
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com';
        $mail->Password = 'your-app-password';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Recipients
        $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email Error: {$mail->ErrorInfo}");
        return false;
    }
}
```

## 🎯 Usage in Frontend

The JavaScript files are already configured to use these PHP endpoints:

### Contact Form
```javascript
fetch('php/contact.php', {
    method: 'POST',
    body: formData
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('Message sent!');
    }
});
```

### Newsletter Form
```javascript
fetch('php/newsletter.php', {
    method: 'POST',
    body: formData
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('Subscribed!');
    }
});
```

## 🔧 Troubleshooting

### Issue: Emails not sending
- Check PHP `mail()` is enabled: `php -i | grep sendmail`
- Check server error logs: `/var/log/apache2/error.log`
- Try using SMTP instead of `mail()`

### Issue: Database connection failed
- Verify MySQL is running: `sudo service mysql status`
- Check credentials in `config.php`
- Ensure database and tables exist

### Issue: CORS errors
- Add these headers to PHP files:
  ```php
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: POST');
  ```

## 📱 Admin Panel (Optional)

Create an admin panel to manage submissions:

```php
// admin/messages.php
<?php
require_once '../php/config.php';
$db = getDBConnection();
$stmt = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
$messages = $stmt->fetchAll();
// Display messages in a table
?>
```

## 🌐 Production Checklist

- [ ] Update database credentials
- [ ] Configure SMTP for emails
- [ ] Enable error logging
- [ ] Add rate limiting
- [ ] Implement CAPTCHA (Google reCAPTCHA)
- [ ] Set up SSL/HTTPS
- [ ] Add CSRF tokens
- [ ] Configure backups
- [ ] Test all forms thoroughly
- [ ] Monitor error logs

## 📞 Support

For issues or questions:
- **Email:** nexorait@outlook.com
- **Phone:** +94 77 635 0902
- **WhatsApp:** +94 70 671 7131

## 📝 License

Copyright © 2026 Nexora. All rights reserved.
