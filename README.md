# PRODIGY_FS_TASK03

## E-Commerce Store - FreshVault

A modern, full-featured e-commerce web application built with PHP, MySQL, and Tailwind CSS. This project features a complete online store with user authentication, shopping cart, order management, payment integration, and a comprehensive admin panel.

## 🚀 Features

### Customer Features
- **User Authentication**: Secure registration, login, and password reset
- **Product Browsing**: Browse products by category with search functionality
- **Shopping Cart**: Add/remove items, update quantities
- **Wishlist**: Save favorite products for later
- **Order Management**: Place orders, track status, view order history
- **User Profile**: Manage personal information and addresses
- **Payment Integration**: Razorpay payment gateway support
- **Email Notifications**: Order confirmations and status updates

### Admin Features
- **Dashboard**: Overview of store statistics and metrics
- **Product Management**: Add, edit, delete products with image uploads
- **Category Management**: Organize products into categories
- **Order Management**: View and update order statuses
- **Customer Management**: View customer information
- **Inventory Tracking**: Monitor stock levels
- **Analytics**: Sales reports and performance metrics
- **Settings**: Configure store settings

## 🎨 Design

- **Modern UI**: Clean, responsive design using Tailwind CSS
- **Purple Theme**: User-facing pages with indigo gradient sidebar
- **Pink Theme**: Admin panel with pink/rose gradient sidebar
- **Mobile Responsive**: Fully responsive with slide-in navigation
- **Toast Notifications**: User-friendly feedback system

## 📋 Prerequisites

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx) or PHP built-in server
- Composer (optional, for dependencies)

## 🛠️ Installation

### 1. Clone the Repository
```bash
git clone https://github.com/MimansaPatle/PRODIGY_FS_TASKS03.git
cd PRODIGY_FS_TASKS03
```

### 2. Database Setup
```bash
# Create database
mysql -u root -p
CREATE DATABASE local_store_db;
exit;

# Import schema
mysql -u root -p local_store_db < database/schema.sql
```

### 3. Configuration
```bash
# Copy example config
cp src/config.php.example src/config.php

# Edit src/config.php with your settings:
# - Database credentials
# - Email settings (Gmail App Password)
# - Razorpay API keys (for payments)
```

### 4. Run Migrations
```bash
php setup-database.php
php run-migrations.php
```

### 5. Create Admin User
```bash
php create-admin.php
# Default: admin@localstore.com / admin123
```

### 6. Start Server
```bash
# Windows
start-server.bat

# Linux/Mac
php -S localhost:8000 -t public
```

### 7. Access Application
- **Store**: http://localhost:8000
- **Admin Panel**: http://localhost:8000/admin/
- **Admin Login**: admin@localstore.com / admin123

## 📁 Project Structure

```
PRODIGY_FS_TASK03/
├── database/           # SQL schema and migrations
├── logs/              # Application logs
├── public/            # Public web root
│   ├── admin/         # Admin panel pages
│   ├── assets/        # CSS, JS, images
│   └── uploads/       # Product images
├── src/               # Core application code
│   ├── includes/      # Reusable components
│   ├── models/        # Database models
│   ├── auth.php       # Authentication
│   ├── cart.php       # Shopping cart logic
│   ├── config.php     # Configuration (not in repo)
│   ├── db.php         # Database connection
│   └── email.php      # Email service
└── README.md
```

## 🔧 Configuration

### Email Setup (Gmail)
1. Enable 2-Factor Authentication in Gmail
2. Generate App Password: https://myaccount.google.com/apppasswords
3. Update `src/config.php`:
```php
define('ADMIN_EMAIL', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-16-char-app-password');
```

### Payment Setup (Razorpay)
1. Sign up at https://razorpay.com
2. Get API keys from Dashboard
3. Update `src/config.php`:
```php
define('RAZORPAY_KEY_ID', 'your-key-id');
define('RAZORPAY_KEY_SECRET', 'your-key-secret');
define('PAYMENT_ENABLED', true);
```

## 🎯 Usage

### Customer Workflow
1. Browse products on homepage
2. Add items to cart or wishlist
3. Register/Login
4. Proceed to checkout
5. Enter shipping details
6. Complete payment
7. Track order status

### Admin Workflow
1. Login to admin panel
2. Add products and categories
3. Manage inventory
4. Process orders
5. Update order statuses
6. View analytics and reports

## 🔐 Security Features

- Password hashing with bcrypt
- SQL injection prevention with prepared statements
- XSS protection with htmlspecialchars
- CSRF protection for forms
- Session security with httponly cookies
- Input validation and sanitization

## 🌐 Technologies Used

- **Backend**: PHP 8.x
- **Database**: MySQL
- **Frontend**: HTML5, Tailwind CSS, JavaScript
- **Payment**: Razorpay API
- **Email**: PHP mail() / SMTP

## 📱 Responsive Design

- Desktop: Full sidebar navigation
- Tablet: Collapsible sidebar
- Mobile: Slide-in menu with toggle button

## 🐛 Troubleshooting

### Database Connection Issues
```bash
# Check MySQL is running
mysql -u root -p

# Verify credentials in src/config.php
```

### Email Not Sending
```bash
# Check Gmail App Password is correct
# Verify SMTP settings in src/config.php
# Check logs/emails.log for errors
```

### Image Upload Issues
```bash
# Ensure uploads directory is writable
chmod 755 public/uploads/products/
```

## 📝 License

This project is created as part of Prodigy InfoTech internship tasks.

## 👤 Author

**Mimansa Patle**
- GitHub: [@MimansaPatle](https://github.com/MimansaPatle)

## 🙏 Acknowledgments

- Prodigy InfoTech for the internship opportunity
- Tailwind CSS for the styling framework
- Razorpay for payment gateway integration

## 📞 Support

For issues or questions, please open an issue on GitHub.

---

**Note**: Remember to update `src/config.php` with your actual credentials before deploying to production. Never commit sensitive credentials to version control.
