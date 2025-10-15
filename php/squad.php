<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
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
      <a href="services.html">Services</a>
      <a href="aboutus.html">About us</a>
    </nav>

    <div class="profile-icon">
      <a href="profile.php">
        <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="Profile">
      </a>
    </div>
  </div>
</header>

  <!-- HERO SECTION -->
  <section class="hero-section">
    <h1>Our Services</h1>
    <p>Home / Services / Packages</p>
  </section>

  <!-- MAIN PACKAGE -->
  <section class="service-package">
    <div class="package-card">
      <div class="package-img">
        <img src="..\images\troo.png" alt="Main Character Package">
      </div>

      <div class="package-content">
        <h2>Main Character</h2>
        <p>
          The Main Character Package is all about you. Enjoy a personalized studio session
          designed to highlight your personality and style, with professional lighting,
          wardrobe guidance, and expert direction to capture your best angles.
          Perfect for portraits, creative shoots, or simply celebrating yourself with stunning images.
        </p>
        <br/> <br/><br/><br/><br/><br/> <br/>

         <div class="price-box">
  <h3>₱1000.00</h3>
  <?php if ($isLoggedIn): ?>
    <a class="service-card" href="../php/booking.php?package=squadgoals&price=3500"><button class="btn-book">Book Now</button></a>
  <?php else: ?>
    <a href="login.php"><button class="btn-book">Log in to Book</button></a>
  <?php endif; ?>
</div>

      </div>
    </div>

    <!-- IMAGE CAROUSEL -->
    <div class="carousel-container">
        <button class="carousel-btn prev">&#10094;</button>
         <div class="carousel">
      <img src="..\images\tropa1.jpg" alt="Look 1">
      <img src="..\images\tropa2.jpg" alt="Look 2">
      <img src="..\images\tropa3.jpeg" alt="Look 3">
      <img src="..\images\tropa4.jpeg" alt="Look 4">
      <img src="..\images\tropa5.jpeg" alt="Look 5">
      <img src="..\images\tropa6.jpeg" alt="Look 6">
      <img src="..\images\tropa7.jpeg" alt="Look 7">
      <img src="..\images\tropa8.jpeg" alt="Look 8">
      <img src="..\images\tropa9.jpeg" alt="Look 9">
      <img src="..\images\tropa10.jpeg" alt="Look 10">
         </div>
      <button class="carousel-btn next">&#10095;</button>
    </div>

   
  </section>
  <br/><br/><br/><br/>
   <!-- PACKAGE DETAILS -->
    <div class="package-details">
      <h3>Main Character Package Includes:</h3>
      <ul>
        <li>1-hour studio session</li>
        <li>Choice of up to 2 backdrop colors (white, black, gray, or beige)</li>
        <li>Unlimited shots during the session</li>
        <li>8 professionally edited digital photos</li>
        <li>Access to all raw files (optional add-on)</li>
        <li>Online gallery for viewing and downloading</li>
      </ul>
    </div>

  <!-- FOOTER -->
  

   
  </footer><footer class="footer">
  <h2>Get In Touch</h2>
  <p class="footer-tagline">Capturing moments. Creating stories. Celebrating you.</p>
  <div class="footer-grid">
    <div class="footer-logo">
      <img src="D:\xampp\htdocs\montra_website\images\1 4.png" alt="Mantra Studio Logo">
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

    next.addEventListener('click', () => {
      carousel.scrollBy({ left: 300, behavior: 'smooth' });
    });

    prev.addEventListener('click', () => {
      carousel.scrollBy({ left: -300, behavior: 'smooth' });
    });
  </script>
</body>
</html>
