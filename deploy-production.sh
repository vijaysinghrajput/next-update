#!/bin/bash

# Production Deployment Script for NextUpdate
# This script handles automatic deployment from Git to production

echo "🚀 NextUpdate Production Deployment"
echo "=================================="

# Configuration
PRODUCTION_URL="https://next-update.skyablyitsolution.com"
GIT_REPO="https://github.com/vijaysinghrajput/next-update.git"
PRODUCTION_DIR="/public_html"  # Adjust this path as needed

echo "📋 Deployment Configuration:"
echo "   Production URL: $PRODUCTION_URL"
echo "   Git Repository: $GIT_REPO"
echo "   Production Directory: $PRODUCTION_DIR"
echo ""

# Check if we're in production environment
if [[ "$HOSTNAME" == *"skyablyitsolution"* ]] || [[ "$PWD" == *"public_html"* ]]; then
    echo "✅ Production environment detected"
    
    # Pull latest changes from Git
    echo "📥 Pulling latest changes from Git..."
    git pull origin main
    
    # Set production environment
    echo "🔧 Setting production environment..."
    export APP_ENV=production
    
    # Clear any caches
    echo "🧹 Clearing caches..."
    rm -rf storage/sessions/*
    rm -rf public/uploads/temp/*
    
    # Set proper permissions
    echo "🔐 Setting proper permissions..."
    chmod 755 public/uploads/
    chmod 755 storage/sessions/
    chmod 644 config/app.php
    
    echo ""
    echo "✅ Production deployment completed!"
    echo "🌐 Your site is live at: $PRODUCTION_URL"
    
else
    echo "⚠️  This script should be run on the production server"
    echo "   Current environment: $HOSTNAME"
    echo "   Current directory: $PWD"
fi

echo ""
echo "📊 Deployment Summary:"
echo "   Environment: Production"
echo "   Database: u715885454_next_update"
echo "   URL: $PRODUCTION_URL"
echo "   Git Branch: main"
