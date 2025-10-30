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
    <title>Terms of Service | Montra Studio</title>
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
<br/><br/><br/> <br/><br/><br/>
    <main>
        <div class="page-container">
            <h1>Terms of Service</h1>
            <p><em>Last updated: October 29, 2025</em></p>

            <p>Please read these Terms of Service ("Terms", "Terms of Service") carefully before using the Montra Studio website (the "Service") operated by Montra Studio ("us", "we", or "our").</p>
            <p>Your access to and use of the Service is conditioned on your acceptance of and compliance with these Terms. These Terms apply to all visitors, users, and others who access or use the Service.</p>

            <h2>1. Accounts</h2>
            <p>When you create an account with us, you must provide information that is accurate, complete, and current at all times. Failure to do so constitutes a breach of the Terms, which may result in immediate termination of your account on our Service.</p>
            <p>You are responsible for safeguarding the password that you use to access the Service and for any activities or actions under your password.</p>

            <h2>2. Bookings and Payments</h2>
            <p>By booking a session, you agree to pay the fee indicated for the selected service. Payments are processed through our third-party payment processor.</p>
            <p>Cancellations or rescheduling must be made at least 48 hours in advance of your appointment time to be eligible for a refund or to apply the payment to a new date.</p>

            <h2>3. Intellectual Property</h2>
            <p>The Service and its original content, features, and functionality are and will remain the exclusive property of Montra Studio. Our photos are provided to you for personal use. Commercial use of the photos without explicit written consent is prohibited.</p>

            <h2>4. Termination</h2>
            <p>We may terminate or suspend your account immediately, without prior notice or liability, for any reason whatsoever, including without limitation if you breach the Terms.</p>

            <h2>5. Limitation of Liability</h2>
            <p>In no event shall Montra Studio, nor its directors, employees, partners, agents, suppliers, or affiliates, be liable for any indirect, incidental, special, consequential or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses, resulting from your access to or use of or inability to access or use the Service.</p>

            <h2>6. Governing Law</h2>
            <p>These Terms shall be governed and construed in accordance with the laws of the Philippines, without regard to its conflict of law provisions.</p>
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