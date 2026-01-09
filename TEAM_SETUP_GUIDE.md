# Quick Start: Team Management Setup

## Step-by-Step Installation

### 1. Update Database (REQUIRED)
Run this command in MySQL or phpMyAdmin:

```sql
USE nexora_db;

-- Create team_members table
CREATE TABLE IF NOT EXISTS team_members (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    bio TEXT DEFAULT NULL,
    image_url VARCHAR(500) DEFAULT NULL,
    linkedin_url VARCHAR(255) DEFAULT NULL,
    twitter_url VARCHAR(255) DEFAULT NULL,
    github_url VARCHAR(255) DEFAULT NULL,
    email VARCHAR(255) DEFAULT NULL,
    phone VARCHAR(50) DEFAULT NULL,
    display_order INT(11) DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_display_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample team members
INSERT INTO team_members (name, position, bio, image_url, linkedin_url, twitter_url, github_url, display_order, status, created_at) VALUES
('John Anderson', 'Chief Technology Officer', 'Leading technology innovation with over 15 years of experience in software architecture and development.', 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop', '#', '#', '#', 1, 'active', NOW()),
('Sarah Mitchell', 'Lead Developer', 'Full-stack developer passionate about creating elegant solutions to complex problems.', 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400&h=400&fit=crop', '#', '#', '#', 2, 'active', NOW()),
('Michael Chen', 'UX/UI Designer', 'Crafting beautiful and intuitive user experiences that delight users and drive engagement.', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop', '#', '#', '#', 3, 'active', NOW()),
('Emily Roberts', 'Project Manager', 'Ensuring projects are delivered on time and exceed client expectations every time.', 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=400&h=400&fit=crop', '#', '#', '#', 4, 'active', NOW());
```

**OR** simply re-import the updated file:
```bash
mysql -u your_username -p nexora_db < php/database_setup.sql
```

### 2. Test the Installation

#### Test Admin Panel:
1. Go to: `http://yoursite.com/admin/`
2. Login with your admin credentials
3. Click "Team Management" in the sidebar
4. You should see the 4 sample team members
5. Try adding a new team member

#### Test Frontend:
1. Go to: `http://yoursite.com/index.html`
2. Scroll to the "Meet Our Team" section
3. You should see team members loaded dynamically
4. Hover over members to see social links

### 3. Customize Your Team

#### Add Real Team Members:
1. In admin panel, click "Add Team Member"
2. Fill in the form:
   - **Name**: Full name of team member
   - **Position**: Their job title
   - **Bio**: Short description (optional)
   - **Image URL**: Direct link to their photo
   - **Social Links**: LinkedIn, Twitter, GitHub (optional)
   - **Display Order**: 1, 2, 3, etc. (lower = appears first)
   - **Status**: Active (to show on website)

#### Upload Team Photos:
Option 1 - Use your server:
- Create folder: `images/team/`
- Upload photos there
- Use URL: `https://yoursite.com/images/team/photo.jpg`

Option 2 - Use free image hosting:
- Upload to: imgur.com, cloudinary.com, or imgbb.com
- Copy the direct image link
- Paste into "Image URL" field

### 4. Files Created/Modified

**New Files:**
- ✅ `admin/team.php` - Team management page
- ✅ `admin/ajax/team_operations.php` - AJAX handler
- ✅ `php/get_team.php` - Public API endpoint
- ✅ `TEAM_MANAGEMENT.md` - Full documentation
- ✅ `TEAM_SETUP_GUIDE.md` - This file

**Modified Files:**
- ✅ `php/database_setup.sql` - Added team_members table
- ✅ `admin/includes/header.php` - Added Team Management link
- ✅ `index.html` - Dynamic team loading
- ✅ `js/main.js` - Team fetch function
- ✅ `css/style.css` - Loading state styles

### 5. Common Issues & Solutions

**Issue: "Team Management" link not showing**
- Solution: Clear browser cache and refresh admin panel
- Check that header.php was updated correctly

**Issue: Team members not loading on website**
- Solution: Check browser console (F12) for errors
- Verify php/get_team.php is accessible
- Check database connection in php/config.php

**Issue: Images not showing**
- Solution: Verify image URLs are correct
- Test URL in browser address bar
- Ensure images are publicly accessible

**Issue: Cannot add/edit members**
- Solution: Verify you're logged in as admin
- Check that ajax/team_operations.php exists
- Check PHP error logs

### 6. Next Steps

1. **Delete Sample Data**: Once you've added real team members, delete the sample ones
2. **Update Social Links**: Replace '#' with real social media URLs
3. **Optimize Images**: Use 400x400px images, under 500KB each
4. **Customize Design**: Edit css/style.css to match your brand
5. **Backup Database**: Regularly backup your team_members table

### 7. Support & Documentation

- **Full Documentation**: See `TEAM_MANAGEMENT.md`
- **Admin Guide**: See `ADMIN_QUICK_START.md`
- **Deployment**: See `DEPLOYMENT.md`
- **Contact**: nexorait@outlook.com

---

## Quick Commands Reference

**View team members in database:**
```sql
SELECT * FROM team_members ORDER BY display_order;
```

**Set all members to active:**
```sql
UPDATE team_members SET status = 'active';
```

**Delete all sample members:**
```sql
DELETE FROM team_members WHERE id <= 4;
```

**Reset display order:**
```sql
UPDATE team_members SET display_order = id;
```

---

**Setup Complete!** 🎉
Your team management system is now ready to use.
