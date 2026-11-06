<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    die("Access denied. Your account is not an administrator.");
}

$username = $_SESSION["username"] ?? "Administrator";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>

    <h1>Admin Dashboard</h1>

    <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>

    <p>Welcome to the Scholarship Application Management System.</p>

    <h3>Administrator Menu</h3>

    <ul>
        <li><a href="manage_scholarships.php">Manage Scholarships</a></li>
        <li><a href="manage_requirements.php">Manage Requirements</a></li>
        <li><a href="manage_applications.php">Manage Applications</a></li>
        <li><a href="manage_students.php">Manage Students</a></li>
        <li><a href="manage_documents.php">Manage Documents</a></li>
    </ul>

    <a href="logout.php">Logout</a>

</body>
</html>
