# CouponHub - Professional Coupons & Deals Website

A complete, production-ready coupon and deals website built with PHP 8+ and MySQL, optimized for shared hosting environments like Hostinger.

![CouponHub](https://via.placeholder.com/800x400?text=CouponHub+Dashboard)

## ✨ Features

### Frontend
- **Responsive Modern UI** - TailwindCSS-powered design that works on all devices
- **Dark/Light Mode** - User preference toggle with system detection
- **Multi-language Support** - Arabic (RTL) and English ready
- **SEO Optimized** - Schema.org markup, sitemap, robots.txt, canonical URLs
- **AJAX Search** - Real-time search with autocomplete suggestions
- **Countdown Timers** - Expiry countdown for time-sensitive deals

### Admin Panel
- **Professional Dashboard** - Statistics and quick actions overview
- **Coupons Manager** - Full CRUD with affiliate links, codes, and status management
- **Stores Manager** - Logo upload, SEO fields, category assignment
- **Articles/Blog** - WordPress-style rich text editor (TinyMCE)
- **Appearance Settings** - Logo, colors, gradients, hero section customization
- **SEO Settings** - Meta tags, Open Graph, Google Analytics, social links
- **User Management** - Admin, Editor, and Writer roles

### Security
- ✅ CSRF Protection on all forms
- ✅ SQL Injection prevention (PDO prepared statements)
- ✅ XSS Protection with input sanitization
- ✅ Bcrypt password hashing
- ✅ Session timeout management
- ✅ File upload MIME-type validation
- ✅ Rate limiting for login attempts

## 🚀 Quick Start (Hostinger Shared Hosting)

### Step 1: Upload Files
1. Download/clone this repository
2. Upload all files to your `public_html` folder via FTP or File Manager
3. Ensure `.htaccess` file is uploaded (enable "show hidden files")

### Step 2: Create Database
1. Log in to Hostinger hPanel
2. Go to **Databases** → **MySQL Databases**
3. Create a new database (e.g., `coupon_website`)
4. Note down the database name, username, and password

### Step 3: Import Database
1. Go to **Databases** → **phpMyAdmin**
2. Select your database
3. Click **Import** tab
4. Choose the `database.sql` file from the repository
5. Click **Go** to import

### Step 4: Configure Database Connection
1. Open `config/db.php` in a text editor
2. Update these values with your Hostinger credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_database_username');
define('DB_PASS', 'your_database_password');
```

### Step 5: Set File Permissions
Via File Manager or FTP, set these permissions:
- `public/uploads/` - **755** or **775** (writable)
- `config/` files - **644** (readable only)

### Step 6: Access Your Site
- **Frontend**: `https://yourdomain.com`
- **Admin Panel**: `https://yourdomain.com/admin/login.php`
- **Default Login**: 
  - Username: `admin`
  - Password: `password`

⚠️ **Change the default password immediately after first login!**

## 📁 Folder Structure

```
/root
├── index.php              # Main entry point & router
├── database.sql           # Database schema
├── sitemap-generator.php  # Dynamic XML sitemap
├── .htaccess              # Apache rewrite rules
├── README.md              # This file
│
├── /app
│   ├── /controllers       # (Reserved for future use)
│   ├── /models            # Database models
│   │   ├── Article.php
│   │   ├── Category.php
│   │   ├── Coupon.php
│   │   ├── Store.php
│   │   └── User.php
│   ├── /views
│   │   ├── /frontend      # Public pages
│   │   ├── /admin         # Admin pages
│   │   └── /partials      # Reusable components
│   ├── /helpers
│   │   ├── functions.php  # Utility functions
│   │   └── security.php   # Security helpers
│   ├── /lang
│   │   ├── en.php         # English translations
│   │   └── ar.php         # Arabic translations
│   └── router.php         # URL routing
│
├── /public
│   ├── /css/style.css     # Custom styles
│   ├── /js/main.js        # JavaScript functions
│   ├── /images            # Static images
│   └── /uploads           # User uploads (logos, images)
│
├── /admin
│   ├── login.php          # Admin login
│   ├── logout.php         # Logout handler
│   ├── dashboard.php      # Main dashboard
│   ├── /includes          # Admin header/footer
│   ├── /coupons           # Coupon management
│   ├── /stores            # Store management
│   ├── /articles          # Article/blog management
│   ├── /categories        # Category management
│   ├── /settings          # Site settings
│   └── /users             # User management
│
└── /config
    ├── config.php         # Application config
    └── db.php             # Database connection
```

## 🔧 Configuration

### Database Settings (`config/db.php`)
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'coupon_website');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### Application Settings (`config/config.php`)
Most settings are managed through the admin panel, but you can modify:
- `SESSION_TIMEOUT` - Session expiry in minutes
- `MAX_UPLOAD_SIZE` - Maximum file upload size
- `ITEMS_PER_PAGE` - Pagination limit

## 🎨 Customization

### Through Admin Panel
1. **Appearance Settings**: Change logo, colors, gradients, hero section
2. **SEO Settings**: Meta tags, social links, analytics
3. **General Settings**: Site name, language, timezone

### Through Code
- **Styles**: Modify `public/css/style.css`
- **JavaScript**: Edit `public/js/main.js`
- **Templates**: Update files in `app/views/`

## 📝 User Roles

| Role | Permissions |
|------|-------------|
| **Admin** | Full access to all features |
| **Editor** | Manage coupons, stores, articles, categories |
| **Writer** | Create and edit articles only |

## 🔒 Security Best Practices

1. **Change Default Password** - First thing after installation
2. **Use HTTPS** - Enable SSL certificate
3. **Regular Backups** - Use Hostinger's backup feature
4. **Keep Files Updated** - Monitor for security updates
5. **Strong Passwords** - Enforce strong passwords for all users

## 🐛 Troubleshooting

### Common Issues

**500 Internal Server Error**
- Check `.htaccess` syntax
- Verify PHP version is 8.0+
- Check file permissions

**Database Connection Failed**
- Verify credentials in `config/db.php`
- Ensure database exists
- Check MySQL service is running

**Images Not Uploading**
- Set `uploads/` folder to 755
- Check PHP `upload_max_filesize` setting
- Verify file type is allowed (jpg, png, gif, webp)

**Admin Login Not Working**
- Clear browser cache/cookies
- Verify database import was successful
- Check if user exists in `users` table

## 📈 SEO Features

- **Auto-generated Sitemap** - `/sitemap.xml`
- **Dynamic Robots.txt** - `/robots.txt`
- **Schema.org Markup** - Article, Store, Offer schemas
- **Canonical URLs** - Prevents duplicate content
- **Open Graph Tags** - Optimized social sharing
- **Breadcrumbs** - Enhanced navigation and SEO

## 🌐 Multi-language Support

The system supports:
- **English (en)** - Default
- **Arabic (ar)** - RTL support included

To add more languages:
1. Create new file in `app/lang/` (e.g., `fr.php`)
2. Copy structure from `en.php`
3. Translate all strings
4. Add language code to `AVAILABLE_LANGUAGES` in `config.php`

## 📄 License

This project is open source and available for personal and commercial use.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit pull requests or create issues for bugs and feature requests.

## 📞 Support

For issues and questions:
- Create a GitHub issue
- Check the troubleshooting section above

---

**Built with ❤️ for shared hosting environments**