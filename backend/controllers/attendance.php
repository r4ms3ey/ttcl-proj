<?php
require_once 'backend/models/Attendance.php';
require_once 'backend/controllers/AttendanceController.php';

header('Content-Type: application/json');

// Get filters
$date = $_GET['date'] ?? null;
$departmentId = $_GET['department'] ?? null;

// Fetch from controller
$data = AttendanceController::view($date, $departmentId);

echo json_encode($data);
