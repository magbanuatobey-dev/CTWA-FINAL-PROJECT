<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $application_id = (int)$_POST["application_id"];
    $status = $_POST["status"];

    if (in_array($status, ["pending", "approved", "rejected"], true)) {
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE applications SET status = ? WHERE application_id = ?"
        );

        mysqli_stmt_bind_param($stmt, "si", $status, $application_id);

        if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) >= 0) {
            $history = mysqli_prepare(
                $conn,
                "INSERT INTO application_status (application_id, status, remarks)
                 VALUES (?, ?, ?)"
            );

            $remarks = "Status updated by administrator.";

            mysqli_stmt_bind_param($history, "iss", $application_id, $status, $remarks);
            mysqli_stmt_execute($history);
            mysqli_stmt_close($history);

            $message = "Application status updated successfully.";
        } else {
            $message = "Failed to update application status.";
        }

        mysqli_stmt_close($stmt);
    } else {
        $message = "Invalid status selected.";
    }
}

$sql = "SELECT a.application_id, a.application_date, a.status,
               st.first_name, st.last_name, st.school, st.course,
               u.email, s.scholarship_name
        FROM applications a
        INNER JOIN students st ON a.student_id = st.student_id
        INNER JOIN users u ON st.user_id = u.user_id
        INNER JOIN scholarships s ON a.scholarship_id = s.scholarship_id
        ORDER BY a.application_date DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Applications</title>
</head>
<body>

<h1>Manage Applications</h1>

<a href="admin_dashboard.php">Back to Dashboard</a>

<p><?php echo htmlspecialchars($message); ?></p>

<hr>

<?php if (mysqli_num_rows($result) > 0): ?>

    <?php while ($row = mysqli_fetch_assoc($result)): ?>

        <h2>Application #<?php echo (int)$row["application_id"]; ?></h2>

        <p>Student: <?php echo htmlspecialchars($row["first_name"] . " " . $row["last_name"]); ?></p>

        <p>Email: <?php echo htmlspecialchars($row["email"]); ?></p>

        <p>School: <?php echo htmlspecialchars($row["school"]); ?></p>

        <p>Course: <?php echo htmlspecialchars($row["course"]); ?></p>

        <p>Scholarship: <?php echo htmlspecialchars($row["scholarship_name"]); ?></p>

        <p>Application Date: <?php echo htmlspecialchars($row["application_date"]); ?></p>

        <p>Current Status: <?php echo htmlspecialchars(ucfirst($row["status"])); ?></p>

        <form method="POST">
            <input type="hidden" name="application_id" value="<?php echo (int)$row["application_id"]; ?>">

            <label>Update Status:</label>

            <select name="status" required>
                <option value="pending" <?php echo $row["status"] === "pending" ? "selected" : ""; ?>>Pending</option>
                <option value="approved" <?php echo $row["status"] === "approved" ? "selected" : ""; ?>>Approved</option>
                <option value="rejected" <?php echo $row["status"] === "rejected" ? "selected" : ""; ?>>Rejected</option>
            </select>

            <button type="submit">Update</button>
        </form>

        <hr>

    <?php endwhile; ?>

<?php else: ?>

    <p>No applications have been submitted yet.</p>

<?php endif; ?>

</body>
</html>
