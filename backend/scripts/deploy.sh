#!/bin/bash

# UniConnect Backend Deployment Script
# Author: UniConnect Development Team
# Version: 1.0.0

set -e

echo "🚀 Starting UniConnect Backend Deployment..."

# Check if required tools are installed
check_requirements() {
    echo "📋 Checking requirements..."
    
    # Check PHP version
    if ! command -v php &>/dev/null; then
        echo "❌ PHP is not installed"
        exit 1
    fi
    
    php_version=$(php -v | cut -d' ' -f2)
    echo "✅ PHP version: $php_version"
    
    # Check Composer
    if ! command -v composer &>/dev/null; then
        echo "❌ Composer is not installed"
        exit 1
    fi
    
    composer_version=$(composer --version)
    echo "✅ Composer version: $composer_version"
    
    # Check Node.js (for frontend)
    if command -v node &>/dev/null; then
        node_version=$(node --version)
        echo "✅ Node.js version: $node_version"
    else
        echo "⚠️  Node.js not found (backend only)"
    fi
    
    # Check Redis
    if command -v redis-cli &>/dev/null; then
        redis_version=$(redis-cli --version)
        echo "✅ Redis version: $redis_version"
    else
        echo "⚠️  Redis CLI not found (using file cache)"
    fi
}

# Backup current deployment
backup_current_deployment() {
    echo "📦 Creating backup of current deployment..."
    
    backup_dir="backups/$(date +%Y%m%d_%H%M%S)"
    mkdir -p $backup_dir
    
    # Backup database
    if [ -f ".env" ]; then
        cp .env $backup_dir/.env.backup
        echo "✅ Environment file backed up"
    fi
    
    # Backup storage
    if [ -d "storage" ]; then
        cp -r storage $backup_dir/
        echo "✅ Storage directory backed up"
    fi
    
    # Backup vendor
    if [ -d "vendor" ]; then
        cp -r vendor $backup_dir/
        echo "✅ Vendor directory backed up"
    fi
    
    echo "✅ Backup completed: $backup_dir"
}

# Install dependencies
install_dependencies() {
    echo "📦 Installing dependencies..."
    
    # Install Composer dependencies
    if [ -f "composer.json" ]; then
        composer install --no-dev --optimize-autoloader
        echo "✅ Composer dependencies installed"
    fi
    
    # Clear caches
    php artisan cache:clear
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    echo "✅ Caches cleared"
}

# Run database migrations
run_migrations() {
    echo "🗄️  Running database migrations..."
    
    php artisan migrate --force
    echo "✅ Database migrations completed"
}

# Optimize for production
optimize_production() {
    echo "⚡ Optimizing for production..."
    
    # Optimize Composer autoloader
    composer dump-autoload --optimize
    
    # Cache configuration for production
    php artisan config:cache
    php artisan route:cache
    
    # Optimize storage
    php artisan storage:link
    
    echo "✅ Production optimization completed"
}

# Set proper permissions
set_permissions() {
    echo "🔐 Setting file permissions..."
    
    # Set storage permissions
    chmod -R 775 storage
    chmod -R 775 bootstrap/cache
    
    # Set bootstrap permissions
    chmod -R 775 bootstrap/cache
    
    echo "✅ File permissions set"
}

# Run tests
run_tests() {
    echo "🧪 Running tests..."
    
    # Run PHPUnit tests
    if [ -f "vendor/bin/phpunit" ]; then
        vendor/bin/phpunit --coverage-html --coverage-text=summary
        echo "✅ Tests completed"
    else
        echo "⚠️  PHPUnit not found, skipping tests"
    fi
}

# Health check
health_check() {
    echo "🏥 Running health checks..."
    
    # Check if application is running
    if curl -f http://localhost:8000/api/v1/health &>/dev/null; then
        echo "✅ Application is responding"
    else
        echo "❌ Application is not responding"
        return 1
    fi
    
    # Check database connection
    php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connection: OK'" 2>/dev/null
    if [ $? -eq 0 ]; then
        echo "✅ Database connection OK"
    else
        echo "❌ Database connection failed"
        return 1
    fi
}

# Main deployment function
main() {
    echo "🎯 UniConnect Backend Deployment Script"
    echo "=================================="
    
    # Check if we're in production mode
    if [ "$APP_ENV" = "production" ]; then
        echo "🚨 PRODUCTION MODE DETECTED"
        backup_current_deployment
    fi
    
    check_requirements
    install_dependencies
    run_migrations
    
    if [ "$APP_ENV" = "production" ]; then
        optimize_production
        run_tests
    else
        echo "🔧 Development mode - skipping optimization"
    fi
    
    set_permissions
    
    health_check
    
    if [ "$APP_ENV" = "production" ]; then
        echo ""
        echo "🎉 DEPLOYMENT COMPLETED SUCCESSFULLY!"
        echo "📊 Deployment Summary:"
        echo "  - Environment: Production"
        echo "  - Dependencies: Installed"
        echo "  - Database: Migrated"
        echo "  - Permissions: Set"
        echo "  - Health Check: Passed"
        echo "  - Application: Ready"
        echo ""
        echo "🌐 UniConnect Backend is now live!"
    else
        echo ""
        echo "🔧 DEVELOPMENT SETUP COMPLETED!"
        echo "📊 Setup Summary:"
        echo "  - Environment: Development"
        echo "  - Dependencies: Installed"
        echo "  - Database: Migrated"
        echo "  - Permissions: Set"
        echo "  - Health Check: Passed"
        echo "  - Application: Ready"
        echo ""
        echo "🛠️  UniConnect Backend is ready for development!"
    fi
}

# Script usage
usage() {
    echo "UniConnect Backend Deployment Script"
    echo "Usage: $0 [options]"
    echo ""
    echo "Options:"
    echo "  --production    Deploy to production environment"
    echo "  --development    Setup development environment"
    echo "  --backup-only    Only create backup"
    echo "  --test-only     Only run tests"
    echo "  --health-only    Only run health checks"
    echo ""
    echo "Environment Variables:"
    echo "  APP_ENV=production|development (default: development)"
    echo ""
    echo "Examples:"
    echo "  $0 --production"
    echo "  $0 --development"
    echo "  $0 --backup-only"
}

# Parse command line arguments
case "${1:-}" in
    --production)
        export APP_ENV=production
        main
        ;;
    --development)
        export APP_ENV=development
        main
        ;;
    --backup-only)
        backup_current_deployment
        ;;
    --test-only)
        run_tests
        ;;
    --health-only)
        health_check
        ;;
    *)
        usage
        exit 1
        ;;
esac
