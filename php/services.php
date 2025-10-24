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
  <title>Our Services | Montra Studio</title>
  <link rel="stylesheet" href="../css/services.css">
   <link rel="stylesheet" href="../css/footer.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
  <style>

      body { font-family: 'Inter', sans-serif; background:#f7f7f7; margin:0; }

    .booking-section { margin-top: 40px; }
    table.bookings { width: 100%; border-collapse: collapse; margin-top: 20px; background:#fff; border-radius:10px; overflow:hidden; }
    table.bookings th, table.bookings td { padding: 12px 16px; border-bottom: 1px solid #eee; text-align: left; }
    table.bookings th { background:#111; color:#fff; font-weight:600; }
    table.bookings tr:last-child td { border-bottom: none; }
    .badge { padding:6px 10px; border-radius:8px; color:#fff; font-weight:600; font-size:0.9em; }
    .badge.approved { background:#28a745; }
    .profile-container { display:flex; justify-content:center; padding:40px 20px; }
    .profile-card { max-width:1100px; width:100%; background:#fff; border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,0.08); padding:30px; }
    h2 { margin-bottom:10px; }
    .text-muted { color:#777; font-size:14px; }

    /* Dropdown profile styles (reuse from your current UI) */
    .profile-icon { position: relative; display: inline-block; }
    .profile-icon img { width: 30px; height: 30px; border-radius: 50%; border: 2px solid #ddd; cursor: pointer; transition: all 0.3s ease; }
    .profile-icon img:hover { border-color: #aaa; transform: scale(1.05); }
    .dropdown-menu { display: none; position: absolute; top: 55px; right: 0; background-color: #fff; border-radius: 10px; box-shadow: 0 8px 25px rgba(0,0,0,0.08); width: 230px; overflow: hidden; z-index: 100; animation: dropdownFade 0.25s ease-in-out; }
    .dropdown-user { background: #f8f8f8; padding: 15px; text-align: left; }
    .dropdown-user p { margin: 3px 0; color: #333; font-size: 14px; }
    .dropdown-user strong { font-weight: 600; color: #222; }
    .member-since { font-size: 12px; color: #777; margin-top: 5px; }
    .dropdown-item { display: block; padding: 12px 16px; text-decoration: none; color: #333; font-size: 14px; transition: background-color 0.2s ease; }
    .dropdown-item:hover { background-color: #f2f2f2; }
    .logout { color: #c0392b; font-weight: 500; }
    .dropdown-menu hr { margin: 8px 0; border: none; border-top: 1px solid #e0e0e0; }
    @keyframes dropdownFade { from { opacity: 0; transform: translateY(-8px);} to { opacity: 1; transform: translateY(0);} }

      .header {
  position: relative;
  z-index: 10;
}
.notification-icon {
  position: relative;
  z-index: 10000; /* make sure dropdown stays on top */
}

    /* Notification Bell Styling */
    .notification-icon {
  position: relative;
  margin-right: 15px;
  cursor: pointer;
  display: inline-block;
}

.notification-icon img {
  width: 30px;
  height: 30px;
  vertical-align: middle;
}

.notif-count {
  position: absolute;
  top: -6px;
  right: -6px;
  background: red;
  color: white;
  font-size: 11px;
  font-weight: bold;
  border-radius: 50%;
  padding: 3px 6px;
  display: none;
}

.notif-dropdown {
  position: absolute;
  right: 0;
  top: 40px;
  width: 250px;
  background: white;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
  z-index: 9999; /* Make sure it’s above everything */
  display: none;
}

.notif-dropdown.active {
  display: block !important;
}

.notif-dropdown p {
  padding: 10px;
  border-bottom: 1px solid #eee;
  font-size: 14px;
  color: #333;
  margin: 0;
  background: white;
}

.notif-dropdown p:hover {
  background: #f5f5f5;
}
  </style>
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
    <a href="completed_bookings.php" class="dropdown-item">Completed Bookings</a>
    <a href="rejected_bookings.php" class="dropdown-item">Rejected Bookings</a>
    <hr>
    <a href="logout.php" class="dropdown-item logout">Logout</a>
  </div>
</div>
<?php else: ?>
<a href="../php/login.php" class="btn outline">  <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="Profile" width="30" height="30"></a>
<?php endif; ?>
  </div>
</header>

  <!-- HERO SECTION -->

<section class="hero">
  <div class="hero-overlay"></div>
  <div class="hero-content">
    <p class="breadcrumb">Home / Services</p>
    <h1>Our Services</h1>
    <p class="subtitle">
      At Mantra Studio, we focus on giving you a photobooth experience that’s fun, stylish, and memorable.
    </p>
  </div>
</section> <br><br> <br><br><br>


  <!-- WHAT WE OFFER -->
<section class="offer-section">
  <div class="offer-overlay"></div>
  <div class="offer-content">
    <h2>What We Offer</h2>
    <p>Capture, print, and share your best moments instantly.</p>
  </div>
</section><br><br><br>


  <!-- SERVICE CARDS -->
<section class="services-grid">
  <a class="service-card" href="../php/maincharacter.php">
    <img src="..\images\Group 134.png" alt="Main Character">
    <div class="overlay"></div>
    <div class="text">
      <h3>Main Character</h3>
      <p>
        The Main Character Package is all about you. Enjoy a personalized studio session designed to highlight your personality and style, with professional lighting, customizable backdrops, and creative guidance to capture your best angles.
      </p>
      <span class="see-more">see more</span> <!-- changed to non-anchor -->
    </div>
  </a>

  <a class="service-card" href="../php/squad.php">
    <img src="..\images\Squadgoals.png" alt="Squad Goals">
    <div class="overlay"></div>
    <div class="text">
      <h3>Squad Goals</h3>
      <p>Celebrate the power of togetherness with our Squad Goals package. Perfect for friends, families, or teams.</p>
      <span class="see-more">see more</span>
    </div>
  </a>

  <a class="service-card" href="../php/couple.php">
    <img src="..\images\BetterTogether.jpg" alt="Better Together">
    <div class="overlay"></div>
    <div class="text">
      <h3>Better Together</h3>
      <p>Celebrate love, connection, and chemistry with our Better Together package...</p>
      <span class="see-more">see more</span>
    </div>
  </a>

 <a class="service-card" href="../php/family.php">

    <img src="..\images\ALLinOneFrame.png" alt="All In One Frame">
    <div class="overlay"></div>
    <div class="text">
      <h3>All In One Frame</h3>
      <p>Nothing beats the beauty of family moments. Our All In One Frame package is designed to capture the love...</p>
      <span class="see-more">see more</span>
    </div>
  </a>
</section>



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
  <p class="footer-copy">© 2025 MantraStudio. All rights reserved.</p>

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
