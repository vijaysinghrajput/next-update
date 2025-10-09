# NextUpdate - Local & Production Setup Guide

## 🚀 **Automatic Git Deployment Setup**

Your NextUpdate project is now configured for automatic deployment from Git to production!

### 📋 **Current Configuration:**

#### **Local Development:**
- **URL**: `http://localhost:8080` or `http://192.168.29.174:8080`
- **Database**: `local_news_app` (root user, no password)
- **Environment**: `development`

#### **Production:**
- **URL**: `https://next-update.skyablyitsolution.com`
- **Database**: `u715885454_next_update` (user: `u715885454_next_update`, password: `Next@411`)
- **Environment**: `production`

## 🔧 **Setup Instructions:**

### **1. Production Server Setup:**

1. **Clone the repository on your production server:**
   ```bash
   cd /public_html
   git clone https://github.com/vijaysinghrajput/next-update.git .
   ```

2. **Set up webhook for automatic deployment:**
   - Upload `webhook-deploy.php` to your server root
   - Go to GitHub → Settings → Webhooks
   - Add webhook URL: `https://next-update.skyablyitsolution.com/webhook-deploy.php`
   - Set content type to `application/json`
   - Select "Just the push event"

### **2. Database Setup:**

#### **Local Database:**
```sql
CREATE DATABASE local_news_app;
-- Import database_schema.sql
```

#### **Production Database:**
```sql
-- Already configured:
-- Database: u715885454_next_update
-- User: u715885454_next_update  
-- Password: Next@411
-- Import database_schema.sql
```

### **3. Environment Detection:**

The system automatically detects the environment:

```php
// Local development
if (strpos($host, 'localhost') !== false) {
    return 'development';
}

// Production
if (strpos($host, 'skyablyitsolution.com') !== false) {
    return 'production';
}
```

## 🚀 **Deployment Workflow:**

### **Automatic Deployment:**
1. **Make changes locally**
2. **Commit and push to GitHub:**
   ```bash
   git add .
   git commit -m "Your changes"
   git push origin main
   ```
3. **Production automatically updates** via webhook

### **Manual Deployment:**
```bash
# On production server
cd /public_html
git pull origin main
chmod 755 public/uploads/
chmod 755 storage/sessions/
```

## 📱 **Mobile App Configuration:**

The mobile app automatically detects the environment:

```javascript
// Development
WEBVIEW_URL: 'http://192.168.29.174:8080'

// Production  
WEBVIEW_URL: 'https://next-update.skyablyitsolution.com'
```

## 🔐 **Security Features:**

- **Environment-specific database credentials**
- **Automatic URL generation**
- **Webhook security with HMAC verification**
- **Proper file permissions**

## 📊 **Monitoring:**

- **Deployment logs**: `deployment.log`
- **Session storage**: `storage/sessions/`
- **Upload directory**: `public/uploads/`

## 🛠️ **Troubleshooting:**

### **If deployment fails:**
1. Check `deployment.log` for errors
2. Verify webhook URL is correct
3. Ensure Git repository is accessible
4. Check file permissions

### **If database connection fails:**
1. Verify database credentials in `config/app.php`
2. Check if database exists
3. Ensure user has proper permissions

## ✅ **Ready to Use:**

Your NextUpdate project is now set up for:
- ✅ **Local development** with dynamic configuration
- ✅ **Automatic production deployment** from Git
- ✅ **Environment-specific settings**
- ✅ **Mobile app support**
- ✅ **Secure webhook deployment**

**Start developing locally and push to Git - production will update automatically!** 🎉
