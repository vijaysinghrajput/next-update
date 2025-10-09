#!/bin/bash

# REAL Production Database Setup Script
# This uses the ACTUAL database dump from your local system

echo "🔥 REAL Production Database Setup"
echo "================================="

# Production database credentials
DB_HOST="localhost"
DB_NAME="u715885454_next_update"
DB_USER="u715885454_next_update"
DB_PASS="Next@411"

echo "📋 Production Database Info:"
echo "   Host: $DB_HOST"
echo "   Database: $DB_NAME"
echo "   User: $DB_USER"
echo ""

# Check if SQL file exists
if [ ! -f "production_database_real.sql" ]; then
    echo "❌ production_database_real.sql not found!"
    echo "   Make sure you have the real database dump file"
    exit 1
fi

echo "✅ Found real database dump file"

# Test connection
echo "🔍 Testing database connection..."
if mysql -h $DB_HOST -u $DB_USER -p$DB_PASS -e "SELECT 1;" 2>/dev/null; then
    echo "✅ Database connection successful"
else
    echo "❌ Database connection failed"
    echo "   Please check your credentials:"
    echo "   Host: $DB_HOST"
    echo "   User: $DB_USER"
    echo "   Password: $DB_PASS"
    exit 1
fi

# Import the REAL database
echo "📥 Importing REAL database with all your data..."
mysql -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME < production_database_real.sql

if [ $? -eq 0 ]; then
    echo "✅ REAL database imported successfully!"
    echo ""
    echo "🎉 Your production database is ready with:"
    echo "   ✅ All tables from local database"
    echo "   ✅ All your existing data"
    echo "   ✅ All relationships and indexes"
    echo "   ✅ Sample data and admin user"
    echo ""
    echo "🌐 Your website should now work at:"
    echo "   https://next-update.skyablyitsolution.com"
else
    echo "❌ Database import failed"
    echo "   Check the error messages above"
fi
