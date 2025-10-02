<?php

// Load environment if not already loaded
require_once __DIR__ . '/../core/Environment.php';
Environment::load();

// App configuration
return [
    'app_name' => Environment::get('APP_NAME', 'Nubilux'),
    'app_version' => Environment::get('APP_VERSION', '2.0.0'),
    'app_url' => Environment::get('APP_URL', 'http://localhost'),
    'debug' => Environment::get('APP_DEBUG', 'false') === 'true',
    'timezone' => Environment::get('TIMEZONE', 'Europe/Oslo'),
    
    // Paths
    'storage_path' => __DIR__ . '/../storage',
    'upload_path' => __DIR__ . '/../storage/uploads',
    'cache_path' => __DIR__ . '/../storage/cache',
    'log_path' => __DIR__ . '/../storage/logs',
    
    // Security
    'session_lifetime' => 120, // minutes
    'csrf_protection' => Environment::get('CSRF_PROTECTION', 'true') === 'true',
    
    // Email
    'mail_from_address' => Environment::get('MAIL_FROM_ADDRESS', 'noreply@nubilux.com'),
    'mail_from_name' => Environment::get('MAIL_FROM_NAME', 'Nubilux System'),
    
    // Features
    'registration_enabled' => true,
    'email_verification_required' => true,
    'password_reset_enabled' => true,
    
    // Modules
    'modules_enabled' => true,
    'auto_load_modules' => true,
];