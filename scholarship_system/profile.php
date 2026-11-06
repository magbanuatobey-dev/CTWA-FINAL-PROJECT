<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $middle_name = trim($_POST["middle_name"]);
    $birth_date = $_POST["birth_date"];
    $gender = $_POST["gender"];
    $address = trim($_POST["address"]);
    $contact_number = trim($_POST["contact_number"]);
    $school = trim($_POST["school"]);
    $course = trim($_POST["course"]);
    $year_level = trim($_POST["year_level"]);
    $gpa = $_POST["gpa"];

    $stmt = mysqli_prepare($conn, "SELECT student_id FROM students WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $_SESSION["user_id"]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        $message = "You already have a student profile.";
    } else {
        mysqli_stmt_close($stmt);

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO students
            (user_id, first_name, last_name, middle_name, birth_date, gender, address, contact_number, school, course, year_level, GPA)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "issssssssssd",
            $_SESSION["user_id"],
            $first_name,
            $last_name,
            $middle_name,
            $birth_date,
            $gender,
            $address,
            $contact_number,
            $school,
            $course,
            $year_level,
            $gpa
        );

        if (mysqli_stmt_execute($stmt)) {
            $message = "Student profile saved successfully!";
        } else {
            $message = "Error saving profile: " . mysqli_error($conn);
        }
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Profile</title>
</head>
<body>

<h2>Student Profile</h2>

<p><?php echo htmlspecialchars($message); ?></p>

<form method="POST">

    <label>First Name:</label><br>
    <input type="text" name="first_name" required><br><br>

    <label>Last Name:</label><br>
    <input type="text" name="last_name" required><br><br>

    <label>Middle Name:</label><br>
    <input type="text" name="middle_name"><br><br>

    <label>Birth Date:</label><br>
    <input type="date" name="birth_date" required><br><br>

    <label>Gender:</label><br>
    <select name="gender" required>
        <option value="">Select Gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
        <option value="Other">Other</option>
    </select><br><br>

    <label>Address:</label><br>
    <input type="text" name="address" required><br><br>

    <label>Contact Number:</label><br>
    <input type="text" name="contact_number" required><br><br>

    <label>School:</label><br>
    <input type="text" name="school" required><br><br>

    <label>Course:</label><br>
    <input type="text" name="course" required><br><br>

    <label>Year Level:</label><br>
    <input type="text" name="year_level" required><br><br>

    <label>GPA:</label><br>
    <input type="number" name="gpa" step="0.01" min="0" max="4" required><br><br>

    <button type="submit">Save Profile</button>

</form>

<br>
<a href="student_dashboard.php">Back to Dashboard</a>

</body>
</html>
