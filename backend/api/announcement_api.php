<?php
require_once __DIR__ . '/../models/Announcement.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {

    // List announcements with optional filters
    case 'list':
        $department_id = $_GET['department_id'] ?? null;
        $date = $_GET['display_date'] ?? null;
        echo json_encode(Announcement::getAll($department_id, $date));
        break;

    // Get single announcement
    case 'get':
        $id = intval($_GET['id']);
        $announcement = Announcement::getById($id);
        echo json_encode($announcement);
        break;

    // Create a new announcement (POST)
    case 'create':
        $data = json_decode(file_get_contents("php://input"), true);
        $success = Announcement::create(
            $data['title'],
            $data['message'],
            $data['department_id'],
            $data['display_date']
        );
        echo json_encode(['success' => $success]);
        break;

    // Update an announcement (POST)
    case 'update':
        $data = json_decode(file_get_contents("php://input"), true);
        $success = Announcement::update(
            $data['id'],
            $data['title'],
            $data['message'],
            $data['department_id'],
            $data['display_date']
        );
        echo json_encode(['success' => $success]);
        break;

    // Delete announcement
    case 'delete':
        $id = intval($_GET['id']);
        echo json_encode(['success' => Announcement::delete($id)]);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}
