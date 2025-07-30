<?php
require_once '../models/FieldWorker.php';
require_once '../models/Attendance.php';
require_once '../services/GroupService.php';
session_start();

class FieldWorkerController
{
    public static function completeProfile($data) {
        $userId = $_SESSION['user_id'];
        return FieldWorker::saveProfile($userId, $data);
    }

    public static function updateProfile($data) {
        $userId = $_SESSION['user_id'];
        return FieldWorker::updateProfile($userId, $data);
    }

    public static function changePassword($newPassword) {
        $userId = $_SESSION['user_id'];
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        return FieldWorker::updatePassword($userId, $hashed);
    }

    public static function checkIn() {
        $userId = $_SESSION['user_id'];
        if (!GroupService::isTodayAllowed($userId)) {
            return ['error' => 'Your group is not scheduled today'];
        }

        return Attendance::checkIn($userId, $_SERVER['REMOTE_ADDR']);
    }

    public static function checkOut() {
        $userId = $_SESSION['user_id'];
        return Attendance::checkOut($userId, $_SERVER['REMOTE_ADDR']);
    }

    public static function uploadDocument($file, $type) {
        $userId = $_SESSION['user_id'];
        return FieldWorker::handleUpload($userId, $file, $type);
    }
}
