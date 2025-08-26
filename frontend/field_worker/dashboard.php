<?php
require_once '../config/db.php';
require_once '../models/User.php';
require_once '../models/Announcement.php';
require_once '../models/Attendance.php';
require_once '../models/Document.php';
require_once '../middleware/AuthMiddleware.php';

AuthMiddleware::checkFieldWorker(); // Only allow field workers

// Get logged-in user
$userId = $_SESSION['user_id'];
$user = User::findById($pdo, $userId);

// Get announcements
$announcements = Announcement::getLatest($pdo);

// Get today's attendance status
$status = Attendance::getTodayStatus($pdo, $userId);

// Get documents
$documents = Document::getByUser($pdo, $userId);

// Handle check-in
if (isset($_POST['check_in'])) {
    Attendance::checkIn($pdo, $userId);
    header("Location: field_worker_dashboard.php");
    exit;
}

// Handle check-out
if (isset($_POST['check_out'])) {
    Attendance::checkOut($pdo, $userId);
    header("Location: field_worker_dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entry-Log System</title>
    <link rel="stylesheet" href="../assets/styles.css"> <!-- Move your CSS to a file -->
</head>
<body>

<header>
    <div>
        <h1>Entry-Log System</h1>
        <small>Field Worker Dashboard</small>
    </div>
    <div class="user-info">
        <strong><?= htmlspecialchars($user['full_name']) ?></strong><br>
        <small><?= htmlspecialchars($user['department_name']) ?></small>
        <br>
        <form action="../controllers/AuthController.php" method="POST">
            <button type="submit" name="logout">Logout</button>
        </form>
    </div>
</header>

<main>
    <!-- Profile Information -->
    <div class="card">
        <h2>Profile Information</h2>
        <div class="info-item"><span>Full Name</span><?= htmlspecialchars($user['full_name']) ?></div>
        <div class="info-item"><span>Department</span><?= htmlspecialchars($user['department_name']) ?></div>
        <div class="info-item"><span>Work Group</span><button class="btn-secondary"><?= htmlspecialchars($user['work_group']) ?></button></div>
        <div class="info-item"><span>Phone</span><?= htmlspecialchars($user['phone']) ?></div>
        <div class="info-item"><span>Email</span><?= htmlspecialchars($user['email']) ?></div>
        <div class="info-item"><span>Work Period</span><?= htmlspecialchars($user['start_date']) ?> - <?= htmlspecialchars($user['end_date']) ?></div>
    </div>

    <!-- Attendance Actions -->
    <div class="card">
        <h2>Attendance Actions</h2>
        <form method="POST">
            <button class="btn-primary" type="submit" name="check_in" <?= ($status == 'Checked In') ? 'disabled' : '' ?>>Check In</button>
            <br><br>
            <button class="btn-secondary" type="submit" name="check_out" <?= ($status != 'Checked In') ? 'disabled' : '' ?>>Check Out</button>
        </form>
    </div>

    <!-- Daily Announcements -->
    <div class="card">
        <h2>Daily Announcements</h2>
        <?php foreach ($announcements as $a): ?>
            <div class="announcement">
                <?= htmlspecialchars($a['message']) ?><br>
                <small><?= htmlspecialchars($a['created_at']) ?></small>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Current Status -->
    <div class="card">
        <h2>Current Status</h2>
        <div class="status"><?= date("h:i:s A") ?></div>
        <div class="status-label"><?= date("m/d/Y") ?></div>
        <div class="status-label">Status: <?= $status ?: 'Not Checked In' ?></div>
    </div>

    <!-- Documentation -->
    <div class="card">
        <h2>Documentation</h2>
        <p>Download templates and upload completed documents</p>
        <div class="doc-buttons">
            <a href="../templates/certificate_template.pdf" class="btn-secondary">Certificate</a>
            <a href="../templates/registration_template.pdf" class="btn-secondary">Registration</a>
        </div>
        <p>Upload Completed Documents:</p>
        <form action="../controllers/DocumentController.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="certificate" required>
            <button class="btn-secondary" type="submit" name="upload_certificate">Upload Certificate</button>
        </form>
        <form action="../controllers/DocumentController.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="registration" required>
            <button class="btn-secondary" type="submit" name="upload_registration">Upload Registration</button>
        </form>
    </div>

</main>

</body>
</html>