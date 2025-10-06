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
        return self::has('user_id');
    }
    
    public static function getUserId() {
        return self::get('user_id');
    }
    
    public static function login($user) {
        self::set('user_id', $user['id']);
        self::set('username', $user['username']);
        self::set('full_name', $user['full_name']);
        self::set('email', $user['email']);
        self::set('points', $user['points']);
        self::set('is_admin', $user['is_admin']);
        self::set('referral_code', $user['referral_code']);
        self::set('is_verified', $user['is_verified']);
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
