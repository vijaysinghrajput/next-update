# 🚀 PRODUCTION DATABASE SETUP - QUICK GUIDE

## 📋 **Your Production Database Info:**
- **Host**: localhost
- **Database**: u715885454_next_update
- **Username**: u715885454_next_update
- **Password**: Next@411

## 🔥 **STEP 1: Get the SQL File**

### **Option A: Download from GitHub**
1. Go to: https://github.com/vijaysinghrajput/next-update
2. Click on `database_schema.sql`
3. Click "Raw" button
4. Copy all content and save as `database_schema.sql`

### **Option B: Create the file manually**
Copy this SQL content and save as `database_schema.sql`:

```sql
-- NextUpdate Production Database Schema
-- Database: u715885454_next_update

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    city VARCHAR(50),
    referral_code VARCHAR(20) UNIQUE,
    referred_by VARCHAR(20),
    points INT DEFAULT 0,
    total_earned_points INT DEFAULT 0,
    total_spent_points INT DEFAULT 0,
    is_admin BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    is_verified BOOLEAN DEFAULT FALSE,
    email_verified BOOLEAN DEFAULT FALSE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cities table
CREATE TABLE cities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    state VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- News articles table
CREATE TABLE news_articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content TEXT NOT NULL,
    excerpt TEXT,
    featured_image VARCHAR(255),
    category_id INT,
    city_id INT,
    user_id INT NOT NULL,
    is_published BOOLEAN DEFAULT FALSE,
    is_featured BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (city_id) REFERENCES cities(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Ads table
CREATE TABLE ads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    heading VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    position ENUM('top_banner', 'between_news', 'bottom_banner', 'popup_modal') NOT NULL,
    call_number VARCHAR(20),
    whatsapp_number VARCHAR(20),
    website_url VARCHAR(255),
    user_id INT NOT NULL,
    status ENUM('pending', 'approved', 'active', 'inactive') DEFAULT 'pending',
    clicks INT DEFAULT 0,
    views INT DEFAULT 0,
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- KYC documents table
CREATE TABLE kyc_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    document_type ENUM('aadhar', 'pan', 'voter_id', 'driving_license', 'passport') NOT NULL,
    document_number VARCHAR(50) NOT NULL,
    front_image VARCHAR(255) NOT NULL,
    back_image VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    rejection_reason TEXT,
    verified_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Payments table
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    points INT NOT NULL,
    payment_method ENUM('upi', 'bank_transfer', 'card') NOT NULL,
    transaction_id VARCHAR(100),
    payment_proof VARCHAR(255),
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Referrals table
CREATE TABLE referrals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    referrer_id INT NOT NULL,
    referred_id INT NOT NULL,
    points_earned INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (referrer_id) REFERENCES users(id),
    FOREIGN KEY (referred_id) REFERENCES users(id)
);

-- Sessions table
CREATE TABLE sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    payload LONGTEXT,
    last_activity INT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert sample data
INSERT INTO categories (name, slug, description) VALUES 
('Politics', 'politics', 'Political news and updates'),
('Sports', 'sports', 'Sports news and events'),
('Technology', 'technology', 'Tech news and innovations'),
('Business', 'business', 'Business and economy news'),
('Entertainment', 'entertainment', 'Entertainment and celebrity news');

INSERT INTO cities (name, state) VALUES 
('Mumbai', 'Maharashtra'),
('Delhi', 'Delhi'),
('Bangalore', 'Karnataka'),
('Chennai', 'Tamil Nadu'),
('Kolkata', 'West Bengal'),
('Hyderabad', 'Telangana'),
('Pune', 'Maharashtra'),
('Ahmedabad', 'Gujarat');

-- Create admin user
INSERT INTO users (username, email, password, full_name, is_admin, is_active, is_verified) VALUES 
('admin', 'admin@nextupdate.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', TRUE, TRUE, TRUE);
```

## 🔥 **STEP 2: Import to Production Database**

### **Method 1: Using cPanel (Easiest)**
1. Login to your cPanel
2. Go to "phpMyAdmin"
3. Select database: `u715885454_next_update`
4. Click "Import" tab
5. Upload the `database_schema.sql` file
6. Click "Go"

### **Method 2: Using Command Line**
```bash
# Connect to your server via SSH
ssh your-username@your-server.com

# Navigate to your website directory
cd /public_html

# Import the database
mysql -h localhost -u u715885454_next_update -p u715885454_next_update < database_schema.sql
# Enter password: Next@411
```

### **Method 3: Using MySQL Workbench**
1. Open MySQL Workbench
2. Connect to your database:
   - Host: localhost
   - Port: 3306
   - Username: u715885454_next_update
   - Password: Next@411
   - Database: u715885454_next_update
3. Open the SQL file
4. Execute the script

## 🔥 **STEP 3: Test the Connection**

Create a test file `test-db.php` on your server:

```php
<?php
// Test database connection
$host = 'localhost';
$dbname = 'u715885454_next_update';
$username = 'u715885454_next_update';
$password = 'Next@411';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    echo "✅ Database connected successfully!";
    
    // Test a simple query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "<br>Users in database: " . $result['count'];
    
} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
?>
```

## ✅ **That's It!**

Your production database will be ready with:
- ✅ All required tables
- ✅ Sample categories and cities
- ✅ Admin user account
- ✅ Proper relationships and indexes

**Admin Login:**
- Username: admin
- Email: admin@nextupdate.com
- Password: password (change this!)
