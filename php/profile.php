<?php
session_start();
require_once '../api/db_connect.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../php/login.php");
    exit();
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$query = "SELECT first_name, last_name, email, created_at FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed (user): " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}
$stmt->close();

// Fetch user bookings (use correct column names from your table)
$bookings = [];
$sql = "SELECT id, package_name, preferred_date, preferred_time, total_price, addons, status, created_at 
        FROM bookings 
        WHERE user_id = ?
        ORDER BY created_at DESC";
$stmt2 = $conn->prepare($sql);
if (!$stmt2) {
    die("Prepare failed (bookings): " . $conn->error);
}
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$result2 = $stmt2->get_result();

while ($row = $result2->fetch_assoc()) {
    $bookings[] = $row;
}
$stmt2->close();
$conn->close();

// Group bookings by status for display
$grouped = [
    'pending' => [],
    'approved' => [],
    'completed' => [],
    'rejected' => []
];

foreach ($bookings as $b) {
    $status = $b['status'] ?? 'pending';
    if (!isset($grouped[$status])) {
        $status = 'pending';
    }
    $grouped[$status][] = $b;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Profile | Montra Studio</title>
  <link rel="stylesheet" href="../css/profile.css">
  <link rel="stylesheet" href="../css/footer.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    /* small table styles if profile.css doesn't cover them */
    .booking-section { margin-top: 40px; }
    .booking-section h3 { margin-top: 30px; }
    table.bookings { width: 100%; border-collapse: collapse; margin-top: 10px; }
    table.bookings th, table.bookings td { padding: 10px; border: 1px solid #e6e6e6; text-align: left; vertical-align: middle; }
    table.bookings th { background:#111; color:#fff; }
    .badge { padding:6px 10px; border-radius:8px; color:#fff; font-weight:600; font-size:0.9em; }
    .badge.pending { background:#ffc107; color:#222; }
    .badge.approved { background:#28a745; }
    .badge.completed { background:#6c757d; }
    .badge.rejected { background:#dc3545; }
    .profile-container { display:flex; justify-content:center; padding:40px 20px; }
    .profile-card { max-width:1100px; width:100%; background:#fff; border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,0.08); padding:30px; }
    .profile-top { display:flex; gap:20px; align-items:center; }
    .profile-img { width:96px; height:96px; border-radius:50%; object-fit:cover; }
    .info-section { margin-top:20px; }
    .button-container { margin-top:20px; }
    .logout-btn { display:inline-block; padding:10px 18px; background:#2f3e55; color:#fff; border-radius:8px; text-decoration:none; }
  </style>
</head>
<body>

  <!-- Navbar -->
 <header class="header">
   <div class="logo">
    <a href="../html/homepage.html"><img src="../images/LOGO.png" alt="Montra Studio Logo" style="height: 100px; width: 300px;"></a>
  </div>

  <div class="header-right">
    <nav class="nav">
      <a href="../html/homepage.html">Home</a>
      <a href="../html/services.html">Services</a>
      <a href="../html/aboutus.html">About us</a>
    </nav>

    <div class="profile-icon">
      <a href="profile.php">
        <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="Profile">
      </a>
    </div>
  </div>
</header>

  <!-- Profile Section -->
  <div class="profile-container">
    <div class="profile-card">
      <div class="profile-top">
        <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="User Avatar" class="profile-img">
        <div>
          <h2>Welcome, <?= htmlspecialchars($user['first_name']) ?>!</h2>
          <p><?= htmlspecialchars($user['email']) ?></p>
          <div class="info-section">
            <p><strong>Name:</strong> <?= htmlspecialchars($user['first_name'] . " " . $user['last_name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Member Since:</strong> <?= (new DateTime($user['created_at']))->format('F j, Y') ?></p>
          </div>
        </div>
      </div>

      <!-- Booking Status Section -->
      <div class="booking-section">
        <h3>Your Bookings</h3>

        <?php
        $statuses = ['pending' => 'Pending', 'approved' => 'Approved', 'completed' => 'Completed', 'rejected' => 'Rejected'];
        foreach ($statuses as $key => $label):
        ?>
          <h4><?= $label ?> (<?= count($grouped[$key]) ?>)</h4>

          <?php if (empty($grouped[$key])): ?>
            <p class="text-muted">No <?= strtolower($label) ?> bookings.</p>
          <?php else: ?>
            <table class="bookings">
              <thead>
                <tr>
                  <th>Package</th>
                  <th>Date</th>
                  <th>Time</th>
                  <th>Add-ons</th>
                  <th>Total (₱)</th>
                  <th>Status</th>
                  <th>Booked On</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($grouped[$key] as $b): ?>
                  <tr>
                    <td><?= htmlspecialchars($b['package_name']) ?></td>
                    <td><?= htmlspecialchars($b['preferred_date']) ?></td>
                    <td><?= htmlspecialchars($b['preferred_time']) ?></td>
                    <td><?= nl2br(htmlspecialchars($b['addons'] ?: '—')) ?></td>
                    <td>₱<?= number_format($b['total_price'], 2) ?></td>
                    <td><span class="badge <?= htmlspecialchars($b['status']) ?>"><?= ucfirst($b['status']) ?></span></td>
                    <td><?= htmlspecialchars((new DateTime($b['created_at']))->format('M d, Y')) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>

        <?php endforeach; ?>

      </div>

      <div class="button-container">
        <a href="logout.php" class="logout-btn">Logout</a>
      </div>
    </div>
  </div>

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
