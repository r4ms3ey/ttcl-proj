<?php
session_start();

class AuthMiddleware
{
    public static function check()
    {
        if (!isset($_SESSION['user_id'])) {
            // Not logged in — redirect to login
            header('Location: /login.php');
            exit;
        }
    }

    public static function checkAdmin()
    {
        self::check();

        if ($_SESSION['role'] !== 'admin') {
            // Not authorized — redirect to dashboard or deny
            header('Location: /unauthorized.php');
            exit;
        }
    }

    public static function checkFieldWorker()
    {
        self::check();

        if ($_SESSION['role'] !== 'field_worker') {
            // Not authorized
            header('Location: /unauthorized.php');
            exit;
        }
    }
}
