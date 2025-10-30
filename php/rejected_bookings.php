<?php
session_start();
require_once '../api/db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../php/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$query = "SELECT first_name, last_name, email, created_at FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch rejected bookings
$sql = "SELECT id, package_name, total_price, addons, preferred_date, preferred_time, special_request, status, created_at 
        FROM bookings 
        WHERE user_id = ? AND status = 'rejected'
        ORDER BY created_at DESC";
$stmt2 = $conn->prepare($sql);
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
$rejected_bookings = $result2->fetch_all(MYSQLI_ASSOC);
$stmt2->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rejected Bookings | Montra Studio</title>
  <link rel="stylesheet" href="../css/profile.css">
  <link rel="stylesheet" href="../css/footer.css">
  <link rel="stylesheet" href="../css/rejected.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  
</head>
<body>

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
                        <a href="user_chat.php" class="dropdown-item">Contact Admin</a>
    <hr>
    <a href="logout.php" class="dropdown-item logout">Logout</a>
  </div>
</div>
<?php else: ?>
<a href="../php/login.php" class="btn outline">Login</a>
<?php endif; ?>
  </div>
</header>
<main>
  <div class="profile-container">
    <div class="profile-card">
      <h2>Your Rejected Bookings</h2>

      <?php if (empty($rejected_bookings)): ?>
        <p class="text-muted">You currently have no rejected bookings.</p>
      <?php else: ?>
        <table class="bookings">
          <thead>
            <tr>
              <th>Package</th>
              <th>Total Price (₱)</th>
              <th>Add-ons</th>
              <th>Preferred Date</th>
              <th>Preferred Time</th>
              <th>Special Request</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rejected_bookings as $b): ?>
              <tr>
                <td><?= htmlspecialchars($b['package_name']) ?></td>
                <td>₱<?= number_format($b['total_price'], 2) ?></td>
                <td><?= nl2br(htmlspecialchars($b['addons'] ?: '—')) ?></td>
                <td><?= htmlspecialchars($b['preferred_date']) ?></td>
                <td><?= htmlspecialchars($b['preferred_time']) ?></td>
                <td><?= nl2br(htmlspecialchars($b['special_request'] ?: '—')) ?></td>
                <td><span class="badge rejected">Rejected</span></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</main>

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
