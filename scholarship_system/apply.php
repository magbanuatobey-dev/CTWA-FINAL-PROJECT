
<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: login.php");
    exit;
}

$message = "";
$scholarship_id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

$stmt = mysqli_prepare($conn, "SELECT student_id FROM students WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $_SESSION["user_id"]);
mysqli_stmt_execute($stmt);
$student_result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($student_result);
mysqli_stmt_close($stmt);

if (!$student) {
    die("Please complete your student profile before applying. <a href='profile.php'>Complete Profile</a>");
}

$student_id = $student["student_id"];

$stmt = mysqli_prepare(
    $conn,
    "SELECT scholarship_name FROM scholarships
     WHERE scholarship_id = ? AND status = 'active'
     AND application_deadline >= CURDATE()"
);
mysqli_stmt_bind_param($stmt, "i", $scholarship_id);
mysqli_stmt_execute($stmt);
$scholarship_result = mysqli_stmt_get_result($stmt);
$scholarship = mysqli_fetch_assoc($scholarship_result);
mysqli_stmt_close($stmt);

if (!$scholarship) {
    die("Scholarship is unavailable or the deadline has passed. <a href='scholarships.php'>Back</a>");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $stmt = mysqli_prepare(
        $conn,
        "SELECT application_id FROM applications
         WHERE student_id = ? AND scholarship_id = ?"
    );

    mysqli_stmt_bind_param($stmt, "ii", $student_id, $scholarship_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        $message = "You have already applied for this scholarship.";
    } else {
        mysqli_stmt_close($stmt);

        $status = "pending";

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO applications (student_id, scholarship_id, status)
             VALUES (?, ?, ?)"
        );

        mysqli_stmt_bind_param($stmt, "iis", $student_id, $scholarship_id, $status);

        if (mysqli_stmt_execute($stmt)) {
            $message = "Your scholarship application was submitted successfully!";
        } else {
            $message = "Application failed. Please try again.";
        }
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Apply for Scholarship</title>
</head>
<body>

<h1>Apply for Scholarship</h1>

<h2><?php echo htmlspecialchars($scholarship["scholarship_name"]); ?></h2>

<p><?php echo htmlspecialchars($message); ?></p>

<?php if ($message === ""): ?>
    <p>Are you sure you want to apply for this scholarship?</p>

    <form method="POST">
        <button type="submit">Submit Application</button>
    </form>
<?php endif; ?>

<br>
<a href="scholarships.php">Back to Scholarships</a>

</body>
</html>
