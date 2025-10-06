#!/bin/bash

# Script to completely replace GitHub repository content
# This will delete all existing files and upload current project

echo "🗑️  Cleaning GitHub Repository..."
echo "=================================="

cd /Users/mac/Documents/workstation/nextupdate

# Add all current files
echo "📁 Adding current project files..."
git add .

# Commit with message
echo "💾 Committing current project..."
git commit -m "Complete project replacement - NextUpdate with dynamic configuration"

# Force push to replace all content
echo "🚀 Force pushing to GitHub (this will delete all old files)..."
git push -f origin master

echo ""
echo "✅ Repository cleaned and updated!"
echo "🌐 View at: https://github.com/vijaysinghrajput/next-update"
echo ""
echo "⚠️  Note: All previous files have been replaced with current project"
