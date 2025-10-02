<?php
// Bootstrap file - loads environment and core dependencies
// Include this at the top of your main files

// Load environment variables first
require_once __DIR__ . '/core/Environment.php';
Environment::load();

// Set timezone
$timezone = Environment::get('TIMEZONE', 'Europe/Oslo');
date_default_timezone_set($timezone);

// Error reporting based on environment
if (Environment::isDebug()) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
} else {
    error_reporting(E_ALL & ~E_NOTICE);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// Set error log path
$logPath = Environment::get('LOG_PATH', __DIR__ . '/storage/logs');
if (!is_dir($logPath)) {
    mkdir($logPath, 0775, true);
}
ini_set('error_log', $logPath . '/php_errors.log');

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    $sessionPath = __DIR__ . '/storage/sessions';
    if (!is_dir($sessionPath)) {
        mkdir($sessionPath, 0775, true);
    }
    
    session_save_path($sessionPath);
    
    ini_set('session.cookie_httponly', Environment::get('SESSION_HTTPONLY', 'true') === 'true' ? 1 : 0);
    ini_set('session.cookie_secure', Environment::get('SESSION_SECURE', 'false') === 'true' ? 1 : 0);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Strict');
    
    session_start();
}

// Load core classes
require_once __DIR__ . '/core/Router.php';
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/core/Model.php';
require_once __DIR__ . '/core/View.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/ModuleLoader.php';

// Helper functions
function env($key, $default = null) {
    return Environment::get($key, $default);
}

function config($key, $default = null) {
    static $config = null;
    
    if ($config === null) {
        $configFile = __DIR__ . '/config/app.php';
        if (file_exists($configFile)) {
            $config = require $configFile;
        } else {
            $config = [];
        }
    }
    
    $keys = explode('.', $key);
    $value = $config;
    
    foreach ($keys as $segment) {
        if (is_array($value) && isset($value[$segment])) {
            $value = $value[$segment];
        } else {
            return $default;
        }
    }
    
    return $value;
}

function app_url($path = '') {
    $baseUrl = Environment::get('APP_URL', 'http://localhost');
    return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
}

function storage_path($path = '') {
    $storagePath = __DIR__ . '/storage';
    return rtrim($storagePath, '/') . '/' . ltrim($path, '/');
}

function public_path($path = '') {
    return __DIR__ . '/' . ltrim($path, '/');
}

function view($template, $data = [], $layout = 'base') {
    $view = new View();
    return $view->render($template, $data, $layout);
}

function redirect($url) {
    header("Location: {$url}");
    exit;
}

function json_response($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function log_message($message, $level = 'info') {
    $logFile = storage_path('logs/app.log');
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}