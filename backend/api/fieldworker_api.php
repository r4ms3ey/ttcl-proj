<?php
require_once __DIR__ . '/../models/FieldWorker.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {

    // Get all field workers
    case 'getAll':
        echo json_encode(FieldWorker::getAllWorkers());
        break;

    // Search field workers
    case 'search':
        $query = $_GET['q'] ?? '';
        echo json_encode(FieldWorker::searchWorkers($query));
        break;

    // Delete a field worker
    case 'delete':
        $id = intval($_GET['id']);
        echo json_encode(['success' => FieldWorker::deleteById($id)]);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}
