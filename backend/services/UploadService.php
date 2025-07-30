<?php
require_once '../models/Document.php';

class UploadService
{
    public static function handleUpload($user_id, $type, $file, $purpose = null, $comment = null)
    {
        // Validate file presence
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'File upload failed or no file uploaded.'];
        }

        // Validate type
        $allowedTypes = ['certificate', 'registration'];
        if (!in_array($type, $allowedTypes)) {
            return ['success' => false, 'message' => 'Invalid document type.'];
        }

        // Create target directory if not exists
        $uploadDir = '../uploads/documents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Sanitize and generate a unique file name
        $originalName = basename($file['name']);
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        $newFileName = uniqid($user_id . '_') . '.' . $ext;
        $targetFile = $uploadDir . $newFileName;

        // Move file
        if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
            return ['success' => false, 'message' => 'Failed to save uploaded file.'];
        }

        // Save to DB
        $saved = Document::upload($user_id, $type, $newFileName, $purpose, $comment);
        if (!$saved) {
            return ['success' => false, 'message' => 'Failed to save document record.'];
        }

        return ['success' => true, 'message' => 'Document uploaded successfully.'];
    }
}
