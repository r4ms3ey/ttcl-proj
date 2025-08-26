<?php
require_once '../models/Document.php';
session_start();

class DocumentController
{
    // 1. Field Worker uploads certificate/registration
    public static function upload($userId, $file, $type) {
        $allowedTypes = ['application/pdf'];
        $deadline = Document::getSubmissionDeadline();

        if (!$file || $file['error'] !== 0) {
            return ['error' => 'File upload failed.'];
        }

        if (!in_array($file['type'], $allowedTypes)) {
            return ['error' => 'Only PDF files are allowed.'];
        }

        if (new DateTime() > new DateTime($deadline)) {
            return ['error' => 'Submission deadline has passed.'];
        }

        // Rename file
        $userInfo = Document::getUserInfo($userId);
        $cleanName = preg_replace('/[^A-Za-z0-9 ]/', '', $userInfo['full_name']);
        $fileName = $cleanName . " " . ucfirst($type) . ".pdf";

        $uploadPath = "../uploads/documents/" . $fileName;
        move_uploaded_file($file['tmp_name'], $uploadPath);

        return Document::markUploaded($userId, $type, $fileName);
    }

    // 2. Admin uploads templates (Word documents)
    public static function uploadTemplate($file, $type) {
        $allowedTypes = [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
            'application/msword' // .doc
        ];

        if (!$file || $file['error'] !== 0) {
            return ['error' => 'Template upload failed.'];
        }

        if (!in_array($file['type'], $allowedTypes)) {
            return ['error' => 'Only Word documents are allowed.'];
        }

        $fileName = strtolower($type) . "_template.docx";
        $uploadPath = "../uploads/templates/" . $fileName;
        move_uploaded_file($file['tmp_name'], $uploadPath);

        return ['success' => 'Template uploaded.'];
    }

    // 3. Admin sets submission deadline
    public static function setDeadline($date) {
        return Document::setSubmissionDeadline($date);
    }

    // 4. Admin fetches all submissions
    public static function listSubmissions() {
        return Document::getAllSubmissions();
    }

    // 5. Admin deletes selected submissions
    public static function deleteDocuments($ids) {
        return Document::deleteMany($ids);
    }

    // 6. Admin bulk download (implemented using zip)
    public static function downloadSelected($ids) {
        return Document::zipDownloads($ids); 
    }
}

if (isset($_GET['action'])) {
    header('Content-Type: application/json; charset=utf-8');

    switch ($_GET['action']) {
        case 'listSubmissions':
            echo json_encode(DocumentController::listSubmissions());
            break;

        case 'deleteDocuments':
            $ids = json_decode(file_get_contents("php://input"), true);
            echo json_encode(DocumentController::deleteDocuments($ids));
            break;

        case 'uploadTemplate':
            if (!empty($_FILES['file']) && isset($_POST['type'])) {
                echo json_encode(DocumentController::uploadTemplate($_FILES['file'], $_POST['type']));
            } else {
                echo json_encode(['error' => 'Missing file or type']);
            }
            break;

        case 'setDeadline':
            $data = json_decode(file_get_contents("php://input"), true);
            echo json_encode(DocumentController::setDeadline($data['date']));
            break;

        default:
            echo json_encode(['error' => 'Invalid action']);
    }
    exit;
}