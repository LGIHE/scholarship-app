#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ $1${NC}"
}

print_header() {
    echo -e "\n${BLUE}========================================${NC}"
    echo -e "${BLUE}$1${NC}"
    echo -e "${BLUE}========================================${NC}\n"
}

# Function to check if a command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Function to check PHP version
check_php_version() {
    if ! command_exists php; then
        print_error "PHP is not installed"
        return 1
    fi
    
    local php_version=$(php -r "echo PHP_VERSION;")
    local required_version="8.2"
    
    if [ "$(printf '%s\n' "$required_version" "$php_version" | sort -V | head -n1)" != "$required_version" ]; then
        print_error "PHP version $php_version is installed, but version $required_version or higher is required"
        return 1
    fi
    
    print_success "PHP version $php_version is installed"
    return 0
}

# Function to check required PHP extensions
check_php_extensions() {
    local required_extensions=("pdo" "mbstring" "tokenizer" "xml" "ctype" "json" "bcmath" "fileinfo" "openssl")
    local missing_extensions=()
    
    for ext in "${required_extensions[@]}"; do
        if ! php -m | grep -qi "^$ext$"; then
            missing_extensions+=("$ext")
        fi
    done
    
    if [ ${#missing_extensions[@]} -eq 0 ]; then
        print_success "All required PHP extensions are installed"
        return 0
    else
        print_error "Missing PHP extensions: ${missing_extensions[*]}"
        return 1
    fi
}

# Function to check Composer
check_composer() {
    if ! command_exists composer; then
        print_error "Composer is not installed"
        print_info "Install Composer from https://getcomposer.org/"
        return 1
    fi
    
    print_success "Composer is installed"
    return 0
}

# Function to check Node.js and npm
check_node() {
    if ! command_exists node; then
        print_warning "Node.js is not installed (required for frontend assets)"
        return 1
    fi
    
    local node_version=$(node -v | sed 's/v//')
    print_success "Node.js version $node_version is installed"
    
    if ! command_exists npm; then
        print_warning "npm is not installed"
        return 1
    fi
    
    print_success "npm is installed"
    return 0
}

# Function to check write permissions
check_permissions() {
    local dirs=("storage" "bootstrap/cache")
    local all_writable=true
    
    for dir in "${dirs[@]}"; do
        if [ ! -w "$dir" ]; then
            print_error "Directory $dir is not writable"
            all_writable=false
        fi
    done
    
    if $all_writable; then
        print_success "All required directories are writable"
        return 0
    else
        print_info "Run: chmod -R 775 storage bootstrap/cache"
        return 1
    fi
}

# Function to prompt for input with default value
prompt_input() {
    local prompt="$1"
    local default="$2"
    local value
    
    if [ -n "$default" ]; then
        read -p "$prompt [$default]: " value
        echo "${value:-$default}"
    else
        read -p "$prompt: " value
        echo "$value"
    fi
}

# Function to prompt for password
prompt_password() {
    local prompt="$1"
    local password
    local password_confirm
    
    while true; do
        read -s -p "$prompt: " password
        echo
        read -s -p "Confirm password: " password_confirm
        echo
        
        if [ "$password" = "$password_confirm" ]; then
            if [ ${#password} -lt 8 ]; then
                print_error "Password must be at least 8 characters long"
                continue
            fi
            echo "$password"
            return 0
        else
            print_error "Passwords do not match. Please try again."
        fi
    done
}

# Function to configure database
configure_database() {
    print_header "Database Configuration"
    
    echo "Select database type:"
    echo "1) SQLite (default, recommended for development)"
    echo "2) MySQL"
    echo "3) PostgreSQL"
    
    local db_choice=$(prompt_input "Enter choice [1-3]" "1")
    
    case $db_choice in
        1)
            DB_CONNECTION="sqlite"
            DB_DATABASE="database/database.sqlite"
            
            # Create SQLite database file if it doesn't exist
            if [ ! -f "$DB_DATABASE" ]; then
                touch "$DB_DATABASE"
                print_success "Created SQLite database file"
            fi
            ;;
        2)
            DB_CONNECTION="mysql"
            DB_HOST=$(prompt_input "Database host" "127.0.0.1")
            DB_PORT=$(prompt_input "Database port" "3306")
            DB_DATABASE=$(prompt_input "Database name" "laravel")
            DB_USERNAME=$(prompt_input "Database username" "root")
            DB_PASSWORD=$(prompt_input "Database password" "")
            ;;
        3)
            DB_CONNECTION="pgsql"
            DB_HOST=$(prompt_input "Database host" "127.0.0.1")
            DB_PORT=$(prompt_input "Database port" "5432")
            DB_DATABASE=$(prompt_input "Database name" "laravel")
            DB_USERNAME=$(prompt_input "Database username" "postgres")
            DB_PASSWORD=$(prompt_input "Database password" "")
            ;;
        *)
            print_error "Invalid choice, using SQLite"
            DB_CONNECTION="sqlite"
            DB_DATABASE="database/database.sqlite"
            touch "$DB_DATABASE"
            ;;
    esac
}

# Function to configure mail
configure_mail() {
    print_header "Mail Configuration"
    
    echo "Select mail driver:"
    echo "1) Log (default, emails saved to log file)"
    echo "2) SMTP"
    echo "3) Mailgun"
    echo "4) Postmark"
    echo "5) SES"
    
    local mail_choice=$(prompt_input "Enter choice [1-5]" "1")
    
    case $mail_choice in
        1)
            MAIL_MAILER="log"
            ;;
        2)
            MAIL_MAILER="smtp"
            MAIL_HOST=$(prompt_input "SMTP host" "smtp.mailtrap.io")
            MAIL_PORT=$(prompt_input "SMTP port" "2525")
            MAIL_USERNAME=$(prompt_input "SMTP username" "")
            MAIL_PASSWORD=$(prompt_input "SMTP password" "")
            MAIL_ENCRYPTION=$(prompt_input "Encryption (tls/ssl)" "tls")
            ;;
        3)
            MAIL_MAILER="mailgun"
            MAILGUN_DOMAIN=$(prompt_input "Mailgun domain" "")
            MAILGUN_SECRET=$(prompt_input "Mailgun secret" "")
            ;;
        4)
            MAIL_MAILER="postmark"
            POSTMARK_TOKEN=$(prompt_input "Postmark token" "")
            ;;
        5)
            MAIL_MAILER="ses"
            AWS_ACCESS_KEY_ID=$(prompt_input "AWS Access Key ID" "")
            AWS_SECRET_ACCESS_KEY=$(prompt_input "AWS Secret Access Key" "")
            AWS_DEFAULT_REGION=$(prompt_input "AWS Region" "us-east-1")
            ;;
        *)
            MAIL_MAILER="log"
            ;;
    esac
    
    MAIL_FROM_ADDRESS=$(prompt_input "Mail from address" "hello@example.com")
    MAIL_FROM_NAME=$(prompt_input "Mail from name" "$APP_NAME")
}

# Function to create .env file
create_env_file() {
    print_header "Application Configuration"
    
    if [ -f .env ]; then
        print_warning ".env file already exists"
        read -p "Do you want to overwrite it? (y/N): " overwrite
        if [[ ! $overwrite =~ ^[Yy]$ ]]; then
            print_info "Keeping existing .env file"
            return 0
        fi
    fi
    
    # Copy .env.example to .env
    cp .env.example .env
    print_success "Created .env file from .env.example"
    
    # Application settings
    APP_NAME=$(prompt_input "Application name" "Laravel")
    APP_ENV=$(prompt_input "Environment (local/production)" "production")
    APP_DEBUG=$(prompt_input "Debug mode (true/false)" "false")
    APP_URL=$(prompt_input "Application URL" "http://localhost")
    
    # Configure database
    configure_database
    
    # Configure mail
    configure_mail
    
    # Update .env file
    sed -i.bak "s|APP_NAME=.*|APP_NAME=\"$APP_NAME\"|" .env
    sed -i.bak "s|APP_ENV=.*|APP_ENV=$APP_ENV|" .env
    sed -i.bak "s|APP_DEBUG=.*|APP_DEBUG=$APP_DEBUG|" .env
    sed -i.bak "s|APP_URL=.*|APP_URL=$APP_URL|" .env
    
    sed -i.bak "s|DB_CONNECTION=.*|DB_CONNECTION=$DB_CONNECTION|" .env
    
    if [ "$DB_CONNECTION" != "sqlite" ]; then
        sed -i.bak "s|# DB_HOST=.*|DB_HOST=$DB_HOST|" .env
        sed -i.bak "s|# DB_PORT=.*|DB_PORT=$DB_PORT|" .env
        sed -i.bak "s|# DB_DATABASE=.*|DB_DATABASE=$DB_DATABASE|" .env
        sed -i.bak "s|# DB_USERNAME=.*|DB_USERNAME=$DB_USERNAME|" .env
        sed -i.bak "s|# DB_PASSWORD=.*|DB_PASSWORD=$DB_PASSWORD|" .env
    fi
    
    sed -i.bak "s|MAIL_MAILER=.*|MAIL_MAILER=$MAIL_MAILER|" .env
    sed -i.bak "s|MAIL_FROM_ADDRESS=.*|MAIL_FROM_ADDRESS=\"$MAIL_FROM_ADDRESS\"|" .env
    sed -i.bak "s|MAIL_FROM_NAME=.*|MAIL_FROM_NAME=\"$MAIL_FROM_NAME\"|" .env
    
    if [ "$MAIL_MAILER" = "smtp" ]; then
        sed -i.bak "s|MAIL_HOST=.*|MAIL_HOST=$MAIL_HOST|" .env
        sed -i.bak "s|MAIL_PORT=.*|MAIL_PORT=$MAIL_PORT|" .env
        sed -i.bak "s|MAIL_USERNAME=.*|MAIL_USERNAME=$MAIL_USERNAME|" .env
        sed -i.bak "s|MAIL_PASSWORD=.*|MAIL_PASSWORD=$MAIL_PASSWORD|" .env
    fi
    
    # Remove backup file
    rm -f .env.bak
    
    print_success "Environment file configured"
}

# Function to install dependencies
install_dependencies() {
    print_header "Installing Dependencies"
    
    print_info "Installing Composer dependencies..."
    if /opt/cpanel/ea-php84/root/usr/bin/php $(which composer) install --no-dev --optimize-autoloader; then
        print_success "Composer dependencies installed"
    else
        print_error "Failed to install Composer dependencies"
        return 1
    fi
    
    if command_exists npm; then
        print_info "Installing npm dependencies..."
        if npm install; then
            print_success "npm dependencies installed"
        else
            print_warning "Failed to install npm dependencies"
        fi
    fi
    
    return 0
}

# Function to generate application key
generate_app_key() {
    print_header "Generating Application Key"
    
    if php artisan key:generate --ansi; then
        print_success "Application key generated"
        return 0
    else
        print_error "Failed to generate application key"
        return 1
    fi
}

# Function to clean database for fresh migration
clean_database() {
    print_info "Cleaning database for fresh migration..."
    
    if [ "$DB_CONNECTION" = "sqlite" ] && [ -f "$DB_DATABASE" ]; then
        print_info "Recreating SQLite database file..."
        rm -f "$DB_DATABASE"
        touch "$DB_DATABASE"
        return 0
    elif [ "$DB_CONNECTION" = "mysql" ] || [ "$DB_CONNECTION" = "pgsql" ]; then
        print_warning "For MySQL/PostgreSQL, we need to drop and recreate the database."
        read -p "Do you want to drop and recreate the database? This will delete ALL data! (y/N): " confirm_drop
        
        if [[ $confirm_drop =~ ^[Yy]$ ]]; then
            print_info "Dropping and recreating database..."
            
            # Create a temporary script to drop and recreate database
            cat > temp_db_reset.php << 'EOF'
<?php
$host = $argv[1] ?? '127.0.0.1';
$port = $argv[2] ?? '3306';
$database = $argv[3] ?? 'laravel';
$username = $argv[4] ?? 'root';
$password = $argv[5] ?? '';
$connection = $argv[6] ?? 'mysql';

try {
    if ($connection === 'mysql') {
        $dsn = "mysql:host={$host};port={$port}";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->exec("DROP DATABASE IF EXISTS `{$database}`");
        $pdo->exec("CREATE DATABASE `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "SUCCESS: MySQL database recreated\n";
    } elseif ($connection === 'pgsql') {
        $dsn = "pgsql:host={$host};port={$port}";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->exec("DROP DATABASE IF EXISTS \"{$database}\"");
        $pdo->exec("CREATE DATABASE \"{$database}\"");
        echo "SUCCESS: PostgreSQL database recreated\n";
    }
    exit(0);
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
EOF

            # Run the database reset script
            local output=$(php temp_db_reset.php "$DB_HOST" "$DB_PORT" "$DB_DATABASE" "$DB_USERNAME" "$DB_PASSWORD" "$DB_CONNECTION" 2>&1)
            local exit_code=$?
            
            # Remove the temporary script
            rm -f temp_db_reset.php
            
            if [ $exit_code -eq 0 ]; then
                print_success "Database recreated successfully"
                return 0
            else
                print_error "Failed to recreate database: $output"
                return 1
            fi
        else
            print_info "Skipping database recreation. You may need to manually clean the database."
            return 1
        fi
    fi
    
    return 0
}

# Function to run migrations
run_migrations() {
    print_header "Running Database Migrations"
    
    print_info "Running migrations..."
    if php artisan migrate --force; then
        print_success "Database migrations completed"
        return 0
    else
        print_error "Failed to run migrations"
        print_info "Attempting to reset and retry migrations..."
        
        # Try to reset and run again
        php artisan migrate:reset --force 2>/dev/null
        
        if php artisan migrate --force; then
            print_success "Database migrations completed after reset"
            return 0
        else
            print_error "Migration failed even after reset."
            
            # Offer to clean database completely
            if clean_database; then
                print_info "Attempting migrations on clean database..."
                if php artisan migrate --force; then
                    print_success "Database migrations completed after cleanup"
                    return 0
                else
                    print_error "Migration still failed. Please check your database configuration and connection."
                    return 1
                fi
            else
                print_error "Could not clean database. Please manually drop and recreate your database, then run the script again."
                return 1
            fi
        fi
    fi
}

# Function to create admin user
create_admin_user() {
    print_header "Create System Admin User"
    
    print_info "Please provide admin user details:"
    
    local admin_name=$(prompt_input "Admin name" "System Admin")
    local admin_email=$(prompt_input "Admin email" "admin@example.com")
    local admin_password=$(prompt_password "Admin password")
    
    # Create a temporary PHP script to create the admin user
    cat > create_admin.php << 'EOF'
<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

$name = $argv[1] ?? 'System Admin';
$email = $argv[2] ?? 'admin@example.com';
$password = $argv[3] ?? 'password';

try {
    // Create System Admin role if it doesn't exist
    $role = Role::firstOrCreate(['name' => 'System Admin', 'guard_name' => 'web']);
    
    // Create or update admin user
    $user = User::updateOrCreate(
        ['email' => $email],
        [
            'name' => $name,
            'password' => bcrypt($password),
            'email_verified_at' => now(),
        ]
    );
    
    // Assign System Admin role
    if (!$user->hasRole('System Admin')) {
        $user->assignRole('System Admin');
    }
    
    echo "SUCCESS: Admin user created/updated successfully\n";
    echo "Email: {$email}\n";
    exit(0);
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
EOF

    # Run the PHP script
    local output=$(php create_admin.php "$admin_name" "$admin_email" "$admin_password" 2>&1)
    local exit_code=$?
    
    # Remove the temporary script
    rm -f create_admin.php
    
    if [ $exit_code -eq 0 ]; then
        print_success "Admin user created successfully"
        print_info "Email: $admin_email"
        return 0
    else
        print_error "Failed to create admin user"
        echo "$output"
        return 1
    fi
}

# Function to optimize application
optimize_application() {
    print_header "Optimizing Application"
    
    print_info "Clearing caches..."
    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    php artisan route:clear
    
    if [ "$APP_ENV" = "production" ]; then
        print_info "Optimizing for production..."
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
        
        if command_exists npm; then
            print_info "Building frontend assets..."
            npm run build
        fi
    fi
    
    print_success "Application optimized"
}

# Function to set permissions
set_permissions() {
    print_header "Setting Permissions"
    
    print_info "Setting directory permissions..."
    chmod -R 775 storage bootstrap/cache
    
    print_success "Permissions set"
}

# Main setup function
main() {
    clear
    echo -e "${GREEN}"
    echo "╔════════════════════════════════════════╗"
    echo "║   Laravel Application Setup Script    ║"
    echo "╔════════════════════════════════════════╗"
    echo -e "${NC}\n"
    
    # Check environment
    print_header "Checking Environment Requirements"
    
    local env_check_failed=false
    
    check_php_version || env_check_failed=true
    check_php_extensions || env_check_failed=true
    check_composer || env_check_failed=true
    check_node || print_warning "Node.js not found (optional but recommended)"
    check_permissions || print_warning "Permission issues detected"
    
    if $env_check_failed; then
        print_error "Environment check failed. Please fix the issues above and try again."
        exit 1
    fi
    
    print_success "Environment check passed!"
    echo
    read -p "Press Enter to continue with setup..."
    
    # Run setup steps
    create_env_file || exit 1
    install_dependencies || exit 1
    generate_app_key || exit 1
    run_migrations || exit 1
    create_admin_user || exit 1
    optimize_application
    set_permissions
    
    # Final message
    print_header "Setup Complete!"
    
    print_success "Your Laravel application has been set up successfully!"
    echo
    print_info "Next steps:"
    echo "  1. Configure your web server to serve from the 'public' directory"
    echo "  2. For Apache: Run './configure_apache.sh' or see APACHE_SETUP.md"
    echo "  3. For development: php artisan serve"
    echo "  4. Access the admin panel at: $APP_URL/admin"
    echo "  5. If using frontend assets, run: npm run dev"
    echo
    print_warning "Important:"
    echo "  - If you see 'Apache is functioning normally', your web server needs configuration"
    echo "  - Make sure to secure your application before deploying to production!"
    echo "  - The document root should point to: $(pwd)/public"
    echo
}

# Run main function
main
