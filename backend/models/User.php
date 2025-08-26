<?php
require_once __DIR__ . '/../../config/Database.php';

class User {

    public static function findByUsername($username) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Plain text password check
    public static function login($username, $password) {
        $user = self::findByUsername($username);

        if ($user && $password === $user['password']) {
            return $user; // login successful
        }
        return false; // invalid credentials
    }

    public static function usernameExists($username) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetchColumn() > 0;
    }

    public static function create($data) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        return $stmt->execute([
            $data['username'],
            $data['password'], // store plain text (not secure)
            $data['role']
        ]);
    }

    public static function updatePassword($userId, $newPassword) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([$newPassword, $userId]);
    }

    public static function findById($userId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
