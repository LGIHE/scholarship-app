# Setup & Deployment Guide

Complete guide for installing, configuring, and deploying the LGF Scholarship Management System.

---

## Prerequisites

- **PHP 8.2+** with extensions: PDO, mbstring, tokenizer, xml, ctype, json, bcmath, fileinfo, openssl
- **Composer**
- **Node.js + npm** (for frontend assets)
- **Database**: MySQL, PostgreSQL, or SQLite

---

## Quick Start

```bash
# 1. Clone the repository
git clone <repo-url> scholarship_app
cd scholarship_app

# 2. Run the interactive setup script
./setup.sh
```

The setup script handles environment configuration, dependency installation, database setup, and admin user creation.

---

## Manual Setup

```bash
# 1. Copy and edit environment file
cp .env.example .env
nano .env

# 2. Install dependencies
composer install --no-dev --optimize-autoloader
npm install

# 3. Generate app key
php artisan key:generate

# 4. Run migrations
php artisan migrate --force

# 5. Build frontend assets
npm run build

# 6. Create the first admin user via Tinker
php artisan tinker
```

```php
$user = \App\Models\User::create([
    'name' => 'System Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('your-password'),
    'email_verified_at' => now(),
]);
$user->assignRole('System Admin');
```

---

## Environment Variables Reference

```env
# Application
APP_NAME="LGF Scholarship System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database (MySQL example)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=scholarship_db
DB_USERNAME=db_user
DB_PASSWORD=db_password

# Mail — using Resend (see Email Setup below)
MAIL_MAILER=resend
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="Luigi Giussani Foundation"
RESEND_API_KEY=re_your_api_key_here

# Session & Cache
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

---

## Email Setup (Resend)

The app uses [Resend](https://resend.com) for transactional email.

### Configure Resend

1. Create a free account at [resend.com](https://resend.com)
2. Generate an API key under **API Keys**
3. Add your domain and verify DNS records (SPF, DKIM, DMARC)
4. Set these values in `.env`:

```env
MAIL_MAILER=resend
MAIL_FROM_ADDRESS=noreply@your-verified-domain.com
RESEND_API_KEY=re_your_api_key_here
```

### Emails Sent by the App

| Trigger | Recipient | Class |
|---------|-----------|-------|
| Applicant registers | Applicant | `WelcomeApplicant` |
| Application submitted | Applicant | `ApplicationReceived` |
| Status → Under Review | Applicant | `ApplicationStatusUpdated` |
| Application approved | Applicant | `ApplicationApproved` |
| Application rejected | Applicant | `ApplicationRejected` |
| System user created | New admin | `SystemUserCreated` |
| Password reset | Any user | Laravel built-in |

All emails are **queued**. Start the queue worker:

```bash
php artisan queue:work
```

For production, use Supervisor to keep the worker running.

### Local Development (No Resend)

Use the log driver — emails are written to `storage/logs/laravel.log`:

```env
MAIL_MAILER=log
```

---

## Web Server Configuration

### Apache

Create a virtual host pointing `DocumentRoot` to the `public/` directory:

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/html/scholarship_app/public

    <Directory /var/www/html/scholarship_app/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # Security headers
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"

    ErrorLog ${APACHE_LOG_DIR}/scholarship_error.log
    CustomLog ${APACHE_LOG_DIR}/scholarship_access.log combined
</VirtualHost>
```

Enable required modules and the site:

```bash
sudo a2enmod rewrite
sudo a2ensite your-app.conf
sudo systemctl reload apache2
```

### Nginx

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/html/scholarship_app/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Shared Hosting (cPanel / Plesk)

If you cannot change the web root, place the Laravel app outside `public_html` and symlink the `public/` contents:

```bash
# App lives at: /home/user/scholarship_app/
cd public_html
ln -s ../scholarship_app/public/* .
ln -s ../scholarship_app/public/.htaccess .
```

Then edit `public_html/index.php` to fix the paths:

```php
require __DIR__.'/../scholarship_app/vendor/autoload.php';
$app = require_once __DIR__.'/../scholarship_app/bootstrap/app.php';
```

---

## File Permissions

```bash
sudo chown -R www-data:www-data /path/to/scholarship_app
sudo find /path/to/scholarship_app -type d -exec chmod 755 {} \;
sudo find /path/to/scholarship_app -type f -exec chmod 644 {} \;
sudo chmod -R 775 storage bootstrap/cache
```

---

## Production Checklist

- [ ] `APP_DEBUG=false` and `APP_ENV=production` in `.env`
- [ ] Strong admin passwords
- [ ] HTTPS configured (SSL certificate)
- [ ] Domain verified in Resend, DNS records added
- [ ] Queue worker running under Supervisor
- [ ] Regular database backups scheduled
- [ ] `.env` excluded from version control
- [ ] Caches optimised:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

---

## Troubleshooting

| Issue | Fix |
|-------|-----|
| 403 Forbidden | Check file ownership and permissions |
| 500 Internal Server Error | Check `storage/logs/laravel.log`; ensure `.env` exists |
| "Apache is functioning normally" | DocumentRoot is not pointing to `public/`; see Apache setup above |
| Composer memory limit | `COMPOSER_MEMORY_LIMIT=-1 composer install` |
| Missing PHP extensions | `sudo apt-get install php8.2-mbstring php8.2-xml php8.2-bcmath` |
| Emails not sending | Verify `RESEND_API_KEY`, check domain verification, ensure queue is running |
| App key not set | `php artisan key:generate` |

### Clear All Caches

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Check Logs

```bash
# Laravel application log
tail -f storage/logs/laravel.log

# Apache error log
sudo tail -f /var/log/apache2/error.log
```
