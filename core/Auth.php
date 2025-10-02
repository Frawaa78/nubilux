<?php

class Auth {
    public static function check() {
        return isset($_SESSION['user_id']);
    }
    
    public static function user() {
        if (!self::check()) {
            return null;
        }
        
        require_once __DIR__ . '/../app/models/User.php';
        $userModel = new User();
        return $userModel->find($_SESSION['user_id']);
    }
    
    public static function login($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
    }
    
    public static function logout() {
        session_destroy();
    }
    
    public static function id() {
        return $_SESSION['user_id'] ?? null;
    }
    
    public static function guest() {
        return !self::check();
    }
}