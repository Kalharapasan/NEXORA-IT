# Nexora Admin Panel - Complete Documentation

## 🔐 Admin Panel System

A complete administrative dashboard for managing contact messages and newsletter subscribers from your Nexora website.

## 📁 Admin File Structure

```
admin/
├── login.php                    # Admin login page
├── dashboard.php                # Main admin dashboard
├── contacts.php                 # Contact messages management
├── subscribers.php              # Newsletter subscribers management
├── settings.php                 # Admin profile & settings
├── logout.php                   # Logout handler
├── css/
│   └── admin-style.css         # Complete admin panel styles
├── js/
│   └── admin.js                # Admin panel JavaScript
├── includes/
│   ├── auth.php                # Authentication & session management
│   ├── header.php              # Common admin header
│   └── footer.php              # Common admin footer
└── ajax/
    ├── get_message.php         # AJAX: Get message details
    └── export.php              # AJAX: Export data to CSV
```

## 🚀 Installation & Setup

### Step 1: Database Setup (IMPORTANT!)

Run the admin setup SQL file to create necessary tables:

```bash
# Using MySQL command line
mysql -u root -p nexora_db < php/admin_setup.sql

# OR using phpMyAdmin
# - Open phpMyAdmin
# - Select nexora_db database
# - Click "Import" tab
# - Select php/admin_setup.sql
# - Click "Go"
```

This will create:
- ✅ `admin_users` table (admin user accounts)
- ✅ `login_attempts` table (security tracking)
- ✅ `admin_activity_log` table (audit trail)
- ✅ Default admin user (username: admin, password: admin123)

### Step 2: Access Admin Panel

1. **Open Admin Login:**
   ```
   http://yourwebsite.com/admin/login.php
   ```

2. **Default Credentials:**
   - Username: `admin`
   - Password: `admin123`

3. **⚠️ CRITICAL:** Change the default password immediately after first login!

### Step 3: Configure File Permissions (Linux/Mac)

```bash
# Make sure PHP can write to session directory
chmod 755 admin/
chmod 644 admin/*.php
chmod 755 admin/includes/
chmod 644 admin/includes/*.php
```

## 📋 Admin Panel Features

### 🏠 Dashboard
- **Statistics Overview:**
  - New messages count
  - Active subscribers count
  - Today's messages
  - Today's subscribers
- **Recent Activities:**
  - Last 5 contact messages
  - Last 5 newsletter subscribers
- **Quick Stats:**
  - Total messages
  - Total subscribers

### 📧 Contact Messages Management
- **View All Messages:**
  - Paginated list (20 per page)
  - Search by name, email, subject, or message
  - Filter by status (new, read, replied, archived)
- **Message Actions:**
  - View full message details in modal
  - Update message status
  - Delete messages
  - Export to CSV
- **Status Tracking:**
  - `new` - Unread message (blue badge)
  - `read` - Message has been viewed (purple badge)
  - `replied` - Admin has replied (green badge)
  - `archived` - Archived for records (gray badge)

### 👥 Newsletter Subscribers Management
- **View All Subscribers:**
  - Paginated list (20 per page)
  - Search by email
  - Filter by status (active, unsubscribed, bounced)
- **Subscriber Actions:**
  - Update subscriber status
  - Delete subscribers
  - Export to CSV
- **Status Tracking:**
  - `active` - Currently subscribed (green badge)
  - `unsubscribed` - Opted out (orange badge)
  - `bounced` - Email bounced (red badge)

### ⚙️ Settings
- **Profile Management:**
  - Update full name
  - Change email address
  - View username and role
- **Password Change:**
  - Secure password update
  - Minimum 6 characters required
- **System Information:**
  - PHP version
  - Server information
  - Database status

## 🔐 Security Features

### Authentication & Authorization
- ✅ Secure session management
- ✅ Password hashing (bcrypt)
- ✅ Login attempt tracking
- ✅ Role-based access control (super_admin, admin, viewer)
- ✅ Activity logging for audit trails
- ✅ Auto-logout on inactivity

### Login Security
- Failed login attempts are logged with IP address
- User agent tracking
- Session hijacking prevention
- CSRF protection ready

### Data Security
- SQL injection prevention (PDO prepared statements)
- XSS protection (htmlspecialchars on all outputs)
- Input validation and sanitization
- Secure password storage

## 📊 Data Export

### Export Features
Both Contact Messages and Newsletter Subscribers can be exported:

1. **Export to CSV:**
   - Click "Export CSV" button on respective pages
   - Downloads CSV file with timestamp
   - UTF-8 encoded (Excel compatible)

2. **Contact Messages Export Includes:**
   - ID, Name, Email, Phone
   - Subject, Message Preview
   - Status, IP Address, Date

3. **Newsletter Subscribers Export Includes:**
   - ID, Email, Status
   - IP Address, Subscribed Date, Unsubscribed Date

## 🎨 Admin Panel UI

### Design Features
- Modern, clean interface
- Responsive design (mobile-friendly)
- Color-coded status badges
- Interactive data tables
- Modal popups for details
- Smooth animations

### Mobile Responsive
- Hamburger menu for mobile
- Touch-friendly buttons
- Optimized layout for small screens
- Scrollable tables

## 🔑 Creating Additional Admin Users

### Method 1: Using SQL

```sql
-- Generate password hash in PHP first:
-- password_hash('your_password', PASSWORD_DEFAULT)

INSERT INTO admin_users (username, email, password, full_name, role, created_at)
VALUES (
    'john_admin',
    'john@nexora.com',
    '$2y$10$yourHashedPasswordHere',
    'John Administrator',
    'admin',
    NOW()
);
```

### Method 2: Create Admin Registration Page (Optional)

You can create an `admin/register.php` page (accessible only to super_admin) to add new admins through the UI.

## 🛠️ Common Tasks

### Change Default Admin Password

1. Login with default credentials
2. Go to Settings
3. Enter current password: `admin123`
4. Enter new password (min 6 characters)
5. Confirm new password
6. Click "Change Password"

### View Contact Message Details

1. Go to Contact Messages page
2. Click the eye icon (👁️) on any message
3. Modal popup shows full details
4. Status automatically changes to "read"

### Export Subscriber List

1. Go to Newsletter Subscribers page
2. (Optional) Apply filters for specific status
3. Click "Export CSV" button
4. CSV file downloads automatically

### Mark Messages as Replied

1. Open Contact Messages page
2. Click edit icon (✏️) next to message
3. Enter new status: `replied`
4. Press OK

### Search & Filter

**Contact Messages:**
- Use search box for name, email, subject, or message content
- Use status dropdown to filter by new/read/replied/archived

**Newsletter Subscribers:**
- Use search box for email
- Use status dropdown to filter by active/unsubscribed/bounced

## 📈 Activity Logging

All admin actions are logged in `admin_activity_log` table:
- Login/Logout
- View messages
- Update statuses
- Delete records
- Export data

**View Activity Log:**
```sql
SELECT 
    al.action, 
    al.description, 
    al.created_at,
    au.username
FROM admin_activity_log al
JOIN admin_users au ON al.admin_id = au.id
ORDER BY al.created_at DESC
LIMIT 50;
```

## 🔍 Database Queries

### Get Dashboard Statistics
```sql
SELECT * FROM dashboard_stats;
```

### Get New Messages Count
```sql
SELECT COUNT(*) FROM contact_messages WHERE status = 'new';
```

### Get Active Subscribers
```sql
SELECT email FROM newsletter_subscribers WHERE status = 'active';
```

### Get Recent Activity
```sql
SELECT * FROM recent_activity;
```

## ⚠️ Troubleshooting

### Cannot Login - "Invalid username or password"

**Check:**
1. Verify admin user exists:
   ```sql
   SELECT * FROM admin_users WHERE username = 'admin';
   ```
2. Reset password if needed:
   ```sql
   UPDATE admin_users 
   SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
   WHERE username = 'admin';
   -- This resets password to: admin123
   ```

### Session Errors

**Solution:**
1. Check PHP session configuration
2. Ensure session directory is writable
3. Clear browser cookies and cache

### Database Connection Failed

**Check:**
1. Verify config.php has correct credentials
2. Test database connection:
   ```bash
   mysql -u username -p nexora_db
   ```
3. Check if nexora_db exists

### Page Not Loading Correctly

**Check:**
1. Verify all files uploaded correctly
2. Check file permissions
3. Check PHP error logs:
   ```bash
   tail -f /var/log/apache2/error.log
   ```

### Export Not Working

**Check:**
1. PHP has write permissions
2. Check PHP error logs
3. Verify database queries execute successfully

## 🛡️ Best Practices

### Security
1. ✅ Change default admin password immediately
2. ✅ Use strong passwords (12+ characters, mixed case, numbers, symbols)
3. ✅ Regularly update passwords (every 90 days)
4. ✅ Review login attempts weekly
5. ✅ Delete unused admin accounts
6. ✅ Keep PHP and MySQL updated

### Data Management
1. ✅ Regularly export data for backups
2. ✅ Archive old messages (status: archived)
3. ✅ Clean up bounced email subscribers
4. ✅ Monitor for spam/fake submissions

### Performance
1. ✅ Delete very old messages (1+ year)
2. ✅ Optimize database tables monthly:
   ```sql
   OPTIMIZE TABLE contact_messages;
   OPTIMIZE TABLE newsletter_subscribers;
   ```

## 🚀 Production Deployment

### Pre-Launch Checklist

1. **Security:**
   - [ ] Changed default admin password
   - [ ] Removed any test admin accounts
   - [ ] Enabled HTTPS (SSL certificate)
   - [ ] Updated database passwords

2. **Configuration:**
   - [ ] Updated email addresses in config.php
   - [ ] Tested email delivery
   - [ ] Verified database connection

3. **Testing:**
   - [ ] Tested login functionality
   - [ ] Tested all CRUD operations
   - [ ] Tested export functionality
   - [ ] Tested on mobile devices

## 📞 Support

For admin panel issues:

**Email:** nexorait@outlook.com  
**Phone:** +94 77 635 0902 / +94 70 671 7131

## 🔄 Version History

### Version 1.0
- ✅ Admin authentication system
- ✅ Dashboard with statistics
- ✅ Contact messages management
- ✅ Newsletter subscribers management
- ✅ Settings & profile management
- ✅ Data export (CSV)
- ✅ Activity logging
- ✅ Mobile responsive design

---

**Admin Panel for Nexora**  
*Secure. Efficient. User-Friendly.*

**Last Updated:** January 2026  
**Version:** 1.0
