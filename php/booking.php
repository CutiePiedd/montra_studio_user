<?php
session_start();
require_once '../api/db_connect.php';

// Redirect if user not logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
  exit();
}

// Get package data from query string
$package = $_GET['package'] ?? 'Unknown';
$base_price = $_GET['price'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book Your Session | Montra Studio</title>
  <link rel="stylesheet" href="../css/booking.css">
  <link rel="stylesheet" href="../css/footer.css">
</head>
<body>

<!-- HEADER -->
<header class="header">
  <div class="logo">
    <img src="../images/LOGO.png" alt="Montra Studio Logo" style="height: 100px; width: 300px;">
  </div>

  <div class="header-right">
    <nav class="nav">
      <a href="../html/index.html">Home</a>
      <a href="../html/services.html">Services</a>
      <a href="../html/aboutus.html">About us</a>
    </nav>

    <div class="profile-icon">
      <a href="../php/profile.php">
        <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="Profile">
      </a>
    </div>
  </div>
</header>

<!-- BOOKING FORM -->
<section class="booking-section">
  <h2>Book Your <?= htmlspecialchars(ucfirst($package)) ?> Session</h2>
  <p>Schedule your perfect photobooth experience with us</p>

  <form action="process_booking.php" method="POST" class="booking-form">
    <input type="hidden" name="package" value="<?= htmlspecialchars($package) ?>">
    <input type="hidden" name="base_price" value="<?= htmlspecialchars($base_price) ?>">

    <!-- Date and Time -->
    <div class="form-row">
      <div class="form-group">
        <label>Preferred Date:</label>
        <input type="date" id="dateInput" name="preferred_date" min="<?= date('Y-m-d') ?>" required>
      </div>
      <div class="form-group">
        <label>Preferred Time:</label>
        <select id="timeSelect" name="preferred_time" required>
          <option value="">Choose a date first</option>
        </select>
      </div>
    </div>

    <!-- Contact -->
    <div class="form-row">
      <div class="form-group full-width">
        <label>Contact Person:</label>
        <input type="text" name="contact_person" placeholder="Full name" required>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Email:</label>
        <input type="email" name="email" placeholder="name@example.com" required>
      </div>
      <div class="form-group">
        <label>Phone Number:</label>
        <input type="tel" name="phone" placeholder="09xxxxxxxxx" required>
      </div>
    </div>

    <!-- Special Request -->
    <div class="form-row">
      <div class="form-group full-width">
        <label>Special Request:</label>
        <textarea name="special_request" placeholder="Tell us about any special requests..."></textarea>
      </div>
    </div>

    <!-- Add-ons -->
    <fieldset>
  <legend>Add-On Services</legend>
  <label><input type="checkbox" name="addons[]" value="instant_photo"> Instant Photo (+₱500)</label><br>
  <label><input type="checkbox" name="addons[]" value="custom_frame"> 1 Custom Photo Frame (+₱300)</label><br>
  <label><input type="checkbox" id="addon_extended" name="addons[]" value="extended_time"> Extended Session Time (+₱100)</label><br>
</fieldset>

    <!-- Summary -->
    <div class="booking-summary">
      <div class="summary-row">
        <span>Photo Session</span>
        <span>₱<span id="basePrice"><?= number_format($base_price) ?></span></span>
      </div>
      <div class="summary-row total">
        <strong>Total Amount:</strong>
        <strong>₱<span id="totalPrice"><?= number_format($base_price) ?></span></strong>
      </div>
    </div>

    <!-- Buttons -->
    <div class="form-buttons">
      <button type="reset" class="clear-btn">Clear</button>
      <button type="submit" class="confirm-btn">Confirm Booking</button>
    </div>
  </form>
</section>

<!-- FOOTER -->
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
const dateInput = document.getElementById('dateInput');
const timeSelect = document.getElementById('timeSelect');
const addonExtended = document.getElementById('addon_extended');
const addonCheckboxes = document.querySelectorAll('input[name="addons[]"]');
const basePrice = Number(<?= json_encode($base_price) ?>);
const totalPriceElem = document.getElementById('totalPrice');

let latestSlots = [];

// Fetch available slots dynamically
async function fetchSlots(date) {
  timeSelect.innerHTML = '<option>Loading...</option>';
  const res = await fetch(`get_available_times.php?date=${encodeURIComponent(date)}`);
  const data = await res.json();
  latestSlots = data.slots || [];
  renderSlots();
}

function renderSlots() {
  if (!latestSlots.length) {
    timeSelect.innerHTML = '<option value="">No available slots</option>';
    return;
  }

  const requireNext = addonExtended.checked;
  const availableSet = new Set(latestSlots.map(s => s.value));
  const options = [];

  for (const s of latestSlots) {
    if (!requireNext) {
      options.push(`<option value="${s.value}">${s.label}</option>`);
      continue;
    }

    const [hh, mm] = s.value.split(':').map(Number);
    const dt = new Date();
    dt.setHours(hh, mm + 30, 0, 0);
    const nextKey = `${dt.getHours().toString().padStart(2,'0')}:${dt.getMinutes().toString().padStart(2,'0')}`;

    if (availableSet.has(nextKey)) {
      options.push(`<option value="${s.value}">${s.label} (can extend)</option>`);
    }
  }

  timeSelect.innerHTML = options.length
    ? '<option value="">Select time</option>' + options.join('')
    : '<option value="">No available slots</option>';
}

function updateTotal() {
  let total = basePrice;
  addonCheckboxes.forEach(cb => {
    if (cb.checked) {
      if (cb.value === 'instant_photo') total += 500;
      if (cb.value === 'custom_frame') total += 300;
      if (cb.value === 'extended_time') total += 100;
    }
  });
  totalPriceElem.textContent = total.toLocaleString();
}

dateInput.addEventListener('change', () => {
  if (dateInput.value) fetchSlots(dateInput.value);
});
addonExtended.addEventListener('change', () => { renderSlots(); updateTotal(); });
addonCheckboxes.forEach(cb => cb.addEventListener('change', updateTotal));
updateTotal();
</script>

</body>
</html>
