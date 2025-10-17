<?php
// Bootstrap file for Next Update Application - Production Fixed Version

// Define application paths
define('APP_ROOT', __DIR__);
define('APP_PATH', APP_ROOT . '/app');
define('CONFIG_PATH', APP_ROOT . '/config');
define('SRC_PATH', APP_ROOT . '/src');
define('PUBLIC_PATH', APP_ROOT . '/public');
define('UPLOADS_PATH', PUBLIC_PATH . '/uploads');

// Manual class loading for production
require_once SRC_PATH . '/services/Config.php';
require_once SRC_PATH . '/services/Database.php';
require_once SRC_PATH . '/services/Session.php';
require_once SRC_PATH . '/services/Router.php';
require_once SRC_PATH . '/services/RSSService.php';
require_once SRC_PATH . '/helpers/ConfigHelper.php';

// Import services
use App\Services\Config;
use App\Services\Database;
use App\Services\Session;

// Load models
require_once SRC_PATH . '/models/User.php';
require_once SRC_PATH . '/models/News.php';
require_once SRC_PATH . '/models/Ad.php';

// Load configuration
$config = require CONFIG_PATH . '/app.php';

// Set global config
Config::setConfig($config);

// Configure session settings for better persistence
if (session_status() === PHP_SESSION_NONE) {
    // Set session save path
    $sessionPath = __DIR__ . '/storage/sessions';
    if (!is_dir($sessionPath)) {
        mkdir($sessionPath, 0755, true);
    }
    
    // Configure session for longer persistence (30 days)
    ini_set('session.gc_maxlifetime', 30 * 24 * 60 * 60); // 30 days
    ini_set('session.cookie_lifetime', 30 * 24 * 60 * 60); // 30 days
    ini_set('session.cookie_httponly', 1); // Security
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 1 : 0);
    ini_set('session.use_strict_mode', 1); // Security
    ini_set('session.cookie_samesite', 'Lax'); // Cross-site compatibility
    
    // Set session cookie parameters
    session_set_cookie_params([
        'lifetime' => 30 * 24 * 60 * 60, // 30 days
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    
    // Set session save path
    session_save_path($sessionPath);
    
    // Configure session for longer lifetime (30 days)
    ini_set('session.gc_maxlifetime', 2592000); // 30 days
    ini_set('session.cookie_lifetime', 2592000); // 30 days
    session_set_cookie_params([
        'lifetime' => 2592000, // 30 days
        'path' => '/',
        'domain' => '',
        'secure' => false, // Set to true if using HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    
    // Start session
    session_start();
    
    // Try to restore session from remember token if user is not logged in
    if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
        try {
            $session = new Session();
            $session->restoreFromRememberToken($_COOKIE['remember_token']);
        } catch (Exception $e) {
            // Ignore errors during session restoration
        }
    }
}

// Database connection
try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
} catch (Exception $e) {
    // For development, show error details
    if (config('app.debug', true)) {
        die('Database connection failed: ' . $e->getMessage());
    } else {
        die('Database connection failed. Please try again later.');
    }
}

// Helper functions
function config($key = null, $default = null) {
    return Config::get($key, $default);
}

function session($key = null, $value = null) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if ($value !== null) {
        $_SESSION[$key] = $value;
        return $value;
    }
    return $_SESSION[$key] ?? null;
}

function db() {
    return Database::getInstance();
}

function pdo() {
    return Database::getInstance()->getConnection();
}

function base_url($path = '') {
    return config('app_url') . '/' . ltrim($path, '/');
}

function asset($path) {
    return base_url('public/assets/' . ltrim($path, '/'));
}

function upload_url($path) {
    return base_url('public/uploads/' . ltrim($path, '/'));
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

function render_admin_page($content, $title = "Admin Panel") {
    $page_title = $title;
    $admin_content = $content;
    include APP_PATH . '/views/components/admin-layout.php';
}

function flash($key, $value = null) {
    if ($value !== null) {
        return Session::flash($key, $value);
    }
    return Session::flash($key);
}

function old($key, $default = '') {
    return $_POST[$key] ?? $default;
}

function csrf_token() {
    if (!session('_csrf_token')) {
        session('_csrf_token', bin2hex(random_bytes(32)));
    }
    return session('_csrf_token');
}

function csrf_field() {
    return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
}

function verify_csrf_token($token) {
    return hash_equals(session('_csrf_token'), $token);
}

// Error reporting
if (config('app.debug', false)) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set(config('app.timezone', 'UTC'));
?>
