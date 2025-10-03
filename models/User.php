<?php
// User model for authentication
require_once 'config/database.php';

class User {
    
    public static function findByEmail($email) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function findById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND is_active = 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function create($data) {
        $db = Database::getConnection();
        
        // Generate verification token
        $verificationToken = bin2hex(random_bytes(32));
        
        $stmt = $db->prepare("
            INSERT INTO users (first_name, last_name, email, password, phone, email_verified, verification_token, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, 0, ?, NOW(), NOW())
        ");
        
        if ($stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['phone'] ?? null,
            $verificationToken
        ])) {
            $userId = $db->lastInsertId();
            
            // Send verification email
            require_once 'services/EmailService.php';
            EmailService::sendVerificationEmail(
                $data['email'], 
                $data['first_name'], 
                $verificationToken
            );
            
            return $userId;
        }
        
        return false;
    }
    
    public static function verifyPassword($email, $password) {
        $user = self::findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user; // Return user regardless of email verification status
        }
        return false;
    }
    
    public static function verifyEmail($token) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE users 
            SET email_verified = 1, verification_token = NULL, updated_at = NOW() 
            WHERE verification_token = ? AND is_active = 1
        ");
        return $stmt->execute([$token]);
    }
    
    public static function createPasswordResetToken($email) {
        $user = self::findByEmail($email);
        if (!$user) {
            return false;
        }
        
        $resetToken = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour
        
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE users 
            SET password_reset_token = ?, password_reset_expires = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        
        if ($stmt->execute([$resetToken, $expiresAt, $user['id']])) {
            // Send password reset email
            require_once 'services/EmailService.php';
            EmailService::sendPasswordResetEmail(
                $user['email'], 
                $user['first_name'], 
                $resetToken
            );
            return true;
        }
        
        return false;
    }
    
    public static function validatePasswordResetToken($token) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT id FROM users 
            WHERE password_reset_token = ? 
            AND password_reset_expires > NOW() 
            AND is_active = 1
        ");
        $stmt->execute([$token]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }
    
    public static function resetPassword($token, $newPassword) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT id FROM users 
            WHERE password_reset_token = ? 
            AND password_reset_expires > NOW() 
            AND is_active = 1
        ");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $stmt = $db->prepare("
                UPDATE users 
                SET password = ?, password_reset_token = NULL, password_reset_expires = NULL, updated_at = NOW() 
                WHERE id = ?
            ");
            return $stmt->execute([
                password_hash($newPassword, PASSWORD_DEFAULT),
                $user['id']
            ]);
        }
        
        return false;
    }
}
?>