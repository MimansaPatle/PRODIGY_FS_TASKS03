# 🚀 LOCAL PANTRY - Setup Guide

This guide will help you set up the LOCAL PANTRY e-commerce platform on your local machine or server.

## 📋 Prerequisites

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Composer (optional)

## 🛠️ Installation Steps

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/local-pantry.git
cd local-pantry
```

### 2. Database Setup

1. Create a MySQL database:
```sql
CREATE DATABASE local_pantry;
```

2. Import the database schema:
```bash
mysql -u root -p local_pantry < database/schema.sql
```

### 3. Configuration Setup

1. Copy the example configuration:
```bash
cp src/config.php.example src/config.php
```

2. Edit `src/config.php` with your settings:

```php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'local_pantry');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');

// Site Configuration
define('SITE_NAME', 'LOCAL PANTRY');
define('SITE_URL', 'http://your-domain.com');

// Email Configuration
define('ADMIN_EMAIL', 'admin@yourstore.com');
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');

// Payment Configuration (Optional)
define('RAZORPAY_KEY_ID', 'your_razorpay_key_id');
define('RAZORPAY_KEY_SECRET', 'your_razorpay_key_secret');
define('PAYMENT_ENABLED', false); // Set to true after adding real keys

// WhatsApp Configuration
define('WHATSAPP_NUMBER', '+91 your_number');
```

### 4. File Permissions

Set proper permissions for upload directories:

```bash
chmod 755 public/uploads/
chmod 755 logs/
```

### 5. Create Admin Account

Run the admin creation script:

```bash
php create-admin.php
```

Follow the prompts to create your admin account.

### 6. Web Server Configuration

#### Apache (.htaccess)
The project includes `.htaccess` files for Apache. Make sure mod_rewrite is enabled.

#### Nginx
Add this to your Nginx configuration:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/local-pantry/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 🔧 Configuration Details

### Email Setup (Gmail SMTP)

1. Enable 2-Factor Authentication on your Gmail account
2. Generate an App Password:
   - Go to Google Account settings
   - Security → 2-Step Verification → App passwords
   - Generate a password for "Mail"
3. Use this app password in `SMTP_PASSWORD`

### Payment Gateway Setup (Razorpay)

1. Sign up at [Razorpay](https://razorpay.com)
2. Get your API keys from the dashboard
3. Add keys to config.php
4. Set `PAYMENT_ENABLED` to `true`

### WhatsApp Integration

Update the WhatsApp number in config.php for payment instructions and support.

## 🎯 Features Overview

### Customer Features
- Product browsing and search
- Shopping cart and wishlist
- User registration and profiles
- Order tracking
- Multiple payment options (COD, UPI, Bank Transfer)

### Admin Features
- Dashboard with analytics
- Product management
- Inventory tracking
- Order management
- Customer management

## 🔒 Security Notes

- Never commit `src/config.php` to version control
- Use strong passwords for database and admin accounts
- Keep your PHP and MySQL versions updated
- Use HTTPS in production
- Regularly backup your database

## 📞 Support

For support and questions:
- Check the documentation in the repository
- Create an issue on GitHub
- Contact: [Your Contact Information]

## 🚀 Going Live

### Production Checklist

1. **Security**:
   - Set `error_reporting(0)` and `ini_set('display_errors', 0)`
   - Use HTTPS
   - Set strong database passwords
   - Configure proper file permissions

2. **Performance**:
   - Enable PHP OPcache
   - Configure proper caching headers
   - Optimize images
   - Use a CDN if needed

3. **Backup**:
   - Set up automated database backups
   - Backup uploaded files
   - Test restore procedures

4. **Monitoring**:
   - Set up error logging
   - Monitor server resources
   - Set up uptime monitoring

---

**Built with ❤️ for local businesses**