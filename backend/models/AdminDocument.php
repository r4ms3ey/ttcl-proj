<?php
require_once __DIR__ . '/../../config/Database.php';

class AdminDocument {

    // Upload new admin template
    public static function upload($title, $file_name, $deadline = null, $purpose = null) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO admindocuments (title, file_name, deadline, purpose) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$title, $file_name, $deadline, $purpose]);
    }

    // Get all templates
    public static function getAll() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM admindocuments ORDER BY uploaded_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get single template by ID
    public static function getById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM admindocuments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Delete template
    public static function delete($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM admindocuments WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Update template info
    public static function update($id, $title, $deadline = null, $purpose = null) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE admindocuments SET title = ?, deadline = ?, purpose = ? WHERE id = ?");
        return $stmt->execute([$title, $deadline, $purpose, $id]);
    }
}
