<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}

$sql = "SELECT d.document_id, d.file_name, d.upload_date,
               r.requirement_name,
               s.scholarship_name,
               st.first_name, st.last_name
        FROM documents d
        INNER JOIN applications a ON d.application_id = a.application_id
        INNER JOIN students st ON a.student_id = st.student_id
        INNER JOIN requirements r ON d.requirement_id = r.requirement_id
        INNER JOIN scholarships s ON a.scholarship_id = s.scholarship_id
        ORDER BY d.upload_date DESC";

$documents = mysqli_query($conn, $sql);

if (!$documents) {
    die("Database error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Documents</title>
</head>
<body>

<h1>Manage Uploaded Documents</h1>

<a href="admin_dashboard.php">Back to Dashboard</a>

<?php if (mysqli_num_rows($documents) > 0): ?>

    <table border="1" cellpadding="10">
        <tr>
            <th>Student Name</th>
            <th>Scholarship</th>
            <th>Requirement</th>
            <th>File Name</th>
            <th>Upload Date</th>
            <th>Action</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($documents)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row["first_name"] . " " . $row["last_name"]); ?></td>
                <td><?php echo htmlspecialchars($row["scholarship_name"]); ?></td>
                <td><?php echo htmlspecialchars($row["requirement_name"]); ?></td>
                <td><?php echo htmlspecialchars($row["file_name"]); ?></td>
                <td><?php echo htmlspecialchars($row["upload_date"]); ?></td>
                <td>
                    <a href="download_document.php?id=<?php echo (int)$row["document_id"]; ?>">Download</a>
                </td>
            </tr>
        <?php endwhile; ?>

    </table>

<?php else: ?>

    <p>No uploaded documents found.</p>

<?php endif; ?>

</body>
</html>
