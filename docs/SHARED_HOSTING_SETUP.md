# Laravel on Shared Hosting Setup Guide

## Overview

Shared hosting environments have limitations, but Laravel can still work with some adjustments. The main challenge is that you can't change the Apache DocumentRoot, so we need to work around this.

## Common Shared Hosting Structure

Most shared hosting providers have a structure like:
```
/home/username/
├── public_html/          # Web-accessible directory (DocumentRoot)
├── laravel-app/          # Your Laravel app (outside public_html for security)
├── logs/
└── tmp/
```

## Setup Methods

### Method 1: Symlink Method (Recommended)

This method keeps your Laravel app secure outside the web root:

1. **Upload your Laravel app outside public_html:**
   ```
   /home/username/laravel-app/
   ```

2. **Create symlinks in public_html:**
   ```bash
   cd public_html
   ln -s ../laravel-app/public/* .
   ln -s ../laravel-app/public/.htaccess .
   ```

3. **Update paths in index.php:**
   Edit `public_html/index.php` and update the paths:
   ```php
   require __DIR__.'/../laravel-app/vendor/autoload.php';
   $app = require_once __DIR__.'/../laravel-app/bootstrap/app.php';
   ```

### Method 2: Public Directory Method

Move Laravel's public contents to public_html:

1. **Upload Laravel app outside public_html**
2. **Move public directory contents:**
   ```bash
   mv laravel-app/public/* public_html/
   mv laravel-app/public/.htaccess public_html/
   ```
3. **Update index.php paths** (same as Method 1)

### Method 3: Subdirectory Method

If you want Laravel in a subdirectory:

1. **Create subdirectory in public_html:**
   ```bash
   mkdir public_html/app
   ```
2. **Upload Laravel to the subdirectory**
3. **Configure accordingly**

## Requirements Check

Most shared hosting should have:
- ✅ PHP 8.1+ (check with your provider)
- ✅ Required PHP extensions
- ✅ Composer (may need to install manually)
- ❌ SSH access (varies by provider)
- ❌ Root access (not available)

## File Permissions

Shared hosting typically handles permissions automatically, but you may need:
```bash
chmod 755 storage/
chmod 755 bootstrap/cache/
```

## Database Configuration

Shared hosting usually provides:
- MySQL database
- Database credentials via control panel
- phpMyAdmin access

Update your `.env` file with the provided credentials.

## Common Issues and Solutions

### Issue 1: "500 Internal Server Error"
**Causes:**
- Missing `.htaccess` file
- Wrong file permissions
- PHP version incompatibility
- Missing `.env` file

**Solutions:**
- Ensure `.htaccess` is in the web root
- Check error logs in your hosting control panel
- Verify PHP version meets requirements
- Ensure `.env` file exists and is configured

### Issue 2: "Composer not found"
**Solution:**
Download composer.phar to your account:
```bash
cd /home/username
curl -sS https://getcomposer.org/installer | php
php composer.phar install --no-dev
```

### Issue 3: "Storage not writable"
**Solution:**
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### Issue 4: "App key not set"
**Solution:**
```bash
php artisan key:generate
```

## Optimization for Shared Hosting

1. **Cache configuration:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

2. **Optimize autoloader:**
   ```bash
   composer install --optimize-autoloader --no-dev
   ```

3. **Set production environment:**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

## Security Considerations

1. **Keep Laravel app outside public_html**
2. **Use strong database passwords**
3. **Keep `.env` file secure**
4. **Regular updates**
5. **Monitor error logs**

## Hosting Provider Specific Notes

### cPanel Hosting
- Use File Manager or FTP
- Database via MySQL Databases section
- Error logs in Error Logs section
- PHP version in Select PHP Version

### Plesk Hosting
- Use File Manager
- Database via Databases section
- Check PHP settings in PHP Settings

### DirectAdmin
- Use File Manager
- Database via MySQL Management
- PHP version in PHP Settings

## Troubleshooting Commands

If you have SSH access:
```bash
# Check PHP version
php -v

# Check Laravel installation
php artisan --version

# Check routes
php artisan route:list

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Check permissions
ls -la storage/
ls -la bootstrap/cache/
```

## Alternative: Laravel Forge or Similar Services

If shared hosting proves too limiting, consider:
- Laravel Forge
- DigitalOcean App Platform
- Heroku
- Vercel (for static sites)

These provide better Laravel support with proper server configuration.