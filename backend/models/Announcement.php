<?php
require_once __DIR__ . '/../../config/Database.php';

class Announcement
{
    // Create a new announcement
    public static function create($title, $message, $department_id, $display_date) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO announcements (title, message, department_id, display_date, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([$title, $message, $department_id, $display_date]);
    }

    // Get one announcement
    public static function getById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM announcements WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update an announcement
    public static function update($id, $title, $message, $department_id, $display_date) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE announcements 
            SET title = ?, message = ?, department_id = ?, display_date = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$title, $message, $department_id, $display_date, $id]);
    }

        // Get all announcements with optional filters
    public static function getAll($department_id = null, $date = null) {
        $db = Database::getConnection();

        $query = "
            SELECT a.*, d.name AS department_name
            FROM announcements a
            JOIN departments d ON a.department_id = d.id
            WHERE 1=1
        ";

        $params = [];

        if ($department_id && $department_id !== 'all') {
            $query .= " AND a.department_id = ?";
            $params[] = $department_id;
        }

        if ($date) {
            $query .= " AND a.display_date = ?";
            $params[] = $date;
        }

        $query .= " ORDER BY a.display_date DESC, a.created_at DESC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Delete an announcement
    public static function delete($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM announcements WHERE id = ?");
        return $stmt->execute([$id]);
    }
}