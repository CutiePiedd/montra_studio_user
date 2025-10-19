<?php
session_start();
require_once '../api/db_connect.php';
session_start();
$isLoggedIn = isset($_SESSION['user_id']);

$id = 1; // Main Character package
$result = mysqli_query($conn, "SELECT * FROM packages WHERE id=$id");
$package = mysqli_fetch_assoc($result);

$includes = explode(',', $package['includes']);
$images = explode(',', $package['images']);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Our Services | Mantra Studio</title>
  <link rel="stylesheet" href="..\css\maincharacter.css" />
    <link rel="stylesheet" href="..\css\footer.css">
</head>

<body>

  <!-- HEADER -->
 <header class="header">
   <div class="logo"> 
    <img src="..\images\LOGO.png" alt="Montra Studio Logo"style="height: 100px; width: 300px;"> 
  </div>

  <div class="header-right">
    <nav class="nav">
        <a href="../html/homepage.html">Home</a>
      <a href="../html/services.html">Services</a>
      <a href="aboutus.html">About us</a>
    </nav>

    <div class="profile-icon">
      <a href="profile.php">
        <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="Profile">
      </a>
    </div>
  </div>
</header>
<br/><br/><br/><br/><br/><br/>
  <!-- HERO SECTION -->
  <section class="hero-section">
    <h1>Our Services</h1>
    <p>Home / Services / Packages</p>
  </section>

  <!-- MAIN PACKAGE -->
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

    <!-- IMAGE CAROUSEL -->
<div class="carousel">
  <?php foreach ($images as $img): ?>
    <img src="../uploads/<?= htmlspecialchars(trim($img)) ?>" alt="Package Image">
  <?php endforeach; ?>
</div>


    <!-- PACKAGE DETAILS -->
    
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

  <!-- FOOTER -->
  

   
  </footer><footer class="footer">
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

  // Duplicate images to simulate infinite loop
  const images = Array.from(carousel.children);
  images.forEach(img => {
    const clone = img.cloneNode(true);
    carousel.appendChild(clone);
  });

  const scrollAmount = 300;
  let scrollPos = 0;

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
</script>
</body>
</html>
