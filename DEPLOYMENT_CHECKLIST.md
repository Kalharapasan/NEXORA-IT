# ✅ DEPLOYMENT CHECKLIST - Team Management System

## Pre-Deployment

### 1. Database Setup
- [ ] Import `php/database_setup.sql`
- [ ] Import `php/admin_setup.sql`
- [ ] Run `php/verify_setup.sql` to test
- [ ] Verify 4 sample team members exist
- [ ] Confirm admin user exists (username: admin)

### 2. File Verification
- [ ] Verify `admin/team.php` exists
- [ ] Verify `admin/ajax/team_operations.php` exists
- [ ] Verify `php/get_team.php` exists
- [ ] Check file permissions (755 for folders, 644 for files)
- [ ] Verify database credentials in `php/config.php`

### 3. Test Admin Panel
- [ ] Login to `/admin/` with admin/admin123
- [ ] Navigate to Team Management
- [ ] View 4 sample members
- [ ] Add a test member
- [ ] Edit the test member
- [ ] Delete the test member
- [ ] Verify statistics update correctly

### 4. Test Frontend
- [ ] Open `index.html`
- [ ] Scroll to "Meet Our Team" section
- [ ] Verify 4 members load dynamically
- [ ] Check images display correctly
- [ ] Test social media links
- [ ] Verify hover effects work
- [ ] Test on mobile devices

### 5. Security
- [ ] Change admin password from default (admin123)
- [ ] Update database credentials if needed
- [ ] Remove or secure `php/verify_setup.sql` in production
- [ ] Verify `.htaccess` is in place
- [ ] Check session security settings
- [ ] Test unauthorized access (should redirect to login)

## Deployment Steps

### 1. Backup Current System
```bash
# Backup current database
mysqldump -u root -p nexora_db > backup_before_team_system.sql

# Backup current files
tar -czf backup_files_$(date +%Y%m%d).tar.gz .
```

### 2. Deploy to Production

#### Option A: Manual Upload (FTP/SFTP)
1. Upload new files:
   - `admin/team.php`
   - `admin/ajax/team_operations.php`
   - `php/get_team.php`
   
2. Upload modified files:
   - `admin/includes/header.php`
   - `admin/dashboard.php`
   - `admin/css/admin-style.css`
   - `index.html`
   - `js/main.js`
   - `css/style.css`
   
3. Upload SQL files:
   - `php/database_setup.sql` (for reference)
   - `php/verify_setup.sql` (for testing)

#### Option B: Git Deployment
```bash
git add .
git commit -m "Add team management system with full CRUD operations"
git push origin main
```

### 3. Database Migration
```bash
# Connect to production database
mysql -u production_user -p production_db

# Run migration
SOURCE php/database_setup.sql;

# Verify
SOURCE php/verify_setup.sql;
```

### 4. Post-Deployment Tests

#### Test 1: Database
```sql
USE nexora_db;
SELECT COUNT(*) FROM team_members;
-- Should return 4
```

#### Test 2: Admin Access
- URL: `https://yoursite.com/admin/team.php`
- Should show team management page
- Should display 4 members

#### Test 3: Frontend Display
- URL: `https://yoursite.com/index.html#team`
- Should show 4 team members
- Should load dynamically from database

#### Test 4: API Endpoint
- URL: `https://yoursite.com/php/get_team.php`
- Should return JSON with 4 members
- Check browser console for errors

### 5. Production Configuration

#### Update URLs in Files
1. **`php/config.php`**
   ```php
   define('SITE_URL', 'https://yourdomain.com');
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'production_db_name');
   define('DB_USER', 'production_user');
   define('DB_PASS', 'secure_password');
   ```

2. **Update Image URLs** (if hosting locally)
   - Replace placeholder URLs with actual image paths
   - Use CDN URLs if available
   - Ensure all images use HTTPS

#### Security Hardening
1. **Change Admin Password**
   ```sql
   UPDATE admin_users 
   SET password = PASSWORD_HASH('your_secure_password', PASSWORD_DEFAULT)
   WHERE username = 'admin';
   ```

2. **Secure Database User**
   ```sql
   CREATE USER 'team_user'@'localhost' IDENTIFIED BY 'secure_password';
   GRANT SELECT, INSERT, UPDATE, DELETE ON nexora_db.team_members TO 'team_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

3. **File Permissions**
   ```bash
   find . -type d -exec chmod 755 {} \;
   find . -type f -exec chmod 644 {} \;
   chmod 600 php/config.php
   ```

4. **Secure Folders**
   ```bash
   # Add .htaccess to logs folder
   echo "Deny from all" > logs/.htaccess
   ```

## Post-Deployment

### 1. Add Real Team Members
- [ ] Login to admin panel
- [ ] Navigate to Team Management
- [ ] Add real team members with actual photos
- [ ] Update positions and bios
- [ ] Add real social media links
- [ ] Set appropriate display order
- [ ] Set all to "active" status

### 2. Delete Sample Data
```sql
-- Option 1: Delete all sample members
DELETE FROM team_members WHERE id <= 4;

-- Option 2: Edit sample members with real data
UPDATE team_members SET 
    name = 'Real Name',
    position = 'Real Position',
    bio = 'Real bio...',
    image_url = 'https://yourcdn.com/real-image.jpg',
    linkedin_url = 'https://linkedin.com/in/realuser',
    twitter_url = 'https://twitter.com/realuser',
    github_url = 'https://github.com/realuser'
WHERE id = 1;
```

### 3. Monitor & Optimize

#### Check Logs
```bash
# PHP error log
tail -f /var/log/php/error.log

# MySQL slow query log
tail -f /var/log/mysql/slow-queries.log

# Apache/Nginx access log
tail -f /var/log/apache2/access.log
```

#### Performance Monitoring
```sql
-- Check query performance
EXPLAIN SELECT * FROM team_members WHERE status = 'active' ORDER BY display_order;

-- Check table size
SELECT 
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
FROM information_schema.TABLES
WHERE table_schema = 'nexora_db'
AND table_name = 'team_members';
```

#### Browser Testing
- [ ] Test in Chrome
- [ ] Test in Firefox
- [ ] Test in Safari
- [ ] Test in Edge
- [ ] Test on mobile (iOS)
- [ ] Test on mobile (Android)
- [ ] Test on tablet

### 4. Setup Backups

#### Daily Backup Script
```bash
#!/bin/bash
# Save as: /scripts/backup_team.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/nexora"

# Backup team_members table
mysqldump -u root -p'password' nexora_db team_members > "$BACKUP_DIR/team_members_$DATE.sql"

# Keep only last 30 days
find $BACKUP_DIR -name "team_members_*.sql" -mtime +30 -delete

echo "Backup completed: $DATE"
```

#### Setup Cron Job
```bash
# Edit crontab
crontab -e

# Add daily backup at 2 AM
0 2 * * * /scripts/backup_team.sh >> /var/log/backup.log 2>&1
```

### 5. Documentation Updates
- [ ] Update internal wiki with team management instructions
- [ ] Share `QUICK_REFERENCE.md` with team
- [ ] Document any customizations made
- [ ] Note production URLs and credentials (securely)

## Troubleshooting Production Issues

### Issue: 500 Internal Server Error
```bash
# Check PHP error log
tail -100 /var/log/php/error.log

# Common causes:
# - Wrong file permissions
# - PHP syntax error (unlikely after testing)
# - Missing PHP extensions (PDO, MySQL)
```

### Issue: Database Connection Failed
```php
// Test connection
<?php
$db = new PDO('mysql:host=localhost;dbname=nexora_db', 'user', 'pass');
echo "Connected successfully";
?>
```

### Issue: Team Not Loading on Frontend
1. Check browser console (F12)
2. Test API directly: `https://yoursite.com/php/get_team.php`
3. Verify database has active members
4. Check CORS settings if using subdomain

### Issue: Images Not Showing
1. Verify image URLs are HTTPS (if site is HTTPS)
2. Test image URL in browser
3. Check if CDN/host allows hotlinking
4. Verify image file size (should be < 5MB)

## Success Criteria

System is successfully deployed when:
- ✅ Database table `team_members` exists with sample data
- ✅ Admin can login and access Team Management
- ✅ Admin can add, edit, delete team members
- ✅ Frontend displays team members dynamically
- ✅ All images load correctly
- ✅ Social media links work
- ✅ Mobile responsive design works
- ✅ No console errors
- ✅ No PHP errors in logs
- ✅ Page load time < 3 seconds
- ✅ Admin password changed from default
- ✅ Backups configured

## Rollback Plan

If issues occur:

### 1. Restore Database
```bash
mysql -u root -p nexora_db < backup_before_team_system.sql
```

### 2. Restore Files
```bash
# If using Git
git reset --hard HEAD~1

# If manual backup
tar -xzf backup_files_YYYYMMDD.tar.gz
```

### 3. Verify Restoration
- Test admin login
- Check if old system works
- Verify no data loss

## Support Contacts

**Technical Issues:**
- Email: nexorait@outlook.com
- Check documentation: `TEAM_MANAGEMENT.md`
- Review testing guide: `TESTING_GUIDE.md`

**Emergency Rollback:**
1. Restore from backup (see Rollback Plan above)
2. Document the issue
3. Contact support with error logs

---

## Final Sign-Off

- [ ] All tests passed
- [ ] Security hardened
- [ ] Backups configured
- [ ] Team trained
- [ ] Documentation updated
- [ ] Monitoring in place
- [ ] Rollback plan tested

**Deployed By:** ___________________  
**Date:** ___________________  
**Production URL:** ___________________  
**Backup Location:** ___________________  

---

**Status: READY FOR DEPLOYMENT** ✅

**Note:** Keep this checklist for future reference and similar deployments.
