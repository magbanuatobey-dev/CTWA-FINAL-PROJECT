<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: login.php");
    exit;
}

$sql = "SELECT * FROM scholarships WHERE status = 'active' ORDER BY application_deadline ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Available Scholarships</title>
</head>
<body>

<h1>Available Scholarships</h1>

<a href="student_dashboard.php">Back to Dashboard</a>

<hr>

<?php if (mysqli_num_rows($result) > 0): ?>

    <?php while ($scholarship = mysqli_fetch_assoc($result)): ?>

        <h2><?php echo htmlspecialchars($scholarship["scholarship_name"]); ?></h2>

        <p><strong>Description:</strong>
            <?php echo nl2br(htmlspecialchars($scholarship["description"])); ?>
        </p>

        <p><strong>Eligibility:</strong>
            <?php echo nl2br(htmlspecialchars($scholarship["eligibility"])); ?>
        </p>

        <p><strong>Benefits:</strong>
            <?php echo nl2br(htmlspecialchars($scholarship["benefits"])); ?>
        </p>

        <p><strong>Deadline:</strong>
            <?php echo htmlspecialchars($scholarship["application_deadline"]); ?>
        </p>

        <a href="apply.php?id=<?php echo (int)$scholarship["scholarship_id"]; ?>">
            Apply Now
        </a>

        <hr>

    <?php endwhile; ?>

<?php else: ?>

    <p>No available scholarships at the moment.</p>

<?php endif; ?>

</body>
</html>
