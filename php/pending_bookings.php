<?php
session_start();
require_once '../api/db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../php/login.php");
    exit();
}

// Fetch user info

$user_id = $_SESSION['user_id'];
$query = "SELECT first_name, last_name, email, created_at FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch only pending bookings
$sql = "SELECT id, package_name, preferred_date, preferred_time, special_request, total_price, addons, status, receipt_image, created_at
        FROM bookings
        WHERE user_id = ? AND status = 'pending'
        ORDER BY created_at DESC";
$stmt2 = $conn->prepare($sql);
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
$bookings = $result2->fetch_all(MYSQLI_ASSOC);
$stmt2->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pending Bookings | Montra Studio</title>
  <link rel="stylesheet" href="../css/profile.css">
  <link rel="stylesheet" href="../css/footer.css">
   <link rel="stylesheet" href="../css/pending.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">


</head>

<body>



<!-- ✅ Keep the top bar intact -->

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

    <a href="user_chat.php" class="dropdown-item">Contact Admin</a>

    <a href="rejected_bookings.php" class="dropdown-item">Rejected Bookings</a>
    <a href="user_album.php" class="dropdown-item">My Album</a>


    <hr>

    <a href="logout.php" class="dropdown-item logout">Logout</a>

  </div>

</div>

<?php else: ?>

<a href="../php/login.php" class="btn outline">Login</a>

<?php endif; ?>

  </div>

</header>



<!-- 🧾 Pending Bookings -->

<main>
   <h2>Your Pending Bookings</h2>

   <?php if (empty($bookings)): ?>
     <div class="alert alert-info text-center shadow-sm">You have no pending bookings at the moment.</div>
   <?php else: ?>
     <div class="row">
       <?php foreach ($bookings as $b): ?>
         <div class="col-lg-6 mb-4"> <div class="booking-card">
             <div class="booking-card-header">
               <h5><?= htmlspecialchars($b['package_name']) ?></h5>
               <span class="badge bg-warning">Pending</span>
             </div>
             <div class="booking-card-body">
               <div class="booking-details">
                 <dl>
                   <dt>Booked On:</dt>
                   <dd><?= htmlspecialchars((new DateTime($b['created_at']))->format('F d, Y')) ?></dd>
                   
                   <dt>Date:</dt>
                   <dd><?= htmlspecialchars($b['preferred_date']) ?></dd>
                   
                   <dt>Time:</dt>
                   <dd><?= htmlspecialchars($b['preferred_time']) ?></dd>
                   
                   <dt>Add-ons:</dt>
                   <dd><?= nl2br(htmlspecialchars($b['addons'] ?: 'None')) ?></dd>

                   <dt>Request:</dt>
                   <dd><?= nl2br(htmlspecialchars($b['special_request'] ?: 'None')) ?></dd>
                   
                   <div class="total-price"> <dt>Total Price:</dt>
                     <dd>₱<?= number_format($b['total_price'], 2) ?></dd>
                   </div>
                 </dl>
               </div>

               <div class="upload-section">
                 <strong>Please pay ₱100 downpayment to confirm.</strong>
                 <p class="gcach-info">GCash: 09671087944 (Montra S.)</p>

                 <?php if (!empty($b['receipt_image'])): ?>
                   <div class="receipt-uploaded">
                     <p>✅ Receipt Uploaded</p>
                 <center>    <img src="../uploads/<?= htmlspecialchars($b['receipt_image']) ?>" 
                          alt="Receipt Thumbnail"
                          class="receipt-thumbnail"
                          onclick="openReceiptModal('../uploads/<?= htmlspecialchars($b['receipt_image']) ?>')"></center>
                     <small class="d-block mt-2 text-muted">Click image to view full size.</small>
                   </div>
                 <?php else: ?>
                   <form class="upload-form" action="upload_receipt.php" method="POST" enctype="multipart/form-data">
                     <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                     <input class="form-control form-control-sm mb-2" type="file" name="receipt" accept="image/*" required>
                     <button type="submit">Upload Receipt</button>
                   </form>
                 <?php endif; ?>
               </div> 
             </div> </div> </div> <?php endforeach; ?>
     </div> <?php endif; ?>
 </main>



<!-- 📸 Modal for full-size receipt -->

<div id="receiptModal" style="

    display:none;

    position:fixed;

    top:0; left:0;

    width:100%; height:100%;

    background:rgba(0,0,0,0.8);

    justify-content:center;

    align-items:center;

    z-index:1000;">

  <img id="receiptPreview" src="" alt="Full Receipt" style="max-width:90%; max-height:90%; border-radius:12px;">

</div>



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



  // Modal for receipts

  const modal = document.getElementById('receiptModal');

  const preview = document.getElementById('receiptPreview');



  function openReceiptModal(src) {

    preview.src = src;

    modal.style.display = 'flex';

  }



  modal.addEventListener('click', () => {

    modal.style.display = 'none';

    preview.src = '';

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