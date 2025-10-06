<?php
// Application Configuration

// Auto-detect environment and base URL
if (!function_exists('getAppUrl')) {
    function getAppUrl() {
        // Check if we're in CLI mode
        if (php_sapi_name() === 'cli') {
            return 'http://localhost:8080';
        }
        
        // Get the current protocol
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        
        // Get the current host (already includes port if present)
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
        
        return $protocol . '://' . $host;
    }
}

// Auto-detect environment
if (!function_exists('getEnvironment')) {
    function getEnvironment() {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        
        if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false || strpos($host, '192.168.') !== false) {
            return 'development';
        } elseif (strpos($host, 'staging') !== false) {
            return 'staging';
        } else {
            return 'production';
        }
    }
}

$environment = getEnvironment();
$appUrl = getAppUrl();

return [
    // App Information
    'app_name' => 'Next Update',
    'app_tagline' => 'Your Local News Hub',
    'app_description' => 'Stay updated with the latest local news, share your stories, and connect with your community.',
    'app_version' => '1.0.0',
    'app_url' => $appUrl,
    'environment' => $environment,
    'debug' => $environment === 'development',
    'timezone' => 'UTC',
    
    // Admin Configuration
    'admin_channel_name' => 'Bansgaonsandesh',
    'admin_email' => 'admin@nextupdate.com',
    
    // Contact Information
    'contact_address' => '123 Main St, Anytown, USA',
    'contact_phone' => '+1 (555) 123-4567',
    'contact_email' => 'info@nextupdate.com',
    
    // Points System
    'welcome_points' => 10,
    'referral_points' => 10,
    'news_post_points' => 10,
    'kyc_verification_cost' => 50,
    
    // Payment Configuration
    'payment' => [
        'upi_id' => '8052553000@ybl',
        'qr_code_image' => 'uploads/payment.png',
        'exchange_rate' => 1, // 1 Rs = 1 Point
    ],
    
    // Database Configuration
    'database' => [
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'local_news_app',
        'charset' => 'utf8mb4'
    ],
    
    // Email Configuration
    'email' => [
        'smtp_host' => 'smtp.gmail.com',
        'smtp_port' => 587,
        'smtp_username' => 'your-email@gmail.com',
        'smtp_password' => 'your-app-password',
        'from_email' => 'noreply@nextupdate.com',
        'from_name' => 'Next Update'
    ],
    
    // File Upload Configuration
    'upload' => [
        'max_file_size' => 5 * 1024 * 1024, // 5MB
        'allowed_image_types' => ['image/jpeg', 'image/png', 'image/jpg'],
        'upload_path' => 'public/uploads/',
        'kyc_path' => 'public/uploads/kyc/',
        'news_path' => 'public/uploads/news/',
        'ads_path' => 'public/uploads/ads/'
    ],
    
    // Security Configuration
    'security' => [
        'session_timeout' => 3600, // 1 hour
        'max_login_attempts' => 5,
        'password_min_length' => 8,
        'csrf_protection' => true
    ],
    
    // Pagination
    'pagination' => [
        'per_page' => 10,
        'max_per_page' => 50
    ],
    
    // Features
    'features' => [
        'kyc_verification' => true,
        'referral_system' => true,
        'points_system' => true,
        'ad_posting' => true,
        'email_notifications' => true
    ]
];
?>
