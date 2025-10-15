<?php
session_start();
require_once '../api/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            header("Location: ../html/services.html");
            exit;
        } else {
            echo "<script>alert('Invalid password!');</script>";
        }
    } else {
        echo "<script>alert('No user found with that email!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Montra Studio</title>
  <link rel="stylesheet" href="../css/login.css">
   <link rel="stylesheet" href="..\css\footer.css">
</head>
<body>
  <!-- HEADER -->
  <header class="header">
    <div class="logo"> 
      <img src="../images/LOGO.png" alt="Montra Studio Logo" style="height: 100px; width: 300px;"> 
    </div>

    <div class="header-right">
      <nav class="nav">
        <a href="../html/homepage.html">Home</a>
        <a href="../html/services.html">Services</a>
        <a href="../html/aboutus.html">About us</a>
      </nav>

      <div class="profile-icon">
        <a href="../html/profile.html">
          <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="Profile">
        </a>
      </div>
    </div>
  </header>

  <!-- LOGIN CARD -->
  <div class="login-section">
    <div class="login-card">
      <h2>Login</h2>
      <p>Don’t Have an Account? <a href="register.php">Sign up Here</a></p>

      <form method="POST" action="">
        <div class="form-group">
          <input type="email" name="email" placeholder="Email" required>
        </div>

        <div class="form-group password-field">
          <input type="password" id="password" name="password" placeholder="Password" required>
          <span class="toggle-password" onclick="togglePassword()">&#128065;</span>
        </div>

        <button type="submit" class="btn-login">Sign In</button>
        <a href="#" class="forgot-password">Forgot Password?</a>
      </form>
    </div>
  </div>

  <script>
    function togglePassword() {
      const input = document.getElementById("password");
      input.type = input.type === "password" ? "text" : "password";
    }
  </script>

  <!-- FOOTER -->
  <footer class="footer">
    <h2>Get In Touch</h2>
    <p class="footer-tagline">Capturing moments. Creating stories. Celebrating you.</p>
    <div class="footer-grid">
      <div class="footer-logo">
        <img src="../images/1 4.png" alt="Mantra Studio Logo">
      </div>
      <div class="footer-links">
        <div>
          <h4>Quick Links</h4>
          <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#">Bookings</a></li>
            <li><a href="#">Gallery</a></li>
            <li><a href="#">About Us</a></li>
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
</body>
</html>
