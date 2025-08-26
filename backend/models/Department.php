<?php
require_once __DIR__ . '/../../config/Database.php';

class Department
{
    // 1. Create a new department
    public static function create($data) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO departments (name, checkin_limit, checkout_limit, group_start_date)
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['name'],
            $data['checkin_limit'],
            $data['checkout_limit'],
            $data['group_start_date']
        ]);
    }

    // 2. Update department details
    public static function update($id, $data) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE departments 
            SET name = ?, checkin_limit = ?, checkout_limit = ?, group_start_date = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['name'],
            $data['checkin_limit'],
            $data['checkout_limit'],
            $data['group_start_date'],
            $id
        ]);
    }

    // 3. Delete a department
    public static function delete($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM departments WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // 4. Get all departments
    public static function getAll() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM departments ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 5. Get a department by ID
    public static function getById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM departments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 6. Get group start date by department ID
    public static function getGroupStartDate($departmentId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT group_start_date FROM departments WHERE id = ?");
        $stmt->execute([$departmentId]);
        return $stmt->fetchColumn();
    }

    public static function all() {
        global $pdo;
        $stmt = $pdo->query("
            SELECT d.id, d.name, d.check_in_limit, d.check_out_limit, 
                   COUNT(w.id) as worker_count
            FROM departments d
            LEFT JOIN workers w ON w.department_id = d.id
            GROUP BY d.id
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
