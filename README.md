# Nubilux

**Smart Home Dashboard Platform** - Intelligent family dashboard for wall-mounted Android displays

## 🚀 Live Production
- **URL:** https://nubilux.com
- **VPS:** Servetheworld GP32 (Ubuntu 22.04)
- **Database:** MySQL 8.0 
- **SSL:** Let's Encrypt certificates
- **Admin:** https://nubilux.com/phpmyadmin/

## ✨ Features

### 👥 User Management
- ✅ User registration with email verification
- ✅ Secure login/logout system  
- ✅ Password reset via email
- ✅ Session management

### 📧 Email System
- ✅ SendGrid API integration
- ✅ HTML email templates
- ✅ Email verification workflow
- ✅ Password reset emails

### 🏠 Household Management
- 🔄 Multi-user households (planned)
- 🔄 Role-based permissions (planned)
- 🔄 Household member management (planned)

## 🏗️ Architecture

```
nubilux/
├── config/
│   └── database.php      # Database configuration
├── models/
│   └── User.php          # User model with authentication
├── services/
│   └── EmailService.php  # SendGrid email service
├── views/
│   ├── home.php          # Homepage template
│   ├── register.php      # Registration template
│   ├── login.php         # Login template
│   └── dashboard.php     # Dashboard template
├── index.php             # Homepage controller
├── register.php          # Registration controller
├── login.php             # Login controller
├── logout.php            # Logout controller
└── dashboard.php         # Dashboard controller
```

## 🛠️ Technology Stack

- **Backend:** PHP 8.1
- **Database:** MySQL 8.0
- **Web Server:** Apache 2.4
- **Email:** SendGrid API
- **SSL:** Let's Encrypt
- **Hosting:** Servetheworld VPS

## 🚀 Deployment

### Production Server
```bash
# Connect via SSH
ssh -i ~/.ssh/nubilux_key root@83.143.87.106

# Application path
/var/www/nubilux/
```

### Database Structure
```sql
-- Users table with email verification
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    email_verified TINYINT(1) DEFAULT 0,
    verification_token VARCHAR(64),
    password_reset_token VARCHAR(64),
    password_reset_expires TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1
);

-- Households table (future feature)
CREATE TABLE households (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    owner_user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (owner_user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## 🔮 Future Features

- 📊 Energy monitoring dashboard
- 📅 Family activity calendar  
- 🚗 Transport planning
- 🏠 Smart home device integration
- 📱 Mobile app companion
- 🎨 Customizable dashboard widgets

## 🔐 Security Features

- ✅ Password hashing (PHP password_hash)
- ✅ SQL injection prevention (PDO prepared statements)
- ✅ Email verification system
- ✅ Secure session management
- ✅ HTTPS enforcement
- ✅ Input validation and sanitization

## 📝 Development Notes

This project evolved from a complex Docker/Azure setup to a simple, cost-effective VPS solution hosted on Servetheworld. The focus is on reliability, simplicity, and maintainability.

**Cost Comparison:**
- Previous Azure setup: ~300+ kr/month
- Current VPS setup: ~200 kr/month
- Much better performance and control

---
*Built with ❤️ for smart family living*