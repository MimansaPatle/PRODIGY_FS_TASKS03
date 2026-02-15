# Installation Guide

## Quick Start

### 1. Clone Repository
```bash
git clone https://github.com/MimansaPatle/PRODIGY_FS_TASKS03.git
cd PRODIGY_FS_TASKS03
```

### 2. Configure
```bash
# Copy config example
cp src/config.php.example src/config.php

# Edit src/config.php with your settings
```

### 3. Setup Database
```bash
# Create database
mysql -u root -p
CREATE DATABASE local_store_db;
exit;

# Run setup scripts
php setup-database.php
php run-migrations.php
php create-admin.php
```

### 4. Start Server
```bash
# Windows
start-server.bat

# Linux/Mac
php -S localhost:8000 -t public
```

### 5. Access Application
- Store: http://localhost:8000
- Admin: http://localhost:8000/admin/
- Login: admin@localstore.com / admin123

## Configuration Details

### Email Setup (Gmail)
1. Enable 2FA in Gmail
2. Generate App Password: https://myaccount.google.com/apppasswords
3. Update in `src/config.php`:
```php
define('ADMIN_EMAIL', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'xxxx xxxx xxxx xxxx');
```

### Payment Setup (Razorpay)
1. Sign up: https://razorpay.com
2. Get API keys from Dashboard
3. Update in `src/config.php`:
```php
define('RAZORPAY_KEY_ID', 'rzp_test_xxxxx');
define('RAZORPAY_KEY_SECRET', 'xxxxx');
define('PAYMENT_ENABLED', true);
```

## Troubleshooting

### Database Connection Error
- Check MySQL is running
- Verify credentials in `src/config.php`

### Email Not Sending
- Verify Gmail App Password
- Check `logs/emails.log`

### Image Upload Issues
```bash
chmod 755 public/uploads/products/
```

## Requirements
- PHP 8.0+
- MySQL 5.7+
- Web server or PHP built-in server
