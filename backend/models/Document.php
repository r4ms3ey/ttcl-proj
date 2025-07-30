<?php
require_once '../config/Database.php';

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
