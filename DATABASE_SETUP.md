# NextUpdate Database Setup Guide

## 🗄️ **Database Configuration**

### **Local Development Database:**
- **Database Name**: `local_news_app`
- **Username**: `root`
- **Password**: (empty)
- **Host**: `localhost`

### **Production Database:**
- **Database Name**: `u715885454_next_update`
- **Username**: `u715885454_next_update`
- **Password**: `Next@411`
- **Host**: `localhost`

## 🚀 **Setup Instructions:**

### **1. Local Development Setup:**

#### **Step 1: Create Local Database**
```sql
-- Connect to MySQL as root
mysql -u root -p

-- Create database
CREATE DATABASE local_news_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user (optional, you can use root)
CREATE USER 'nextupdate'@'localhost' IDENTIFIED BY 'password123';
GRANT ALL PRIVILEGES ON local_news_app.* TO 'nextupdate'@'localhost';
FLUSH PRIVILEGES;
```

#### **Step 2: Import Database Schema**
```bash
# Navigate to your project directory
cd /Users/mac/Documents/workstation/nextupdate

# Import the database schema
mysql -u root -p local_news_app < database_schema.sql
```

### **2. Production Database Setup:**

#### **Step 1: Access Production Database**
```bash
# Connect to your production database
mysql -h localhost -u u715885454_next_update -p u715885454_next_update
# Password: Next@411
```

#### **Step 2: Import Database Schema**
```bash
# Upload database_schema.sql to your server
# Then import it
mysql -h localhost -u u715885454_next_update -p u715885454_next_update < database_schema.sql
```

## 📋 **Database Schema Overview:**

The database includes these main tables:

### **Core Tables:**
- `users` - User accounts and profiles
- `news_articles` - News posts and articles
- `ads` - Advertisement management
- `categories` - News categories
- `cities` - City locations
- `kyc_documents` - KYC verification documents
- `payments` - Payment transactions
- `referrals` - Referral system
- `sessions` - User sessions

### **Key Features:**
- ✅ User registration and authentication
- ✅ News posting and management
- ✅ Advertisement system
- ✅ KYC verification
- ✅ Payment processing
- ✅ Referral system
- ✅ Points system

## 🔧 **Environment Detection:**

The system automatically detects the environment and uses the correct database:

```php
// Local Development
if (strpos($host, 'localhost') !== false) {
    // Uses: local_news_app database
}

// Production
if (strpos($host, 'skyablyitsolution.com') !== false) {
    // Uses: u715885454_next_update database
}
```

## 🛠️ **Troubleshooting:**

### **Common Issues:**

1. **Database Connection Failed:**
   - Check database credentials in `config/app.php`
   - Ensure database exists
   - Verify user permissions

2. **Table Not Found:**
   - Import `database_schema.sql`
   - Check table names and structure

3. **Permission Denied:**
   - Grant proper privileges to database user
   - Check user permissions

### **Test Database Connection:**
```php
<?php
require 'bootstrap.php';

try {
    $pdo = pdo();
    echo "✅ Database connected successfully!";
    echo "\nEnvironment: " . getEnvironment();
    echo "\nDatabase: " . config('database.database');
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage();
}
?>
```

## 📊 **Sample Data:**

After importing the schema, you can add sample data:

```sql
-- Sample categories
INSERT INTO categories (name, description) VALUES 
('Politics', 'Political news and updates'),
('Sports', 'Sports news and events'),
('Technology', 'Tech news and innovations'),
('Business', 'Business and economy news');

-- Sample cities
INSERT INTO cities (name, state, country) VALUES 
('Mumbai', 'Maharashtra', 'India'),
('Delhi', 'Delhi', 'India'),
('Bangalore', 'Karnataka', 'India'),
('Chennai', 'Tamil Nadu', 'India');
```

## ✅ **Verification:**

After setup, verify everything works:

1. **Start your local server:**
   ```bash
   php -S 0.0.0.0:8080 -t . index.php
   ```

2. **Visit:** `http://localhost:8080`

3. **Check for errors** in the browser console or server logs

4. **Test database operations** like user registration, news posting, etc.

Your database is now ready for both local development and production! 🎉
