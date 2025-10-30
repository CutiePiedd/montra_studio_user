<?php 
session_start();
require_once '../api/db_connect.php';

// Check if user is logged in
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
if ($user_id) {
    $stmt = $conn->prepare("SELECT first_name, last_name, email, created_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
} else {
    $user = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Montra Studio</title>
    <link rel="stylesheet" href="../css/homepage.css"> <link rel="stylesheet" href="../css/page.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>
<body>

    <header class="header">
        <div class="logo">
            <a href="../php/homepage.php"><img src="../images/LOGO.png" alt="Montra Studio Logo" style="height:100px; width:300px;"></a>
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

    <main> <br/><br/><br/><br/><br/><br/><br/>
        <div class="page-container">
            <h1>Contact Us</h1>
            <p>Have a question or a special request? Reach out to us directly. We're here to help!</p>
            <h2>Our Information</h2>
            <p><strong>Phone:</strong> +0908126802823</p>
            <p><strong>Email:</strong> MontraTeam@gmail.com</p>
            <p><strong>Address:</strong> 075-B Tapuac, Dagupan, Pangasinan</p>
        </div>
    </main>

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
                        <li><a href="faqs.php">FAQs</a></li>
                        <li><a href="contact_us.php">Contact Us</a></li>
                        <li><a href="privacy_policy.php">Privacy Policy</a></li>
                        <li><a href="terms_of_service.php">Terms of Service</a></li>
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
    // Check if the elements exist before adding event listeners
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