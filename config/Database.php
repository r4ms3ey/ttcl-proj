<?php
class Database {
    private static $host = 'localhost';
    private static $dbname = 'attendance_db';
    private static $user = 'root';
    private static $pass = '';
    private static $conn = null;

    public static function getConnection() {
        if (self::$conn === null) {
            $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$dbname . ";charset=utf8mb4";
            try {
                self::$conn = new PDO($dsn, self::$user, self::$pass);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return self::$conn;
    }
}
