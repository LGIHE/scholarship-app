# Apache Configuration for Laravel Application

## Problem
If you see "Apache is functioning normally" instead of your Laravel application, it means Apache is not configured to serve your Laravel app from the correct directory.

## Solution

### Option 1: Virtual Host Configuration (Recommended)

Create a new Apache virtual host configuration file:

**For Ubuntu/Debian:**
```bash
sudo nano /etc/apache2/sites-available/your-app.conf
```

**For CentOS/RHEL:**
```bash
sudo nano /etc/httpd/conf.d/your-app.conf
```

Add the following configuration (replace paths and domain as needed):

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    ServerAlias www.your-domain.com
    DocumentRoot /path/to/your/laravel/app/public
    
    <Directory /path/to/your/laravel/app/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/laravel_error.log
    CustomLog ${APACHE_LOG_DIR}/laravel_access.log combined
</VirtualHost>
```

**Enable the site (Ubuntu/Debian):**
```bash
sudo a2ensite your-app.conf
sudo a2dissite 000-default.conf  # Disable default site
sudo systemctl reload apache2
```

**Restart Apache (CentOS/RHEL):**
```bash
sudo systemctl restart httpd
```

### Option 2: Modify Default Apache Configuration

If you want to serve Laravel from the default Apache directory:

**Ubuntu/Debian:**
```bash
sudo nano /etc/apache2/sites-available/000-default.conf
```

**CentOS/RHEL:**
```bash
sudo nano /etc/httpd/conf/httpd.conf
```

Change the `DocumentRoot` line to point to your Laravel public directory:
```apache
DocumentRoot /path/to/your/laravel/app/public
```

And add a Directory block:
```apache
<Directory /path/to/your/laravel/app/public>
    AllowOverride All
    Require all granted
</Directory>
```

### Option 3: Symlink Method (Quick Fix)

If your Laravel app is in `/var/www/html/your-app/`, you can create a symlink:

```bash
# Remove default Apache index file
sudo rm /var/www/html/index.html

# Create symlink to Laravel public directory
sudo ln -s /var/www/html/your-app/public/* /var/www/html/
sudo ln -s /var/www/html/your-app/public/.htaccess /var/www/html/
```

## Required Apache Modules

Make sure these Apache modules are enabled:

```bash
# Ubuntu/Debian
sudo a2enmod rewrite
sudo a2enmod ssl  # If using HTTPS
sudo systemctl restart apache2

# CentOS/RHEL
# Edit /etc/httpd/conf/httpd.conf and uncomment:
# LoadModule rewrite_module modules/mod_rewrite.so
sudo systemctl restart httpd
```

## File Permissions

Set proper permissions for your Laravel application:

```bash
# Set ownership (replace www-data with your web server user)
sudo chown -R www-data:www-data /path/to/your/laravel/app

# Set directory permissions
sudo find /path/to/your/laravel/app -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /path/to/your/laravel/app -type f -exec chmod 644 {} \;

# Set storage and cache permissions
sudo chmod -R 775 /path/to/your/laravel/app/storage
sudo chmod -R 775 /path/to/your/laravel/app/bootstrap/cache
```

## SSL Configuration (Optional)

For HTTPS, add this to your virtual host:

```apache
<VirtualHost *:443>
    ServerName your-domain.com
    DocumentRoot /path/to/your/laravel/app/public
    
    SSLEngine on
    SSLCertificateFile /path/to/your/certificate.crt
    SSLCertificateKeyFile /path/to/your/private.key
    
    <Directory /path/to/your/laravel/app/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## Troubleshooting

### Check Apache Error Logs
```bash
# Ubuntu/Debian
sudo tail -f /var/log/apache2/error.log

# CentOS/RHEL
sudo tail -f /var/log/httpd/error_log
```

### Check Laravel Logs
```bash
tail -f /path/to/your/laravel/app/storage/logs/laravel.log
```

### Test Apache Configuration
```bash
# Ubuntu/Debian
sudo apache2ctl configtest

# CentOS/RHEL
sudo httpd -t
```

### Common Issues

1. **403 Forbidden Error**: Check file permissions and ownership
2. **500 Internal Server Error**: Check Laravel logs and ensure `.env` file exists
3. **404 Not Found**: Ensure mod_rewrite is enabled and `.htaccess` is working

## Quick Commands Summary

Replace `/path/to/your/laravel/app` with your actual Laravel application path:

```bash
# Find your current directory
pwd

# Set permissions
sudo chown -R www-data:www-data $(pwd)
sudo chmod -R 775 storage bootstrap/cache

# Enable Apache modules
sudo a2enmod rewrite
sudo systemctl restart apache2

# Check if your app is accessible
curl -I http://your-domain.com
```

## Example Complete Virtual Host

Here's a complete example for a production setup:

```apache
<VirtualHost *:80>
    ServerName example.com
    ServerAlias www.example.com
    DocumentRoot /var/www/html/scholarship-app/public
    
    <Directory /var/www/html/scholarship-app/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # Security headers
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    
    # Logging
    ErrorLog ${APACHE_LOG_DIR}/scholarship_error.log
    CustomLog ${APACHE_LOG_DIR}/scholarship_access.log combined
    
    # Redirect to HTTPS (optional)
    # Redirect permanent / https://example.com/
</VirtualHost>
```