# Nexora Website - Complete Package

A modern, responsive business website with advanced features including contact form with database storage, newsletter subscription system, particle animations, and comprehensive mobile optimization.

## 📁 File Structure

```
nexora-website/
├── index.html              # Main HTML file
├── css/
│   └── style.css          # All styles and animations (2000+ lines)
├── js/
│   └── main.js            # JavaScript functionality (700+ lines)
├── php/
│   ├── config.php         # Configuration & database connection
│   ├── contact.php        # Contact form handler with DB storage
│   ├── newsletter.php     # Newsletter subscription handler
│   ├── database_setup.sql # Database schema and tables
│   ├── admin_setup.sql    # Admin panel database setup
│   └── README.md          # PHP backend documentation
├── admin/                  # Admin Panel (NEW!)
│   ├── login.php          # Admin login page
│   ├── dashboard.php      # Main admin dashboard
│   ├── contacts.php       # Manage contact messages
│   ├── subscribers.php    # Manage newsletter subscribers
│   ├── settings.php       # Admin settings & profile
│   ├── logout.php         # Logout handler
│   ├── includes/          # Authentication & common files
│   ├── ajax/              # AJAX handlers
│   ├── css/               # Admin panel styles
│   ├── js/                # Admin panel JavaScript
│   └── README.md          # Admin panel documentation
└── README.md              # This file
```

## 🚀 Features

### Design & UI
- ✨ Modern, professional design with gradient backgrounds
- 🎨 Custom Google Fonts (Outfit & Playfair Display)
- 📱 Fully responsive for all devices (5 breakpoints)
- 🎭 Smooth animations and transitions
- 🌊 Parallax effects and interactive floating particles (60 particles)
- 💫 Scroll-triggered fade-in animations
- 🔝 Back to top button with smooth scroll
- 📊 Scroll progress indicator
- 🎯 WhatsApp floating button

### Functionality
- 📧 Working contact form with dual email system (admin + user confirmation)
- 💾 Database storage for all contact form submissions
- 📬 Newsletter subscription system with duplicate prevention
- 📊 Animated statistics counter with smooth increments
- 🎯 Smooth scroll navigation
- 📲 Mobile menu with hamburger icon
- 🔄 Testimonial slider (auto-rotating every 5 seconds)
- 🎬 Loading screen animation
- ♿ Accessibility features
- 🔒 Input validation and sanitization
- 🛡️ SQL injection and XSS protection
- ⚡ AJAX form submissions (no page reload)
- 🔐 **Complete Admin Panel** - Manage all data with secure dashboard

### Sections
1. **Hero Header** - Eye-catching introduction with CTAs and particle animation
2. **Statistics** - Animated counters showing achievements
3. **About** - Company overview with image
4. **Services** - 6 detailed service cards with hover effects
5. *Prerequisites
- Web server with PHP 7.4+ support (Apache/Nginx)
- MySQL 5.7+ or MariaDB 10.3+
- PHP PDO extension enabled
- PHP mail() function or SMTP server configured

### Step 1: Database Setup

1. **Create Database & Tables**
   ```bash
   # Option A: Using MySQL command line
   mysql -u root -p < php/database_setup.sql
   mysql -u root -p < php/admin_setup.sql
   
   # Option B: Using phpMyAdmin
   # - Open phpMyAdmin
   # - Click "Import" tab
   # - Select php/database_setup.sql (import first)
   # - Then select php/admin_setup.sql (import second)
   # - Click "Go" for each
   ```

2. **Configure Database Connection**
   Edit `php/config.php` (lines 8-11):
   ```php
   define('DB_HOST', 'localhost');        // Your database host
   define('DB_NAME', 'nexora_db');        // Database name
   define('DB_USER', 'your_username');    // Your MySQL username
   define('DB_PASS', 'your_password');    // Your MySQL password
   ```

3. **Verify Database**
   - Check that tables exist: `contact_messages`, `newsletter_subscribers`, `admin_users`
   - Test connection by submitting contact form

### Step 2: Upload Files

Upload all files to your web server maintaining the folder structure:
```
your-domain.com/
├── index.html
├── css/style.css
├── js/main.js
└── php/
    ├── config.php
    ├── contact.php
    ├── newsletter.php
    └── database_setup.sql
```

### Step 3: Configure PHP Settings

Edit `php/config.php` to update:
- **Database credentials** (lines 8-11)
- **Company information** (lines 14-20)
- **Email settings** (lines 23-25)
- **Contact details** (already set to your info)

### Step 4: Email Configuration

**Option A: PHP mail() Function (Basic)**
- Already configured in contact.php and newsletter.php
- Test by submitting forms

**Option B: SMTP (Recommended for Production)**
- Install PHPMailer: `composer require phpmailer/phpmailer`
- Update email functions in config.php to use SMTP
- Configure SMTP settings (Gmail, SendGrid, etc.)

See `php/README.md` for detailed SMTP setup instructions.

### Step 5: Test All Features

1. **Contact Form**
   - Submit a test message
   - Check database: `SELECT * FROM contact_messages`
   - Verify emails received (admin + user confirmation)

2. **Newsletter**& Newsletter

### Contact Form Features
- ✅ Dual email system (admin notification + user confirmation)
- ✅ Database storage with timestamps and IP tracking
- ✅ Beautiful HTML email templates
- ✅ Input validation and sanitization
- ✅ AJAX submission (no page reload)
- ✅ Success/error notifications
- ✅ Status tracking (new, read, replied)

### Newsletter System Features
- ✅ Email subscription with database storage
- ✅ Duplicate prevention (checks existing subscribers)
- ✅ Welcome email with HTML template
- ✅ Admin notification for new subscribers
- ✅ Active/inactive status management
- ✅ Unsubscribe capability ready

## 🔐 Admin Panel

### Access Admin Panel
- **URL:** `http://yourwebsite.com/admin/`
- **Default Username:** `admin`
- **Default Password:** `admin123`
- ⚠️ **IMPORTANT:** Change password immediately after first login!

### Admin Panel Features
- 📊 **Dashboard** - Overview with statistics and recent activity
- 📧 **Contact Messages** - View, search, filter, update status, export to CSV
- 👥 **Newsletter Subscribers** - Manage subscribers, export data
- ⚙️ **Settings** - Update profile, change password
- 📥 **Data Export** - Download all data as CSV files
- 🔐 **Secure Authentication** - Password hashing, session management
- 📝 **Activity Logging** - Track all admin actions

### Managing Contact Messages
1. Login to admin panel
2. Navigate to "Contact Messages"
3. View all submitted messages with details
4. Update status (new → read → replied → archived)
5. Export to CSV for records
6. Delete old or spam messages

### Managing Newsletter Subscribers
1. Go to "Newsletter Subscribers"
2. View all subscribers with status
3. Search by email or filter by status
4. Export subscriber list for email campaigns
5. Update status or remove subscribers

For complete admin panel documentation, see [admin/README.md](admin/README.md)

### Basic Setup (PHP mail)
Both systems are configured to use PHP's built-in mail() function.

### Advanced Setup (SMTP - Recommended)
For better email delivery, use PHPMailer with SMTP:

1. Install PHPMailer
   ```bash
   composer require phpmailer/phpmailer
   ```

2. Update `php/config.php` sendEmail() function

3. Configure SMTP settings:
   ```php
   $mail->isSMTP();
   $mail->Host = 'smtp.gmail.com';
   $mail->SMTPAuth = true;
   $mail->Username = 'your-email@gmail.com';
   $mail->Password = 'your-app-password';
   $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
   $mail->Port = 587;
   ```

### Database Tables

**contact_messages** (10 fields):
- id, name, email, phone, subject, message
- ip_address, user_agent, status, created_at, updated_at

**newsletter_subscribers** (8 fields):
- id, email, status, subscribed_at
- unsubscribed_at, ip_address, user_agent, updated_at

### Testing
1. Fill out the contact form
2. Subscribe to newsletter
3. Check database for entries
4. Verify emails in inbox (check spam folder)
5. Check server error logs if issues occur: `php/error.log`
- Team member information
- Testimonials
- Portfolio projects

### Step 7: Add Your Logo (Optional)

Replace text logo with image in navbar
### 3. Test Contact Form
Make sure your server supports PHP mail() function or configure SMTP.

##✅ Input sanitization (htmlspecialchars, filter_var)
- ✅ SQL injection prevention (PDO prepared statements)
- ✅ XSS protection (all user input escaped)
- ✅ Email validation (filter_var with FILTER_VALIDATE_EMAIL)
- ✅ IP address logging for audit trails
- ✅ User agent tracking
- ⚠️ CSRF protection (recommended to add tokens)
- ⚠️ Rate limiting (recommended for production)
- ⚠️ reCAPTCHA (recommended to prevent spam
- Testimonials

### 5. Add Your Logo
Replace text logo with image in navbar (optional):
```html
<a href="#" class="nav-logo">
    <img src="images/logo.png" alt="Nexora">
</a>
```

## 🔧 Configuration

### Contact Information (Already Updated)
```
Email: nexorait@outlook.com
Phone 1: +94 77 635 0902
Phone 2: +94 70 671 7131
WhatsApp: +94 70 671 7131
Address: 218 Doalakanda, Dehiaththakandiya, Sri Lanka
```

### Customizing Colors
Edit CSS variables in `css/style.css`:
```css
:root {
  --primary: #1e3c72;          /* Main color */
  --primary-light: #2a5298;    /* Light variant */
  --accent: #3d6cb9;           /* Accent color */
  --text-dark: #1a1a1a;        /* Dark text */
  --text-light: #666;          /* Light text */
}
```

### Adding More Testimonials
Edit the testimonials array in `js/main.js`:
```javascript
const testimonials = [
  {
    content: "Your testimonial here...",
    author: "Name",
    role: "Position, Company"
  },
  // Add more...
];
```

## 📧 Contact Form Setup

### Basic Setup (PHP mail)
The contact form is already configured to use PHP's built-in mail() function.

### Advanced Setup (SMTP)
For better email delivery, consider using PHPMailer with SMTP:

1. Download PHPMailer
2. Update `php/contact.php` to use SMTP
3. Configure your SMTP settings (Gmail, SendGrid, etc.)

### Testing
1. Fill out the form on your website
2. Check spam folder if email doesn't arrive
3. Check server error logs if form submission fails

## 🎨 Customization Tips

### Changing Images
Replace image URLs in `index.html`:
```html
<img src="your-image-path.jpg" alt="Description">
```

### Adding New Sections
Follow the existing pattern:
```html
<section id="new-section" class="fade-in">
  <div class="section-header">
### Current Optimizations
- ✅ Efficient CSS with custom properties
- ✅ Optimized JavaScript (no heavy libraries)
- ✅ Lazy loading for images
- ✅ Smooth scroll with requestAnimationFrame
- ✅ Debounced scroll events
- ✅ CDN for Font Awesome icons

### Recommended for Production
- Minify CSS and JS files
- Compress images (use WebP format)
- Enable Gzip compression
- Add browser caching headers (.htaccess)
- Implement service workers for offline support
- Use image lazy loading library (lazysizes)
- Enable database query caching
```

### Modifying Navigation
Upda�️ Database Management

### Viewing Data

**Contact Messages:**
```sql
SELECT * FROM contact_messages ORDER BY created_at DESC;
```

**Newsletter Subscribers:**
```sql
### Immediate (Required)
1. ✅ Run database setup SQL files (database_setup.sql + admin_setup.sql)
2. ✅ Configure database credentials in config.php
3. ✅ Test contact form and newsletter
4. ✅ Verify emails are being sent
5. ✅ **Login to admin panel and change default password**

### Short Term (Important)
6. Upload to your hosting server
7. Set up SSL certificate (HTTPS)
8. Configure SMTP for reliable email delivery
9. Add your own images and content
10. Update social media links
11. Test on multiple devices
12. **Set up regular database backups**

### Long Term (Recommended)
13. Add Google Analytics for tracking
14. Implement reCAPTCHA for spam prevention
15. Set up automated database backups
16. Add rate limiting to forms
17. Submit to search engines
18. Implement admin dashboard enhancements
19. Add email templates customization
20. Create unsubscribe functionality
### Content Management
- Keep content updated regularly
- Add new portfolio projects as you complete them
- Update testimonials with recent client feedback
- Refresh team member information

### Monitoring
- **Check admin panel daily** for new inquiries
- Monitor contact_messages table for new submissions
- Review newsletter_subscribers growth
- Review email delivery success rates
- Check server error logs weekly
- Monitor login attempts in admin panel
- Check server error logs weekly

### Maintenance
- Backup database weekly (automated recommended)
- Test forms monthly to ensure functionality
- Update images and optimize for web
- Review and respond to all contact messages within 24 hours

### Analytics & Growth
- Install Google Analytics to track visitors
- Monitor which services get most inquiries
- Track newsletter conversion rates
- A/B test different CTAs

### Security
- Update PHP and MySQL regularly
- Monitor for suspicious form submissions
- Review IP logs for unusual patterns
- Keep database credentials secureups
14. Add rate limiting to forms
15. Submit to search engines
16. Implement admin dashboard (future feature)
17. Add email templates customization
18. Create unsubscribe functionality
-- Newsletter statistics
SELECT * FROM newsletter_stats;
```

### Managing Entries

**Mark message as read:**
```sql
UPDATE contact_messages SET status = 'read' WHERE id = 1;
```

**Export subscribers:**
```sql
SELECT email FROM newsletter_subscribers 
WHERE status = 'active' 
INTO OUTFILE '/tmp/subscribers.csv';
```
## 📚 Additional Resources

- **PHP Backend Documentation:** See `php/README.md` for detailed PHP setup
- **Database Schema:** Review `php/database_setup.sql` for table structures
- **API Endpoints:**
  - `POST /php/contact.php` - Submit contact form
  - `POST /php/newsletter.php` - Subscribe to newsletter

## 🆘 Troubleshooting

### Contact Form Not Working
1. Check database connection in config.php
2. Verify PHP mail() is enabled on server
3. Check error logs: `tail -f /var/log/apache2/error.log`
4. Ensure AJAX URL is correct in main.js

### Emails Not Sending
1. Test PHP mail function: `php -r "mail('test@example.com','Test','Body');"`
2. Check spam folders
3. Consider switching to SMTP (more reliable)
4. Verify FROM email address is valid

### Database Connection Failed
1. Verify MySQL service is running
2. Check credentials in config.php
3. Ensure database nexora_db exists
4. Test connection: `mysql -u username -p nexora_db`

### Animations Not Working
1. Clear browser cache
2. Check JavaScript console for errors (F12)
3. Verify main.js is loaded correctly
4. Ensure no JavaScript conflicts

---

**Built with ❤️ for Nexora**  
*Empowering Your Business with Smart Software Solutions*

**Version:** 2.0  
**Last Updated:** January 2026  
**Technical Stack:** HTML5, CSS3, JavaScript ES6+, PHP 7.4+, MySQL 5.7+
mysqldump -u username -p nexora_db > backup.sql

# Backup specific table
mysqldump -u username -p nexora_db contact_messages > contacts_backup.sql
```
- **v2.0** - Major update with database integration & Admin Panel
  - ✅ Complete Admin Panel system
  - ✅ Admin authentication with secure login
  - ✅ Dashboard with statistics
  - ✅ Contact messages management interface
  - ✅ Newsletter subscribers management
  - ✅ Data export to CSV
  - ✅ Activity logging
  - ✅ MySQL database backend
  - ✅ Newsletter subscription system
  - ✅ Enhanced contact form with dual emails
  - ✅ Database storage for all submissions
  - ✅ Advanced PHP backend
  - ✅ New sections: Portfolio, Team, Technologies, Newsletter
  - ✅ Particle animation system
  - ✅ Enhanced security features
  - New sections: Portfolio, Team, Technologies, Newsletter
  - Particle animation system
  - Enhanced security features

- **v1.0** - Initial release
  - Responsive design
  - Basic contact form
  - Animations
  - Mobile menu
  - Cor
    <a href="#new-section">New Section</a>
  </nav>
</div>
```

## 🌐 Browser Support

- ✅ Chrome (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Edge (latest)
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)

## 📱 Mobile Optimization

- Responsive grid layouts
- Touch-friendly buttons (minimum 44px)
- Optimized font sizes
- Hamburger menu for navigation
- Fast loading with optimized assets

## 🔒 Security Features

- Input sanitization in contact form
- CSRF protection ready
- SQL injection prevention (if database added)
- XSS prevention
- Spam protection (honeypot field ready)

## 🚀 Performance Optimization

- Minify CSS and JS for production
- Optimize images (compress before upload)
- Enable browser caching (.htaccess)
- Use CDN for fonts and libraries
- Lazy loading for images

## 📞 Support & Contact

For questions or support with this website:

**Email:** nexorait@outlook.com  
**Phone:** +94 77 635 0902 / +94 70 671 7131  
**WhatsApp:** +94 70 671 7131  
**Address:** 218 Doalakanda, Dehiaththakandiya, Sri Lanka

## 📝 License

This website template is created for Nexora. All rights reserved.

## 🔄 Version History

- **v1.0** - Initial release with all core features
  - Responsive design
  - Contact form
  - Animations
  - Mobile menu
  - Complete sections

## 🎯 Next Steps

1. Upload to your hosting server
2. Test all functionality
3. Update social media links
4. Add Google Analytics (optional)
5. Set up SSL certificate
6. Submit to search engines
7. Add your own images
8. Customize content

## 💡 Tips for Success

- Keep content updated regularly
- Monitor contact form submissions
- Test on multiple devices
- Optimize images before uploading
- Backup regularly
- Use analytics to track visitors
- Update testimonials periodically

---

**Built with ❤️ for Nexora**  
*Empowering Your Business with Smart Software Solutions*