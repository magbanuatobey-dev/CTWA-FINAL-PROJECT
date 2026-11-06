
<!DOCTYPE html>
<html>
<head>
    <title>Scholarship Application Management System</title>
</head>
<body>

    <h1>Scholarship Application Management System</h1>

    <p>Welcome to the Scholarship Application Management System.</p>

    <p>Apply for available scholarships and manage your applications.</p>

    <a href="login.php">Login</a>
    <a href="register.php">Register</a>

    <hr>

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
        <p>Email: @email</p>
        <p>Office Hours: Monday - Friday</p>
        <button onclick="document.getElementById('contact').style.display='none'">
            Close
        </button>
    </div>

</body>
</html>
