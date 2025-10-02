<?php

class Environment {
    private static $loaded = false;
    private static $vars = [];
    
    public static function load($path = null) {
        if (self::$loaded) {
            return;
        }
        
        if ($path === null) {
            $path = __DIR__ . '/../.env';
        }
        
        if (!file_exists($path)) {
            self::$loaded = true;
            return;
        }
        
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Parse key=value
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes if present
                if (($value[0] === '"' && $value[-1] === '"') || 
                    ($value[0] === "'" && $value[-1] === "'")) {
                    $value = substr($value, 1, -1);
                }
                
                self::$vars[$key] = $value;
                
                // Also set as environment variable
                if (!getenv($key)) {
                    putenv("{$key}={$value}");
                }
            }
        }
        
        self::$loaded = true;
    }
    
    public static function get($key, $default = null) {
        self::load();
        
        // First check our loaded vars
        if (isset(self::$vars[$key])) {
            return self::$vars[$key];
        }
        
        // Then check system environment
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }
        
        return $default;
    }
    
    public static function set($key, $value) {
        self::$vars[$key] = $value;
        putenv("{$key}={$value}");
    }
    
    public static function all() {
        self::load();
        return self::$vars;
    }
    
    public static function isDevelopment() {
        return self::get('APP_ENV', 'production') === 'development';
    }
    
    public static function isProduction() {
        return self::get('APP_ENV', 'production') === 'production';
    }
    
    public static function isDebug() {
        return self::get('APP_DEBUG', 'false') === 'true';
    }
}