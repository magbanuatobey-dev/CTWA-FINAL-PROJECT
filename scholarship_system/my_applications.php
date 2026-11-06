<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: login.php");
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT a.application_id, a.application_date, a.status,
            s.scholarship_name, s.application_deadline
     FROM applications a
     INNER JOIN students st ON a.student_id = st.student_id
     INNER JOIN scholarships s ON a.scholarship_id = s.scholarship_id
     WHERE st.user_id = ?
     ORDER BY a.application_date DESC"
);

mysqli_stmt_bind_param($stmt, "i", $_SESSION["user_id"]);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Applications</title>
</head>
<body>

<h1>My Applications</h1>

<a href="student_dashboard.php">Back to Dashboard</a>

<hr>

<?php if (mysqli_num_rows($result) > 0): ?>

    <?php while ($application = mysqli_fetch_assoc($result)): ?>

        <h2><?php echo htmlspecialchars($application["scholarship_name"]); ?></h2>

        <p>
            Application ID:
            <?php echo (int)$application["application_id"]; ?>
        </p>

        <p>
            Application Date:
            <?php echo htmlspecialchars($application["application_date"]); ?>
        </p>

        <p>
            Deadline:
            <?php echo htmlspecialchars($application["application_deadline"]); ?>
        </p>

        <p>
            Status:
            <?php echo htmlspecialchars(ucfirst($application["status"])); ?>
        </p>

        <hr>

    <?php endwhile; ?>

<?php else: ?>

    <p>You have not submitted any applications yet.</p>

<?php endif; ?>

</body>
</html>
