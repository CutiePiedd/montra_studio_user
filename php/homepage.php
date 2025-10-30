<?php 
session_start();
require_once '../api/db_connect.php';

// Check if user is logged in before assigning
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// If logged in, fetch user details
if ($user_id) {
    $stmt = $conn->prepare("SELECT first_name, last_name, email, created_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
} else {
    $user = null; // Ensures profile dropdown doesn't break
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Montra Studio</title>
  <link rel="stylesheet" href="../css/homepage.css">
  <link rel="stylesheet" href="../css/footer.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>

<body>
  <!-- HEADER -->
 <header class="header">
  <div class="logo">
    <a href="../html/homepage.html"><img src="../images/LOGO.png" alt="Montra Studio Logo" style="height:100px; width:300px;"></a>
  </div>

  <div class="header-right">
    <nav class="nav">
      <a href="../php/homepage.php">Home</a>
      <a href="../php/services.php">Services</a>
      <a href="../php/aboutus.php">About us</a>
    </nav>
 <?php if (isset($_SESSION['user_id'])): ?>
<!-- Notification Bell (Only for logged-in users) -->
<div class="notification-icon">
  <img src="https://cdn-icons-png.flaticon.com/512/1827/1827392.png" 
       alt="Notifications" id="notifBell">
  <span id="notifCount" class="notif-count"></span>

  <!-- Added: Default message inside dropdown so it's visible -->
  <div id="notifDropdown" class="notif-dropdown">
    <p style="text-align:center; color:#888;">Loading notifications...</p>
  </div>
</div>
<?php endif; ?>
   <?php if ($user): ?>
<div class="profile-icon dropdown">
  <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="Profile" class="dropdown-toggle" id="profileToggle">
  <div class="dropdown-menu" id="profileMenu">
    <div class="dropdown-user">
      <p><strong><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></strong></p>
      <p><?= htmlspecialchars($user['email']) ?></p>
      <p class="member-since">Member since <?= (new DateTime($user['created_at']))->format('F Y') ?></p>
    </div>
    <hr>
    <a href="pending_bookings.php" class="dropdown-item">Pending Bookings</a>
                        <a href="approved_bookings.php" class="dropdown-item">Approved Bookings</a>
                        <a href="rejected_bookings.php" class="dropdown-item">Rejected Bookings</a>
                        <a href="user_album.php" class="dropdown-item">My Album</a>
                        <a href="user_chat.php" class="dropdown-item">Contact Admin</a><hr>
    <a href="logout.php" class="dropdown-item logout">Logout</a>
  </div>
</div>
<?php else: ?>
<a href="../php/login.php" class="btn outline">Login</a>
<?php endif; ?>
  </div>
</header>
  <!-- HERO -->
  <section class="hero">
    <div class="hero-text">
      <img src="../images/LOGO2.png" alt="Hero model" style="height: 200px; width: 500px;">
      <p>“Welcome to Momentra — a studio dedicated to the art of preserving life’s fleeting beauty. <br>
      Every photograph is more than an image; it’s a memory, a story, a piece of time held still. <br>
      Through light, detail, and vision, we transform ordinary moments into timeless works of art, <br>
      so your memories live on forever.”</p>
      <div class="hero-buttons">
        <a href="services.php" class="btn">Book Now</a>
        <a href="aboutus.php" class="btn outline">Learn More</a>
      </div>
    </div>
    <div class="hero-img">
      <img src="../images/home.png" alt="Hero model" style="height: 400px; width: 500px;">
    </div>
  </section>

  <!-- FEATURES -->
  <section class="features">
    <br><br>
    <h2>You are in the perfect place if you want…</h2>
    <div class="features-grid">
      <div class="feature">
        <h3>01</h3>
        <p>Creative photos that tell your story</p>
      </div>
      <div class="feature">
        <h3>02</h3>
        <p>High-quality photography every time</p>
      </div>
      <div class="feature">
        <h3>03</h3>
        <p>Relaxed sessions, natural results</p>
      </div>
    </div>
  </section>

  <!-- PACKAGES -->
  <section class="packages">
    <br><br>
    <p class="packages-intro">
      Our studio is fully customizable — whether you’re looking for minimal elegance,
      bold colors, or something completely unique, we’ll shape the space to match your vision.
    </p><br><br><br><br><br>
    <div class="packages-grid">
      <div class="package-card">
        <img src="../images/image 13.png" alt="">
        <h3>Main Character Package</h3>
        <a href="../php/maincharacter.php" class="btn outline">Learn More</a>
      </div>
      <div class="package-card">
        <img src="../images/image 14.png" alt="">
        <h3>Better Together</h3>
        <a href="../php/couple.php" class="btn outline">Learn More</a>
      </div>
      <div class="package-card">
        <img src="../images/image 15.png" alt="">
        <h3>All in One Frame Package</h3>
        <a href="../php/family.php" class="btn outline">Learn More</a>
      </div>
      <div class="package-card">
        <img src="../images/image.png" alt="">
        <h3>Squad Goals Package</h3>
        <a href="../php/squad.php" class="btn outline">Learn More</a>
      </div>
    </div>
  </section>

  <!-- PROCESS -->
  <section class="process">
    <h2>Simple Booking Process</h2>
    <p class="process-subtitle">Get started in just a few steps.</p>
    <div class="process-steps">
      <div class="step"><div class="circle">1</div><h3>Create account</h3><p>Sign up with your email and create your profile.</p></div>
      <div class="step"><div class="circle">2</div><h3>Choose Date & Time</h3><p>Select your preferred session slot from available times.</p></div>
      <div class="step"><div class="circle">3</div><h3>Confirm Booking</h3><p>Review details and confirm your photoshoot session.</p></div>
      <div class="step"><div class="circle">4</div><h3>Enjoy your session</h3><p>Arrive at the studio and capture amazing memories.</p></div>
    </div>
  </section>

  <!-- INCLUDED -->
  <section class="included">
    <div class="included-content">
      <div class="included-image">
        <img src="../images/image 16.png" alt="Phone showing session">
      </div>
      <div class="included-text">
        <h2>WHAT’S INCLUDED</h2>
        <ul>
          <li>A customized studio hour session with professional lighting setups</li>
          <li>Multiple backdrop options to match your vibe</li>
          <li>Guidance on poses to bring out your best angles</li>
          <li>10 professionally retouched digital photos</li>
          <li>Access to all raw shots (optional add-on)</li>
        </ul>
      </div>
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
             <li><a href="homepage.php">Home</a></li>
            <li><a href="pending_bookings.php">Bookings</a></li>
            <li><a href="user_album.php">Gallery</a></li>
            <li><a href="aboutus.php">About Us</a></li>
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
const profileToggle = document.getElementById('profileToggle');
const profileMenu = document.getElementById('profileMenu');
profileToggle.addEventListener('click', () => {
  profileMenu.style.display = profileMenu.style.display === 'block' ? 'none' : 'block';
});
window.addEventListener('click', (event) => {
  if (!profileToggle.contains(event.target) && !profileMenu.contains(event.target)) {
    profileMenu.style.display = 'none';
  }
});
</script>
  <?php if (isset($_SESSION['user_id'])): ?>
  <!-- Notifications Script -->
  <script>
document.addEventListener("DOMContentLoaded", function () {
  const bell = document.getElementById('notifBell');
  const dropdown = document.getElementById('notifDropdown');
  const notifCount = document.getElementById('notifCount');

  if (!bell || !dropdown) return;

  function fetchNotifications() {
    fetch('../php/fetch_notifications.php')
      .then(res => res.json())
      .then(data => {
        dropdown.innerHTML = ''; // clear old

        if (data.length > 0) {
          notifCount.textContent = data.length;
          notifCount.style.display = 'inline-block';

          data.forEach(item => {
            const p = document.createElement('p');
            p.textContent = item.message;
            dropdown.appendChild(p);
          });
        } else {
          notifCount.style.display = 'none';
          dropdown.innerHTML = '<p style="color:#777;text-align:center;">No new notifications</p>';
        }
      })
      .catch(err => {
        console.error('Notification fetch error:', err);
        dropdown.innerHTML = '<p style="color:red;text-align:center;">Error loading notifications</p>';
      });
  }

  fetchNotifications();
  setInterval(fetchNotifications, 5000);

  bell.addEventListener('click', () => {
    dropdown.classList.toggle('active');
  });
});

</script>

  <?php endif; ?>
</body>
</html>
