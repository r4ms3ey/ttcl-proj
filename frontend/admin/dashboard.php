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
$allowed = [
    "attendance.php",
    "workers.php",
    "departments.php",
    "announcements.php",
    "documents.php"
];

// pick page if valid, otherwise default
$kurasa = isset($_GET['kurasa']) && in_array($_GET['kurasa'], $allowed) 
    ? $_GET['kurasa'] 
    : "attendance.php";

// Fetch stats using PDO
$totalWorkers = $conn->query("SELECT COUNT(*) as total FROM field_worker_profiles")->fetch(PDO::FETCH_ASSOC)['total'];
$presentToday = $conn->query("SELECT COUNT(*) as total FROM attendance WHERE DATE(created_at)=CURDATE() AND status='Present'")->fetch(PDO::FETCH_ASSOC)['total'];
$totalDepartments = $conn->query("SELECT COUNT(*) as total FROM departments")->fetch(PDO::FETCH_ASSOC)['total'];
$pendingDocs = $conn->query("
    SELECT COUNT(*) as total 
    FROM documents d
    WHERE d.status='uploaded'
      AND d.user_id IN (
          SELECT user_id
          FROM documents
          WHERE status='uploaded'
          GROUP BY user_id
          HAVING COUNT(*) = 1
      )
")->fetch(PDO::FETCH_ASSOC)['total'];
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
    <style>
                    .glow-greeting {
                        color: #000000ff;
                        background: linear-gradient(90deg, #f8fa6eff 0%, #f3e521ff 100%);
                        padding: 3px 14px;
                        border-radius: 8px;
                        box-shadow: 0 0 12px 2px #f3e521ff, 0 0 24px 4px #71730aff;
                        font-weight: bold;
                        animation: glowPulse 2s infinite alternate;
                    }
                    @keyframes glowPulse {
                        0% {
                            box-shadow: 0 0 12px 2px #f3e5219f, 0 0 24px 4px #71730a9a;
                        }
                        100% {
                            box-shadow: 0 0 24px 6px #f3e5219f, 0 0 36px 12px #71730a9a;
                        }
                    }
    </style>
</head>
<body style="background: linear-gradient(135deg, #3d98ffff 0%, #ffed50ff 100%);">
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
                <?php
                $hour = (int)date('H');
                if ($hour < 12) {
                    $greeting = "Good morning🌄";
                } elseif ($hour < 18) {
                    $greeting = "Good afternoon🌞";
                } else {
                    $greeting = "Good evening🌜";
                }
                ?>
                <span class="glow-greeting"><?php echo $greeting; ?>, Administrator</span>
                
                <a href="../../logout.php" class="logout-btn">
                    Logout <span class="logout-icon"><i class="fas fa-sign-out-alt"></i></span>
                </a>
            </div>

            <span class="current-time glow-greeting" style="margin-top:10px;"><?php echo date('H:i'); ?></span>
        </div>
    </header>

    <!-- Dashboard Stats -->
    <section class="dashboard">
        <div class="stats">
            <div class="stat-card">
                <span class="stat-icon stat-icon-1"><i class="fa-solid fa-users"></i></span>
                <span class="stat-value stat-value-1"><?php echo $totalWorkers; ?></span>
                <span class="stat-label stat-label-1">Total Workers</span>
            </div>
            <div class="stat-card">
                <span class="stat-icon stat-icon-2"><i class="fa-solid fa-circle-check"></i></span>
                <span class="stat-value stat-value-2"><?php echo $presentToday; ?></span>
                <span class="stat-label stat-label-2">Present Today</span>
            </div>
            <div class="stat-card">
                <span class="stat-icon stat-icon-3"><i class="fas fa-building"></i></span>
                <span class="stat-value stat-value-3"><?php echo $totalDepartments; ?></span>
                <span class="stat-label stat-label-3">Departments</span>
            </div>
            <div class="stat-card">
                <span class="stat-icon stat-icon-4"><i class="fas fa-file-alt"></i></span>
                <span class="stat-value stat-value-4"><?php echo $pendingDocs; ?></span>
                <span class="stat-label stat-label-4">Pending Docs</span>
            </div>
        </div>
    </section>

    <!-- System Management Tabs -->
    <section class="management">
        <h2>System Management</h2>
        <div class="tabs">
            <form method="get" style="display:inline;">
                <button type="submit" name="kurasa" value="attendance.php" class="tab <?php echo ($kurasa === 'attendance.php') ? 'active' : ''; ?>"><i class="fas fa-calendar-check"></i>Attendance</button>
            </form>
            <form method="get" style="display:inline;">
                <button type="submit" name="kurasa" value="workers.php" class="tab <?php echo ($kurasa === 'workers.php') ? 'active' : ''; ?>"><i class="fas fa-users"></i>Field Workers</button>
            </form>
            <form method="get" style="display:inline;">
                <button type="submit" name="kurasa" value="departments.php" class="tab <?php echo ($kurasa === 'departments.php') ? 'active' : ''; ?>"><i class="fas fa-building"></i>Departments</button>
            </form>
            <form method="get" style="display:inline;">
                <button type="submit" name="kurasa" value="announcements.php" class="tab <?php echo ($kurasa === 'announcements.php') ? 'active' : ''; ?>"><i class="fas fa-bullhorn"></i>Announcements</button>
            </form>
            <form method="get" style="display:inline;">
                <button type="submit" name="kurasa" value="documents.php" class="tab <?php echo ($kurasa === 'documents.php') ? 'active' : ''; ?>"><i class="fas fa-file-alt"></i>Documentation</button>
            </form>
        </div>
    </section>

    <!-- Load content dynamically -->
    <main id="content">
        <?php include __DIR__ . "/" . $kurasa; ?>
    </main>
</body>
</html>
