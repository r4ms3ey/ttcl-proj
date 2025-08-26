<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {

    public static function login($username, $password) {
        
        $user = User::login($username, $password); // now works with plain text

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            session_regenerate_id(true);

            if ($user['role'] === 'admin') {
                header('Location: /ttcl_proj/frontend/admin/dashboard.php');
            } else {
                header('Location: /ttcl_proj/frontend/field_worker/dashboard.php');
            }
            exit;
        } else {
            $_SESSION['error'] = 'Invalid credentials';
            header('Location: /ttcl_proj/index.php');
            exit;
        }
    }

    public static function logout() {
        session_start();
        session_unset();
        session_destroy();
        header('Location: /ttcl_proj/index.php');
        exit;
    }
}
