<?php
namespace App\Services;

class Config {
    private static $config = null;
    
    public static function setConfig($config) {
        self::$config = $config;
    }
    
    public static function get($key = null, $default = null) {
        if (self::$config === null) {
            self::$config = require_once __DIR__ . '/../../config/app.php';
        }
        
        if ($key === null) {
            return self::$config;
        }
        
        $keys = explode('.', $key);
        $value = self::$config;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
    
    public static function app($key = null, $default = null) {
        return self::get($key, $default);
    }
    
    public static function database($key = null, $default = null) {
        return self::get('database.' . $key, $default);
    }
    
    public static function email($key = null, $default = null) {
        return self::get('email.' . $key, $default);
    }
    
    public static function upload($key = null, $default = null) {
        return self::get('upload.' . $key, $default);
    }
    
    public static function security($key = null, $default = null) {
        return self::get('security.' . $key, $default);
    }
}
?>
