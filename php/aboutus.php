<?php 
session_start();
require_once '../api/db_connect.php';

// Check if user is logged in before assigning
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Cast to int to be safe, prevents potential type issues
$user_id_int = (int)$user_id;

// If logged in, fetch user details
if ($user_id) {
    $stmt = $conn->prepare("SELECT first_name, last_name, email, created_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id_int); // Use the integer ID
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
  <title>About Us | Montra Studio</title>
  <link rel="stylesheet" href="../css/aboutus.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <style>

      body { font-family: 'Inter', sans-serif; background:#f7f7f7; margin:0; }

      /* === START: Header Layout Fixes === */
      .header-right {
        display: flex;
        align-items: center;
      }
      .profile-icon { 
        position: relative; 
        display: inline-block; 
        margin-left: 15px; /* Adds space between bell and icon */
      }
      /* === END: Header Layout Fixes === */

      /* Dropdown profile styles (reuse from your current UI) */
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
        /* Note: Your aboutus.css probably makes this fixed. 
           If not, you may need to add 'position: fixed' */
      }
      .notification-icon {
        position: relative;
        z-index: 10000;
      }

 .notification-icon {
  position: relative; /* Crucial for positioning the badge and dropdown */
  margin: 0 15px;
  margin-top: -10px; /* Gives it space from the profile icon */
  display: flex;
  align-items: center;

}
 .notification-icon img{
  filter: invert();
 }
/* 2. The bell icon itself */
#notifBell {
  width: 24px; 
  height: 24px;
  cursor: pointer;
  transition: transform 0.2s ease;
}

#notifBell:hover {
  transform: scale(1.1);
}

/* 3. The red notification count badge */
.notif-count {
  position: absolute;
  top: -5px;      /* Position overlapping the top right */
  right: -8px;    /* Position overlapping the top right */
  
  background-color: #e74c3c; /* A strong red */
  color: white;
  font-size: 11px;
  font-weight: bold;
  line-height: 1;
  border-radius: 50%; /* Makes it a circle */
  
  min-width: 18px;   /* Ensures it's circular even with 1 digit */
  height: 18px;
  
  /* Use flex to center the number perfectly */
  display: flex; 
  align-items: center;
  justify-content: center;
  
  padding: 2px;
  box-sizing: border-box; /* Ensures padding doesn't break size */
  border: 2px solid white; /* Makes it "pop" from the bell */
  
  /* This is controlled by your JS */
  display: none; 
}

/* 4. The dropdown container */
.notif-dropdown {
  display: none; /* Hidden by default */
  position: absolute;
  top: 160%;     /* Position below the icon (100% + margin) */
  right: -10px;  /* Align to the right side */
  
  width: 330px; /* A good fixed width */
  background: #ffffff;
  border-radius: 8px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
  border: 1px solid #eee;
  z-index: 1001; /* Above other content */

  /* ✅ THIS IS THE SCROLLABLE PART */
  max-height: 400px;
  overflow-y: auto;
  
  /* Add a little fade-in animation */
  opacity: 0;
  transform: translateY(-10px);
  transition: opacity 0.2s ease, transform 0.2s ease;
}

/* 5. The active state (toggled by JS) */
.notif-dropdown.active {
  display: block;
  opacity: 1;
  transform: translateY(0);
}

/* 6. Individual notification items (the <p> tags) */
.notif-dropdown p {
  margin: 0;
  padding: 12px 15px;
  font-size: 14px;
  color: #333;
  border-bottom: 1px solid #f0f0f0;
  white-space: normal; /* Allow text to wrap */
}

/* 7. Hover effect for items */
.notif-dropdown p:hover {
  background-color: #f9f9f9;
}

/* 8. Don't put a border on the last item */
.notif-dropdown p:last-child {
  border-bottom: none;
  border-radius: 0 0 8px 8px; /* Match container rounding */
}

/* 9. Styling for the "No notifications" or "Loading" text */
.notif-dropdown p[style*="text-align:center"] {
  font-style: italic;
  color: #888;
  padding: 20px 15px;
}
.notif-dropdown p[style*="text-align:center"]:hover {
  background-color: #fff; /* Don't highlight this one */
}

/* 10. Optional: Custom Scrollbar */
.notif-dropdown::-webkit-scrollbar {
  width: 6px;
}
.notif-dropdown::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 0 8px 8px 0;
}
.notif-dropdown::-webkit-scrollbar-thumb {
  background: #ccc;
  border-radius: 6px;
}
.notif-dropdown::-webkit-scrollbar-thumb:hover {
  background: #aaa;
}
      /* Login button style (for consistency) */
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
  <section class="hero-section">
    <div class="overlay">
    <header class="header">
      <div class="logo"> <a href="../php/homepage.php"><img src="../images/logo0.png" alt="Montra Studio Logo" style="height:70px; width:170px;"></a>
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
          <a href="user_chat.php" class="dropdown-item">Contact Admin</a><hr>
          <a href="logout.php" class="dropdown-item logout">Logout</a>
        </div>
      </div>
      <?php else: ?>
      <a href="../php/login.php" class="btn outline">Login</a>
      <?php endif; ?>
      </div>
    </header>
    </div>
    <div class="hero-content">
      <h1>About Us</h1>
      <p>At Montra Studio, we offer self-service photobooths with easy controls and instant results, customizable backdrops and props to match your vibe, high-quality prints and digital copies on the spot, and a cozy private space where you can relax, pose, and play—perfect whether you’re with friends, celebrating your individuality, or simply looking for something fun and different to do.</p>
      <a href="#" class="btn">See Our Services</a>
    </div>
  </section>

  <section class="what-we-do">
    <h2>WHAT WE DO</h2>
    <p>At Montra Studio, we offer self-service photoshoots with easy controls and instant results that allow you to bring your vision to life. With high-quality lighting, professional-grade equipment, and a comfortable space, we make creating art fun and effortless for everyone.</p>
  </section>

  <section class="mission-vision">
    <div class="mission">
      <img src="../images/image 40.png" alt="Studio interior">
      <div class="text">
        <h3>Our mission</h3>
        <p>At Montra Studio, our mission is to create a space where people can freely express themselves, cultivate creativity, and connect meaningfully through the art of photography. We aim to bring out confidence, individuality, and authenticity in every client.</p>
        <a href="#" class="btn">See Our Services</a>
      </div>
    </div>

    <div class="vision">
      <div class="text">
        <h3>Our Vision</h3>
        <p>Our vision is to become the leading photostudio that redefines how people capture memories—transforming ordinary shoots into extraordinary snapshots, and inspiring a culture where self-expression and creativity are always welcomed.</p>
        <a href="#" class="btn-outline">Contact Us</a>
      </div>
      <img src="../images/image 39.jpg" alt="Studio building">
    </div>
  </section>

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
           <li><a href="homepage.php">Home</a></li>
            <li><a href="pending_bookings.php">Bookings</a></li>
            <li><a href="user_album.php">Gallery</a></li>
            <li><a href="aboutus.php">About Us</a></li>
        </ul>
      </div>
      <div>
        <h4>Support</h4>
        <ul>
           <li><a href="../php/faqs.php">FAQs</a></li>
                        <li><a href="../php/contact_us.php">Contact Us</a></li>
                        <li><a href="../php/privacy_policy.php">Privacy Policy</a></li>
                        <li><a href="../php/terms_of_service.php">Terms of Service</a></li>
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
const profileToggle = document.getElementById('profileToggle');
const profileMenu = document.getElementById('profileMenu');

if (profileToggle && profileMenu) { // Added check
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