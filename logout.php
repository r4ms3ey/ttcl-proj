<?php
session_start();
require_once 'backend/controllers/AuthController.php';
AuthController::logout();
?>
