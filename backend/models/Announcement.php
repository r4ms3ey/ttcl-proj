<?php
require_once __DIR__ . '/../../config/database.php';

class Announcement
{
    // Create a new announcement
    public static function create($title, $message, $file_name = null) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO announcements (title, message, file_name, created_at) VALUES (?, ?, ?, NOW())");
        return $stmt->execute([$title, $message, $file_name]);
    }

    // Get all announcements
    public static function getAll() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM announcements ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get one announcement
    public static function getById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM announcements WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update an announcement
    public static function update($id, $title, $message, $file_name = null) {
        $db = Database::getConnection();
        if ($file_name) {
            $stmt = $db->prepare("UPDATE announcements SET title = ?, message = ?, file_name = ?, updated_at = NOW() WHERE id = ?");
            return $stmt->execute([$title, $message, $file_name, $id]);
        } else {
            $stmt = $db->prepare("UPDATE announcements SET title = ?, message = ?, updated_at = NOW() WHERE id = ?");
            return $stmt->execute([$title, $message, $id]);
        }
    }

    // Delete an announcement
    public static function delete($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM announcements WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
