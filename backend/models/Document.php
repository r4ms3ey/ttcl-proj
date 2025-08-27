<?php
require_once __DIR__ . '/../../config/Database.php';

class Document
{
    // Upload a new document
    public static function upload($user_id, $type, $file_name, $status = 'pending') {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO documents (user_id, type, file_name, status, uploaded_at) VALUES (?, ?, ?, ?, NOW())");
        return $stmt->execute([$user_id, $type, $file_name, $status]);
    }

    // Get all documents (admin or for reports)
    public static function getAll() {
        $db = Database::getConnection();

        $stmt = $db->query("
            SELECT fwp.user_id,
                fwp.full_name,
                dpt.name as department,
                MAX(CASE WHEN doc.type = 'certificate' THEN doc.file_name END) as certificate_file,
                MAX(CASE WHEN doc.type = 'registration' THEN doc.file_name END) as registration_file,
                MAX(CASE WHEN doc.type = 'certificate' THEN doc.status END) as certificate_status,
                MAX(CASE WHEN doc.type = 'registration' THEN doc.status END) as registration_status,
                MAX(doc.uploaded_at) as last_uploaded,
                MAX(doc.id) as document_id
            FROM field_worker_profiles fwp
            LEFT JOIN documents doc ON fwp.user_id = doc.user_id
            LEFT JOIN departments dpt ON fwp.department_id = dpt.id
            GROUP BY fwp.user_id, fwp.full_name, dpt.name
            ORDER BY last_uploaded DESC
        ");

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ensure missing/uploaded flags
        foreach ($rows as &$row) {
            $row['certificate_status'] = $row['certificate_file'] ? 'uploaded' : 'missing';
            $row['registration_status'] = $row['registration_file'] ? 'uploaded' : 'missing';
        }

        return $rows;
    }



    // Get documents by user ID
    public static function getByUser($user_id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM documents WHERE user_id = ? ORDER BY uploaded_at DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get a specific document
    public static function getById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM documents WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update document status (e.g., approved/rejected)
    public static function updateStatus($id, $status) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE documents SET status = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    // Delete a document
    public static function delete($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM documents WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
