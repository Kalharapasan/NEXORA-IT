# Team Management System - Setup & Usage Guide

## Overview
The admin panel now includes a complete team member management system that allows you to add, edit, delete, and manage your company's team members. The team section on the website dynamically loads from the database.

## Database Setup

### Step 1: Run the Database Migration
Execute the updated SQL file to create the team_members table:

```bash
# If using MySQL command line:
mysql -u your_username -p nexora_db < php/database_setup.sql

# Or import via phpMyAdmin:
# 1. Open phpMyAdmin
# 2. Select nexora_db database
# 3. Go to Import tab
# 4. Choose php/database_setup.sql
# 5. Click "Go"
```

### Database Structure
The `team_members` table includes:
- **id**: Unique identifier
- **name**: Team member's full name
- **position**: Job title/role
- **bio**: Brief description (optional)
- **image_url**: Photo URL
- **email**: Contact email (optional)
- **phone**: Contact phone (optional)
- **linkedin_url**: LinkedIn profile link
- **twitter_url**: Twitter profile link
- **github_url**: GitHub profile link
- **display_order**: Order in which members appear (0 = first)
- **status**: active/inactive
- **created_at**: Timestamp
- **updated_at**: Auto-updated timestamp

## Admin Panel Access

### Accessing Team Management
1. Login to admin panel: `http://yoursite.com/admin/`
2. Click "Team Management" in the sidebar navigation
3. You'll see all team members in a grid layout

### Adding a New Team Member
1. Click the "Add Team Member" button
2. Fill in the required fields:
   - **Name** (required)
   - **Position** (required)
   - **Image URL** (required) - Use full URL to image
   - **Bio** (optional) - Brief description
   - **Email** (optional)
   - **Phone** (optional)
   - **LinkedIn URL** (optional)
   - **Twitter URL** (optional)
   - **GitHub URL** (optional)
   - **Display Order** (0 = first, higher numbers appear later)
   - **Status** (active/inactive)
3. Preview the image by entering the URL
4. Click "Save" to add the member

### Editing a Team Member
1. Find the team member card
2. Click the "Edit" button
3. Modify the desired fields
4. Click "Save" to update

### Deleting a Team Member
1. Find the team member card
2. Click the "Delete" button
3. Confirm the deletion
4. Member will be permanently removed

### Managing Display Order
- Set lower numbers (0, 1, 2) for team members you want to appear first
- Members with same display_order are sorted by creation date
- Update display_order to reorganize team layout

### Active vs Inactive Status
- **Active**: Member appears on the public website
- **Inactive**: Member hidden from public but remains in database

## Frontend Integration

### How Team Members Appear on Website
The team section automatically loads from the database:
- Only **active** team members are displayed
- Members are sorted by **display_order** (ascending)
- Social media links appear if provided
- Fallback image shown if image URL is invalid

### Technical Details
- **Endpoint**: `php/get_team.php`
- **Method**: GET request
- **Response**: JSON with team members array
- **Auto-refresh**: Page reload required to see changes

### Customizing Team Display
Edit `css/style.css` to modify:
- Grid layout (currently: auto-fit, min 250px)
- Card styling (border-radius, shadows, etc.)
- Hover effects
- Social media icon styles

## Image Recommendations

### Best Practices for Team Photos
- **Dimensions**: 400x400 pixels (square format)
- **File Format**: JPG or PNG
- **File Size**: Under 500KB for fast loading
- **Background**: Solid color or professional setting
- **Style**: Consistent across all team members

### Image Hosting Options
1. **Upload to your server**: Store in `/images/team/` folder
2. **Use CDN**: Cloudinary, Imgur, AWS S3
3. **External URLs**: Unsplash, direct image links

### Example Image URL Formats
```
https://yoursite.com/images/team/john-anderson.jpg
https://res.cloudinary.com/yourcloud/image/upload/v123456/team/sarah.jpg
https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop
```

## Security & Permissions

### Admin Authentication
- Team management requires admin login
- All operations are session-protected
- AJAX requests verify authentication

### Input Validation
- Name and position are required
- URLs are validated for proper format
- XSS protection via htmlspecialchars()
- SQL injection prevention via prepared statements

## API Endpoints

### Get All Active Team Members (Public)
```php
GET /php/get_team.php
Response: {
  "success": true,
  "members": [...]
}
```

### Get Single Member (Admin Only)
```php
GET /admin/ajax/team_operations.php?action=get&id=1
Response: {
  "success": true,
  "member": {...}
}
```

### Add Team Member (Admin Only)
```php
POST /admin/ajax/team_operations.php
Body: {
  "action": "add",
  "name": "John Doe",
  "position": "Developer",
  ...
}
```

### Update Team Member (Admin Only)
```php
POST /admin/ajax/team_operations.php
Body: {
  "action": "update",
  "id": 1,
  "name": "John Doe",
  ...
}
```

### Delete Team Member (Admin Only)
```php
POST /admin/ajax/team_operations.php
Body: {
  "action": "delete",
  "id": 1
}
```

## Troubleshooting

### Team Members Not Showing on Website
1. Check if members are set to "active" status
2. Clear browser cache and refresh
3. Verify database connection in `php/config.php`
4. Check browser console for JavaScript errors
5. Ensure `php/get_team.php` is accessible

### Images Not Loading
1. Verify image URL is correct and accessible
2. Check if image URL uses HTTPS (if your site uses HTTPS)
3. Test image URL in browser address bar
4. Check image file permissions on server
5. Verify image host allows hotlinking

### Cannot Add/Edit Members
1. Confirm you're logged in as admin
2. Check browser console for errors
3. Verify `admin/ajax/team_operations.php` exists
4. Check PHP error logs
5. Ensure database connection is working

### Database Errors
1. Verify table exists: `SHOW TABLES LIKE 'team_members'`
2. Check table structure: `DESCRIBE team_members`
3. Verify database permissions
4. Check PHP error logs for SQL errors

## Sample Team Member Data

### Example 1: Tech Lead
```json
{
  "name": "Sarah Johnson",
  "position": "Chief Technology Officer",
  "bio": "Leading innovation with 15+ years in software architecture",
  "image_url": "https://yoursite.com/images/team/sarah.jpg",
  "email": "sarah@nexora.com",
  "linkedin_url": "https://linkedin.com/in/sarahjohnson",
  "twitter_url": "https://twitter.com/sarahtech",
  "github_url": "https://github.com/sarahj",
  "display_order": 1,
  "status": "active"
}
```

### Example 2: Developer
```json
{
  "name": "Mike Chen",
  "position": "Senior Full Stack Developer",
  "bio": "Passionate about clean code and user experience",
  "image_url": "https://yoursite.com/images/team/mike.jpg",
  "email": "mike@nexora.com",
  "linkedin_url": "https://linkedin.com/in/mikechen",
  "github_url": "https://github.com/mikechen",
  "display_order": 2,
  "status": "active"
}
```

## Support
For issues or questions:
- Check PHP error logs: `/logs/` folder
- Check browser console for JavaScript errors
- Verify database queries in phpMyAdmin
- Contact: nexorait@outlook.com
