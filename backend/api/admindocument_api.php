<?php
require_once __DIR__ . '/../models/AdminDocument.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {

    case 'list':
        echo json_encode(AdminDocument::getAll());
        break;

    case 'get':
        $id = intval($_GET['id']);
        echo json_encode(AdminDocument::getById($id));
        break;

    case 'upload':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $deadline = $_POST['deadline'] ?? null;
            $purpose = $_POST['purpose'] ?? null;

            if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                $fileTmp = $_FILES['file']['tmp_name'];
                $fileName = basename($_FILES['file']['name']);
                $uploadDir = __DIR__ . '/../uploads/templates/';
                if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

                move_uploaded_file($fileTmp, $uploadDir . $fileName);

                $success = AdminDocument::upload($title, $fileName, $deadline, $purpose);
                echo json_encode(['success' => $success, 'file' => $fileName]);
            } else {
                echo json_encode(['success' => false, 'error' => 'No file uploaded']);
            }
        }
        break;

    case 'delete':
        $id = intval($_GET['id']);
        echo json_encode(['success' => AdminDocument::delete($id)]);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}
