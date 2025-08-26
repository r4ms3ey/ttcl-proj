<?php
session_start();

$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']); // clear it after showing
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entry-Log System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">

</head>
<body>

    <div class="left">
        <h1><span>Entry-Log</span> System</h1>
        <p>Professional attendance and activity tracking system for field workers and administrators.</p>

        <div class="feature-box">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <div>
                <strong>Time Tracking</strong>
                Accurate check-in and check-out management
            </div>
        </div>

        <div class="feature-box">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-8 0v2"/><circle cx="12" cy="7" r="4"/></svg>
            <div>
                <strong>Department Management</strong>
                Organize workers by departments and groups
            </div>
        </div>

        <div class="feature-box">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1l3 5h6l-3 5 3 5h-6l-3 5-3-5H3l3-5-3-5h6z"/></svg>
            <div>
                <strong>Secure Access</strong>
                Role-based access with location verification
            </div>
        </div>
    </div>

    <div class="right">
        <div class="login-card">
            <h2>Welcome Back</h2>
            <p>Sign in to access your dashboard</p>
            
            <?php if ($error): ?>
                <div style="color:red; margin-bottom:10px;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>


            <form method="POST" action="login.php">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Enter your username" required  autocomplete="username">
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
                </div>
                <button type="submit" class="btn">Sign In</button>
            </form>
        </div>
    </div>

</body>
</html>
