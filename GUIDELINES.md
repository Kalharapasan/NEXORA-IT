# NEXORA IT - Complete Project Guidelines

**Version:** 2.0.1 (Enterprise Edition)  
**Last Updated:** January 10, 2026  
**Project Type:** Corporate Website with Enterprise Admin Panel

---

## 📑 Table of Contents

1. [Project Overview](#project-overview)
2. [Quick Start Guide](#quick-start-guide)
3. [Installation Guidelines](#installation-guidelines)
4. [Admin Panel Usage](#admin-panel-usage)
5. [Development Guidelines](#development-guidelines)
6. [Security Guidelines](#security-guidelines)
7. [Maintenance Guidelines](#maintenance-guidelines)
8. [Database Management](#database-management)
9. [Email Configuration](#email-configuration)
10. [Deployment Guidelines](#deployment-guidelines)
11. [Troubleshooting](#troubleshooting)
12. [Best Practices](#best-practices)

---

## 📋 Project Overview

### What is NEXORA IT?

A modern, responsive business website with an enterprise-grade admin panel featuring:
- Professional corporate website with particle animations
- Contact form with database storage and dual email notifications
- Newsletter subscription system with auto-notifications
- Complete admin dashboard with 7 major V2.0 features
- Multi-user admin system with role-based access control
- Bulk operations, email templates, notifications system

### Technology Stack

- **Frontend:** HTML5, CSS3, JavaScript ES6+
- **Backend:** PHP 7.4+ / 8.0+
- **Database:** MySQL 5.7+ / MariaDB 10.3+
- **Server:** Apache / Nginx
- **Security:** bcrypt password hashing, PDO prepared statements, CSRF protection
- **Features:** 12 database tables, 3 views, 2 stored procedures, 4 triggers

### Project Statistics

- **Total Files:** 50+ files
- **Lines of Code:** 7,400+ lines (4,000 PHP + 2,000 CSS + 700 JS + 700 SQL)
- **Documentation:** 1,880+ lines across 10 documentation files
- **Database Objects:** 12 tables, 3 views, 2 procedures, 4 triggers
- **Admin Features:** 7 major features with 20+ enhancements

---

## 🚀 Quick Start Guide

### Prerequisites

✅ PHP 7.4+ with PDO extension  
✅ MySQL 5.7+ or MariaDB 10.3+  
✅ Apache/Nginx web server  
✅ Basic knowledge of PHP, MySQL, HTML/CSS

### 5-Minute Setup

```bash
# 1. Copy project to web server
cp -r Project/ /var/www/html/nexora/

# 2. Create database
mysql -u root -p -e "CREATE DATABASE nexora_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 3. Import database (ONE FILE DOES IT ALL!)
mysql -u root -p nexora_db < Sql/Complete.sql

# 4. Configure database connection
nano php/config.php
# Update: DB_HOST, DB_NAME, DB_USER, DB_PASS

# 5. Set permissions
chmod 644 php/config.php
chmod 755 admin/ php/

# 6. Access website
# Website: http://yourserver/nexora/
# Admin: http://yourserver/nexora/admin/
```

### First Login

```
URL: http://yourserver/nexora/admin/
Username: admin
Password: admin123

⚠️ CHANGE PASSWORD IMMEDIATELY!
Go to: My Settings > Change Password
```

---

## 📦 Installation Guidelines

### Step 1: Environment Setup

#### Windows (XAMPP)

```powershell
# 1. Download and install XAMPP
# https://www.apachefriends.org/

# 2. Copy project
Copy-Item -Recurse "d:\Web Project\NEXORA IT\Project" "C:\xampp\htdocs\nexora"

# 3. Start services
# Open XAMPP Control Panel
# Start: Apache + MySQL

# 4. Access: http://localhost/nexora/
```

#### Linux (Ubuntu/Debian)

```bash
# 1. Install LAMP stack
sudo apt update
sudo apt install apache2 mysql-server php php-mysql php-mbstring php-xml

# 2. Copy project
sudo cp -r Project/ /var/www/html/nexora/

# 3. Set ownership
sudo chown -R www-data:www-data /var/www/html/nexora/

# 4. Restart Apache
sudo systemctl restart apache2

# 5. Access: http://localhost/nexora/
```

#### macOS (MAMP)

```bash
# 1. Download and install MAMP
# https://www.mamp.info/

# 2. Copy project
cp -r Project/ /Applications/MAMP/htdocs/nexora/

# 3. Start MAMP
# Open MAMP application
# Start Servers

# 4. Access: http://localhost:8888/nexora/
```

### Step 2: Database Setup

#### Method 1: Complete.sql (Recommended)

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE nexora_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import complete setup
mysql -u root -p nexora_db < Sql/Complete.sql

# ✅ Done! Everything is set up
```

**What Complete.sql includes:**
- ✅ All 12 core tables with indexes
- ✅ 3 analytical views
- ✅ 2 stored procedures
- ✅ 4 automatic triggers
- ✅ Default admin user
- ✅ 3 email templates
- ✅ 15 system settings
- ✅ Initial dashboard data
- ✅ Sample data (commented - optional)
- ✅ Helper queries for inspection

#### Method 2: Individual Files

```bash
# Import in order:
mysql -u root -p nexora_db < Sql/database_setup.sql
mysql -u root -p nexora_db < Sql/admin_setup.sql
mysql -u root -p nexora_db < Sql/admin_features_update.sql
mysql -u root -p nexora_db < Sql/verify_setup.sql
```

#### Method 3: phpMyAdmin

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Click "New" → Create database `nexora_db`
3. Select collation: `utf8mb4_unicode_ci`
4. Click "Import" tab
5. Choose `Sql/Complete.sql`
6. Click "Go"
7. ✅ Success! Check for 12 tables

### Step 3: Configuration

#### Database Configuration (php/config.php)

```php
// Database Configuration
define('DB_HOST', 'localhost');        // Your MySQL host
define('DB_NAME', 'nexora_db');        // Database name
define('DB_USER', 'your_username');    // MySQL username
define('DB_PASS', 'your_password');    // MySQL password

// Site Configuration
define('SITE_NAME', 'NEXORA IT');
define('SITE_URL', 'http://yoursite.com');
define('ADMIN_EMAIL', 'nexorait@outlook.com');
```

#### Email Configuration (Optional)

```php
// In php/config.php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
```

### Step 4: Verify Installation

```bash
# Check database tables
mysql -u root -p nexora_db -e "SHOW TABLES;"

# Expected output: 12 tables
# admin_activity_logs, admin_notifications, admin_users, backup_history,
# contact_messages, dashboard_chart_data, dashboard_stats, email_templates,
# login_attempts, newsletter_subscribers, system_settings, team_members

# Check default admin
mysql -u root -p nexora_db -e "SELECT username, email, role FROM admin_users;"

# Expected: admin | admin@nexorait.com | super_admin
```

### Step 5: Security Setup

```bash
# 1. Change default admin password
# Login to admin panel → My Settings → Change Password

# 2. Set file permissions (Linux/Mac)
chmod 644 php/config.php
chmod 755 admin/ php/
find . -type f -name "*.php" -exec chmod 644 {} \;

# 3. Disable error display (production)
# In php/config.php:
# error_reporting(0);
# ini_set('display_errors', 0);

# 4. Update security settings
# Admin Panel → System Settings → Security tab
```

---

## 🎯 Admin Panel Usage

### Dashboard Overview

**URL:** `/admin/dashboard.php`

**Features:**
- 📊 4 Key Statistics Cards (Messages, Subscribers, Team Members, Users)
- 📈 30-Day Analytics Chart (Messages & Subscribers)
- 📧 Recent Contact Messages (Last 10)
- 👥 Recent Newsletter Subscribers (Last 10)
- 📝 Quick Actions Menu

### Managing Contact Messages

**URL:** `/admin/contacts.php`

#### View Messages
1. Navigate to "Contact Messages"
2. View all submissions with: Name, Email, Subject, Status, Date
3. Use search bar to find specific messages
4. Filter by status: All, New, Read, Replied, Archived

#### Respond to Messages
1. Click message row to view details
2. Read full message content
3. Click "Reply" to send response
4. Use email templates for faster responses
5. Status automatically updates to "Replied"

#### Bulk Operations
1. Select multiple messages (checkboxes)
2. Choose action from dropdown:
   - Mark as Read
   - Mark as Replied
   - Archive Selected
   - Delete Selected
3. Click "Apply" button
4. Confirm action in popup
5. ✅ Messages processed instantly

#### Export to CSV
1. Click "Export to CSV" button
2. File downloads: `contacts_YYYY-MM-DD.csv`
3. Open in Excel/Google Sheets
4. Use for reporting or backups

### Managing Newsletter Subscribers

**URL:** `/admin/subscribers.php`

#### View Subscribers
1. Navigate to "Newsletter Subscribers"
2. View all subscribers with: Email, Status, Source, Date
3. Search by email address
4. Filter by status: All, Active, Unsubscribed

#### Bulk Operations
1. Select multiple subscribers
2. Choose action:
   - Activate Selected
   - Unsubscribe Selected
   - Delete Selected
3. Apply changes
4. ✅ Instant processing

#### Export Subscriber List
1. Click "Export to CSV"
2. Use for email campaigns
3. Import to MailChimp, SendGrid, etc.

### Managing Team Members

**URL:** `/admin/team.php`

#### Add Team Member
1. Go to Team Management
2. Click "Add New Member" button
3. Fill in details:
   - Name (required)
   - Position (required)
   - Bio (required, 50-500 characters)
   - Email (required)
   - Image URL
   - LinkedIn URL
   - Display Order
   - Active Status
4. Click "Save"
5. ✅ Member added and visible on website

#### Edit Team Member
1. Click "Edit" button on member row
2. Update any field
3. Save changes
4. ✅ Website updates immediately

#### Delete Team Member
1. Click "Delete" button
2. Confirm deletion
3. ✅ Removed from website

### Admin User Management (V2.0)

**URL:** `/admin/admin_users.php`  
**Access:** Super Admin only

#### User Roles

| Role | Permissions |
|------|-------------|
| **Super Admin** | Full access - all features, settings, user management |
| **Admin** | Manage content, view/edit messages, subscribers, team |
| **Viewer** | Read-only access - view data only, no edits |

#### Add New Admin User
1. Go to Admin Tools → Admin Users
2. Click "Add New User"
3. Enter details:
   - Username (unique)
   - Email (unique)
   - Password (min 8 characters)
   - Full Name
   - Role (Super Admin / Admin / Viewer)
4. Click "Save"
5. ✅ User can now login

#### Manage Existing Users
- View last login time
- Toggle active/inactive status
- Edit user details
- Reset passwords
- Delete users (except yourself)
- View activity logs per user

### Email Templates (V2.0)

**URL:** `/admin/email_templates.php`

#### Create Template
1. Navigate to Content → Email Templates
2. Click "Add New Template"
3. Fill in:
   - Template Name
   - Template Type (contact_reply, newsletter, welcome, etc.)
   - Subject Line
   - Email Body (supports HTML and variables)
4. Use variables:
   - `{{name}}` - Recipient name
   - `{{email}}` - Recipient email
   - `{{subject}}` - Original subject
   - `{{message}}` - Original message
   - `{{date}}` - Current date
5. Preview template
6. Mark as Active
7. Save

#### Use Templates
- When replying to contact messages
- Automated welcome emails
- Newsletter campaigns
- System notifications

### System Settings (V2.0)

**URL:** `/admin/system_settings.php`  
**Access:** Super Admin only

#### Settings Categories

**General Settings:**
- Site Name
- Site URL
- Admin Email
- Company Name
- Company Phone
- Company Address

**Feature Settings:**
- Enable/Disable Contact Form
- Enable/Disable Newsletter
- Enable/Disable Team Section
- Auto-reply to contacts
- Email notifications

**File Settings:**
- Max Upload Size
- Allowed File Types
- Upload Directory

**Security Settings:**
- Session Timeout
- Max Login Attempts
- Password Min Length
- Force Password Change

**Backup Settings:**
- Auto Backup Enabled
- Backup Frequency
- Backup Retention Days

### Notifications (V2.0)

**URL:** `/admin/notifications.php`

#### View Notifications
- Check bell icon (🔔) in sidebar for unread count
- Click to view notification center
- Filter by type: All, Info, Success, Warning, Error
- Filter by status: All, Unread, Read

#### Automatic Notifications
System creates notifications for:
- ✉️ New contact message received
- 📧 New newsletter subscriber
- 👤 New admin user created
- ⚙️ System settings changed
- 💾 Backup completed
- ⚠️ Security alerts

#### Manage Notifications
- Mark individual as read
- Mark all as read
- Delete old notifications
- Click notification to go to relevant page

### Activity Logs (V2.0)

**URL:** `/admin/activity_logs.php`

#### View All Activities
- Complete audit trail of all admin actions
- Columns: User, Action, Table, Details, IP Address, Date
- Search by user or action
- Filter by date range
- Filter by action type (create, update, delete, login, logout)

#### Export Logs
- Click "Export to CSV"
- Use for compliance reports
- Security audits
- Performance analysis

#### What Gets Logged
- User logins/logouts
- Contact message actions
- Subscriber management
- Team member changes
- Admin user management
- Email template changes
- System settings updates
- Bulk operations
- Data exports

### System Information (V2.0)

**URL:** `/admin/system_info.php`

#### Dashboard Features
- 📊 Database Statistics (table sizes, row counts)
- 🖥️ PHP Configuration (version, memory limit, extensions)
- 📁 File System Info (disk space, upload limits)
- ⚙️ Server Info (OS, web server, MySQL version)
- 📈 Performance Metrics
- 🔒 Security Status

#### Database Health Check
- Table sizes and row counts
- Index efficiency
- Last backup date
- Database size vs. limit

#### System Requirements Check
- PHP version compatibility
- Required extensions status
- File permissions
- Configuration recommendations

---

## 💻 Development Guidelines

### File Structure

```
Project/
├── index.html              # Main website
├── css/
│   └── style.css          # All website styles
├── js/
│   └── main.js            # Website JavaScript
├── php/
│   ├── config.php         # Database config & functions
│   ├── contact.php        # Contact form handler
│   ├── newsletter.php     # Newsletter handler
│   ├── get_team.php       # Team API endpoint
│   └── README.md          # PHP documentation
├── Sql/
│   ├── Complete.sql       # ⭐ All-in-one setup
│   └── *.sql              # Individual setup files
├── admin/
│   ├── *.php              # Admin pages
│   ├── includes/          # Shared components
│   ├── ajax/              # AJAX endpoints
│   ├── css/               # Admin styles
│   └── js/                # Admin JavaScript
└── Doc/
    └── *.md               # Documentation files
```

### Coding Standards

#### PHP Standards

```php
<?php
// 1. Always use PHP opening tag
// 2. No closing tag at end of pure PHP files
// 3. Use strict types when possible
declare(strict_types=1);

// 4. Follow PSR-12 naming conventions
class AdminUserManager {
    private $db;
    
    public function getUserById(int $id): ?array {
        // Method logic
    }
}

// 5. Use prepared statements (ALWAYS!)
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);

// 6. Validate and sanitize ALL inputs
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');

// 7. Use meaningful variable names
$userEmail = $_POST['email'];  // Good
$e = $_POST['email'];           // Bad

// 8. Add comments for complex logic
// Calculate 30-day average with null handling
$average = array_sum($data) / (count($data) ?: 1);

// 9. Handle errors gracefully
try {
    $result = $this->performOperation();
} catch (Exception $e) {
    error_log($e->getMessage());
    return ['success' => false, 'message' => 'Operation failed'];
}

// 10. Return consistent response formats
return [
    'success' => true,
    'data' => $result,
    'message' => 'Operation completed'
];
```

#### JavaScript Standards

```javascript
// 1. Use strict mode
'use strict';

// 2. Use const/let, never var
const API_URL = '/php/contact.php';
let messageCount = 0;

// 3. Use arrow functions for callbacks
items.forEach(item => {
    console.log(item);
});

// 4. Use template literals
const message = `Hello ${name}, you have ${count} messages.`;

// 5. Use async/await for AJAX
async function loadData() {
    try {
        const response = await fetch(API_URL);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error:', error);
    }
}

// 6. Add JSDoc comments
/**
 * Submit form via AJAX
 * @param {HTMLFormElement} form - The form element
 * @returns {Promise<Object>} Response data
 */
async function submitForm(form) {
    // Implementation
}

// 7. Use event delegation
document.addEventListener('click', (e) => {
    if (e.target.matches('.delete-btn')) {
        handleDelete(e);
    }
});
```

#### CSS Standards

```css
/* 1. Use CSS variables for colors */
:root {
    --primary: #1e3c72;
    --secondary: #2a5298;
    --success: #28a745;
}

/* 2. Use BEM naming for components */
.card {}
.card__header {}
.card__body {}
.card--highlighted {}

/* 3. Group related properties */
.element {
    /* Positioning */
    position: relative;
    top: 0;
    left: 0;
    
    /* Display & Box Model */
    display: flex;
    width: 100%;
    padding: 1rem;
    margin: 0 auto;
    
    /* Typography */
    font-size: 1rem;
    color: var(--text-dark);
    
    /* Visual */
    background: white;
    border-radius: 8px;
    
    /* Animation */
    transition: all 0.3s ease;
}

/* 4. Mobile-first responsive design */
.container {
    width: 100%;
}

@media (min-width: 768px) {
    .container {
        width: 750px;
    }
}

/* 5. Comment sections */
/* ===========================
   HEADER STYLES
   =========================== */
```

#### SQL Standards

```sql
-- 1. Use uppercase for SQL keywords
SELECT id, name, email
FROM users
WHERE status = 'active'
ORDER BY created_at DESC;

-- 2. Use meaningful table and column names
CREATE TABLE newsletter_subscribers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    status ENUM('active', 'unsubscribed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Always define indexes for foreign keys
CREATE INDEX idx_user_id ON admin_activity_logs(user_id);
CREATE INDEX idx_created_at ON contact_messages(created_at);

-- 4. Use foreign key constraints
ALTER TABLE admin_notifications
ADD CONSTRAINT fk_notifications_user
FOREIGN KEY (user_id) REFERENCES admin_users(id)
ON DELETE CASCADE;

-- 5. Add comments to complex queries
-- Get 30-day analytics with zero-filling for missing dates
SELECT d.date, COALESCE(m.count, 0) as messages
FROM date_dimension d
LEFT JOIN message_counts m ON d.date = m.date
WHERE d.date >= CURDATE() - INTERVAL 30 DAY;
```

### Adding New Features

#### Step 1: Database Changes

```sql
-- 1. Create new table (if needed)
CREATE TABLE new_feature (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Add to Complete.sql for future installations

-- 3. Create migration script for existing installations
-- Sql/migrations/add_new_feature.sql
```

#### Step 2: Backend (PHP)

```php
// 1. Add functions to php/config.php or create new PHP file
function getNewFeatureData($pdo) {
    $stmt = $pdo->query("SELECT * FROM new_feature ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 2. Create admin page: admin/new_feature.php
// 3. Add AJAX handler: admin/ajax/new_feature_operations.php
```

#### Step 3: Frontend (Admin)

```php
// admin/new_feature.php
<?php
require_once 'includes/auth.php';
requireLogin();
require_once 'includes/header.php';
?>

<div class="page-header">
    <h1>New Feature</h1>
</div>

<div class="card">
    <!-- Feature content -->
</div>

<?php require_once 'includes/footer.php'; ?>
```

#### Step 4: Navigation

```php
// admin/includes/header.php
// Add menu item
<li>
    <a href="new_feature.php">
        <i class="fas fa-star"></i>
        <span>New Feature</span>
    </a>
</li>
```

#### Step 5: Documentation

```markdown
<!-- Doc/NEW_FEATURE.md -->
# New Feature Documentation

## Overview
Description of the feature

## Usage
How to use it

## API Endpoints
POST /admin/ajax/new_feature_operations.php
```

### Testing Checklist

Before deploying new features:

- [ ] Database migrations tested
- [ ] All CRUD operations work
- [ ] Input validation implemented
- [ ] XSS protection added
- [ ] SQL injection prevention verified
- [ ] Error handling implemented
- [ ] Success/error messages shown
- [ ] Responsive design tested
- [ ] Cross-browser compatibility checked
- [ ] Activity logging added
- [ ] Permissions verified
- [ ] Documentation updated

---

## 🔒 Security Guidelines

### Authentication & Authorization

```php
// 1. Always check authentication
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// 2. Check permissions for sensitive actions
function requirePermission($role) {
    if ($_SESSION['admin_role'] !== $role) {
        die('Access denied');
    }
}

// Usage:
requirePermission('super_admin'); // Only super admin can access
```

### Input Validation

```php
// 1. Validate email
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
if (!$email) {
    die('Invalid email');
}

// 2. Sanitize HTML input
$name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');

// 3. Validate integers
$id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
if ($id === false) {
    die('Invalid ID');
}

// 4. Validate string length
if (strlen($_POST['message']) < 10 || strlen($_POST['message']) > 1000) {
    die('Message must be between 10 and 1000 characters');
}

// 5. Use whitelist for enums
$status = $_POST['status'];
$allowed = ['new', 'read', 'replied', 'archived'];
if (!in_array($status, $allowed)) {
    die('Invalid status');
}
```

### SQL Injection Prevention

```php
// ✅ GOOD: Use prepared statements
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);

// ✅ GOOD: Named parameters
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute(['email' => $email]);

// ❌ BAD: Never concatenate user input
$query = "SELECT * FROM users WHERE email = '$email'"; // VULNERABLE!
```

### XSS Prevention

```php
// 1. Escape output in HTML
echo htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');

// 2. Use in templates
<div class="name"><?= htmlspecialchars($name) ?></div>

// 3. For JSON responses
header('Content-Type: application/json');
echo json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP);
```

### CSRF Protection

```php
// 1. Generate token
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 2. Add to forms
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

// 3. Verify on submission
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF token validation failed');
}
```

### Password Security

```php
// 1. Hash passwords (already implemented)
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// 2. Verify passwords
if (password_verify($inputPassword, $hashedPassword)) {
    // Login successful
}

// 3. Password requirements
function validatePassword($password) {
    if (strlen($password) < 8) {
        return false;
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return false; // Must have uppercase
    }
    if (!preg_match('/[a-z]/', $password)) {
        return false; // Must have lowercase
    }
    if (!preg_match('/[0-9]/', $password)) {
        return false; // Must have number
    }
    return true;
}
```

### File Upload Security

```php
// 1. Validate file type
$allowed = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($_FILES['file']['type'], $allowed)) {
    die('Invalid file type');
}

// 2. Validate file size (5MB max)
if ($_FILES['file']['size'] > 5 * 1024 * 1024) {
    die('File too large');
}

// 3. Generate unique filename
$extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
$filename = uniqid() . '.' . $extension;

// 4. Move to safe directory
$uploadDir = '/var/www/uploads/';
move_uploaded_file($_FILES['file']['tmp_name'], $uploadDir . $filename);
```

### Rate Limiting

```php
// Implement rate limiting for forms
$ip = $_SERVER['REMOTE_ADDR'];
$key = "ratelimit_contact_$ip";

// Check attempts in last 5 minutes
$attempts = (int)($redis->get($key) ?? 0);
if ($attempts >= 3) {
    die('Too many requests. Try again later.');
}

// Increment attempts
$redis->incr($key);
$redis->expire($key, 300); // 5 minutes
```

### Security Headers

```php
// Add to php/config.php or .htaccess
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'");
```

---

## 🔧 Maintenance Guidelines

### Daily Tasks

1. **Check Notifications**
   - Login to admin panel
   - Check bell icon for alerts
   - Respond to new contact messages

2. **Monitor Activity Logs**
   - Review recent admin actions
   - Check for suspicious activity
   - Verify all logins are legitimate

3. **Review New Submissions**
   - Check contact messages
   - Review new subscribers
   - Reply to urgent inquiries

### Weekly Tasks

1. **Database Maintenance**
   ```sql
   -- Run these queries weekly
   OPTIMIZE TABLE contact_messages;
   OPTIMIZE TABLE newsletter_subscribers;
   OPTIMIZE TABLE admin_activity_logs;
   
   -- Update dashboard stats
   CALL update_dashboard_stats();
   CALL update_dashboard_chart_data();
   ```

2. **Backup Database**
   ```bash
   # Manual backup
   mysqldump -u root -p nexora_db > backup_$(date +%Y%m%d).sql
   
   # Compress backup
   gzip backup_$(date +%Y%m%d).sql
   
   # Move to backup directory
   mv backup_*.sql.gz /path/to/backups/
   ```

3. **Review System Info**
   - Check database size
   - Monitor disk space
   - Review PHP error logs
   - Check table sizes

4. **Clean Old Data**
   ```sql
   -- Delete old notifications (90+ days)
   DELETE FROM admin_notifications 
   WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY) 
   AND is_read = 1;
   
   -- Archive old activity logs (6+ months)
   -- Or export and delete
   ```

### Monthly Tasks

1. **Security Audit**
   - Review all admin users
   - Check for inactive accounts
   - Review login attempts
   - Update passwords

2. **Performance Review**
   - Check slow query log
   - Review database indexes
   - Analyze page load times
   - Optimize images

3. **Content Update**
   - Update team member information
   - Refresh testimonials
   - Update portfolio/projects
   - Review and update content

4. **Update Dependencies**
   ```bash
   # Update PHP packages (if using Composer)
   composer update
   
   # Check for security updates
   # Review PHP and MySQL versions
   ```

### Quarterly Tasks

1. **Full System Backup**
   ```bash
   # Backup database
   mysqldump -u root -p nexora_db > full_backup.sql
   
   # Backup files
   tar -czf nexora_files_backup.tar.gz /var/www/html/nexora/
   
   # Store off-site (cloud storage)
   ```

2. **Security Update**
   - Update PHP to latest version
   - Update MySQL to latest version
   - Review and update security policies
   - Test all security features

3. **Performance Optimization**
   - Database query optimization
   - Image compression
   - Code minification
   - CDN implementation review

4. **Disaster Recovery Test**
   - Test backup restoration
   - Verify all data restored correctly
   - Document recovery time

---

## 💾 Database Management

### Daily Database Tasks

```sql
-- Check database size
SELECT 
    TABLE_NAME,
    ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) AS 'Size (MB)'
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'nexora_db'
ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC;

-- Check recent activity
SELECT COUNT(*) as total_contacts, 
       SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new_messages
FROM contact_messages;

-- Check subscriber growth
SELECT COUNT(*) as total_subscribers,
       SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today
FROM newsletter_subscribers
WHERE status = 'active';
```

### Database Optimization

```sql
-- Analyze tables for optimization
ANALYZE TABLE contact_messages;
ANALYZE TABLE newsletter_subscribers;
ANALYZE TABLE admin_activity_logs;

-- Optimize tables
OPTIMIZE TABLE contact_messages;
OPTIMIZE TABLE newsletter_subscribers;
OPTIMIZE TABLE admin_activity_logs;

-- Check table fragmentation
SELECT TABLE_NAME,
       ROUND(DATA_FREE / 1024 / 1024, 2) AS 'Fragmented (MB)'
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'nexora_db'
AND DATA_FREE > 0;
```

### Backup Strategies

#### Automated Daily Backup Script

```bash
#!/bin/bash
# backup_script.sh

# Configuration
DB_NAME="nexora_db"
DB_USER="root"
DB_PASS="your_password"
BACKUP_DIR="/backups/nexora"
DATE=$(date +%Y%m%d_%H%M%S)
FILENAME="nexora_backup_$DATE.sql"

# Create backup directory if not exists
mkdir -p $BACKUP_DIR

# Dump database
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > "$BACKUP_DIR/$FILENAME"

# Compress backup
gzip "$BACKUP_DIR/$FILENAME"

# Delete backups older than 30 days
find $BACKUP_DIR -name "*.sql.gz" -mtime +30 -delete

# Log completion
echo "$(date): Backup completed - $FILENAME.gz" >> "$BACKUP_DIR/backup.log"
```

#### Set up Cron Job (Linux)

```bash
# Edit crontab
crontab -e

# Add daily backup at 2 AM
0 2 * * * /path/to/backup_script.sh

# Add weekly database optimization
0 3 * * 0 mysql -u root -p'password' nexora_db -e "CALL update_dashboard_stats(); OPTIMIZE TABLE contact_messages, newsletter_subscribers, admin_activity_logs;"
```

### Database Restoration

```bash
# Restore from backup
mysql -u root -p nexora_db < backup_file.sql

# Restore and verify
mysql -u root -p nexora_db < backup_file.sql
mysql -u root -p nexora_db -e "SHOW TABLES;"
mysql -u root -p nexora_db -e "SELECT COUNT(*) FROM contact_messages;"
```

### Database Migration

When updating to new version:

```sql
-- 1. Backup current database
mysqldump -u root -p nexora_db > pre_migration_backup.sql

-- 2. Run migration script
SOURCE Sql/migrations/migration_v2.1.sql;

-- 3. Verify migration
SELECT * FROM system_settings WHERE setting_key = 'db_version';

-- 4. Test functionality
-- Login to admin panel and test all features
```

---

## 📧 Email Configuration

### Using PHP mail() Function (Basic)

Already configured in `php/config.php`. Works on most servers but may have deliverability issues.

**Pros:**
- No configuration needed
- Works immediately

**Cons:**
- Emails may go to spam
- No delivery tracking
- Limited features

### Using SMTP (Recommended)

Better deliverability and reliability.

#### Option 1: Gmail SMTP

```php
// php/config.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendEmailSMTP($to, $subject, $body) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'your-email@gmail.com';
        $mail->Password   = 'your-app-password'; // Generate from Google Account
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Recipients
        $mail->setFrom('your-email@gmail.com', 'NEXORA IT');
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email Error: {$mail->ErrorInfo}");
        return false;
    }
}
```

#### Option 2: SendGrid

```php
// Install SendGrid: composer require sendgrid/sendgrid

function sendEmailSendGrid($to, $subject, $body) {
    $email = new \SendGrid\Mail\Mail();
    $email->setFrom("support@nexorait.com", "NEXORA IT");
    $email->setSubject($subject);
    $email->addTo($to);
    $email->addContent("text/html", $body);
    
    $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
    
    try {
        $response = $sendgrid->send($email);
        return $response->statusCode() == 202;
    } catch (Exception $e) {
        error_log('SendGrid Error: ' . $e->getMessage());
        return false;
    }
}
```

#### Option 3: AWS SES

```php
// Install AWS SDK: composer require aws/aws-sdk-php

use Aws\Ses\SesClient;

function sendEmailAWS($to, $subject, $body) {
    $client = new SesClient([
        'version' => 'latest',
        'region'  => 'us-east-1',
        'credentials' => [
            'key'    => 'YOUR_AWS_KEY',
            'secret' => 'YOUR_AWS_SECRET',
        ],
    ]);
    
    try {
        $result = $client->sendEmail([
            'Source' => 'support@nexorait.com',
            'Destination' => [
                'ToAddresses' => [$to],
            ],
            'Message' => [
                'Subject' => [
                    'Data' => $subject,
                ],
                'Body' => [
                    'Html' => [
                        'Data' => $body,
                    ],
                ],
            ],
        ]);
        return true;
    } catch (Exception $e) {
        error_log('AWS SES Error: ' . $e->getMessage());
        return false;
    }
}
```

### Email Testing

```php
// Create test script: test_email.php
<?php
require_once 'php/config.php';

$result = sendEmail(
    'test@example.com',
    'Test Email',
    '<h1>Test Email</h1><p>If you receive this, email is working!</p>'
);

echo $result ? 'Email sent successfully!' : 'Email failed to send.';
```

---

## 🚀 Deployment Guidelines

### Pre-Deployment Checklist

- [ ] All features tested locally
- [ ] Database backup created
- [ ] Config files updated for production
- [ ] Error display disabled
- [ ] Debug mode off
- [ ] HTTPS configured
- [ ] Email SMTP configured
- [ ] Default passwords changed
- [ ] File permissions set correctly
- [ ] .htaccess security rules added

### Deployment Steps

#### 1. Prepare Production Server

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install required software
sudo apt install apache2 mysql-server php php-mysql php-mbstring php-xml -y

# Enable Apache modules
sudo a2enmod rewrite
sudo systemctl restart apache2

# Secure MySQL
sudo mysql_secure_installation
```

#### 2. Upload Files

```bash
# Via FTP/SFTP:
# - Upload all files to /var/www/html/nexora/
# - Maintain folder structure

# Via Git:
cd /var/www/html/
git clone https://your-repo.com/nexora.git
```

#### 3. Configure Production Database

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE nexora_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Create database user
mysql -u root -p -e "CREATE USER 'nexora_user'@'localhost' IDENTIFIED BY 'strong_password';"
mysql -u root -p -e "GRANT ALL PRIVILEGES ON nexora_db.* TO 'nexora_user'@'localhost';"
mysql -u root -p -e "FLUSH PRIVILEGES;"

# Import database
mysql -u nexora_user -p nexora_db < Sql/Complete.sql
```

#### 4. Update Configuration

```php
// php/config.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'nexora_db');
define('DB_USER', 'nexora_user');
define('DB_PASS', 'strong_password');

define('SITE_URL', 'https://yourdomain.com');
define('ADMIN_EMAIL', 'admin@yourdomain.com');

// Disable error display
error_reporting(0);
ini_set('display_errors', 0);
```

#### 5. Set File Permissions

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/html/nexora/

# Set directory permissions
find /var/www/html/nexora/ -type d -exec chmod 755 {} \;

# Set file permissions
find /var/www/html/nexora/ -type f -exec chmod 644 {} \;

# Protect config file
chmod 600 /var/www/html/nexora/php/config.php
```

#### 6. Configure Apache Virtual Host

```apache
# /etc/apache2/sites-available/nexora.conf
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /var/www/html/nexora
    
    <Directory /var/www/html/nexora>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/nexora_error.log
    CustomLog ${APACHE_LOG_DIR}/nexora_access.log combined
</VirtualHost>
```

```bash
# Enable site
sudo a2ensite nexora.conf
sudo systemctl reload apache2
```

#### 7. Install SSL Certificate (Let's Encrypt)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-apache -y

# Get certificate
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com

# Auto-renewal is configured automatically
# Test renewal:
sudo certbot renew --dry-run
```

#### 8. Create .htaccess Security Rules

```apache
# .htaccess in root directory
# Enable rewrite engine
RewriteEngine On

# Force HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Security headers
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>

# Protect sensitive files
<FilesMatch "\.(sql|md|log|ini)$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Protect config
<Files "config.php">
    Order allow,deny
    Deny from all
</Files>

# Directory listing
Options -Indexes
```

#### 9. Configure Automated Backups

```bash
# Create backup script
sudo nano /usr/local/bin/nexora_backup.sh

#!/bin/bash
DATE=$(date +%Y%m%d)
mysqldump -u nexora_user -p'password' nexora_db | gzip > /backups/nexora_$DATE.sql.gz
find /backups/ -name "*.sql.gz" -mtime +30 -delete

# Make executable
sudo chmod +x /usr/local/bin/nexora_backup.sh

# Add to crontab
sudo crontab -e
0 2 * * * /usr/local/bin/nexora_backup.sh
```

#### 10. Post-Deployment Testing

```bash
# Test website
curl -I https://yourdomain.com

# Test admin panel
curl -I https://yourdomain.com/admin/

# Test database connection
mysql -u nexora_user -p nexora_db -e "SELECT COUNT(*) FROM admin_users;"

# Check logs
tail -f /var/log/apache2/nexora_error.log
```

### Post-Deployment Security

1. **Change Default Credentials**
   - Login: https://yourdomain.com/admin/
   - Go to My Settings → Change Password

2. **Update System Settings**
   - Admin Panel → System Settings
   - Update all site URLs
   - Configure email settings
   - Enable security features

3. **Set Up Monitoring**
   - Configure uptime monitoring
   - Set up error alerts
   - Monitor disk space
   - Watch for security issues

4. **Configure Backups**
   - Verify automated backups work
   - Test restoration process
   - Store backups off-site

---

## 🔍 Troubleshooting

### Common Issues and Solutions

#### Issue: Cannot connect to database

**Symptoms:**
- "Connection failed" error
- Website shows database error
- Admin panel won't load

**Solutions:**

```bash
# 1. Check MySQL is running
sudo systemctl status mysql

# If stopped, start it:
sudo systemctl start mysql

# 2. Verify credentials in config.php
cat php/config.php | grep DB_

# 3. Test connection manually
mysql -u nexora_user -p nexora_db

# 4. Check user permissions
mysql -u root -p -e "SHOW GRANTS FOR 'nexora_user'@'localhost';"

# 5. Reset user password
mysql -u root -p -e "ALTER USER 'nexora_user'@'localhost' IDENTIFIED BY 'new_password';"
```

#### Issue: Contact form not working

**Symptoms:**
- Form doesn't submit
- No success message
- Data not saved to database

**Solutions:**

```javascript
// 1. Check browser console for errors (F12)
// Look for JavaScript errors or AJAX failures

// 2. Check PHP error log
tail -f /var/log/apache2/error.log

// 3. Verify database table exists
mysql -u root -p nexora_db -e "DESCRIBE contact_messages;"

// 4. Test PHP file directly
// Visit: http://yoursite.com/php/contact.php
// Should return JSON error (no POST data)

// 5. Check file permissions
ls -l php/contact.php
# Should be readable: -rw-r--r--
```

#### Issue: Emails not sending

**Symptoms:**
- Form submits successfully
- Data saved to database
- But no email received

**Solutions:**

```php
// 1. Test PHP mail function
<?php
$result = mail('test@example.com', 'Test', 'Test email');
echo $result ? 'Mail sent' : 'Mail failed';
?>

// 2. Check mail logs
tail -f /var/log/mail.log

// 3. Verify SMTP configuration (if using SMTP)
// Check credentials in config.php

// 4. Test with different email provider
// Try Gmail instead of Outlook, or vice versa

// 5. Check spam folder

// 6. Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', '/tmp/php-errors.log');
```

#### Issue: Admin panel shows 403 Forbidden

**Symptoms:**
- Can't access /admin/
- Shows "Forbidden" error

**Solutions:**

```bash
# 1. Check file permissions
ls -la admin/

# 2. Fix permissions
chmod 755 admin/
chmod 644 admin/*.php

# 3. Check ownership
sudo chown -R www-data:www-data admin/

# 4. Check .htaccess
cat admin/.htaccess
# Should not have "Deny from all"

# 5. Check Apache configuration
# Make sure AllowOverride is set to All
```

#### Issue: Images/CSS not loading

**Symptoms:**
- Website displays but looks broken
- No images, no styling

**Solutions:**

```bash
# 1. Check file paths in HTML
# Open page source, verify CSS/JS URLs

# 2. Check file permissions
ls -l css/style.css
ls -l js/main.js

# 3. Clear browser cache
# Ctrl+Shift+Delete or Cmd+Shift+Delete

# 4. Check for 404 errors
# Open browser console (F12) → Network tab

# 5. Verify file exists
ls -la css/style.css
```

#### Issue: Session errors / Can't stay logged in

**Symptoms:**
- Logged out immediately after login
- "Session expired" messages

**Solutions:**

```php
// 1. Check PHP session configuration
<?php phpinfo(); ?>
// Look for session.save_path

// 2. Verify session directory exists and is writable
ls -la /var/lib/php/sessions/

// 3. Fix session directory permissions
sudo chmod 1733 /var/lib/php/sessions/

// 4. Check session settings in php.ini
sudo nano /etc/php/7.4/apache2/php.ini
// session.cookie_secure = 0 (or 1 for HTTPS)
// session.cookie_httponly = 1

// 5. Clear browser cookies and try again
```

#### Issue: Database tables missing

**Symptoms:**
- "Table doesn't exist" errors
- Empty admin panel

**Solutions:**

```bash
# 1. Check which tables exist
mysql -u root -p nexora_db -e "SHOW TABLES;"

# 2. Re-import Complete.sql
mysql -u root -p nexora_db < Sql/Complete.sql

# 3. Verify import
mysql -u root -p nexora_db -e "SELECT COUNT(*) FROM admin_users;"

# 4. Check for import errors
# Look at MySQL error log
tail -f /var/log/mysql/error.log
```

#### Issue: Slow performance

**Symptoms:**
- Pages load slowly
- Database queries take long time

**Solutions:**

```sql
-- 1. Check for missing indexes
SELECT 
    TABLE_NAME,
    CONCAT('ALTER TABLE ', TABLE_NAME, ' ADD INDEX idx_', COLUMN_NAME, ' (', COLUMN_NAME, ');') AS AddIndex
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'nexora_db'
AND COLUMN_KEY = ''
AND COLUMN_NAME IN ('user_id', 'status', 'created_at');

-- 2. Optimize tables
OPTIMIZE TABLE contact_messages, newsletter_subscribers, admin_activity_logs;

-- 3. Analyze slow queries
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 2;

-- Check slow query log
tail -f /var/log/mysql/slow-query.log

-- 4. Check table sizes
SELECT 
    TABLE_NAME,
    ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) AS 'Size (MB)'
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'nexora_db'
ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC;
```

```bash
# 5. Enable PHP OpCache
sudo nano /etc/php/7.4/apache2/php.ini
# Add:
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000

# Restart Apache
sudo systemctl restart apache2
```

---

## ✅ Best Practices

### Security Best Practices

1. **Keep Software Updated**
   - Update PHP regularly
   - Update MySQL regularly
   - Monitor security bulletins

2. **Use Strong Passwords**
   - Minimum 12 characters
   - Mix of uppercase, lowercase, numbers, symbols
   - Change every 90 days
   - Never reuse passwords

3. **Implement 2FA** (Future Enhancement)
   - Add Google Authenticator support
   - SMS verification for sensitive actions

4. **Regular Security Audits**
   - Review admin users monthly
   - Check activity logs for anomalies
   - Monitor login attempts
   - Review file permissions

5. **Backup Strategy**
   - Daily automated backups
   - Weekly off-site backups
   - Monthly full system backups
   - Test restoration quarterly

### Development Best Practices

1. **Use Version Control**
   ```bash
   git init
   git add .
   git commit -m "Initial commit"
   git branch develop
   ```

2. **Follow Coding Standards**
   - PSR-12 for PHP
   - ESLint for JavaScript
   - Consistent naming conventions

3. **Comment Your Code**
   - Explain complex logic
   - Document function parameters
   - Add TODO comments for future work

4. **Test Before Deploying**
   - Test on staging server first
   - Run all functionality tests
   - Check error logs
   - Verify backups work

5. **Use Environment Variables**
   ```php
   // .env file (never commit to Git!)
   DB_HOST=localhost
   DB_NAME=nexora_db
   DB_USER=nexora_user
   DB_PASS=secret_password
   ```

### Performance Best Practices

1. **Database Optimization**
   - Add indexes to frequently queried columns
   - Use EXPLAIN to analyze queries
   - Limit result sets
   - Cache repeated queries

2. **Frontend Optimization**
   - Minify CSS and JavaScript
   - Compress images
   - Use lazy loading
   - Enable browser caching

3. **Server Optimization**
   - Enable Gzip compression
   - Use PHP OpCache
   - Configure MySQL query cache
   - Set appropriate PHP memory limits

4. **Monitoring**
   - Set up uptime monitoring
   - Monitor server resources
   - Track error rates
   - Monitor response times

### User Experience Best Practices

1. **Clear Feedback**
   - Show success messages
   - Display error messages clearly
   - Use loading indicators
   - Provide confirmation dialogs

2. **Responsive Design**
   - Test on mobile devices
   - Ensure touch targets are large enough
   - Optimize for slow connections

3. **Accessibility**
   - Use semantic HTML
   - Add alt text to images
   - Ensure keyboard navigation works
   - Use ARIA labels

4. **Documentation**
   - Keep documentation updated
   - Include screenshots
   - Provide examples
   - Write for different skill levels

---

## 📞 Support & Resources

### Documentation Files

- **README.md** - Project overview and quick start
- **Doc/ADMIN_FEATURES.md** - Complete feature documentation (530+ lines)
- **Doc/ADMIN_QUICK_REFERENCE.md** - Quick reference guide (480+ lines)
- **Doc/CHANGELOG_V2.md** - Version history (450+ lines)
- **Doc/UPDATE_SUMMARY_V2.md** - User-friendly overview (420+ lines)
- **Doc/ADMIN_INSTALLATION.md** - Installation guide
- **Doc/DEPLOYMENT.md** - Deployment checklist
- **Doc/TESTING_GUIDE.md** - Testing procedures
- **php/README.md** - PHP backend documentation

### Useful SQL Queries

```sql
-- Database health check
SELECT 
    TABLE_NAME,
    TABLE_ROWS,
    ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) AS 'Size_MB'
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'nexora_db';

-- Recent admin activity
SELECT u.username, l.action, l.table_name, l.created_at
FROM admin_activity_logs l
JOIN admin_users u ON l.user_id = u.id
ORDER BY l.created_at DESC
LIMIT 20;

-- Newsletter growth
SELECT 
    DATE(created_at) as date,
    COUNT(*) as subscribers
FROM newsletter_subscribers
WHERE status = 'active'
AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY DATE(created_at)
ORDER BY date;

-- Contact message statistics
SELECT 
    status,
    COUNT(*) as count,
    ROUND(COUNT(*) * 100.0 / SUM(COUNT(*)) OVER (), 2) as percentage
FROM contact_messages
GROUP BY status;
```

### Contact Information

**NEXORA IT Support**
- **Email:** nexorait@outlook.com
- **Phone:** +94 77 635 0902
- **WhatsApp:** +94 70 671 7131
- **Address:** 218 Doalakanda, Dehiaththakandiya, Sri Lanka

### Additional Resources

- **MySQL Documentation:** https://dev.mysql.com/doc/
- **PHP Documentation:** https://www.php.net/docs.php
- **Apache Documentation:** https://httpd.apache.org/docs/
- **Security Best Practices:** https://owasp.org/

---

## 📊 Project Statistics Summary

### Code Metrics
- **Total Files:** 50+ files
- **PHP Code:** 4,000+ lines
- **CSS Code:** 2,000+ lines
- **JavaScript Code:** 700+ lines
- **SQL Code:** 700+ lines (Complete.sql: 724 lines)
- **Documentation:** 1,880+ lines across 10 files
- **Total Lines of Code:** 9,280+ lines

### Database Metrics
- **Tables:** 12 core tables
- **Views:** 3 analytical views
- **Stored Procedures:** 2 procedures
- **Triggers:** 4 automatic triggers
- **Foreign Keys:** 52 constraints
- **Indexes:** 35+ indexes
- **Estimated Size:** 2-5 MB (empty), 10-50 MB (typical usage)

### Feature Metrics
- **Admin Features:** 7 major features
- **Enhancements:** 20+ improvements
- **Security Features:** 10+ security measures
- **API Endpoints:** 15+ AJAX endpoints
- **Email Templates:** 3 default templates
- **User Roles:** 3 roles (Super Admin, Admin, Viewer)

---

**Document Version:** 2.0.1  
**Last Updated:** January 10, 2026  
**Maintained By:** NEXORA IT Development Team  

---

*For the latest version of this document and updates, check the `/Doc/` folder in your project.*
