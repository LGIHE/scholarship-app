# Application Setup Guide

This guide will help you deploy and set up the Laravel Scholarship Management Application.

## Prerequisites

Before running the setup script, ensure you have the following installed:

### Required
- **PHP 8.2 or higher** with the following extensions:
  - PDO
  - mbstring
  - tokenizer
  - xml
  - ctype
  - json
  - bcmath
  - fileinfo
  - openssl
- **Composer** (PHP dependency manager)

### Optional but Recommended
- **Node.js and npm** (for frontend asset compilation)
- **Database server** (MySQL, PostgreSQL, or use SQLite)

## Quick Start

1. **Clone or download the application** to your server

2. **Run the setup script:**
   ```bash
   ./setup.sh
   ```

3. **Follow the interactive prompts** to configure your application

## What the Setup Script Does

The setup script performs the following tasks:

### 1. Environment Check
- Verifies PHP version (8.2+)
- Checks required PHP extensions
- Verifies Composer installation
- Checks Node.js and npm (optional)
- Validates directory permissions

### 2. Application Configuration
Prompts you to configure:
- Application name
- Environment (local/production)
- Debug mode
- Application URL

### 3. Database Configuration
Choose from:
- **SQLite** (default, no additional setup required)
- **MySQL** (requires host, port, database name, username, password)
- **PostgreSQL** (requires host, port, database name, username, password)

### 4. Mail Configuration
Choose from:
- **Log** (default, emails saved to log files)
- **SMTP** (requires host, port, credentials)
- **Mailgun** (requires domain and secret)
- **Postmark** (requires token)
- **Amazon SES** (requires AWS credentials)

### 5. Dependency Installation
- Installs Composer dependencies
- Installs npm dependencies (if Node.js is available)

### 6. Application Setup
- Generates application encryption key
- Runs database migrations
- Creates database tables

### 7. Admin User Creation
Prompts you to create a System Admin user:
- Admin name
- Admin email
- Admin password (minimum 8 characters)

The admin user will have access to the Filament admin panel.

### 8. Optimization
- Clears all caches
- For production: caches configuration, routes, and views
- Builds frontend assets (if npm is available)

### 9. Permissions
- Sets proper permissions for storage and cache directories

## Manual Setup (Alternative)

If you prefer to set up manually or the script fails:

```bash
# 1. Copy environment file
cp .env.example .env

# 2. Edit .env file with your configuration
nano .env

# 3. Install dependencies
composer install --no-dev --optimize-autoloader
npm install

# 4. Generate application key
php artisan key:generate

# 5. Create database (for SQLite)
touch database/database.sqlite

# 6. Run migrations
php artisan migrate --force

# 7. Create admin user manually
php artisan tinker
```

Then in tinker:
```php
$user = \App\Models\User::create([
    'name' => 'System Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('your-password'),
    'email_verified_at' => now(),
]);

$role = \Spatie\Permission\Models\Role::firstOrCreate([
    'name' => 'System Admin',
    'guard_name' => 'web'
]);

$user->assignRole('System Admin');
```

## Post-Setup

### Starting the Application

**Development:**
```bash
php artisan serve
```
Access at: http://localhost:8000

**Production:**
Configure your web server (Nginx/Apache) to point to the `public` directory.

### Accessing the Admin Panel

Navigate to: `http://your-domain.com/admin`

Login with the admin credentials you created during setup.

### Available User Roles

The application supports the following roles:
- **System Admin** - Full access to admin panel
- **Committee Member** - Access to admin panel with limited permissions
- **Applicant** - Can submit scholarship applications
- **Scholar** - Scholarship recipients with access to their profile

## Troubleshooting

### Permission Errors
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Database Connection Issues
- Verify database credentials in `.env`
- Ensure database server is running
- For SQLite, ensure `database/database.sqlite` exists and is writable

### Missing PHP Extensions
Install missing extensions (Ubuntu/Debian example):
```bash
sudo apt-get install php8.2-mbstring php8.2-xml php8.2-bcmath php8.2-mysql
```

### Composer Memory Issues
```bash
COMPOSER_MEMORY_LIMIT=-1 composer install
```

### Clear All Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

## Security Considerations

Before deploying to production:

1. **Set `APP_DEBUG=false`** in `.env`
2. **Use strong passwords** for admin users
3. **Configure proper file permissions**
4. **Enable HTTPS** on your web server
5. **Set up regular backups** of your database
6. **Keep dependencies updated**: `composer update`
7. **Configure firewall rules** appropriately
8. **Review and secure** your `.env` file (never commit to version control)

## Environment Variables Reference

Key environment variables you may need to configure:

```env
# Application
APP_NAME="Your App Name"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database (MySQL example)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Mail (SMTP example)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"

# Session & Cache
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

## Support

For issues or questions:
1. Check the Laravel documentation: https://laravel.com/docs
2. Check the Filament documentation: https://filamentphp.com/docs
3. Review application logs in `storage/logs/laravel.log`

## License

This application is open-sourced software licensed under the MIT license.
