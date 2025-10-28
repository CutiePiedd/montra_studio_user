<?php
session_start();
require_once '../api/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $raw_password = $_POST['password']; // Get the raw password

    // --- START: New Password Validation ---
    $errors = [];
    if (strlen($raw_password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }
    if (!preg_match('/[A-Z]/', $raw_password)) {
        $errors[] = "Password must include at least one uppercase letter.";
    }
    if (!preg_match('/[a-z]/', $raw_password)) {
        $errors[] = "Password must include at least one lowercase letter.";
    }
    if (!preg_match('/[0-9]/', $raw_password)) {
        $errors[] = "Password must include at least one number.";
    }
    if (!preg_match('/[^A-Za-z0-9]/', $raw_password)) {
        // This regex checks for any character that is NOT a letter or number
        $errors[] = "Password must include at least one symbol (e.g., !@#$).";
    }

    if (!empty($errors)) {
        // If there are any errors, combine them into one message
        $error_message = "Registration Failed:\\n" . implode("\\n", $errors);
        
        // Send the user back with the error message
        echo "<script>alert('$error_message'); window.history.back();</script>";
        exit;
    }
    // --- END: New Password Validation ---

    // If validation passes, *then* hash the password
    $password = password_hash($raw_password, PASSWORD_DEFAULT);

    // Check if email already exists
    $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already registered! Please login.'); window.location.href='login.php';</script>";
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $first_name, $last_name, $email, $password);

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['user_name'] = $first_name . ' ' . $last_name;
        header("Location: ../php/services.php");
        exit;
    } else {
        echo "<script>alert('Registration failed!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Create Account | Montra Studio</title>
  <link rel="stylesheet" href="../css/signup.css" />
</head>
<body>

  <!-- HEADER -->
  <header class="header">
    <div class="logo"> 
      <img src="../images/LOGO.png" alt="Montra Studio Logo" style="height: 100px; width: 300px;"> 
    </div>

    <div class="header-right">
      <nav class="nav">
        <a href="../php/homepage.php">Home</a>
        <a href="../php/services.php">Services</a>
        <a href="../php/aboutus.php">About us</a>
      </nav>

    
    </div>
  </header>


  <!-- SIGNUP SECTION -->
  <section class="signup-section">
    <div class="signup-box">
      <h2>Create Account</h2>
      <p>Already Have an Account? <a href="login.php">Sign in Here</a></p>

      <form method="POST" action="">
        <div class="form-group">
          <input type="text" name="first_name" placeholder="First Name" required>
        </div>

        <div class="form-group">
          <input type="text" name="last_name" placeholder="Last Name" required>
        </div>

        <div class="form-group">
          <input type="email" name="email" placeholder="Email" required>
        </div>

        <div class="form-group password-field">
          <input type="password" name="password" id="password" placeholder="Password" required
       pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$"
       title="Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one symbol (e.g., !@#$).">
          <span class="toggle-password" onclick="togglePassword()">&#128065;</span>
        </div>

        <button type="submit" class="btn">Create</button>
      </form>
    </div>
  </section>


  <!-- FOOTER -->
  <footer class="footer">
    <h2>Get In Touch</h2>
    <p class="footer-tagline">Capturing moments. Creating stories. Celebrating you.</p>
    <div class="footer-grid">
      <div class="footer-logo">
        <img src="../images/1 4.png" alt="Montra Studio Logo">
      </div>
      <div class="footer-links">
        <div>
          <h4>Quick Links</h4>
          <ul>
            <li><a href="../html/homepage.html">Home</a></li>
            <li><a href="../php/booking.php">Bookings</a></li>
            <li><a href="#">Gallery</a></li>
            <li><a href="../html/aboutus.html">About Us</a></li>
          </ul>
        </div>
        <div>
          <h4>Support</h4>
          <ul>
            <li><a href="#">FAQs</a></li>
            <li><a href="#">Contact Us</a></li>
            <li><a href="#">Privacy Policy</a></li>
            <li><a href="#">Terms of Service</a></li>
          </ul>
        </div>
        <div>
          <h4>Contacts</h4>
          <ul>
            <li>+0908126802823</li>
            <li>075-B Tapuac, Dagupan, Pangasinan</li>
            <li>MontraTeam@gmail.com</li>
          </ul>
        </div>
      </div>
    </div>
    <p class="footer-copy">© 2025 MontraStudio. All rights reserved.</p>
  </footer>

  <script>
    function togglePassword() {
      const input = document.getElementById("password");
      input.type = input.type === "password" ? "text" : "password";
    }
  </script>

</body>
</html>
