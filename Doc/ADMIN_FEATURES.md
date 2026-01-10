# Admin Features Documentation

## Overview
This document describes all the advanced admin panel features for NEXORA IT project.

## Table of Contents
1. [Bulk Operations](#bulk-operations)
2. [Admin User Management](#admin-user-management)
3. [Activity Logs](#activity-logs)
4. [System Information](#system-information)
5. [Email Templates](#email-templates)
6. [System Settings](#system-settings)
7. [Notifications](#notifications)

---

## Bulk Operations

### Features
- **Select Multiple Items**: Use checkboxes to select multiple contacts or subscribers
- **Bulk Actions**: Perform actions on multiple items at once
- **Supported Actions**:
  - **Contacts**: Mark as Read, Archive, Delete
  - **Subscribers**: Activate, Unsubscribe, Delete

### How to Use
1. Navigate to Contacts or Subscribers page
2. Select items using checkboxes
3. Click "Bulk Actions" button
4. Choose desired action
5. Confirm the operation

### Technical Details
- Backend: `admin/ajax/bulk_operations.php`
- JavaScript functions handle UI interactions
- AJAX calls for async processing
- Activity logging for all bulk operations

---

## Admin User Management

### Access Level
- **Super Admin Only**: Only super admins can manage admin users

### Features
- **User Roles**:
  - `super_admin`: Full access to all features
  - `admin`: Standard admin access (cannot manage users)
  - `viewer`: Read-only access

- **User Management**:
  - Add new admin users
  - Toggle active/inactive status
  - Delete admin users
  - View last login information

### How to Use
1. Navigate to **Admin Tools > Admin Users**
2. Click "Add Admin User" to create new user
3. Fill in required information:
   - Username (unique)
   - Email
   - Full Name
   - Password (min 8 chars)
   - Role
4. Toggle status or delete as needed

### Security
- Passwords are hashed using `password_hash()`
- Super admin role required for access
- Activity logging for all user management actions
- Cannot delete yourself

---

## Activity Logs

### Features
- **Comprehensive Logging**: All admin actions are logged
- **Filtering Options**:
  - By Admin User
  - By Action Type
  - By Date Range
- **Export**: Export logs to CSV
- **Pagination**: 50 records per page

### Logged Actions
- Admin login/logout
- Contact message updates
- Subscriber management
- Team member changes
- Admin user management
- System settings changes
- Bulk operations
- Email template modifications

### How to Use
1. Navigate to **Admin Tools > Activity Logs**
2. Use filters to narrow down results
3. Click "Export Logs" to download CSV
4. View detailed information for each action

### Technical Details
- Table: `admin_activity_logs`
- Automatic logging via `logAdminActivity()` function
- Includes IP address and timestamp
- 90-day retention recommended

---

## System Information

### Features
- **Database Statistics**:
  - Contact messages count
  - Newsletter subscribers count
  - Team members count
  - Admin users count
  - Activity logs count
  - Database size

- **PHP Configuration**:
  - PHP version
  - Memory limit
  - Max upload size
  - Max execution time
  - Loaded extensions

- **Server Information**:
  - Server software
  - Document root
  - Server IP
  - Operating system

- **Recent Activity**:
  - Last 10 admin actions
  - Timestamp and user info

### How to Use
1. Navigate to **Admin Tools > System Info**
2. View real-time system statistics
3. Check PHP and server configuration
4. Monitor recent admin activity

### Use Cases
- Troubleshooting issues
- Checking server capabilities
- Monitoring system health
- Verifying configuration

---

## Email Templates

### Access Level
- **Admin and Super Admin**: Can manage email templates

### Features
- **Template Types**:
  - Contact Reply
  - Newsletter
  - Welcome
  - Notification
  - Custom

- **Variable Support**:
  - Use `{{variable}}` for dynamic content
  - Common variables: `{{name}}`, `{{email}}`, `{{subject}}`, `{{message}}`

- **Template Management**:
  - Create new templates
  - Edit existing templates
  - Preview templates
  - Toggle active/inactive status
  - Delete templates

### How to Use
1. Navigate to **Content > Email Templates**
2. Click "Add Template"
3. Fill in template details:
   - Template Name
   - Template Type
   - Subject (with variables)
   - Body (with variables)
   - Available variables list
4. Save and activate template

### Variables
Common variables that can be used:
- `{{name}}` - Recipient name
- `{{email}}` - Recipient email
- `{{subject}}` - Message subject
- `{{message}}` - Message content
- `{{custom_message}}` - Custom admin message

### Example Template
```
Subject: Re: {{subject}}

Dear {{name}},

Thank you for contacting NEXORA IT.

{{custom_message}}

Best regards,
NEXORA IT Team
```

---

## System Settings

### Access Level
- **Super Admin Only**: Only super admins can modify system settings

### Setting Categories

#### General Settings
- `site_name`: Website name
- `site_email`: Primary contact email
- `site_phone`: Contact phone number
- `maintenance_mode`: Enable/disable maintenance mode

#### Feature Settings
- `enable_newsletter`: Enable newsletter subscription
- `enable_contact_form`: Enable contact form

#### File Settings
- `max_upload_size`: Maximum file upload size (bytes)
- `allowed_file_types`: Allowed file extensions (JSON array)

#### Display Settings
- `items_per_page`: Items per page in admin lists

#### Security Settings
- `session_timeout`: Session timeout in seconds
- `enable_recaptcha`: Enable Google reCAPTCHA

#### Backup Settings
- `auto_backup_enabled`: Enable automatic backups
- `backup_frequency`: Backup frequency in days

### How to Use
1. Navigate to **Admin Tools > System Settings**
2. Modify settings as needed
3. Settings are grouped by category
4. Click "Save All Settings" to apply changes
5. Changes take effect immediately

### Setting Types
- **String**: Text values
- **Number**: Numeric values
- **Boolean**: Toggle switches (on/off)
- **JSON**: Structured data (arrays/objects)

---

## Notifications

### Features
- **Real-time Notifications**: Get notified of important events
- **Notification Types**:
  - Info (blue)
  - Success (green)
  - Warning (orange)
  - Error (red)

- **Notification Actions**:
  - Mark as read
  - Mark all as read
  - Delete notification
  - Clear all read notifications
  - Action links (direct to related page)

### Automatic Notifications

#### New Contact Message
- Triggered when new contact form is submitted
- Sent to all active admins
- Includes sender info and subject
- Link to view message

#### New Subscriber
- Triggered when new newsletter subscription
- Sent to all active admins
- Includes subscriber email
- Link to subscribers page

#### Team Updates
- Triggered when team member is added/updated/deleted
- Includes member name and action

#### System Events
- Sent to super admins only
- Includes system errors, backups, etc.

### How to Use
1. Navigate to **Content > Notifications**
2. View unread badge count in sidebar
3. Use filter tabs to view specific types
4. Click notification to see details
5. Mark as read or delete as needed

### Filter Options
- All notifications
- Unread only
- Read only
- By type (info/success/warning/error)

---

## Database Schema

### New Tables

#### email_templates
- `id` - Primary key
- `name` - Template name
- `subject` - Email subject
- `body` - Email body
- `template_type` - Template category
- `variables` - Available variables (JSON)
- `is_active` - Active status
- `created_by` - Creator admin ID
- `created_at`, `updated_at` - Timestamps

#### system_settings
- `id` - Primary key
- `setting_key` - Unique setting identifier
- `setting_value` - Setting value
- `setting_type` - Data type (string/number/boolean/json)
- `category` - Setting category
- `description` - Setting description
- `is_public` - Can be accessed from frontend
- `updated_by` - Last updater admin ID
- `updated_at` - Last update timestamp

#### admin_notifications
- `id` - Primary key
- `admin_id` - Target admin user
- `title` - Notification title
- `message` - Notification message
- `notification_type` - Type (info/success/warning/error)
- `is_read` - Read status
- `action_url` - Optional action URL
- `created_at`, `read_at` - Timestamps

#### backup_history
- `id` - Primary key
- `backup_name` - Backup file name
- `backup_path` - File path
- `backup_type` - Type (full/database/files)
- `file_size` - Backup size in bytes
- `status` - Status (success/failed/in_progress)
- `error_message` - Error details if failed
- `created_by` - Creator admin ID
- `created_at` - Creation timestamp

#### dashboard_chart_data
- `id` - Primary key
- `date` - Data date
- `messages_count` - Total messages
- `subscribers_count` - Total active subscribers
- `new_messages` - New messages that day
- `new_subscribers` - New subscribers that day

---

## API Endpoints

### Bulk Operations
**Endpoint**: `admin/ajax/bulk_operations.php`
**Method**: POST
**Parameters**:
- `action`: Action to perform (delete/mark_read/archive/activate/unsubscribe)
- `type`: Target type (contacts/subscribers)
- `ids[]`: Array of IDs to process

**Response**:
```json
{
  "success": true,
  "message": "3 items processed successfully",
  "count": 3
}
```

---

## Helper Functions

### Notification Functions (`admin/includes/notifications.php`)

#### createNotification()
Create a notification for specific or all admins
```php
createNotification($adminId, $title, $message, $type, $actionUrl);
```

#### notifyNewContact()
Notify admins of new contact message
```php
notifyNewContact($contactData);
```

#### notifyNewSubscriber()
Notify admins of new subscriber
```php
notifyNewSubscriber($subscriberData);
```

#### getUnreadNotificationCount()
Get unread notification count for admin
```php
$count = getUnreadNotificationCount($adminId);
```

---

## Security Considerations

1. **Role-Based Access**: Features restricted by admin role
2. **Password Hashing**: All passwords use bcrypt
3. **CSRF Protection**: Session-based authentication
4. **SQL Injection Prevention**: Prepared statements used throughout
5. **XSS Protection**: All output is HTML-escaped
6. **Activity Logging**: All actions are logged with IP address
7. **Rate Limiting**: Contact and newsletter forms are rate-limited
8. **Input Validation**: All user input is validated and sanitized

---

## Installation

1. **Run SQL Script**:
   ```bash
   mysql -u username -p database_name < Sql/admin_features_update.sql
   ```

2. **Verify Tables Created**:
   - email_templates
   - system_settings
   - admin_notifications
   - backup_history
   - dashboard_chart_data

3. **Test Features**:
   - Login as super admin
   - Access all new features
   - Verify permissions work correctly

---

## Troubleshooting

### Notifications Not Appearing
- Check `admin_notifications` table exists
- Verify notification creation in PHP error log
- Check admin user is active
- Verify session has correct admin_id

### Bulk Operations Not Working
- Check JavaScript console for errors
- Verify `bulk_operations.php` is accessible
- Check PHP error log for backend errors
- Ensure database connection is working

### Templates Not Saving
- Verify `email_templates` table exists
- Check user has admin or super_admin role
- Review PHP error log
- Check for unique constraint violations

### Settings Not Loading
- Verify `system_settings` table exists
- Check user has super_admin role
- Ensure default settings were inserted
- Review PHP error log

---

## Maintenance

### Regular Tasks

#### Clean Old Notifications (Monthly)
```php
require_once 'admin/includes/notifications.php';
deleteOldNotifications(30); // Delete read notifications older than 30 days
```

#### Backup Database (Weekly)
Use cron job or manual backup:
```bash
mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql
```

#### Review Activity Logs (Weekly)
- Check for suspicious activity
- Monitor failed login attempts
- Review bulk operations

#### Update System Settings (As Needed)
- Review and update contact information
- Adjust rate limits if needed
- Update file upload limits

---

## Future Enhancements

Potential features for future updates:
- [ ] Dashboard charts and analytics
- [ ] Email sending functionality
- [ ] Automated backups
- [ ] Two-factor authentication
- [ ] API access tokens
- [ ] Advanced reporting
- [ ] User permissions granularity
- [ ] Scheduled notifications
- [ ] Custom fields for contacts
- [ ] CRM integration

---

## Support

For issues or questions:
1. Check PHP error log: `error_log()`
2. Review activity logs in admin panel
3. Check database connectivity
4. Verify file permissions
5. Review this documentation

---

**Version**: 2.0  
**Last Updated**: 2024  
**Author**: NEXORA IT Development Team
