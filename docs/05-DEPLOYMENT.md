# Deployment Guide - Nubilux v2.0

## 🚀 Production Deployment

### Forutsetninger
- Ubuntu 22.04 eller nyere
- Apache 2.4+ eller Nginx
- PHP 8.1+
- MySQL 8.0+ eller MariaDB 10.6+
- SSL-sertifikat (Let's Encrypt anbefales)

### Steg-for-steg Deployment

#### 1. Server Setup
```bash
# Oppdater system
sudo apt update && sudo apt upgrade -y

# Installer nødvendige pakker
sudo apt install apache2 php8.1 php8.1-mysql php8.1-curl php8.1-json php8.1-mbstring php8.1-xml mysql-server git unzip

# Aktiver Apache moduler
sudo a2enmod rewrite ssl headers

# Restart Apache
sudo systemctl restart apache2
```

#### 2. Database Setup
```bash
# Sikre MySQL
sudo mysql_secure_installation

# Opprett database og bruker
sudo mysql -u root -p
```

```sql
CREATE DATABASE nubilux CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'nubilux'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON nubilux.* TO 'nubilux'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### 3. Deploy Application
```bash
# Gå til web root
cd /var/www

# Clone repository
sudo git clone https://github.com/Frawaa78/nubilux.git
sudo chown -R www-data:www-data nubilux
sudo chmod -R 755 nubilux

# Lag storage directories
sudo mkdir -p nubilux/storage/{uploads,logs,cache,sessions}
sudo chown -R www-data:www-data nubilux/storage
sudo chmod -R 775 nubilux/storage
```

#### 4. Environment Configuration
```bash
# Kopier environment template
cd /var/www/nubilux
sudo cp .env.example .env
sudo chown www-data:www-data .env
sudo chmod 600 .env
```

Rediger `.env`:
```ini
# Database
DB_HOST=localhost
DB_NAME=nubilux
DB_USER=nubilux
DB_PASS=strong_password_here

# SendGrid
SENDGRID_API_KEY=your_sendgrid_api_key_here

# App
APP_URL=https://nubilux.com
APP_ENV=production
APP_DEBUG=false

# Security
SESSION_SECURE=true
SESSION_HTTPONLY=true
CSRF_PROTECTION=true
```

#### 5. Apache Virtual Host
```bash
sudo nano /etc/apache2/sites-available/nubilux.conf
```

```apache
<VirtualHost *:80>
    ServerName nubilux.com
    ServerAlias www.nubilux.com
    DocumentRoot /var/www/nubilux
    
    # Redirect to HTTPS
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</VirtualHost>

<VirtualHost *:443>
    ServerName nubilux.com
    ServerAlias www.nubilux.com
    DocumentRoot /var/www/nubilux
    
    # SSL Configuration
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/nubilux.com/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/nubilux.com/privkey.pem
    
    # Security Headers
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Strict-Transport-Security "max-age=63072000; includeSubDomains; preload"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    
    # PHP Configuration
    php_admin_value upload_max_filesize 32M
    php_admin_value post_max_size 32M
    php_admin_value memory_limit 256M
    php_admin_value max_execution_time 300
    
    # Directory Configuration
    <Directory /var/www/nubilux>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
        
        # Security: Prevent access to sensitive files
        <FilesMatch "^\.">
            Require all denied
        </FilesMatch>
        
        <FilesMatch "\.(env|md|json|lock)$">
            Require all denied
        </FilesMatch>
    </Directory>
    
    # Protect storage directory
    <Directory /var/www/nubilux/storage>
        Options -Indexes
        AllowOverride None
        Require all denied
    </Directory>
    
    # Allow uploads directory for serving files
    <Directory /var/www/nubilux/storage/uploads>
        Options -Indexes
        AllowOverride None
        Require all granted
    </Directory>
    
    # Logging
    ErrorLog ${APACHE_LOG_DIR}/nubilux_error.log
    CustomLog ${APACHE_LOG_DIR}/nubilux_access.log combined
</VirtualHost>
```

#### 6. SSL Certificate (Let's Encrypt)
```bash
# Installer Certbot
sudo apt install certbot python3-certbot-apache

# Generer sertifikat
sudo certbot --apache -d nubilux.com -d www.nubilux.com

# Test auto-renewal
sudo certbot renew --dry-run
```

#### 7. Enable Site
```bash
# Aktiver site
sudo a2ensite nubilux.conf
sudo a2dissite 000-default.conf

# Test configuration
sudo apache2ctl configtest

# Restart Apache
sudo systemctl restart apache2
```

#### 8. Database Migration
```bash
# Import database schema
mysql -u nubilux -p nubilux < /var/www/nubilux/database/schema.sql

# Run migrations (if any)
php /var/www/nubilux/database/migrate.php
```

## 🔧 Development Deployment

### Local Development Setup
```bash
# Clone repository
git clone https://github.com/Frawaa78/nubilux.git
cd nubilux

# Copy environment
cp .env.example .env

# Setup local database
mysql -u root -p
```

```sql
CREATE DATABASE nubilux_dev;
GRANT ALL PRIVILEGES ON nubilux_dev.* TO 'dev'@'localhost' IDENTIFIED BY 'devpassword';
```

### Docker Development (Valgfritt)
```yaml
# docker-compose.yml
version: '3.8'
services:
  web:
    image: php:8.1-apache
    ports:
      - "8000:80"
    volumes:
      - .:/var/www/html
    environment:
      - DB_HOST=db
      - DB_NAME=nubilux
      - DB_USER=root
      - DB_PASS=rootpassword
    depends_on:
      - db

  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: rootpassword
      MYSQL_DATABASE: nubilux
    volumes:
      - db_data:/var/lib/mysql
    ports:
      - "3306:3306"

  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    ports:
      - "8080:80"
    environment:
      - PMA_HOST=db
      - PMA_USER=root
      - PMA_PASSWORD=rootpassword

volumes:
  db_data:
```

```bash
# Start development environment
docker-compose up -d
```

## 🔄 CI/CD Pipeline

### GitHub Actions Workflow
```yaml
# .github/workflows/deploy.yml
name: Deploy to Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.1'
        
    - name: Install dependencies
      run: composer install --no-dev --optimize-autoloader
      
    - name: Run tests
      run: php vendor/bin/phpunit
      
    - name: Deploy to server
      uses: appleboy/ssh-action@v0.1.5
      with:
        host: ${{ secrets.HOST }}
        username: ${{ secrets.USERNAME }}
        key: ${{ secrets.SSH_KEY }}
        script: |
          cd /var/www/nubilux
          git pull origin main
          composer install --no-dev --optimize-autoloader
          php database/migrate.php
          sudo systemctl reload apache2
```

## 📊 Monitoring & Maintenance

### Log Rotation
```bash
# /etc/logrotate.d/nubilux
/var/www/nubilux/storage/logs/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    copytruncate
    su www-data www-data
}
```

### Backup Script
```bash
#!/bin/bash
# /usr/local/bin/nubilux-backup.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/nubilux"

# Create backup directory
mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u nubilux -p$DB_PASS nubilux > $BACKUP_DIR/db_$DATE.sql

# Files backup
tar -czf $BACKUP_DIR/files_$DATE.tar.gz -C /var/www nubilux

# Remove old backups (keep 30 days)
find $BACKUP_DIR -name "*.sql" -mtime +30 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete

echo "Backup completed: $DATE"
```

### Cron Jobs
```bash
# crontab -e (as root)

# Daily backup
0 2 * * * /usr/local/bin/nubilux-backup.sh

# Clean old sessions (hourly)
0 * * * * find /var/www/nubilux/storage/sessions -name "sess_*" -mtime +1 -delete

# Update weather cache (every 15 minutes)
*/15 * * * * curl -s https://nubilux.com/api/v1/weather/current > /dev/null

# SSL certificate renewal (monthly)
0 3 1 * * certbot renew --quiet
```

### Health Check Script
```bash
#!/bin/bash
# /usr/local/bin/nubilux-health.sh

URL="https://nubilux.com"
RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" $URL)

if [ $RESPONSE -eq 200 ]; then
    echo "OK: Site is responding"
    exit 0
else
    echo "ERROR: Site returned HTTP $RESPONSE"
    # Send alert email
    echo "Nubilux health check failed" | mail -s "Site Down Alert" admin@nubilux.com
    exit 1
fi
```

## 🚨 Troubleshooting

### Common Issues

#### Permission Problems
```bash
# Fix file permissions
sudo chown -R www-data:www-data /var/www/nubilux
sudo chmod -R 755 /var/www/nubilux
sudo chmod -R 775 /var/www/nubilux/storage
```

#### Database Connection Issues
```bash
# Test database connection
php -r "
$pdo = new PDO('mysql:host=localhost;dbname=nubilux', 'nubilux', 'password');
echo 'Database connection OK';
"
```

#### Apache Issues
```bash
# Check Apache status
sudo systemctl status apache2

# Check Apache error logs
sudo tail -f /var/log/apache2/error.log

# Test Apache configuration
sudo apache2ctl configtest
```

#### SSL Issues
```bash
# Check SSL certificate
openssl x509 -in /etc/letsencrypt/live/nubilux.com/fullchain.pem -text -noout

# Test SSL configuration
curl -I https://nubilux.com
```

### Performance Optimization

#### PHP OpCache
```ini
# /etc/php/8.1/apache2/conf.d/10-opcache.ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
opcache.validate_timestamps=0
```

#### MySQL Optimization
```ini
# /etc/mysql/mysql.conf.d/mysqld.cnf
[mysqld]
innodb_buffer_pool_size=1G
innodb_log_file_size=256M
innodb_flush_log_at_trx_commit=2
query_cache_type=1
query_cache_size=64M
```

Dette skal gi deg en komplett guide for å deploye Nubilux i både development og production miljøer! 🚀