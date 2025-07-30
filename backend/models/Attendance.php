<?php
require_once '../config/Database.php';

class Attendance
{
    // 1. Record a check-in
    public static function checkIn($userId, $type = 'checkin') {
        $db = Database::getConnection();

        // Prevent multiple check-ins per day
        $stmt = $db->prepare("SELECT id FROM attendance WHERE user_id = ? AND DATE(checkin_time) = CURDATE()");
        $stmt->execute([$userId]);
        if ($stmt->fetch()) return false;

        $stmt = $db->prepare("INSERT INTO attendance (user_id, type, checkin_time) VALUES (?, ?, NOW())");
        return $stmt->execute([$userId, $type]);
    }

    // 2. Record a check-out
    public static function checkOut($userId) {
        $db = Database::getConnection();

        // Must already have a check-in today
        $stmt = $db->prepare("SELECT id FROM attendance WHERE user_id = ? AND DATE(checkin_time) = CURDATE()");
        $stmt->execute([$userId]);
        $record = $stmt->fetch();

        if (!$record) return false;

        $stmt = $db->prepare("UPDATE attendance SET checkout_time = NOW() WHERE id = ?");
        return $stmt->execute([$record['id']]);
    }

    // 3. Get today's attendance for a user
    public static function getToday($userId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM attendance WHERE user_id = ? AND DATE(checkin_time) = CURDATE()");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 4. Get full attendance for a user
    public static function getByUser($userId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM attendance WHERE user_id = ? ORDER BY checkin_time DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 5. Get all attendance logs (admin use)
    public static function getAll() {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT a.*, u.username 
            FROM attendance a 
            JOIN users u ON a.user_id = u.id 
            ORDER BY a.checkin_time DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 6. Get attendance by date (admin filter)
    public static function getByDate($date) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT a.*, u.username 
            FROM attendance a 
            JOIN users u ON a.user_id = u.id 
            WHERE DATE(a.checkin_time) = ?
            ORDER BY a.checkin_time ASC
        ");
        $stmt->execute([$date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
