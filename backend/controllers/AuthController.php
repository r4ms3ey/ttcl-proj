<?php
require_once '../models/User.php';
session_start();

class AuthController {
    
    public static function login($username, $password) {
        $user = User::findByUsername($username);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            
            if ($user['role'] === 'admin') {
                header('Location: /admin/dashboard.php');
            } else {
                header('Location: /field_worker/dashboard.php');
            }
            exit;
        } else {
            $_SESSION['error'] = 'Invalid credentials';
            header('Location: /index.php');
        }
    }

    public static function logout() {
        session_destroy();
        header('Location: /index.php');
        exit;
    }

    public static function changePassword($userId, $newPassword) {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        User::updatePassword($userId, $hashed);
    }
}
