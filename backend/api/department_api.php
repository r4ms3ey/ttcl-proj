<?php
require_once __DIR__ . '/../models/Department.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? null;

switch ($action) {
    case 'getAll':
        $departments = Department::getAll();
        echo json_encode($departments);
        break;

    case 'get':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $dept = Department::getById($id);
            echo json_encode($dept ?: []);
        } else {
            echo json_encode(['error' => 'Missing id']);
        }
        break;

    case 'delete':
        if (!isset($_POST['id'])) {
            echo json_encode(["success" => false, "message" => "Missing department ID"]);
            exit;
        }
        $id = intval($_POST['id']);
        $success = Department::delete($id);
        echo json_encode(["success" => $success]);
        break;

    case 'list':
        echo json_encode(Department::getAll());
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}
