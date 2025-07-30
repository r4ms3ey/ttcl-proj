<?php
require_once '../config/Database.php';
class GroupService {
    public static function isTodayAllowed($userId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("           
            SELECT f.group_name, d.group_start_date
            FROM field_worker_profiles f
            JOIN departments d ON f.department_id = d.id
            WHERE f.user_id = ?
        ");
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return false; 
        }

        $group = $row['group_name'];     // A or B
        $startDate = new DateTime($row['group_start_date']);
        $today = new DateTime();

        // Block weekends
        if (in_array($today->format('N'), [6, 7])) {
            return false;
        }

        // Count weekdays between group start and today
        $interval = new DatePeriod($startDate, new DateInterval('P1D'), $today);
        $workDays = 0;

        foreach ($interval as $date) {
            if ((int)$date->format('N') < 6) $workDays++;
        }

        // A on even, B on odd
        return ($group == 'A' && $workDays % 2 == 0)
            || ($group == 'B' && $workDays % 2 == 1);
    }
}
