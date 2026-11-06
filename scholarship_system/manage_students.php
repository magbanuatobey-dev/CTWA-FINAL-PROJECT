
<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}

$sql = "SELECT st.student_id, st.first_name, st.last_name,
               st.middle_name, st.birth_date, st.gender,
               st.address, st.contact_number, st.school,
               st.course, st.year_level, st.GPA, u.email
        FROM students st
        INNER JOIN users u ON st.user_id = u.user_id
        ORDER BY st.student_id DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Students</title>
</head>
<body>

<h1>Manage Students</h1>

<a href="admin_dashboard.php">Back to Dashboard</a>

<hr>

<?php if (mysqli_num_rows($result) > 0): ?>

    <?php while ($student = mysqli_fetch_assoc($result)): ?>

        <h2>
            <?php echo htmlspecialchars($student["first_name"] . " " . $student["last_name"]); ?>
        </h2>

        <p>Student ID: <?php echo (int)$student["student_id"]; ?></p>
        <p>Email: <?php echo htmlspecialchars($student["email"]); ?></p>
        <p>Middle Name: <?php echo htmlspecialchars($student["middle_name"] ?? ""); ?></p>
        <p>Birth Date: <?php echo htmlspecialchars($student["birth_date"]); ?></p>
        <p>Gender: <?php echo htmlspecialchars($student["gender"]); ?></p>
        <p>Address: <?php echo htmlspecialchars($student["address"]); ?></p>
        <p>Contact Number: <?php echo htmlspecialchars($student["contact_number"]); ?></p>
        <p>School: <?php echo htmlspecialchars($student["school"]); ?></p>
        <p>Course: <?php echo htmlspecialchars($student["course"]); ?></p>
        <p>Year Level: <?php echo htmlspecialchars($student["year_level"]); ?></p>
        <p>GPA: <?php echo htmlspecialchars($student["GPA"]); ?></p>

        <hr>

    <?php endwhile; ?>

<?php else: ?>

    <p>No student profiles have been registered yet.</p>

<?php endif; ?>

</body>
</html>
