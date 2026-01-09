# 🚀 Quick Start - Team Management

## Installation (2 Minutes)

### 1. Import Database
```bash
mysql -u root -p nexora_db < php/database_setup.sql
```

### 2. Login to Admin
- URL: `http://localhost/admin/`
- Username: `admin`
- Password: `admin123`

### 3. Access Team Management
- Click "Team Management" in sidebar
- You'll see 4 sample team members

---

## Daily Usage

### Add Team Member
1. Click **"Add Team Member"**
2. Enter **Name** and **Position** (required)
3. Paste **Image URL**
4. Add social links (optional)
5. Set **Display Order** (0 = first)
6. Click **"Save"**

### Edit Team Member
1. Find member card
2. Click **"Edit"**
3. Make changes
4. Click **"Save"**

### Delete Team Member
1. Find member card
2. Click **"Delete"**
3. Confirm

### Change Order
1. Edit member
2. Change **Display Order** number
3. Lower numbers = appears first
4. Save

---

## Files Created

| File | Purpose |
|------|---------|
| `admin/team.php` | Team management page |
| `admin/ajax/team_operations.php` | AJAX handler |
| `php/get_team.php` | Public API |
| `TEAM_MANAGEMENT.md` | Full documentation |
| `TEAM_SETUP_GUIDE.md` | Setup instructions |
| `TESTING_GUIDE.md` | Testing checklist |
| `IMPLEMENTATION_SUMMARY.md` | Complete overview |

---

## Files Modified

| File | What Changed |
|------|--------------|
| `php/database_setup.sql` | Added team_members table |
| `admin/includes/header.php` | Added nav link |
| `admin/dashboard.php` | Added team stats |
| `admin/css/admin-style.css` | Added teal color |
| `index.html` | Dynamic team loading |
| `js/main.js` | Team fetch function |
| `css/style.css` | Loading styles |

---

## Database

**Table:** `team_members`

**Key Fields:**
- `name` - Member name (required)
- `position` - Job title (required)
- `image_url` - Photo URL (required)
- `status` - active/inactive
- `display_order` - Display sequence (0 = first)

**Sample Data:** 4 members pre-loaded

---

## API Endpoints

**Get Team (Public):**
```
GET /php/get_team.php
```

**Admin Operations (Protected):**
```
GET  /admin/ajax/team_operations.php?action=get&id=1
POST /admin/ajax/team_operations.php
     { "action": "add", "name": "...", ... }
POST /admin/ajax/team_operations.php
     { "action": "update", "id": 1, "name": "...", ... }
POST /admin/ajax/team_operations.php
     { "action": "delete", "id": 1 }
```

---

## Common Tasks

### Add Real Team Photos

**Option 1 - Your Server:**
1. Create folder: `/images/team/`
2. Upload photos (400x400px, JPG)
3. Use URL: `https://yoursite.com/images/team/photo.jpg`

**Option 2 - Free Hosting:**
1. Upload to imgur.com or imgbb.com
2. Copy direct image link
3. Paste in "Image URL" field

### Hide Member Temporarily
1. Edit member
2. Change status to **"Inactive"**
3. Save
4. Member hidden from website (stays in database)

### Reorder Team
1. Edit first member → Display Order: **1**
2. Edit second member → Display Order: **2**
3. Continue for all members
4. Website updates automatically

---

## Troubleshooting

**Team not loading on website:**
- Check browser console (F12)
- Test: `http://localhost/php/get_team.php`
- Verify members are "active"
- Hard refresh (Ctrl+F5)

**Cannot login to admin:**
```sql
UPDATE admin_users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE username = 'admin';
-- Password: admin123
```

**Images not showing:**
- Test URL in browser address bar
- Use HTTPS images if site is HTTPS
- Check file size (< 5MB)
- Verify image host allows hotlinking

**Database errors:**
- Check `php/config.php` credentials
- Verify MySQL is running
- Re-import: `SOURCE php/database_setup.sql;`

---

## Security Checklist

- [ ] Change admin password (default: admin123)
- [ ] Use HTTPS in production
- [ ] Regular database backups
- [ ] Keep PHP updated
- [ ] Secure file permissions (755/644)

---

## Image Recommendations

- **Size:** 400x400 pixels (square)
- **Format:** JPG or PNG
- **File Size:** Under 500KB
- **Style:** Consistent across all members
- **Background:** Professional, solid color

---

## Quick SQL Commands

**Count team members:**
```sql
SELECT COUNT(*) FROM team_members;
```

**View all members:**
```sql
SELECT name, position, status, display_order 
FROM team_members 
ORDER BY display_order;
```

**Set all to active:**
```sql
UPDATE team_members SET status = 'active';
```

**Delete all sample data:**
```sql
DELETE FROM team_members WHERE id <= 4;
```

**Reset display order:**
```sql
UPDATE team_members SET display_order = id;
```

---

## Support

**Documentation Files:**
- `TEAM_MANAGEMENT.md` - Complete guide
- `TEAM_SETUP_GUIDE.md` - Installation steps
- `TESTING_GUIDE.md` - Testing procedures
- `IMPLEMENTATION_SUMMARY.md` - Full overview

**Contact:**
- Email: nexorait@outlook.com
- Check error logs in `/logs/`
- Browser console (F12) for JS errors

---

## Status: ✅ READY

All features tested and working:
- ✅ Database created
- ✅ Admin panel functional
- ✅ CRUD operations working
- ✅ Frontend displays team
- ✅ Security implemented
- ✅ Responsive design
- ✅ Documentation complete

**Next:** Import database → Login → Add your team!

---

**Last Updated:** January 10, 2026  
**Version:** 1.0.0
