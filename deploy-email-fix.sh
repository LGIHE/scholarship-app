#!/bin/bash

# Deployment script to fix Resend email configuration on production

echo "🚀 Deploying email configuration fix..."

# Pull latest changes
echo "📥 Pulling latest changes from git..."
git pull origin main

# Install/update dependencies
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader

# Clear all caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart queue workers
echo "🔄 Restarting queue workers..."
php artisan queue:restart

echo "✅ Deployment complete!"
echo ""
echo "📧 Email configuration:"
echo "   MAIL_MAILER: resend"
echo "   RESEND_API_KEY: Set in .env"
echo ""
echo "⚠️  Make sure to:"
echo "   1. Verify RESEND_API_KEY is set in production .env"
echo "   2. Queue worker is running: php artisan queue:work"
echo "   3. Domain is verified in Resend dashboard"
