<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
</head>
<body>

    <h1>Student Dashboard</h1>

    <h2>
        Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!
    </h2>

    <p>Welcome to the Scholarship Application Management System.</p>

    <hr>

    <h3>Student Menu</h3>

    <ul>
        <li><a href="profile.php">My Profile</a></li>
        <li><a href="scholarships.php">Available Scholarships</a></li>
        <li><a href="my_applications.php">My Applications</a></li>
        <li><a href="upload_documents.php">Upload Documents</a></li>
        <li><a href="my_documents.php">My Documents</a></li>
    </ul>

    <hr>

    <h3>More Information</h3>

    <button onclick="document.getElementById('about').style.display='block'">
        About Us
    </button>

    <button onclick="document.getElementById('services').style.display='block'">
        Our Services
    </button>

    <button onclick="document.getElementById('contact').style.display='block'">
        Contact Us
    </button>

    <hr>

    <div id="about" style="display:none;">
        <h2>About Us</h2>
        <p>
            The Scholarship Application Management System helps students
            find and apply for scholarship opportunities.
        </p>
        <button onclick="document.getElementById('about').style.display='none'">
            Close
        </button>
    </div>

    <div id="services" style="display:none;">
        <h2>Our Services</h2>
        <ul>
            <li>View Available Scholarships</li>
            <li>Submit Scholarship Applications</li>
            <li>Upload Required Documents</li>
            <li>Track Application Status</li>
            <li>Manage Student Profiles</li>
        </ul>
        <button onclick="document.getElementById('services').style.display='none'">
            Close
        </button>
    </div>

    <div id="contact" style="display:none;">
        <h2>Contact Us</h2>
        <p>Scholarship Administration Office</p>
        <p>Email: Add your official email here</p>
        <p>Office Hours: Monday - Friday</p>
        <button onclick="document.getElementById('contact').style.display='none'">
            Close
        </button>
    </div>

    <hr>

    <a href="logout.php">Logout</a>

</body>
</html>
