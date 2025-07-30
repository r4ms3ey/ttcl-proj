<?php
session_start();
require_once '../controllers/AuthController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    AuthController::login($_POST['username'], $_POST['password']);
}

