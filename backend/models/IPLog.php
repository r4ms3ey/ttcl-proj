<?php
require_once '../config/Database.php';

class Document
{
    // Upload a new document
    public static function upload($user_id, $type, $file_name, $purpose = null, $comment = null, $status = 'uploaded') {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO documents (user_id, type, file_name, purpose, comment, status, uploaded_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([$user_id, $type, $file_name, $purpose, $comment, $status]);
    }

    // Get all documents
    public static function getAll() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM documents ORDER BY uploaded_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    // Update document status, purpose, comment
    public static function update($id, $status = null, $purpose = null, $comment = null) {
        $db = Database::getConnection();
        $fields = [];
        $params = [];

        if ($status !== null) {
            $fields[] = "status = ?";
            $params[] = $status;
        }
        if ($purpose !== null) {
            $fields[] = "purpose = ?";
            $params[] = $purpose;
        }
        if ($comment !== null) {
            $fields[] = "comment = ?";
            $params[] = $comment;
        }

        if (empty($fields)) return false;

        $fields[] = "uploaded_at = NOW()"; // update timestamp
        $params[] = $id;

        $sql = "UPDATE documents SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }

    // Delete a document
    public static function delete($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM documents WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
