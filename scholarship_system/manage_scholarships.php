
<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "add") {
        $name = trim($_POST["scholarship_name"]);
        $description = trim($_POST["description"]);
        $eligibility = trim($_POST["eligibility"]);
        $benefits = trim($_POST["benefits"]);
        $deadline = $_POST["application_deadline"];
        $status = $_POST["status"];

        if (!in_array($status, ["active", "inactive"], true)) {
            $message = "Invalid scholarship status.";
        } else {
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO scholarships
                (scholarship_name, description, eligibility, benefits, application_deadline, status)
                VALUES (?, ?, ?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "ssssss",
                $name,
                $description,
                $eligibility,
                $benefits,
                $deadline,
                $status
            );

            $message = mysqli_stmt_execute($stmt)
                ? "Scholarship added successfully!"
                : "Failed to add scholarship.";

            mysqli_stmt_close($stmt);
        }
    }

    if ($action === "update") {
        $id = (int)$_POST["scholarship_id"];
        $name = trim($_POST["scholarship_name"]);
        $description = trim($_POST["description"]);
        $eligibility = trim($_POST["eligibility"]);
        $benefits = trim($_POST["benefits"]);
        $deadline = $_POST["application_deadline"];
        $status = $_POST["status"];

        if (!in_array($status, ["active", "inactive"], true)) {
            $message = "Invalid scholarship status.";
        } else {
            $stmt = mysqli_prepare(
                $conn,
                "UPDATE scholarships
                 SET scholarship_name = ?, description = ?, eligibility = ?,
                     benefits = ?, application_deadline = ?, status = ?
                 WHERE scholarship_id = ?"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "ssssssi",
                $name,
                $description,
                $eligibility,
                $benefits,
                $deadline,
                $status,
                $id
            );

            $message = mysqli_stmt_execute($stmt)
                ? "Scholarship updated successfully!"
                : "Failed to update scholarship.";

            mysqli_stmt_close($stmt);
        }
    }

    if ($action === "delete") {
        $id = (int)$_POST["scholarship_id"];

        $stmt = mysqli_prepare(
            $conn,
            "DELETE FROM scholarships WHERE scholarship_id = ?"
        );

        mysqli_stmt_bind_param($stmt, "i", $id);

        $message = mysqli_stmt_execute($stmt)
            ? "Scholarship deleted successfully!"
            : "Failed to delete scholarship.";

        mysqli_stmt_close($stmt);
    }
}

$edit_scholarship = null;

if (isset($_GET["edit"])) {
    $id = (int)$_GET["edit"];

    $stmt = mysqli_prepare(
        $conn,
        "SELECT * FROM scholarships WHERE scholarship_id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $edit_scholarship = mysqli_fetch_assoc(
        mysqli_stmt_get_result($stmt)
    );

    mysqli_stmt_close($stmt);
}

$result = mysqli_query(
    $conn,
    "SELECT * FROM scholarships ORDER BY scholarship_id DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Scholarships</title>
</head>
<body>

<h1>Manage Scholarships</h1>

<a href="admin_dashboard.php">Back to Dashboard</a>

<p><?php echo htmlspecialchars($message); ?></p>

<hr>

<h2><?php echo $edit_scholarship ? "Edit Scholarship" : "Add Scholarship"; ?></h2>

<form method="POST">

    <input type="hidden" name="action" value="<?php echo $edit_scholarship ? "update" : "add"; ?>">

    <?php if ($edit_scholarship): ?>
        <input type="hidden" name="scholarship_id" value="<?php echo (int)$edit_scholarship["scholarship_id"]; ?>">
    <?php endif; ?>

    <label>Scholarship Name:</label><br>
    <input type="text" name="scholarship_name" value="<?php echo htmlspecialchars($edit_scholarship["scholarship_name"] ?? ""); ?>" required><br><br>

    <label>Description:</label><br>
    <textarea name="description" required><?php echo htmlspecialchars($edit_scholarship["description"] ?? ""); ?></textarea><br><br>

    <label>Eligibility:</label><br>
    <textarea name="eligibility" required><?php echo htmlspecialchars($edit_scholarship["eligibility"] ?? ""); ?></textarea><br><br>

    <label>Benefits:</label><br>
    <textarea name="benefits" required><?php echo htmlspecialchars($edit_scholarship["benefits"] ?? ""); ?></textarea><br><br>

    <label>Application Deadline:</label><br>
    <input type="date" name="application_deadline" value="<?php echo htmlspecialchars($edit_scholarship["application_deadline"] ?? ""); ?>" required><br><br>

    <label>Status:</label><br>
    <select name="status" required>
        <option value="active" <?php echo ($edit_scholarship["status"] ?? "active") === "active" ? "selected" : ""; ?>>Active</option>
        <option value="inactive" <?php echo ($edit_scholarship["status"] ?? "") === "inactive" ? "selected" : ""; ?>>Inactive</option>
    </select><br><br>

    <button type="submit">
        <?php echo $edit_scholarship ? "Update Scholarship" : "Add Scholarship"; ?>
    </button>

    <?php if ($edit_scholarship): ?>
        <a href="manage_scholarships.php">Cancel</a>
    <?php endif; ?>

</form>

<hr>

<h2>Existing Scholarships</h2>

<?php if (mysqli_num_rows($result) > 0): ?>

    <?php while ($row = mysqli_fetch_assoc($result)): ?>

        <h3><?php echo htmlspecialchars($row["scholarship_name"]); ?></h3>

        <p>Description: <?php echo nl2br(htmlspecialchars($row["description"])); ?></p>

        <p>Eligibility: <?php echo nl2br(htmlspecialchars($row["eligibility"])); ?></p>

        <p>Benefits: <?php echo nl2br(htmlspecialchars($row["benefits"])); ?></p>

        <p>Deadline: <?php echo htmlspecialchars($row["application_deadline"]); ?></p>

        <p>Status: <?php echo htmlspecialchars($row["status"]); ?></p>

        <a href="manage_scholarships.php?edit=<?php echo (int)$row["scholarship_id"]; ?>">Edit</a>

        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this scholarship? Related applications and records may also be deleted.');">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="scholarship_id" value="<?php echo (int)$row["scholarship_id"]; ?>">
            <button type="submit">Delete</button>
        </form>

        <hr>

    <?php endwhile; ?>

<?php else: ?>

    <p>No scholarships have been added yet.</p>

<?php endif; ?>

</body>
</html>
