<?php
session_start();
require_once 'backend/controllers/AuthController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if ($username !== '' && $password !== '') {
        AuthController::login($username, $password);
    } else {
        $_SESSION['error'] = 'Please enter both username and password';
        header('Location: index.php');
        exit;
    }
}
