<?php
require_once '../models/Admin.php';
require_once '../models/FieldWorker.php';
require_once '../models/Department.php';
require_once '../models/Announcement.php';
require_once '../models/Document.php';
require_once '../models/Attendance.php';
session_start();

class AdminController
{
    // 1. Add single or multiple field workers
    public static function addFieldWorkers($fieldWorkers) {
        foreach ($fieldWorkers as $worker) {
            $worker['password'] = password_hash('ttcl1234', PASSWORD_DEFAULT);
            FieldWorker::create($worker);
        }
    }

    // 2. Update field worker
    public static function updateFieldWorker($id, $data) {
        return FieldWorker::update($id, $data);
    }

    // 3. Delete one or multiple field workers
    public static function deleteFieldWorkers($ids) {
        return FieldWorker::deleteMany($ids);
    }

    // 4. Add or update department
    public static function addDepartment($data) {
        return Department::create($data);
    }

    public static function updateDepartment($id, $data) {
        return Department::update($id, $data);
    }

    public static function deleteDepartment($id) {
        return Department::delete($id);
    }

    // 5. Create announcement
    public static function postAnnouncement($data) {
        return Announcement::create($data);
    }

    public static function updateAnnouncement($id, $data) {
        return Announcement::update($id, $data);
    }

    public static function deleteAnnouncement($id) {
        return Announcement::delete($id);
    }

    // 6. Upload certificate & registration template
    public static function uploadTemplate($file, $type) {
        return Document::uploadTemplate($file, $type);
    }

    public static function setSubmissionDeadline($deadlineDate) {
        return Document::setDeadline($deadlineDate);
    }

    // 7. Attendance management
    public static function getAttendance($filters = []) {
        return Attendance::filter($filters);
    }

    public static function deleteAttendanceRecords($ids) {
        return Attendance::deleteMany($ids);
    }

    // 8. View submitted documents
    public static function getSubmittedDocuments() {
        return Document::getAllSubmissions();
    }

    public static function deleteDocuments($ids) {
        return Document::deleteMany($ids);
    }
}
