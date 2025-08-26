<?php
require_once __DIR__ . '/../backend/models/Department.php';

if (isset($_GET['action']) && $_GET['action'] === 'view') {
    header('Content-Type: application/json');
    echo json_encode(Department::all()); // return all depts with worker count
    exit;
}
