# Nexora Website - Deployment Guide

## 📦 Complete Package Contents

Your website package includes:

```
nexora-website/
├── index.html              # Main website file
├── css/
│   └── style.css          # All styles (17KB+)
├── js/
│   └── main.js            # All functionality (12KB+)
├── php/
│   ├── contact.php        # Email handler
│   └── config.php         # Site configuration
├── logs/                   # Contact form logs
├── .htaccess              # Apache configuration
├── .gitignore             # Git ignore rules
├── README.md              # Documentation
└── DEPLOYMENT.md          # This file
```

## 🚀 Deployment Steps

### Step 1: Choose Your Hosting

**Recommended Hosting Providers in Sri Lanka:**
- Lanka.com
- Hosting.lk
- SLTMobitel Web Hosting
- Netpluz Sri Lanka
- Any cPanel hosting with PHP support

**Requirements:**
- PHP 7.0 or higher
- Apache/Nginx web server
- Mail function support (or SMTP)
- At least 50MB storage
- SSL certificate (recommended)

### Step 2: Prepare Your Domain

1. **Purchase a domain** (if you haven't):
   - nexora.lk
   - nexora.com
   - nexorait.com (suggested)

2. **Configure DNS**:
   - Point A record to your server IP
   - Wait 24-48 hours for propagation

### Step 3: Upload Files

**Method 1: Using cPanel File Manager**
1. Log into your cPanel
2. Go to File Manager
3. Navigate to `public_html` or `www` folder
4. Upload the entire `nexora-website` folder
5. Extract if uploaded as ZIP
6. Move contents to root (optional)

**Method 2: Using FTP**
1. Download FileZilla (free FTP client)
2. Connect using your FTP credentials:
   - Host: ftp.yourdomain.com
   - Username: your_ftp_user
   - Password: your_ftp_password
   - Port: 21
3. Upload all files to `public_html`

**Method 3: Using SSH/Terminal**
```bash
# Connect to server
ssh user@yourdomain.com

# Navigate to web root
cd /home/user/public_html

# Upload using SCP (from local machine)
scp -r nexora-website/* user@yourdomain.com:/home/user/public_html/
```

### Step 4: Set File Permissions

Set correct permissions for security:

```bash
# Set directory permissions
find /path/to/website -type d -exec chmod 755 {} \;

# Set file permissions
find /path/to/website -type f -exec chmod 644 {} \;

# Make logs directory writable
chmod 755 logs/
chmod 666 logs/contact_submissions.log

# Protect PHP files
chmod 644 php/*.php
```

### Step 5: Configure PHP Mail

**Option A: Using Built-in mail() Function**

1. Check if mail() is enabled:
```php
<?php
if (function_exists('mail')) {
    echo "Mail function is available";
} else {
    echo "Mail function is NOT available";
}
?>
```

2. If not available, contact your hosting provider

**Option B: Using SMTP (Recommended)**

1. Install PHPMailer:
```bash
composer require phpmailer/phpmailer
```

2. Update `php/contact.php` with SMTP settings:
```php
use PHPMailer\PHPMailer\PHPMailer;

$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'your-email@gmail.com';
$mail->Password = 'your-app-password';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;
```

### Step 6: Configure Contact Form

1. Edit `php/config.php`:
```php
define('CONTACT_EMAIL', 'nexorait@outlook.com');
```

2. Test the contact form:
   - Visit your website
   - Fill out the contact form
   - Check if email arrives
   - Check spam folder if needed

### Step 7: Install SSL Certificate

**Free SSL with Let's Encrypt (cPanel):**

1. Log into cPanel
2. Go to "SSL/TLS Status"
3. Click "Run AutoSSL"
4. Wait for certificate installation

**Manual SSL Installation:**

1. Purchase SSL certificate
2. Generate CSR in cPanel
3. Submit CSR to certificate authority
4. Install certificate files

**After SSL Installation:**

Uncomment in `.htaccess`:
```apache
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### Step 8: Test Your Website

**Checklist:**
- [ ] Homepage loads correctly
- [ ] All sections visible
- [ ] Navigation works
- [ ] Mobile menu functions
- [ ] Contact form submits
- [ ] Email arrives in inbox
- [ ] Images load properly
- [ ] Animations work
- [ ] Responsive on mobile
- [ ] SSL certificate active
- [ ] No console errors

**Testing Tools:**
- PageSpeed Insights: https://pagespeed.web.dev/
- Mobile-Friendly Test: https://search.google.com/test/mobile-friendly
- SSL Checker: https://www.sslshopper.com/ssl-checker.html

### Step 9: Configure Backup

**cPanel Backup:**
1. Go to cPanel > Backup
2. Set up automated backups
3. Schedule weekly backups

**Manual Backup:**
```bash
# Create backup
tar -czf nexora-backup-$(date +%Y%m%d).tar.gz /path/to/website

# Download via FTP
# Or use cPanel File Manager
```

### Step 10: Set Up Analytics (Optional)

**Google Analytics:**

1. Create account at analytics.google.com
2. Add tracking code before `</head>`:
```html
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'GA_MEASUREMENT_ID');
</script>
```

## 🔧 Post-Deployment Configuration

### Update Contact Information

All contact information is already configured:
```
Email: nexorait@outlook.com
Phone: +94 77 635 0902 / +94 70 671 7131
WhatsApp: +94 70 671 7131
Address: 218 Doalakanda, Dehiaththakandiya, Sri Lanka
```

### Add Social Media Links

Edit `index.html` footer section:
```html
<a href="https://facebook.com/yourpage">📘</a>
<a href="https://twitter.com/yourhandle">🐦</a>
<a href="https://linkedin.com/company/yourcompany">💼</a>
<a href="https://instagram.com/yourhandle">📷</a>
```

### Replace Placeholder Images

Replace Unsplash URLs with your own:
```html
<!-- Before -->
<img src="https://images.unsplash.com/photo-...">

<!-- After -->
<img src="images/your-image.jpg">
```

### SEO Optimization

1. **Add meta tags** in `<head>`:
```html
<meta name="description" content="Nexora - Business software solutions in Sri Lanka">
<meta name="keywords" content="POS systems, business software, Sri Lanka">
<meta name="author" content="Nexora">
<meta property="og:title" content="Nexora - Empowering Your Business">
<meta property="og:description" content="Smart software solutions for businesses">
<meta property="og:image" content="https://yourdomain.com/images/og-image.jpg">
```

2. **Submit to search engines**:
   - Google Search Console
   - Bing Webmaster Tools

3. **Create sitemap.xml**:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>https://yourdomain.com/</loc>
    <lastmod>2026-01-05</lastmod>
    <priority>1.0</priority>
  </url>
</urlset>
```

## 🔒 Security Checklist

- [ ] SSL certificate installed
- [ ] File permissions set correctly
- [ ] .htaccess configured
- [ ] PHP error display disabled
- [ ] Contact form has spam protection
- [ ] Regular backups scheduled
- [ ] Strong passwords used
- [ ] Admin area protected (if applicable)
- [ ] Database secured (if applicable)
- [ ] Sensitive files not publicly accessible

## 📱 Mobile Testing

Test on actual devices:
- iPhone (Safari)
- Android (Chrome)
- Tablet (iPad/Android)

Or use browser dev tools:
- Chrome DevTools (F12)
- Firefox Developer Tools
- Safari Web Inspector

## 🐛 Common Issues & Solutions

### Issue 1: Contact Form Not Working
**Solution:**
- Check PHP mail() is enabled
- Verify email in config.php
- Check server error logs
- Try SMTP instead

### Issue 2: Images Not Loading
**Solution:**
- Check file paths
- Verify file permissions (644)
- Ensure images are uploaded
- Check .htaccess rules

### Issue 3: CSS/JS Not Loading
**Solution:**
- Clear browser cache
- Check file paths in HTML
- Verify file permissions
- Check console for errors

### Issue 4: Mobile Menu Not Working
**Solution:**
- Check JavaScript errors
- Verify main.js is loaded
- Clear browser cache
- Test in different browser

### Issue 5: Slow Loading
**Solution:**
- Optimize images (compress)
- Enable Gzip compression
- Use browser caching
- Minify CSS/JS

## 📊 Performance Optimization

1. **Optimize Images:**
```bash
# Use tools like:
- TinyPNG.com
- ImageOptim
- Squoosh.app
```

2. **Minify CSS/JS:**
```bash
# Use online tools:
- cssminifier.com
- javascript-minifier.com
```

3. **Enable Caching:**
Already configured in `.htaccess`

4. **Use CDN:**
- Cloudflare (free)
- Amazon CloudFront
- StackPath

## 🎯 Launch Checklist

Before going live:

- [ ] All content reviewed and approved
- [ ] Contact information verified
- [ ] Test all forms and functionality
- [ ] Check mobile responsiveness
- [ ] Verify SSL certificate
- [ ] Test page load speed
- [ ] Spell check all text
- [ ] Test all links
- [ ] Add favicon
- [ ] Set up email alerts
- [ ] Configure backups
- [ ] Add analytics
- [ ] Submit to search engines
- [ ] Share on social media
- [ ] Monitor for first 48 hours

## 📞 Support

If you need help:

**Technical Support:**
- Email: nexorait@outlook.com
- Phone: +94 77 635 0902
- WhatsApp: +94 70 671 7131

**Hosting Support:**
Contact your hosting provider for:
- Server configuration
- PHP settings
- Email issues
- SSL certificates

## 📚 Additional Resources

- **cPanel Documentation:** https://docs.cpanel.net/
- **PHP Manual:** https://www.php.net/manual/
- **Web Hosting Guide:** Contact your provider
- **WordPress Alternative:** If you prefer CMS

## ✅ Success!

Once deployed, your website will be live at:
- https://yourdomain.com

Remember to:
1. Monitor contact form submissions
2. Update content regularly
3. Keep backups current
4. Check analytics weekly
5. Respond to inquiries promptly

---

**Congratulations on your new website! 🎉**

*For updates or modifications, edit the HTML, CSS, and JS files directly or contact us for assistance.*