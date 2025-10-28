<?php
// --- Start: Logic from your advanced header (family.php) ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../api/db_connect.php';

// Check if user is logged in before assigning
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// If logged in, fetch user details
if ($user_id) {
    // Cast user_id to int to be safe
    $user_id_int = (int)$user_id; 
    $stmt = $conn->prepare("SELECT first_name, last_name, email, created_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id_int);
    $stmt->execute();
    $result_user = $stmt->get_result(); // Renamed to avoid conflict
    $user = $result_user->fetch_assoc();
    $stmt->close();
} else {
    $user = null; // Ensures profile dropdown doesn't break
}
// --- End: Logic from your advanced header ---


// --- Start: Original Logic from maincharacter.php ---
$isLoggedIn = isset($_SESSION['user_id']); // This is still useful

$id = 1; // Main Character package
$package_result = mysqli_query($conn, "SELECT * FROM packages WHERE id=$id"); // Renamed to avoid conflict
$package = mysqli_fetch_assoc($package_result);

$includes = explode(',', $package['includes']);
$images = explode(',', $package['images']);
// --- End: Original Logic from maincharacter.php ---
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Our Services | Mantra Studio</title>
  
  <link rel="stylesheet" href="..\css\maincharacter.css" />
  <link rel="stylesheet" href="..\css\footer.css">
  
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  
  <style>
      body { font-family: 'Inter', sans-serif; background:#f7f7f7; margin:0; }

      /* === START: Our Layout Fixes === */
      .header-right {
        display: flex;
        align-items: center;
      }
      .profile-icon { 
        position: relative; 
        display: inline-block; 
        margin-right: 77px; /* This separates it from the bell */
      }
      /* === END: Our Layout Fixes === */
      
      /* Dropdown profile styles */
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
        position: fixed;
        z-index: 10;
        /* Added for consistency */
        width: 100%;
        background: #fff; 
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      }
      .notification-icon {
        position: relative;
        z-index: 10000;
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
        z-index: 9999;
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
      .breadcrumb a {
        color: white;         
        text-decoration: none;  
      }
      .breadcrumb span {
        color: white;
      }
      .breadcrumb {
        color: white;
      }
      /* Style for the login button */
      .btn.outline {
        padding: 8px 16px;
        border: 1px solid #ddd;
        border-radius: 8px;
        text-decoration: none;
        color: #333;
        font-weight: 500;
        transition: all 0.2s ease;
      }
      .btn.outline:hover {
        background: #f5f5f5;
        border-color: #ccc;
      }
  </style>
  </head>

<body>

  <header class="header">
    <div class="logo"> <a href="../php/homepage.php"><img src="../images/LOGO.png" alt="Montra Studio Logo" style="height: 100px; width: 300px;">
      </a>
    </div>

    <div class="header-right">
      <nav class="nav">
        <a href="../php/homepage.php">Home</a>
        <a href="../php/services.php">Services</a>
        <a href="../php/aboutus.php">About us</a>
      </nav>
      
    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="notification-icon">
      <img src="https://cdn-icons-png.flaticon.com/512/1827/1827392.png" 
           alt="Notifications" id="notifBell">
      <span id="notifCount" class="notif-count"></span>
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
      <a href="../php/login.php" class="btn outline" style="margin-right: 55px;">Login</a>
    <?php endif; ?>
    </div>
  </header>
  <br/><br/><br/><br/><br/><br/>
  <section class="hero-section">
    <h1>Our Services</h1>
    <p class="breadcrumb">
  <a href="homepage.php">Home</a> / 
  <a href="services.php">Services</a> / 
  <span>Packages</span>
</p>
  </section>

  <section class="service-package">
    <div class="package-card">
      <div class="package-img">
       <img src="../uploads/<?= htmlspecialchars($package['main_image']) ?>" alt="Main Character Package">

      </div>

      <div class="package-content">
       <h2><?= htmlspecialchars($package['name']) ?></h2>
<p><?= nl2br(htmlspecialchars($package['description'])) ?></p>

        <br/> <br/><br/><br/><br/><br/> <br/>

       <div class="price-box">
  <h3>₱<?= number_format($package['price'], 2) ?></h3>
  <?php if ($isLoggedIn): ?>
    <a class="service-card" href="../php/booking.php?package=maincharacter&price=<?= $package['price'] ?>">
      <button class="btn-book">Book Now</button>
    </a>
  <?php else: ?>
    <a href="login.php"><button class="btn-book">Log in to Book</button></a>
  <?php endif; ?>
</div>



      </div>
    </div>

    <div class="carousel">
  <?php foreach ($images as $img): ?>
    <img src="../uploads/<?= htmlspecialchars(trim($img)) ?>" alt="Package Image">
  <?php endforeach; ?>
</div>


    </section>
  <br/><br/><br/><br/>
  <div class="package-details">
      <h3>Main Character Package Includes:</h3>
      <ul>
  <?php foreach ($includes as $item): ?>
    <li><?= htmlspecialchars(trim($item)) ?></li>
  <?php endforeach; ?>
</ul>

    </div>

  <footer class="footer">
    <h2>Get In Touch</h2>
    <p class="footer-tagline">Capturing moments. Creating stories. Celebrating you.</p>
    <div class="footer-grid">
      <div class="footer-logo">
        <img src="..\images\1 4.png" alt="Mantra Studio Logo">
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
    <p class="footer-copy">© 2025 MOntraStudio. All rights reserved.</p>
  </footer>

  <script>
  const carousel = document.querySelector('.carousel');
  const next = document.querySelector('.next');
  const prev = document.querySelector('.prev');

  if (carousel) { // Added a check to prevent errors
    // Duplicate images to simulate infinite loop
    const images = Array.from(carousel.children);
    images.forEach(img => {
      const clone = img.cloneNode(true);
      carousel.appendChild(clone);
    });

    const scrollAmount = 300;
    let scrollPos = 0;

    if (next && prev) { // Check if buttons exist
      next.addEventListener('click', () => {
        scrollPos += scrollAmount;
        if (scrollPos >= carousel.scrollWidth / 2) {
          scrollPos = 0; // reset to start
        }
        carousel.scrollTo({ left: scrollPos, behavior: 'smooth' });
      });

      prev.addEventListener('click', () => {
        scrollPos -= scrollAmount;
        if (scrollPos < 0) {
          scrollPos = carousel.scrollWidth / 2 - scrollAmount;
        }
        carousel.scrollTo({ left: scrollPos, behavior: 'smooth' });
      });
    }
  }
  </script>

  <script>
  const profileToggle = document.getElementById('profileToggle');
  const profileMenu = document.getElementById('profileMenu');
  
  if (profileToggle && profileMenu) {
      profileToggle.addEventListener('click', () => {
        profileMenu.style.display = profileMenu.style.display === 'block' ? 'none' : 'block';
      });
      
      window.addEventListener('click', (event) => {
        if (!profileToggle.contains(event.target) && !profileMenu.contains(event.target)) {
          profileMenu.style.display = 'none';
        }
      });
  }
  </script>
  <?php if (isset($_SESSION['user_id'])): ?>
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