<?php
session_start();
require_once '../includes/auth.php';

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (authenticateAdmin($username, $password)) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header('Location: dashboard.php');
        exit;
    } else {
        $login_error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - POLITE MEAT SUPPLIERS</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-login">
        <div class="login-container">
            <div class="login-header">
                <h1><i class="fas fa-lock"></i> Admin Login</h1>
                <p>POLITE MEAT SUPPLIERS Management System</p>
            </div>
            
            <?php if (isset($login_error)): ?>
                <div class="error-message"><?= htmlspecialchars($login_error) ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn login-btn">Login</button>
            </form>
        </div>
    </div>
</body>
</html>