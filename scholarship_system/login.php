
<?php
session_start();
require_once "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = mysqli_prepare(
        $conn,
        "SELECT user_id, username, password, role FROM users WHERE email = ?"
    );

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $user["password"])) {
            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = trim($user["role"]);

            mysqli_stmt_close($stmt);

            if ($_SESSION["role"] === "admin") {
                header("Location: admin_dashboard.php");
                exit;
            }

            if ($_SESSION["role"] === "student") {
                header("Location: student_dashboard.php");
                exit;
            }

            session_destroy();
            die("Invalid account role.");
        } else {
            $message = "Incorrect email or password.";
        }
    } else {
        $message = "Incorrect email or password.";
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Scholarship System Login</title>
</head>
<body>

    <h2>Scholarship System Login</h2>

    <p><?php echo htmlspecialchars($message); ?></p>

    <form method="POST">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a></p>

</body>
</html>
