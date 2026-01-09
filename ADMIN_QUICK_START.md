# 🚀 NEXORA ADMIN PANEL - QUICK START GUIDE

## ⚡ Fast Setup (5 Minutes)

### Step 1: Import Admin Database (1 minute)
```bash
mysql -u root -p nexora_db < php/admin_setup.sql
```
**OR using phpMyAdmin:**
1. Open phpMyAdmin
2. Select `nexora_db` database
3. Click "Import"
4. Choose `php/admin_setup.sql`
5. Click "Go"

✅ This creates admin users table and default login

### Step 2: Access Admin Panel (30 seconds)
Open in browser:
```
http://localhost/admin/
or
http://yourwebsite.com/admin/
```

### Step 3: Login with Default Credentials (30 seconds)
- **Username:** `admin`
- **Password:** `admin123`

### Step 4: Change Password IMMEDIATELY! (1 minute)
1. After login, click "Settings" in sidebar
2. Enter current password: `admin123`
3. Enter new strong password (min 6 characters)
4. Confirm new password
5. Click "Change Password"

### Step 5: Start Managing Data (2 minutes)
- **Dashboard** - View statistics
- **Contact Messages** - See all form submissions
- **Newsletter Subscribers** - Manage email list

## 🎯 Common Admin Tasks

### View New Contact Messages
1. Click "Contact Messages" in sidebar
2. New messages shown with blue "new" badge
3. Click eye icon (👁️) to view full message
4. Status automatically changes to "read"

### Export Data to CSV
1. Go to "Contact Messages" or "Newsletter Subscribers"
2. Click "Export CSV" button (top right)
3. File downloads automatically

### Search & Filter
- Use search box to find specific messages/emails
- Use status dropdown to filter by status
- Click "Clear Filters" to reset

### Update Message Status
1. Find the message in list
2. Click edit icon (✏️)
3. Enter new status: `read`, `replied`, or `archived`
4. Click OK

## 📊 Dashboard Overview

**Statistics Cards:**
- 🔵 New Messages - Unread contact form submissions
- 🟢 Active Subscribers - Current newsletter subscribers
- 🟠 Today's Messages - Messages received today
- 🟣 Today's Subscribers - New subscribers today

**Recent Activity:**
- Last 5 contact messages
- Last 5 newsletter subscribers
- Quick stats summary

## 🔐 Security Reminders

✅ **DO:**
- Change default password immediately
- Use strong passwords (12+ characters)
- Logout when finished
- Regularly review login attempts
- Export data for backups

❌ **DON'T:**
- Share admin credentials
- Use default password in production
- Leave admin panel open unattended
- Use simple passwords

## 🛠️ Troubleshooting

### Can't Login?
**Reset password using SQL:**
```sql
UPDATE admin_users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE username = 'admin';
```
This resets password to: `admin123`

### No Data Showing?
1. Check if main database was imported: `database_setup.sql`
2. Verify website forms are submitting data
3. Check database connection in `php/config.php`

### Page Not Loading?
1. Verify all files uploaded correctly
2. Check file permissions
3. Check PHP error logs

## 📞 Need Help?

**Full Documentation:** See `admin/README.md`

**Contact Support:**
- Email: nexorait@outlook.com
- Phone: +94 77 635 0902 / +94 70 671 7131

## ✨ Quick Tips

1. **Daily Tasks:**
   - Check Dashboard for new messages
   - Respond to contact inquiries
   - Monitor subscriber growth

2. **Weekly Tasks:**
   - Export data for backups
   - Review login attempts
   - Clean up old/archived messages

3. **Monthly Tasks:**
   - Change admin password
   - Optimize database tables
   - Review and update subscriber list

---

**Admin Panel v1.0** | January 2026
