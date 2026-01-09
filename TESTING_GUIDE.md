# System Test & Verification Guide

## Pre-Installation Checklist

Before testing, ensure:
- [ ] Web server (Apache/Nginx) is running
- [ ] MySQL/MariaDB is running
- [ ] PHP 7.4+ is installed
- [ ] PDO MySQL extension is enabled
- [ ] Database credentials in `php/config.php` are correct

## Installation Steps

### Step 1: Create Database
```bash
# Open MySQL command line or phpMyAdmin
mysql -u root -p
```

```sql
-- Create database
CREATE DATABASE IF NOT EXISTS nexora_db 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Verify database created
SHOW DATABASES LIKE 'nexora_db';
```

### Step 2: Import Database Structure
```bash
# Option 1: Command line
mysql -u root -p nexora_db < php/database_setup.sql

# Option 2: phpMyAdmin
# 1. Select nexora_db database
# 2. Click Import tab
# 3. Choose php/database_setup.sql
# 4. Click Go
```

### Step 3: Import Admin Setup
```bash
mysql -u root -p nexora_db < php/admin_setup.sql
```

### Step 4: Verify Tables Created
```sql
USE nexora_db;

-- Check all tables exist
SHOW TABLES;

-- Should show:
-- admin_activity_log
-- admin_users
-- contact_messages
-- login_attempts
-- newsletter_subscribers
-- team_members

-- Verify team_members structure
DESCRIBE team_members;

-- Check sample data
SELECT * FROM team_members;
```

## Testing Checklist

### 1. Database Tests ✅

#### Test: Team Members Table
```sql
-- Count team members
SELECT COUNT(*) FROM team_members;
-- Expected: 4 rows

-- Verify active members
SELECT name, position, status FROM team_members WHERE status = 'active';
-- Expected: 4 active members

-- Test display order
SELECT name, display_order FROM team_members ORDER BY display_order;
-- Expected: Ordered by display_order (1, 2, 3, 4)
```

#### Test: Views
```sql
-- Test team stats view
SELECT * FROM team_stats;
-- Expected: total_members=4, active_members=4, inactive_members=0

-- Test newsletter stats
SELECT * FROM newsletter_stats;

-- Test dashboard stats
SELECT * FROM dashboard_stats;
```

### 2. Admin Panel Tests ✅

#### Test: Admin Login
1. Navigate to: `http://localhost/admin/` or `http://yoursite.com/admin/`
2. Default credentials:
   - Username: `admin`
   - Password: `admin123`
3. **Action**: Change password immediately after first login!

#### Test: Dashboard
1. Login to admin panel
2. Verify dashboard displays:
   - ✅ 5 stat cards (Messages, Subscribers, Today's, Team Members)
   - ✅ Team Members count shows 4
   - ✅ Recent messages table
   - ✅ Recent subscribers table
   - ✅ Quick statistics section

#### Test: Team Management Page
1. Click "Team Management" in sidebar
2. Verify:
   - ✅ Stats cards show: Total=4, Active=4, Inactive=0
   - ✅ 4 team member cards displayed
   - ✅ Each card shows: photo, name, position, bio, social links
   - ✅ Each card has Edit and Delete buttons
   - ✅ "Add Team Member" button visible at top

#### Test: Add Team Member
1. Click "Add Team Member" button
2. Fill form:
   - Name: `Test User`
   - Position: `Test Developer`
   - Bio: `This is a test member`
   - Image URL: `https://via.placeholder.com/400x400?text=Test`
   - LinkedIn: `https://linkedin.com/in/test`
   - Display Order: `5`
   - Status: `Active`
3. Click "Save"
4. Verify:
   - ✅ Success message appears
   - ✅ Page reloads
   - ✅ New member appears in grid
   - ✅ Stats updated (Total=5, Active=5)

#### Test: Edit Team Member
1. Find the test member card
2. Click "Edit" button
3. Modify:
   - Position: `Senior Test Developer`
   - Bio: `Updated test member bio`
4. Click "Save"
5. Verify:
   - ✅ Success message appears
   - ✅ Changes reflected in card

#### Test: Image Preview
1. Click "Add Team Member"
2. Enter image URL: `https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop`
3. Verify:
   - ✅ Image preview appears below input
   - ✅ Preview updates when URL changes

#### Test: Delete Team Member
1. Find the test member card
2. Click "Delete" button
3. Confirm deletion in popup
4. Verify:
   - ✅ Confirmation dialog appears
   - ✅ Success message after confirming
   - ✅ Member removed from grid
   - ✅ Stats updated (Total=4, Active=4)

#### Test: Status Toggle
1. Edit any team member
2. Change status to "Inactive"
3. Save
4. Verify:
   - ✅ Badge shows "Inactive" in red
   - ✅ Stats updated (Active=3, Inactive=1)
   - ✅ Member doesn't appear on website

### 3. Frontend Tests ✅

#### Test: Team Section Loading
1. Navigate to: `http://localhost/index.html` or `http://yoursite.com/index.html`
2. Scroll to "Meet Our Team" section
3. Verify:
   - ✅ Loading spinner appears briefly
   - ✅ Team members load dynamically
   - ✅ Only active members displayed
   - ✅ Members ordered by display_order

#### Test: Team Member Display
1. Check each team member card:
   - ✅ Photo displays correctly
   - ✅ Name and position shown
   - ✅ Hover effect works (card lifts, shadow increases)
   - ✅ Social icons appear on hover
   - ✅ Social links work (open in new tab)

#### Test: Dynamic Updates
1. In admin panel, set a member to "Inactive"
2. Refresh website homepage
3. Verify:
   - ✅ Inactive member not displayed
   - ✅ Only active members show

#### Test: Display Order
1. In admin, change display orders:
   - Member A: order = 10
   - Member B: order = 5
   - Member C: order = 1
2. Refresh website
3. Verify:
   - ✅ Members display in correct order (C, B, A)

### 4. API Endpoint Tests ✅

#### Test: Public API
```bash
# Test get_team.php endpoint
curl http://localhost/php/get_team.php

# Or open in browser:
http://localhost/php/get_team.php
```

Expected Response:
```json
{
  "success": true,
  "members": [
    {
      "id": "1",
      "name": "John Anderson",
      "position": "Chief Technology Officer",
      "bio": "Leading technology innovation...",
      "image_url": "https://...",
      "linkedin_url": "#",
      "twitter_url": "#",
      "github_url": "#",
      "status": "active",
      "display_order": "1"
    }
  ]
}
```

#### Test: Admin API (requires login)
```bash
# Test team operations (must be logged in as admin)
# Get single member
curl -X GET http://localhost/admin/ajax/team_operations.php?action=get&id=1 \
  -H "Cookie: PHPSESSID=your_session_id"
```

### 5. Error Handling Tests ✅

#### Test: Invalid Image URL
1. Add team member with invalid image URL
2. Verify:
   - ✅ Placeholder image shows
   - ✅ No console errors

#### Test: Database Connection Failure
1. Temporarily modify `php/config.php` with wrong password
2. Load website
3. Verify:
   - ✅ Error message displays gracefully
   - ✅ No sensitive info exposed

#### Test: Missing Required Fields
1. Try to add team member without name
2. Verify:
   - ✅ Form validation prevents submission
   - ✅ Error message shows "required"

### 6. Security Tests ✅

#### Test: Unauthorized Access
1. Logout from admin panel
2. Try to access: `http://localhost/admin/team.php`
3. Verify:
   - ✅ Redirected to login page

#### Test: AJAX Endpoint Protection
```bash
# Try to access without login
curl http://localhost/admin/ajax/team_operations.php?action=list
# Expected: 401 Unauthorized
```

#### Test: SQL Injection Prevention
1. Try to add member with name: `'; DROP TABLE team_members; --`
2. Verify:
   - ✅ Name saved as-is (escaped properly)
   - ✅ No database damage

#### Test: XSS Prevention
1. Add member with name: `<script>alert('XSS')</script>`
2. View on website
3. Verify:
   - ✅ Script not executed
   - ✅ HTML entities escaped

### 7. Responsive Design Tests ✅

#### Test: Mobile View (Admin)
1. Open admin panel
2. Resize browser to mobile width (375px)
3. Verify:
   - ✅ Sidebar collapses/hamburger menu
   - ✅ Team cards stack vertically
   - ✅ Forms are usable
   - ✅ Buttons accessible

#### Test: Mobile View (Frontend)
1. Open website on mobile
2. Check team section
3. Verify:
   - ✅ Grid adjusts to 1-2 columns
   - ✅ Cards remain readable
   - ✅ Images scale properly
   - ✅ Touch interactions work

## Common Issues & Solutions

### Issue: "Table 'team_members' doesn't exist"
**Solution:**
```sql
-- Re-import database
SOURCE php/database_setup.sql;
```

### Issue: Team not loading on website
**Solution:**
1. Check browser console (F12) for errors
2. Verify `php/get_team.php` is accessible
3. Check database connection in `php/config.php`
4. Test API directly: `http://localhost/php/get_team.php`

### Issue: Cannot login to admin
**Solution:**
```sql
-- Reset admin password
USE nexora_db;
UPDATE admin_users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE username = 'admin';
-- Password reset to: admin123
```

### Issue: Changes not appearing on website
**Solution:**
1. Hard refresh browser (Ctrl+F5)
2. Clear browser cache
3. Check member status is "active"
4. Verify JavaScript console for errors

### Issue: Images not loading
**Solution:**
1. Test image URL in browser address bar
2. Check if site uses HTTPS (image must also be HTTPS)
3. Verify image host allows hotlinking
4. Use absolute URLs, not relative paths

## Performance Tests ✅

### Test: Page Load Speed
- Dashboard should load in < 2 seconds
- Team page should load in < 2 seconds
- Frontend team section should load in < 1 second

### Test: Database Queries
```sql
-- Check query performance
EXPLAIN SELECT * FROM team_members WHERE status = 'active' ORDER BY display_order;
-- Should use indexes
```

### Test: Large Dataset
```sql
-- Add 50 test members
INSERT INTO team_members (name, position, image_url, display_order, status, created_at)
SELECT 
    CONCAT('Test User ', n),
    'Test Position',
    'https://via.placeholder.com/400x400',
    n,
    'active',
    NOW()
FROM (
    SELECT (@row := @row + 1) as n
    FROM information_schema.tables, (SELECT @row := 4) r
    LIMIT 50
) numbers;

-- Test page load with 54 members
-- Should still load quickly with pagination
```

## Final Verification

All systems operational when:
- ✅ Database tables created successfully
- ✅ Sample data inserted (4 team members)
- ✅ Admin login works
- ✅ Dashboard shows team statistics
- ✅ Team management page displays all members
- ✅ Can add, edit, delete team members
- ✅ Website displays active team members dynamically
- ✅ All social links work
- ✅ Images display correctly
- ✅ Responsive on mobile devices
- ✅ No console errors
- ✅ Security measures in place

## Next Steps After Testing

1. **Change Admin Password**
   ```sql
   -- In admin panel, go to Settings and change password
   -- Or use this SQL:
   UPDATE admin_users 
   SET password = PASSWORD_HASH('your_new_secure_password', PASSWORD_DEFAULT)
   WHERE username = 'admin';
   ```

2. **Add Real Team Members**
   - Delete or edit sample members
   - Add your actual team
   - Use real photos (400x400px recommended)
   - Add real social media links

3. **Backup Database**
   ```bash
   mysqldump -u root -p nexora_db > backup_$(date +%Y%m%d).sql
   ```

4. **Monitor Logs**
   - Check PHP error logs
   - Monitor MySQL slow query log
   - Check browser console for JS errors

5. **Performance Optimization**
   - Enable PHP OPcache
   - Enable MySQL query cache
   - Optimize images (compress to < 500KB)
   - Enable browser caching

## Support

If any test fails:
1. Check PHP error log: `/var/log/php/error.log` (or check xampp/logs/)
2. Check MySQL error log
3. Browser console (F12) for JavaScript errors
4. Verify file permissions (755 for directories, 644 for files)
5. Contact: nexorait@outlook.com

---

**Testing Complete!** 🎉  
Document all test results and proceed with deployment.
