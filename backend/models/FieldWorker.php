<?php
require_once __DIR__ . '/../../config/Database.php';

class FieldWorker
{
    // 1. Save profile after first login
    public static function saveProfile($userId, $data) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO field_worker_profiles 
            (user_id, full_name, phone, department_id, college, start_date, end_date, email, group_name)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $userId,
            $data['full_name'],
            $data['phone'],
            $data['department_id'],
            $data['college'],
            $data['start_date'],
            $data['end_date'],
            $data['email'],
            $data['group_name']
        ]);
    }

    // 2. Update profile (after initial setup)
    public static function updateProfile($userId, $data) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE field_worker_profiles
            SET full_name = ?, phone = ?, department_id = ?, college = ?, start_date = ?, end_date = ?, email = ?, group_name = ?
            WHERE user_id = ?
        ");
        return $stmt->execute([
            $data['full_name'],
            $data['phone'],
            $data['department_id'],
            $data['college'],
            $data['start_date'],
            $data['end_date'],
            $data['email'],
            $data['group_name'],
            $userId
        ]);
    }

    // 3. Change password (used by controller)
    public static function updatePassword($userId, $newHash) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([$newHash, $userId]);
    }

    // 4. Get profile details (used to display dashboard)
    public static function getProfile($userId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT f.*, d.name AS department_name
            FROM field_worker_profiles f
            JOIN departments d ON f.department_id = d.id
            WHERE f.user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 5. Get group name only (for group logic)
    public static function getGroup($userId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT group_name FROM field_worker_profiles WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }

    // 6. Handle document upload (certificate or registration)
    public static function handleUpload($userId, $file, $type) {
        $db = Database::getConnection();

        $filename = $file['name'];
        $path = '../uploads/documents/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $path)) {
            return ['error' => 'File upload failed.'];
        }

        $stmt = $db->prepare("
            INSERT INTO documents (user_id, type, file_name, uploaded_at, status)
            VALUES (?, ?, ?, NOW(), 'uploaded')
            ON DUPLICATE KEY UPDATE
                file_name = VALUES(file_name),
                uploaded_at = NOW(),
                status = 'uploaded'
        ");

        return $stmt->execute([$userId, $type, $filename]);
    }

 // Get all workers
    public static function getAllWorkers() {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT f.*, d.name AS department_name, u.role
            FROM field_worker_profiles f
            JOIN departments d ON f.department_id = d.id
            JOIN users u ON f.user_id = u.id
            ORDER BY f.start_date DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Search workers
    public static function searchWorkers($search) {
        $db = Database::getConnection();
        $query = "
            SELECT f.*, d.name AS department_name, u.role
            FROM field_worker_profiles f
            JOIN departments d ON f.department_id = d.id
            JOIN users u ON f.user_id = u.id
            WHERE f.full_name LIKE ? OR f.email LIKE ? OR d.name LIKE ?
            ORDER BY f.start_date DESC
        ";
        $stmt = $db->prepare($query);
        $like = "%$search%";
        $stmt->execute([$like, $like, $like]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Delete worker
    public static function deleteById($userId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM field_worker_profiles WHERE user_id = ?");
        $stmt->execute([$userId]);
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$userId]);
    }
}


