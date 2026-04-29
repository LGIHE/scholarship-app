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

# Function to detect OS
detect_os() {
    if [ -f /etc/debian_version ]; then
        echo "debian"
    elif [ -f /etc/redhat-release ]; then
        echo "redhat"
    else
        echo "unknown"
    fi
}

# Function to get current directory
get_app_path() {
    echo "$(pwd)"
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

# Main configuration function
main() {
    clear
    print_header "Apache Configuration for Laravel"
    
    local os=$(detect_os)
    local app_path=$(get_app_path)
    
    print_info "Detected OS: $os"
    print_info "Laravel app path: $app_path"
    
    # Get configuration details
    local domain=$(prompt_input "Enter your domain name" "localhost")
    local use_ssl=$(prompt_input "Enable SSL/HTTPS? (y/N)" "n")
    
    print_header "Configuring Apache Virtual Host"
    
    # Create virtual host configuration
    local vhost_content="<VirtualHost *:80>
    ServerName $domain"
    
    if [ "$domain" != "localhost" ]; then
        vhost_content="$vhost_content
    ServerAlias www.$domain"
    fi
    
    vhost_content="$vhost_content
    DocumentRoot $app_path/public
    
    <Directory $app_path/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # Security headers
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection \"1; mode=block\"
    
    # Logging"
    
    if [ "$os" = "debian" ]; then
        vhost_content="$vhost_content
    ErrorLog \${APACHE_LOG_DIR}/laravel_error.log
    CustomLog \${APACHE_LOG_DIR}/laravel_access.log combined"
        local vhost_file="/etc/apache2/sites-available/laravel-app.conf"
    else
        vhost_content="$vhost_content
    ErrorLog /var/log/httpd/laravel_error.log
    CustomLog /var/log/httpd/laravel_access.log combined"
        local vhost_file="/etc/httpd/conf.d/laravel-app.conf"
    fi
    
    if [[ $use_ssl =~ ^[Yy]$ ]]; then
        vhost_content="$vhost_content
    
    # Redirect to HTTPS
    Redirect permanent / https://$domain/"
    fi
    
    vhost_content="$vhost_content
</VirtualHost>"
    
    # Add SSL virtual host if requested
    if [[ $use_ssl =~ ^[Yy]$ ]]; then
        vhost_content="$vhost_content

<VirtualHost *:443>
    ServerName $domain"
        
        if [ "$domain" != "localhost" ]; then
            vhost_content="$vhost_content
    ServerAlias www.$domain"
        fi
        
        vhost_content="$vhost_content
    DocumentRoot $app_path/public
    
    SSLEngine on
    # SSLCertificateFile /path/to/your/certificate.crt
    # SSLCertificateKeyFile /path/to/your/private.key
    
    <Directory $app_path/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # Security headers
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection \"1; mode=block\"
    Header always set Strict-Transport-Security \"max-age=31536000; includeSubDomains\"
    
    # Logging"
        
        if [ "$os" = "debian" ]; then
            vhost_content="$vhost_content
    ErrorLog \${APACHE_LOG_DIR}/laravel_ssl_error.log
    CustomLog \${APACHE_LOG_DIR}/laravel_ssl_access.log combined"
        else
            vhost_content="$vhost_content
    ErrorLog /var/log/httpd/laravel_ssl_error.log
    CustomLog /var/log/httpd/laravel_ssl_access.log combined"
        fi
        
        vhost_content="$vhost_content
</VirtualHost>"
    fi
    
    # Write virtual host file
    print_info "Creating virtual host configuration..."
    echo "$vhost_content" | sudo tee "$vhost_file" > /dev/null
    
    if [ $? -eq 0 ]; then
        print_success "Virtual host configuration created: $vhost_file"
    else
        print_error "Failed to create virtual host configuration"
        exit 1
    fi
    
    # Enable required Apache modules
    print_info "Enabling required Apache modules..."
    
    if [ "$os" = "debian" ]; then
        sudo a2enmod rewrite
        sudo a2enmod headers
        
        if [[ $use_ssl =~ ^[Yy]$ ]]; then
            sudo a2enmod ssl
        fi
        
        # Enable the site and disable default
        sudo a2ensite laravel-app.conf
        sudo a2dissite 000-default.conf 2>/dev/null || true
        
        # Test configuration
        if sudo apache2ctl configtest; then
            print_success "Apache configuration test passed"
            sudo systemctl reload apache2
            print_success "Apache reloaded"
        else
            print_error "Apache configuration test failed"
            exit 1
        fi
    else
        # For RedHat/CentOS, modules are typically compiled in
        print_info "Please ensure mod_rewrite and mod_headers are enabled in httpd.conf"
        
        # Test configuration
        if sudo httpd -t; then
            print_success "Apache configuration test passed"
            sudo systemctl restart httpd
            print_success "Apache restarted"
        else
            print_error "Apache configuration test failed"
            exit 1
        fi
    fi
    
    # Set proper permissions
    print_header "Setting File Permissions"
    
    local web_user="www-data"
    if [ "$os" = "redhat" ]; then
        web_user="apache"
    fi
    
    print_info "Setting ownership to $web_user..."
    sudo chown -R $web_user:$web_user "$app_path"
    
    print_info "Setting directory permissions..."
    sudo find "$app_path" -type d -exec chmod 755 {} \;
    sudo find "$app_path" -type f -exec chmod 644 {} \;
    
    print_info "Setting storage and cache permissions..."
    sudo chmod -R 775 "$app_path/storage"
    sudo chmod -R 775 "$app_path/bootstrap/cache"
    
    print_success "Permissions set successfully"
    
    # Final instructions
    print_header "Configuration Complete!"
    
    print_success "Apache has been configured for your Laravel application"
    echo
    print_info "Your application should now be accessible at:"
    echo "  http://$domain"
    
    if [[ $use_ssl =~ ^[Yy]$ ]]; then
        echo "  https://$domain (after SSL certificate setup)"
        echo
        print_warning "Don't forget to:"
        echo "  1. Install your SSL certificate"
        echo "  2. Update the SSL certificate paths in $vhost_file"
    fi
    
    echo
    print_info "Useful commands:"
    echo "  - Check Apache status: sudo systemctl status apache2"
    echo "  - View error logs: sudo tail -f /var/log/apache2/error.log"
    echo "  - View Laravel logs: tail -f $app_path/storage/logs/laravel.log"
    echo "  - Test configuration: sudo apache2ctl configtest"
    
    if [ "$domain" = "localhost" ]; then
        echo
        print_warning "You're using localhost. For production, consider:"
        echo "  1. Setting up a proper domain name"
        echo "  2. Configuring SSL/HTTPS"
        echo "  3. Setting up a firewall"
    fi
}

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    print_error "Please don't run this script as root. It will use sudo when needed."
    exit 1
fi

# Check if Apache is installed
if ! command -v apache2 >/dev/null 2>&1 && ! command -v httpd >/dev/null 2>&1; then
    print_error "Apache is not installed. Please install Apache first."
    exit 1
fi

# Run main function
main