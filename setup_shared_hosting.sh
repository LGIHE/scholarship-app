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

# Function to prompt for input
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

# Function to detect current directory structure
detect_structure() {
    local current_dir=$(pwd)
    local parent_dir=$(dirname "$current_dir")
    
    print_info "Current directory: $current_dir"
    print_info "Parent directory: $parent_dir"
    
    # Check if we're in public_html or similar
    if [[ "$current_dir" == *"public_html"* ]] || [[ "$current_dir" == *"www"* ]] || [[ "$current_dir" == *"htdocs"* ]]; then
        echo "web_root"
    else
        echo "outside_web_root"
    fi
}

# Function to setup symlink method
setup_symlink_method() {
    local app_dir=$(pwd)
    local public_html_dir
    
    print_header "Symlink Method Setup"
    
    # Try to detect public_html directory
    if [ -d "../public_html" ]; then
        public_html_dir="../public_html"
    elif [ -d "../www" ]; then
        public_html_dir="../www"
    elif [ -d "../htdocs" ]; then
        public_html_dir="../htdocs"
    else
        public_html_dir=$(prompt_input "Enter path to your web root directory (public_html)" "../public_html")
    fi
    
    print_info "Laravel app directory: $app_dir"
    print_info "Web root directory: $public_html_dir"
    
    # Check if public_html exists
    if [ ! -d "$public_html_dir" ]; then
        print_error "Web root directory not found: $public_html_dir"
        return 1
    fi
    
    # Backup existing index.html if it exists
    if [ -f "$public_html_dir/index.html" ]; then
        print_info "Backing up existing index.html..."
        mv "$public_html_dir/index.html" "$public_html_dir/index.html.backup"
    fi
    
    # Create symlinks
    print_info "Creating symlinks..."
    
    # Remove existing files that might conflict
    rm -f "$public_html_dir/index.php"
    rm -f "$public_html_dir/.htaccess"
    
    # Create symlinks for all files in public directory
    for file in public/*; do
        if [ -f "$file" ]; then
            local filename=$(basename "$file")
            ln -sf "$app_dir/$file" "$public_html_dir/$filename"
            print_success "Linked: $filename"
        fi
    done
    
    # Create symlinks for hidden files
    for file in public/.*; do
        if [ -f "$file" ] && [ "$(basename "$file")" != "." ] && [ "$(basename "$file")" != ".." ]; then
            local filename=$(basename "$file")
            ln -sf "$app_dir/$file" "$public_html_dir/$filename"
            print_success "Linked: $filename"
        fi
    done
    
    # Update index.php to point to correct paths
    print_info "Updating index.php paths..."
    
    local relative_path=$(realpath --relative-to="$public_html_dir" "$app_dir")
    
    cat > "$public_html_dir/index.php" << EOF
<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists(\$maintenance = __DIR__.'/${relative_path}/storage/framework/maintenance.php')) {
    require \$maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/${relative_path}/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

\$app = require_once __DIR__.'/${relative_path}/bootstrap/app.php';

\$kernel = \$app->make(Kernel::class);

\$response = \$kernel->handle(
    \$request = Request::capture()
)->send();

\$kernel->terminate(\$request, \$response);
EOF

    print_success "Symlink method setup complete!"
    return 0
}

# Function to setup public directory method
setup_public_method() {
    local app_dir=$(pwd)
    local public_html_dir
    
    print_header "Public Directory Method Setup"
    
    # Get public_html directory
    if [ -d "../public_html" ]; then
        public_html_dir="../public_html"
    elif [ -d "../www" ]; then
        public_html_dir="../www"
    elif [ -d "../htdocs" ]; then
        public_html_dir="../htdocs"
    else
        public_html_dir=$(prompt_input "Enter path to your web root directory (public_html)" "../public_html")
    fi
    
    print_info "Laravel app directory: $app_dir"
    print_info "Web root directory: $public_html_dir"
    
    # Check if public_html exists
    if [ ! -d "$public_html_dir" ]; then
        print_error "Web root directory not found: $public_html_dir"
        return 1
    fi
    
    # Backup existing index.html if it exists
    if [ -f "$public_html_dir/index.html" ]; then
        print_info "Backing up existing index.html..."
        mv "$public_html_dir/index.html" "$public_html_dir/index.html.backup"
    fi
    
    # Copy public directory contents
    print_info "Copying public directory contents..."
    
    cp -r public/* "$public_html_dir/"
    cp public/.htaccess "$public_html_dir/" 2>/dev/null || true
    
    # Update index.php paths
    print_info "Updating index.php paths..."
    
    local relative_path=$(realpath --relative-to="$public_html_dir" "$app_dir")
    
    sed -i.bak "s|__DIR__.'/../vendor/autoload.php'|__DIR__.'/${relative_path}/vendor/autoload.php'|" "$public_html_dir/index.php"
    sed -i.bak "s|__DIR__.'/../bootstrap/app.php'|__DIR__.'/${relative_path}/bootstrap/app.php'|" "$public_html_dir/index.php"
    sed -i.bak "s|__DIR__.'/../storage/framework/maintenance.php'|__DIR__.'/${relative_path}/storage/framework/maintenance.php'|" "$public_html_dir/index.php"
    
    # Remove backup file
    rm -f "$public_html_dir/index.php.bak"
    
    print_success "Public directory method setup complete!"
    return 0
}

# Function to set permissions
set_permissions() {
    print_header "Setting Permissions"
    
    print_info "Setting storage permissions..."
    chmod -R 755 storage/ 2>/dev/null || print_warning "Could not set storage permissions (may not be needed)"
    
    print_info "Setting cache permissions..."
    chmod -R 755 bootstrap/cache/ 2>/dev/null || print_warning "Could not set cache permissions (may not be needed)"
    
    print_success "Permissions set (where possible)"
}

# Function to optimize for production
optimize_app() {
    print_header "Optimizing Application"
    
    print_info "Clearing caches..."
    php artisan config:clear 2>/dev/null || print_warning "Could not clear config cache"
    php artisan cache:clear 2>/dev/null || print_warning "Could not clear application cache"
    php artisan view:clear 2>/dev/null || print_warning "Could not clear view cache"
    
    print_info "Caching for production..."
    php artisan config:cache 2>/dev/null || print_warning "Could not cache config"
    php artisan route:cache 2>/dev/null || print_warning "Could not cache routes"
    php artisan view:cache 2>/dev/null || print_warning "Could not cache views"
    
    print_success "Application optimized"
}

# Function to test setup
test_setup() {
    print_header "Testing Setup"
    
    # Check if Laravel is working
    if php artisan --version >/dev/null 2>&1; then
        print_success "Laravel is working correctly"
        local version=$(php artisan --version)
        print_info "Version: $version"
    else
        print_error "Laravel is not working correctly"
        return 1
    fi
    
    # Check database connection
    if php artisan migrate:status >/dev/null 2>&1; then
        print_success "Database connection is working"
    else
        print_warning "Database connection may have issues"
    fi
    
    return 0
}

# Main function
main() {
    clear
    echo -e "${GREEN}"
    echo "╔════════════════════════════════════════╗"
    echo "║     Laravel Shared Hosting Setup      ║"
    echo "╚════════════════════════════════════════╝"
    echo -e "${NC}\n"
    
    # Detect current structure
    local structure=$(detect_structure)
    
    if [ "$structure" = "web_root" ]; then
        print_warning "You appear to be in the web root directory!"
        print_info "For security, Laravel should be outside the web root."
        print_info "Please move your Laravel app outside public_html and run this script again."
        exit 1
    fi
    
    print_info "Great! You're outside the web root directory."
    
    # Choose setup method
    echo "Choose setup method:"
    echo "1) Symlink method (recommended - more secure)"
    echo "2) Public directory method (simpler but less secure)"
    echo
    
    local method=$(prompt_input "Enter choice [1-2]" "1")
    
    case $method in
        1)
            setup_symlink_method || exit 1
            ;;
        2)
            setup_public_method || exit 1
            ;;
        *)
            print_error "Invalid choice"
            exit 1
            ;;
    esac
    
    # Set permissions
    set_permissions
    
    # Optimize application
    optimize_app
    
    # Test setup
    test_setup
    
    # Final instructions
    print_header "Setup Complete!"
    
    print_success "Your Laravel application has been configured for shared hosting!"
    echo
    print_info "Next steps:"
    echo "  1. Test your application by visiting your domain"
    echo "  2. Access the admin panel at: your-domain.com/admin"
    echo "  3. Monitor error logs in your hosting control panel"
    echo "  4. Keep your .env file secure and outside web root"
    echo
    print_warning "Important notes:"
    echo "  - Your Laravel app is outside the web root for security"
    echo "  - Check your hosting control panel for error logs"
    echo "  - Ensure your database credentials are correct in .env"
    echo "  - Contact your hosting provider if you encounter issues"
    echo
    
    if [ -f "../public_html/index.html.backup" ]; then
        print_info "Your original index.html was backed up as index.html.backup"
    fi
}

# Check if we're in a Laravel directory
if [ ! -f "artisan" ]; then
    print_error "This doesn't appear to be a Laravel directory (artisan file not found)"
    exit 1
fi

# Run main function
main