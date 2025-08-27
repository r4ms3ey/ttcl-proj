<?php
require_once __DIR__ . '/../models/Attendance.php';

header("Content-Type: application/json");

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'delete':
        $id = $_POST['id'] ?? null;
        if (!$id) {
            echo json_encode(["success" => false, "message" => "Missing ID"]);
            exit;
        }
        $success = Attendance::delete($id);
        echo json_encode(["success" => $success]);
        break;

    case 'deleteMany':
        $input = json_decode(file_get_contents("php://input"), true);
        $ids = $input['ids'] ?? [];
        $success = Attendance::deleteMany($ids);
        echo json_encode(["success" => $success]);
        break;
    case 'getAll':
        $search = $_GET['search'] ?? '';
        $department = $_GET['department'] ?? 'all';
        $date = $_GET['date'] ?? null;

        $records = Attendance::getAll($search, $department, $date);
        echo json_encode($records);
        break;


    default:
        echo json_encode(["success" => false, "message" => "Invalid action"]);
}
