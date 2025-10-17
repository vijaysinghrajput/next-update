<?php
namespace App\Services;

class Session {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    public static function get($key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;
    }
    
    public static function has($key) {
        self::start();
        return isset($_SESSION[$key]);
    }
    
    public static function remove($key) {
        self::start();
        unset($_SESSION[$key]);
    }
    
    public static function destroy() {
        self::start();
        session_destroy();
    }
    
    public static function flash($key, $value = null) {
        self::start();
        
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
        } else {
            $value = $_SESSION['_flash'][$key] ?? null;
            unset($_SESSION['_flash'][$key]);
            return $value;
        }
    }
    
    public static function isLoggedIn() {
        // Check if user is logged in via session
        if (self::has('user_id')) {
            return true;
        }
        
        // Check if user has remember me token
        if (isset($_COOKIE['remember_token']) && !empty($_COOKIE['remember_token'])) {
            return self::restoreFromRememberToken($_COOKIE['remember_token']);
        }
        
        return false;
    }
    
    public static function restoreFromRememberToken($token) {
        try {
            // Load User model
            require_once __DIR__ . '/../models/User.php';
            $userModel = new \App\Models\User();
            
            // Find user by remember token (you'll need to add this field to users table)
            $user = $userModel->getByRememberToken($token);
            
            if ($user) {
                // Restore session
                self::login($user, true);
                return true;
            }
        } catch (\Exception $e) {
            // Log error and continue
            error_log('Remember token restore failed: ' . $e->getMessage());
        }
        
        return false;
    }
    
    public static function refreshSession() {
        if (self::isLoggedIn()) {
            // Update login time to extend session
            self::set('last_activity', time());
            
            // Regenerate session ID for security
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_regenerate_id(true);
            }
        }
    }
    
    public static function getUserId() {
        return self::get('user_id');
    }
    
    public static function login($user, $remember = false) {
        self::set('user_id', $user['id']);
        self::set('username', $user['username']);
        self::set('full_name', $user['full_name']);
        self::set('email', $user['email']);
        self::set('points', $user['points']);
        self::set('is_admin', $user['is_admin']);
        self::set('referral_code', $user['referral_code']);
        self::set('is_verified', $user['is_verified']);
        self::set('login_time', time());
        
        // If remember me is checked, extend session lifetime
        if ($remember) {
            // Extend session cookie for 30 days
            $cookieParams = session_get_cookie_params();
            setcookie(session_name(), session_id(), 
                time() + (30 * 24 * 60 * 60), // 30 days
                $cookieParams['path'],
                $cookieParams['domain'],
                $cookieParams['secure'],
                $cookieParams['httponly']
            );
            
            // Set a remember token cookie
            $rememberToken = bin2hex(random_bytes(32));
            self::set('remember_token', $rememberToken);
            setcookie('remember_token', $rememberToken, 
                time() + (30 * 24 * 60 * 60), // 30 days
                '/', '', 
                isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                true
            );
        }
    }
    
    public static function logout() {
        self::start();
        // Clear all session data
        $_SESSION = array();
        // Destroy the session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        // Destroy the session
        session_destroy();
    }
}
?>
