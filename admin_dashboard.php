<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Admin Dashboard</title></head>
<body>
<h2>Welcome, Admin <?php echo $_SESSION['name']; ?> 👑</h2>
<p>This is the Admin Dashboard.</p>
<a href="logout.php">Logout</a>
</body>
</html>
