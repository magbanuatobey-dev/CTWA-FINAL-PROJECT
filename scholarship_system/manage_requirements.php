
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
        $scholarship_id = (int)$_POST["scholarship_id"];
        $name = trim($_POST["requirement_name"]);
        $description = trim($_POST["description"]);

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO requirements (scholarship_id, requirement_name, description)
             SELECT scholarship_id, ?, ? FROM scholarships WHERE scholarship_id = ?"
        );

        mysqli_stmt_bind_param($stmt, "ssi", $name, $description, $scholarship_id);

        $message = mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) === 1
            ? "Requirement added successfully!"
            : "Failed to add requirement.";

        mysqli_stmt_close($stmt);
    }

    if ($action === "update") {
        $id = (int)$_POST["requirement_id"];
        $name = trim($_POST["requirement_name"]);
        $description = trim($_POST["description"]);

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE requirements SET requirement_name = ?, description = ?
             WHERE requirement_id = ?"
        );

        mysqli_stmt_bind_param($stmt, "ssi", $name, $description, $id);

        $message = mysqli_stmt_execute($stmt)
            ? "Requirement updated successfully!"
            : "Failed to update requirement.";

        mysqli_stmt_close($stmt);
    }

    if ($action === "delete") {
        $id = (int)$_POST["requirement_id"];

        $stmt = mysqli_prepare(
            $conn,
            "DELETE FROM requirements WHERE requirement_id = ?"
        );

        mysqli_stmt_bind_param($stmt, "i", $id);

        $message = mysqli_stmt_execute($stmt)
            ? "Requirement deleted successfully!"
            : "Failed to delete requirement.";

        mysqli_stmt_close($stmt);
    }
}

$edit_requirement = null;

if (isset($_GET["edit"])) {
    $id = (int)$_GET["edit"];

    $stmt = mysqli_prepare(
        $conn,
        "SELECT * FROM requirements WHERE requirement_id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $edit_requirement = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    mysqli_stmt_close($stmt);
}

$scholarships = mysqli_query(
    $conn,
    "SELECT scholarship_id, scholarship_name FROM scholarships ORDER BY scholarship_name"
);

$requirements = mysqli_query(
    $conn,
    "SELECT r.*, s.scholarship_name
     FROM requirements r
     INNER JOIN scholarships s ON r.scholarship_id = s.scholarship_id
     ORDER BY r.requirement_id DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Requirements</title>
</head>
<body>

<h1>Manage Requirements</h1>

<a href="admin_dashboard.php">Back to Dashboard</a>

<p><?php echo htmlspecialchars($message); ?></p>

<h2><?php echo $edit_requirement ? "Edit Requirement" : "Add Requirement"; ?></h2>

<form method="POST">

    <input type="hidden" name="action" value="<?php echo $edit_requirement ? "update" : "add"; ?>">

    <?php if ($edit_requirement): ?>
        <input type="hidden" name="requirement_id" value="<?php echo (int)$edit_requirement["requirement_id"]; ?>">
    <?php endif; ?>

    <?php if (!$edit_requirement): ?>

        <label>Scholarship:</label><br>

        <select name="scholarship_id" required>
            <option value="">Select Scholarship</option>

            <?php while ($scholarship = mysqli_fetch_assoc($scholarships)): ?>
                <option value="<?php echo (int)$scholarship["scholarship_id"]; ?>">
                    <?php echo htmlspecialchars($scholarship["scholarship_name"]); ?>
                </option>
            <?php endwhile; ?>

        </select><br><br>

    <?php endif; ?>

    <label>Requirement Name:</label><br>
    <input type="text" name="requirement_name" value="<?php echo htmlspecialchars($edit_requirement["requirement_name"] ?? ""); ?>" required><br><br>

    <label>Description:</label><br>
    <textarea name="description"><?php echo htmlspecialchars($edit_requirement["description"] ?? ""); ?></textarea><br><br>

    <button type="submit">
        <?php echo $edit_requirement ? "Update Requirement" : "Add Requirement"; ?>
    </button>

    <?php if ($edit_requirement): ?>
        <a href="manage_requirements.php">Cancel</a>
    <?php endif; ?>

</form>

<hr>

<h2>Existing Requirements</h2>

<?php if (mysqli_num_rows($requirements) > 0): ?>

    <?php while ($row = mysqli_fetch_assoc($requirements)): ?>

        <h3><?php echo htmlspecialchars($row["requirement_name"]); ?></h3>

        <p>Scholarship: <?php echo htmlspecialchars($row["scholarship_name"]); ?></p>

        <p>Description: <?php echo nl2br(htmlspecialchars($row["description"] ?? "")); ?></p>

        <a href="manage_requirements.php?edit=<?php echo (int)$row["requirement_id"]; ?>">Edit</a>

        <form method="POST" onsubmit="return confirm('Delete this requirement?');">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="requirement_id" value="<?php echo (int)$row["requirement_id"]; ?>">
            <button type="submit">Delete</button>
        </form>

        <hr>

    <?php endwhile; ?>

<?php else: ?>

    <p>No requirements have been added yet.</p>

<?php endif; ?>

</body>
</html>
