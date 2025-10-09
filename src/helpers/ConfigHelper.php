<?php

namespace App\Helpers;

class ConfigHelper {
    
    /**
     * Get the current environment
     */
    public static function getEnvironment() {
        // Use global function if available, otherwise implement here
        if (function_exists('getEnvironment')) {
            return getEnvironment();
        }
        
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        
        if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false || strpos($host, '192.168.') !== false) {
            return 'development';
        } elseif (strpos($host, 'staging') !== false) {
            return 'staging';
        } else {
            return 'production';
        }
    }
    
    /**
     * Get the current app URL
     */
    public static function getAppUrl() {
        // Use global function if available, otherwise implement here
        if (function_exists('getAppUrl')) {
            return getAppUrl();
        }
        
        // Check if we're in CLI mode
        if (php_sapi_name() === 'cli') {
            return 'http://localhost:8080';
        }
        
        // Get the current protocol
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        
        // Get the current host
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
        
        // Get the current port if not standard
        $port = $_SERVER['SERVER_PORT'] ?? '';
        if ($port && $port !== '80' && $port !== '443') {
            $host .= ':' . $port;
        }
        
        return $protocol . '://' . $host;
    }
    
    /**
     * Get the current domain
     */
    public static function getDomain() {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return explode(':', $host)[0]; // Remove port if present
    }
    
    /**
     * Get the current port
     */
    public static function getPort() {
        return $_SERVER['SERVER_PORT'] ?? '8080';
    }
    
    /**
     * Check if we're in development mode
     */
    public static function isDevelopment() {
        return self::getEnvironment() === 'development';
    }
    
    /**
     * Check if we're in production mode
     */
    public static function isProduction() {
        return self::getEnvironment() === 'production';
    }
    
    /**
     * Get the local IP address for mobile development
     */
    public static function getLocalIP() {
        if (self::isDevelopment()) {
            // Try to get the actual local IP
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            if (strpos($host, '192.168.') !== false) {
                return explode(':', $host)[0];
            }
            
            // Fallback to detecting from network interfaces
            if (function_exists('gethostbyname')) {
                $localIP = gethostbyname(gethostname());
                if ($localIP && $localIP !== gethostname()) {
                    return $localIP;
                }
            }
        }
        
        return '192.168.29.174'; // Fallback
    }
    
    /**
     * Get the mobile app WebView URL
     */
    public static function getMobileAppUrl() {
        if (self::isDevelopment()) {
            $localIP = self::getLocalIP();
            $port = self::getPort();
            return "http://{$localIP}:{$port}";
        }
        
        return self::getAppUrl();
    }
    
    /**
     * Get configuration for mobile app
     */
    public static function getMobileAppConfig() {
        return [
            'environment' => self::getEnvironment(),
            'app_url' => self::getAppUrl(),
            'mobile_app_url' => self::getMobileAppUrl(),
            'local_ip' => self::getLocalIP(),
            'port' => self::getPort(),
            'domain' => self::getDomain(),
            'is_development' => self::isDevelopment(),
            'is_production' => self::isProduction(),
        ];
    }
}
