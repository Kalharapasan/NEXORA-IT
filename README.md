# Nexora Website - Complete Package

A modern, responsive business website with advanced features including contact form, animations, and mobile optimization.

## 📁 File Structure

```
nexora-website/
├── index.html              # Main HTML file
├── css/
│   └── style.css          # All styles and animations
├── js/
│   └── main.js            # JavaScript functionality
├── php/
│   ├── contact.php        # Contact form handler
│   └── config.php         # Configuration settings
└── README.md              # This file
```

## 🚀 Features

### Design & UI
- ✨ Modern, professional design with gradient backgrounds
- 🎨 Custom Google Fonts (Outfit & Playfair Display)
- 📱 Fully responsive for all devices
- 🎭 Smooth animations and transitions
- 🌊 Parallax effects and floating particles
- 💫 Scroll-triggered fade-in animations
- 🔝 Back to top button

### Functionality
- 📧 Working contact form with PHP email handler
- 📊 Animated statistics counter
- 🎯 Smooth scroll navigation
- 📲 Mobile menu with hamburger icon
- 🔄 Testimonial slider (auto-rotating)
- 🎬 Loading screen animation
- ♿ Accessibility features

### Sections
1. **Hero Header** - Eye-catching introduction with CTAs
2. **Statistics** - Animated counters showing achievements
3. **About** - Company overview with image
4. **Services** - 6 detailed service cards
5. **POS Systems** - Product showcase with features
6. **Testimonials** - Client success stories
7. **Contact** - Multiple contact options + form
8. **Footer** - Complete site links and information

## 📋 Setup Instructions

### 1. Upload Files
Upload all files to your web server maintaining the folder structure.

### 2. Configure PHP
Edit `php/config.php` to update:
- Company information
- Contact details (already set to your info)
- Social media links
- Email settings

### 3. Test Contact Form
Make sure your server supports PHP mail() function or configure SMTP.

### 4. Update Content
Edit `index.html` to customize:
- Images (replace Unsplash URLs with your own)
- Text content
- Service descriptions
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
    <h2 class="section-title">Section Title</h2>
    <p class="section-subtitle">Description</p>
  </div>
  <!-- Your content here -->
</section>
```

### Modifying Navigation
Update both desktop and mobile nav in `index.html`:
```html
<nav class="desktop-nav">
  <a href="#new-section">New Section</a>
</nav>

<div class="mobile-menu">
  <nav>
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