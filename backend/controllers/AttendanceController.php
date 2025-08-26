<?php
require_once __DIR__ . '/../../backend/models/Attendance.php';
require_once __DIR__ . '/../services/GroupService.php';
require_once __DIR__ . '/../services/LocationService.php';


class AttendanceController
{
    // Field Worker: Check-In
    public static function checkIn() {
        $userId = $_SESSION['user_id'];

        if (!GroupService::isTodayAllowed($userId)) {
            return ['error' => 'Your group is not scheduled to work today.'];
        }

        if (Attendance::hasCheckedInToday($userId)) {
            return ['error' => 'You have already checked in today.'];
        }

        if (!LocationService::isAllowedIP($_SERVER['REMOTE_ADDR'])) {
            return ['error' => 'Check-in denied. Unauthorized location/IP.'];
        }

        return Attendance::checkIn($userId);
    }

    // Field Worker: Check-Out
    public static function checkOut() {
        $userId = $_SESSION['user_id'];

        if (!Attendance::hasCheckedInToday($userId)) {
            return ['error' => 'You must check in before checking out.'];
        }

        if (Attendance::hasCheckedOutToday($userId)) {
            return ['error' => 'You have already checked out today.'];
        }

        return Attendance::checkOut($userId);
    }

    // Admin: View attendance (with optional filters)
    public static function view($date = null, $departmentId = null) {
        return Attendance::filter($date, $departmentId);
    }

    // Admin: Delete multiple records
    public static function deleteMany($ids = []) {
        return Attendance::deleteMany($ids);
    }
}
