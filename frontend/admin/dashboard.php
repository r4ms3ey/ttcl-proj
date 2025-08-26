<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php'); // redirect to login if not admin
    exit;
}

// Include database connection
require_once __DIR__ . '/../../config/database.php';
$conn = Database::getConnection(); // get PDO connection

// Allowed pages for tabs
$allowed = ["attendance.php", "workers.php", "departments.php", "announcements.php", "documents.php"];
$kurasa = isset($_GET['kurasa']) && in_array($_GET['kurasa'], $allowed) ? $_GET['kurasa'] : "attendance.php";

// Fetch stats using PDO
$totalWorkers = $conn->query("SELECT COUNT(*) as total FROM field_worker_profiles")->fetch(PDO::FETCH_ASSOC)['total'];
$presentToday = $conn->query("SELECT COUNT(*) as total FROM attendance WHERE DATE(created_at)=CURDATE() AND status='Present'")->fetch(PDO::FETCH_ASSOC)['total'];
$totalDepartments = $conn->query("SELECT COUNT(*) as total FROM departments")->fetch(PDO::FETCH_ASSOC)['total'];
$pendingDocs = $conn->query("SELECT COUNT(*) as total FROM documents WHERE status='Pending'")->fetch(PDO::FETCH_ASSOC)['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Entry-Log System</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="dashboard.js"></script>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-left">
            <span class="clock-icon"><i class="fas fa-clock"></i></span>
            <div class="title-section">
                <h1>Entry-Log System</h1>
                <span class="dashboard-label">Administrator Dashboard</span>
            </div>
        </div>
        <div class="header-right">
            <div class="user-section">
                <span>Administrator</span>
                <a href="../../logout.php" class="logout-btn">
                    Logout <span class="logout-icon"><i class="fas fa-sign-out-alt"></i></span>
                </a>
            </div>
            <span class="user-role">System Admin</span>
        </div>
    </header>

    <!-- Dashboard Stats -->
    <section class="dashboard">
        <div class="stats">
            <div class="stat-card">
                <span class="stat-icon"><i class="fa-solid fa-users"></i></span>
                <span class="stat-value"><?php echo $totalWorkers; ?></span>
                <span class="stat-label">Total Workers</span>
            </div>
            <div class="stat-card">
                <span class="stat-icon"><i class="fa-solid fa-circle-check"></i></span>
                <span class="stat-value"><?php echo $presentToday; ?></span>
                <span class="stat-label">Present Today</span>
            </div>
            <div class="stat-card">
                <span class="stat-icon"><i class="fas fa-building"></i></span>
                <span class="stat-value"><?php echo $totalDepartments; ?></span>
                <span class="stat-label">Departments</span>
            </div>
            <div class="stat-card">
                <span class="stat-icon"><i class="fas fa-file-alt"></i></span>
                <span class="stat-value"><?php echo $pendingDocs; ?></span>
                <span class="stat-label">Pending Docs</span>
            </div>
        </div>
    </section>

    <!-- System Management Tabs -->
    <section class="management">
        <h2>System Management</h2>
        <div class="tabs">
            <a href="?kurasa=attendance.php" class="tab <?php echo ($kurasa === 'attendance.php') ? 'active' : ''; ?>">Attendance</a>
            <a href="?kurasa=workers.php" class="tab <?php echo ($kurasa === 'workers.php') ? 'active' : ''; ?>">Field Workers</a>
            <a href="?kurasa=departments.php" class="tab <?php echo ($kurasa === 'departments.php') ? 'active' : ''; ?>">Departments</a>
            <a href="?kurasa=announcements.php" class="tab <?php echo ($kurasa === 'announcements.php') ? 'active' : ''; ?>">Announcements</a>
            <a href="?kurasa=documents.php" class="tab <?php echo ($kurasa === 'documents.php') ? 'active' : ''; ?>">Documentation</a>
        </div>
    </section>

    <!-- Load content dynamically -->
    <main id="content">
        <?php include __DIR__ . "/" . $kurasa; ?>
    </main>
</body>
</html>
