<?php
require_once __DIR__ . '/../../config/database.php';

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
public static function getAll($search = '', $department = 'all', $date = null) {
    $db = Database::getConnection();

    $query = "SELECT 
                  a.id,
                  f.user_id,
                  f.full_name,
                  d.name AS department,
                  DATE(a.checkin_time) AS date,
                  a.checkin_time AS check_in,
                  a.checkout_time AS check_out,
                  TIMESTAMPDIFF(HOUR, a.checkin_time, a.checkout_time) AS total_hours,
                  CASE
                      WHEN a.id IS NULL THEN 'Absent'
                      WHEN a.checkin_time IS NOT NULL AND a.checkout_time IS NULL THEN 'Checked In'
                      ELSE 'Present'
                  END AS status
              FROM field_worker_profiles f
              LEFT JOIN users u ON u.id = f.user_id
              LEFT JOIN departments d ON f.department_id = d.id
              LEFT JOIN attendance a 
                  ON a.user_id = f.user_id
                  " . ($date ? "AND DATE(a.checkin_time) = ?" : "");

    $params = [];
    if ($date) {
        $params[] = $date;
    }

    $where = " WHERE 1=1";

    // Search filter
    if ($search) {
        $where .= " AND (f.full_name LIKE ? OR d.name LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    // Department filter
    if ($department !== 'all') {
        $where .= " AND d.name = ?";
        $params[] = $department;
    }

    $order = " ORDER BY f.full_name ASC, a.checkin_time DESC";

    $stmt = $db->prepare($query . $where . $order);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // 6. Get attendance by date (admin filter)
    public static function getByDate($date) {
         $db = Database::getConnection();

    $stmt = $db->prepare("
        SELECT a.id,
               f.full_name,
               d.name AS department,
               a.checkin_time AS check_in,
               a.checkout_time AS check_out,
               TIMESTAMPDIFF(HOUR, a.checkin_time, a.checkout_time) AS total_hours,
               a.status,
               u.username
        FROM attendance a
        JOIN users u ON a.user_id = u.id
        JOIN field_worker_profiles f ON f.user_id = u.id
        JOIN departments d ON f.department_id = d.id
        WHERE DATE(a.checkin_time) = ?
        ORDER BY a.checkin_time ASC
    ");

    $stmt->execute([$date]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}