<?php
session_start();
require_once '../api/db_connect.php';

// --- User & Session Logic ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT first_name, last_name, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$user_full_name = $user['first_name'] . ' ' . $user['last_name'];


// --- Album Logic ---
$album_query = $conn->prepare("SELECT id FROM albums WHERE user_id = ?");
$album_query->bind_param("i", $user_id);
$album_query->execute();
$album_result = $album_query->get_result();

if ($album_result->num_rows === 0) {
    $album_name = $user_full_name . "'s Album";
    $insert_album = $conn->prepare("INSERT INTO albums (user_id, album_name) VALUES (?, ?)");
    $insert_album->bind_param("is", $user_id, $album_name);
    $insert_album->execute();
    $album_id = $insert_album->insert_id;
} else {
    $album_id = $album_result->fetch_assoc()['id'];
}

// --- NEW: Fetch ALL images at once for filtering ---
$all_images = [];
$images_query = $conn->prepare("
    SELECT image_path, album_type
    FROM album_images
    WHERE album_id = ?
    ORDER BY uploaded_at DESC
");
$images_query->bind_param("i", $album_id);
$images_query->execute();
$images_result = $images_query->get_result();
while ($row = $images_result->fetch_assoc()) {
    $all_images[] = $row;
}

// This list will be used to generate the filter tabs
$albums_to_display = ['maincharacter' => 'Main Character', 'couple' => 'Couple', 'family' => 'Family', 'squad' => 'Squad'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Album - Montra Studio</title>
    
    <link rel="stylesheet" href="../css/services.css"> 
    <link rel="stylesheet" href="../css/footer.css">
        <link rel="stylesheet" href="../css/user-album.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

</head>
<body>

    <header class="header">
        <div class="logo">
            <a href="homepage.php"><img src="../images/LOGO.png" alt="Montra Studio Logo" style="height:100px; width:300px;"></a>
        </div>
        <div class="header-right">
            <nav class="nav">
                <a href="homepage.php">Home</a>
                <a href="services.php">Services</a>
                <a href="aboutus.php">About us</a>
            </nav>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="notification-icon">
                    <img src="https://cdn-icons-png.flaticon.com/512/1827/1827392.png" alt="Notifications" id="notifBell">
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
                <a href="login.php" class="btn outline">
                    <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="Profile" width="30" height="30">
                </a>
            <?php endif; ?>
        </div>
    </header>

    <main class="album-container">
        
        <div class="album-header">
            <h2>Your Personal Album</h2>
            <p class="subtitle">All your moments from Montra Studio, all in one place.</p>
        </div>

        <?php if (empty($all_images)): ?>
            <div class="no-images">
                <p>Your album is currently empty.</p>
                <p>Once your photos from Montra Studio are ready, they will appear here!</p>
            </div>
        <?php else: ?>
            <div class="gallery-filters">
                <button class="filter-btn active" data-filter="all">All</button>
                <?php
                // Create buttons only for categories that have images
                $found_types = array_unique(array_column($all_images, 'album_type'));
                foreach ($albums_to_display as $type => $label):
                    if (in_array($type, $found_types)):
                ?>
                    <button class="filter-btn" data-filter="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($label) ?></button>
                <?php
                    endif;
                endforeach;
                ?>
            </div>

            <div class="gallery-masonry">
                <?php foreach ($all_images as $img): ?>
                    <div class="image-card" data-category="<?= htmlspecialchars($img['album_type']) ?>">
                        <img src="/admin_montra/<?= htmlspecialchars($img['image_path']) ?>" 
                             alt="<?= htmlspecialchars($albums_to_display[$img['album_type']] ?? 'Album') ?> Image">
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>
    
    <div class="lightbox-overlay" id="lightboxOverlay">
        <div class="lightbox-content">
            <img src="" alt="Enlarged view" id="lightboxImage">
            <a href="#" class="lightbox-close" id="lightboxClose">&times;</a>
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
                        <li><a href="homepage.php">Home</a></li>
                        <li><a href="services.php">Services</a></li>
                        <li><a href="user_album.php">My Album</a></li>
                        <li><a href="aboutus.php">About Us</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Support</h4>
                    <ul>
                        <li><a href="#">FAQs</a></li>
                        <li><a href="user_chat.php">Contact Us</a></li>
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
                            p.style.cursor = 'pointer';
                            p.onclick = () => {
                                if (item.message.includes('approved')) {
                                    window.location.href = 'approved_bookings.php';
                                } else {
                                    window.location.href = 'pending_bookings.php';
                                }
                            };
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
        setInterval(fetchNotifications, 10000); 

        bell.addEventListener('click', () => {
            dropdown.classList.toggle('active');
        });
    });
    </script>
    <?php endif; ?>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const lightboxOverlay = document.getElementById('lightboxOverlay');
        const lightboxImage = document.getElementById('lightboxImage');
        const lightboxClose = document.getElementById('lightboxClose');
        
        // This selector now targets all images inside the masonry gallery
        const galleryImages = document.querySelectorAll('.gallery-masonry .image-card img');

        galleryImages.forEach(img => {
            img.addEventListener('click', () => {
                const highResSrc = img.getAttribute('src');
                lightboxImage.setAttribute('src', highResSrc);
                lightboxOverlay.classList.add('active');
            });
        });

        function closeLightbox() {
            lightboxOverlay.classList.remove('active');
            setTimeout(() => {
                lightboxImage.setAttribute('src', ''); 
            }, 300);
        }

        lightboxClose.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation(); 
            closeLightbox();
        });

        lightboxOverlay.addEventListener('click', (e) => {
            if (e.target === lightboxOverlay) { 
                closeLightbox();
            }
        });
        
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && lightboxOverlay.classList.contains('active')) {
                closeLightbox();
            }
        });
    });
    </script>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const filterButtons = document.querySelectorAll('.filter-btn');
        const imageCards = document.querySelectorAll('.gallery-masonry .image-card');

        // Show 'all' images by default
        function showAllImages() {
            imageCards.forEach(card => {
                card.classList.add('show');
            });
        }
        
        // Initially show all images
        showAllImages();

        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                const filter = button.getAttribute('data-filter');
                
                // Update active button
                filterButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                
                // Filter images
                imageCards.forEach(card => {
                    if (filter === 'all') {
                        card.classList.add('show');
                    } else if (card.getAttribute('data-category') === filter) {
                        card.classList.add('show');
                    } else {
                        card.classList.remove('show');
                    }
                });
            });
        });
    });
    </script>

</body>
</html>