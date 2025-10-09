#!/bin/bash

# Database Setup Script for NextUpdate
# This script helps set up the database for both local and production

echo "🗄️  NextUpdate Database Setup"
echo "============================="

# Get current environment
if [[ "$1" == "production" ]]; then
    ENV="production"
    DB_NAME="u715885454_next_update"
    DB_USER="u715885454_next_update"
    DB_PASS="Next@411"
    DB_HOST="localhost"
else
    ENV="development"
    DB_NAME="local_news_app"
    DB_USER="root"
    DB_PASS=""
    DB_HOST="localhost"
fi

echo "📋 Environment: $ENV"
echo "📋 Database: $DB_NAME"
echo "📋 User: $DB_USER"
echo ""

# Check if MySQL is running
if ! pgrep -x "mysqld" > /dev/null; then
    echo "❌ MySQL is not running. Please start MySQL first."
    exit 1
fi

echo "✅ MySQL is running"

# Test database connection
echo "🔍 Testing database connection..."
if mysql -h $DB_HOST -u $DB_USER -p$DB_PASS -e "SELECT 1;" 2>/dev/null; then
    echo "✅ Database connection successful"
else
    echo "❌ Database connection failed"
    echo "Please check your database credentials and ensure the database exists."
    exit 1
fi

# Create database if it doesn't exist
echo "📁 Creating database if it doesn't exist..."
mysql -h $DB_HOST -u $DB_USER -p$DB_PASS -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null

# Import database schema
echo "📥 Importing database schema..."
if [ -f "database_schema.sql" ]; then
    mysql -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME < database_schema.sql
    echo "✅ Database schema imported successfully"
else
    echo "❌ database_schema.sql not found"
    echo "Please ensure the database schema file exists in the current directory"
    exit 1
fi

# Test the application
echo "🧪 Testing application..."
if php -r "require 'bootstrap.php'; try { pdo(); echo 'Database connection test: SUCCESS'; } catch (Exception \$e) { echo 'Database connection test: FAILED - ' . \$e->getMessage(); }" 2>/dev/null; then
    echo "✅ Application database connection test passed"
else
    echo "❌ Application database connection test failed"
fi

echo ""
echo "🎉 Database setup completed for $ENV environment!"
echo "📊 Database: $DB_NAME"
echo "👤 User: $DB_USER"
echo "🌐 You can now start your application!"
