<?php
session_start();
require_once '../api/db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../php/login.php"); // Corrected path
    exit();
}

$user_id = $_SESSION['user_id'];
// $userName = $_SESSION['user_name']; // User name not directly used, but good to have if needed later

// Fetch user info (FOR TOP BAR)
$queryUser = "SELECT first_name, last_name, email, created_at FROM users WHERE id = ?";
$stmtUser = $conn->prepare($queryUser);
$stmtUser->bind_param("i", $user_id);
$stmtUser->execute();
$user = $stmtUser->get_result()->fetch_assoc();
$stmtUser->close();


// Fetch admin info (assuming single admin)
$admin_query = "SELECT id, name, email FROM admins LIMIT 1";
$admin_result = $conn->query($admin_query);
$admin = $admin_result->fetch_assoc();
$admin_id = $admin['id']; // Needed for chat JS

// Close DB connection here as it's not needed further down in HTML
$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chat with Admin | Montra Studio</title>

  <link rel="stylesheet" href="../css/profile.css"> <link rel="stylesheet" href="../css/footer.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


  <style>
    /* --- Sticky Header & Body --- (Copied/Adapted from previous pages) */
    .header {
        position: fixed !important;
        top: 0;
        left: 0;
        width: 100%;
        background-color: #fff9f9; /* Your header background color */
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 25px;
        box-sizing: border-box;
        min-height: 80px;
    }
    .header-right {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    body {
      font-family: 'Inter', sans-serif;
      margin: 0;
      background-color: #fff9f9;/*grey background */
      padding-top: 120px; /* Adjust based on FINAL header height */
      /* --- ADD THESE LINES --- */
  /* This makes the body fill the viewport */
  display: flex;
  flex-direction: column;
  height: 100vh;
  box-sizing: border-box;
  /* --- End of added lines --- */
    }
    main {
      flex-grow: 1;
   
   /* --- Optional: Make main a flex container for chat-container --- */
   display: flex;
       padding: 0 15px;
       min-height: 0;
    }

    /* --- Chat Container --- (Styled like a dash-card) */
    .chat-container {
      width: 100%; /* Take full width within main */
      height: 100%; /* Adjust height: viewport - (padding-top + main margin-bottom + footer guess) */

      background-color: #fff;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    /* --- Chat Header --- */
    .chat-header {
      background-color: #f7fafc; /* Light header */
      color: #2d3748; /* Dark text */
      padding: 1rem 1.5rem;
      text-align: center;
      font-weight: 600;
      font-size: 1.1rem;
      border-bottom: 1px solid #e2e8f0;
      flex-shrink: 0; /* Prevent shrinking */
    }

    /* --- Chat Messages Area --- */
    .chat-messages {
      flex-grow: 1; /* Take remaining space */
      padding: 1.5rem;
      overflow-y: auto;
      background: #f8f9fa; /* Match body background */
      display: flex;
      flex-direction: column;
      min-height: 0;
    }

    /* --- Message Bubbles --- */
    .message {
      margin-bottom: 10px;
      padding: 10px 15px;
      border-radius: 18px;
      max-width: 75%; /* Limit bubble width */
      line-height: 1.4;
      font-size: 0.95rem;
      word-wrap: break-word; /* Wrap long words */
    }
    /* User's messages (Blue, Right) */
    .message.user {
      background-color: #3182ce; /* Blue */
      color: white;
      margin-left: auto; /* Push to right */
      border-bottom-right-radius: 5px; /* Tail effect */
    }
    /* Admin's messages (Grey, Left) */
    .message.admin {
      background-color: #e2e8f0; /* Light Grey */
      color: #2d3748; /* Dark text */
      margin-right: auto; /* Push to left */
      border-bottom-left-radius: 5px; /* Tail effect */
    }

    /* --- Chat Input Area --- */
    .chat-input {
      display: flex;
      align-items: center;
      border-top: 1px solid #e2e8f0;
      padding: 1rem 1.5rem;
      background: #fff;
      flex-shrink: 0; /* Prevent shrinking */
    }
    .chat-input input {
      flex-grow: 1;
      border: none;
      outline: none;
      padding: 10px 18px; /* Adjust padding */
      border-radius: 20px; /* Fully rounded */
      background-color: #f1f4f8; /* Input background */
      font-size: 0.95rem;
      margin-right: 1rem;
    }
    .chat-input button {
      flex-shrink: 0;
      border: none;
      background-color: #3182ce; /* Blue button */
      color: white;
      border-radius: 50%; /* Circular button */
      cursor: pointer;
      width: 40px; /* Button size */
      height: 40px; /* Button size */
      font-size: 1rem;
      display: flex; /* Center icon */
      align-items: center; /* Center icon */
      justify-content: center; /* Center icon */
      transition: background-color 0.2s ease;
    }
    .chat-input button:hover {
      background-color: #2c5282; /* Darker blue on hover */
    }


    /* --- Copied Header Dropdown/Notification Styles --- */
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
    .notification-icon { position: relative; z-index: 10000; }
    .notification-icon { position: relative; margin-right: 15px; cursor: pointer; display: inline-block; }
    .notification-icon img { width: 30px; height: 30px; vertical-align: middle; }
    .notif-count { position: absolute; top: -6px; right: -6px; background: red; color: white; font-size: 11px; font-weight: bold; border-radius: 50%; padding: 3px 6px; display: none; }
    .notif-dropdown { position: absolute; right: 0; top: 40px; width: 250px; background: white; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15); z-index: 9999; display: none; }
    .notif-dropdown.active { display: block !important; }
    .notif-dropdown p { padding: 10px; border-bottom: 1px solid #eee; font-size: 14px; color: #333; margin: 0; background: white; }
    .notif-dropdown p:hover { background: #f5f5f5; }
    /* --- Basic Footer Style --- */
     .footer {
         background-color: #f1f1f1; /* Example background */
         padding: 20px 0;
         text-align: center;
         margin-top: 40px;
         border-top: 1px solid #e2e8f0;
         color: #555;
     }
  </style>
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
      <img src="https://cdn-icons-png.flaticon.com/512/1827/1827392.png" alt="Notifications" id="notifBell">
      <span id="notifCount" class="notif-count"></span>
      <div id="notifDropdown" class="notif-dropdown">
        <p style="text-align:center; color:#888;">Loading notifications...</p>
      </div>
    </div>
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

<main>
  <div class="chat-container">
    <div class="chat-header">Chat with Montra Admin</div>
    <div class="chat-messages" id="chatMessages">
      </div>
    <div class="chat-input">
      <input type="text" id="messageText" placeholder="Type your message here..." autocomplete="off">
      <button onclick="sendMessage()"><i class="fas fa-paper-plane"></i></button> </div>
  </div>
</main>

<script>
  const userId = <?php echo $user_id; ?>;
  const adminId = <?php echo $admin_id; ?>;
  const chatBox = document.getElementById('chatMessages');

  function loadMessages() {
    fetch(`../api/get_messages.php?user_id=${userId}&admin_id=${adminId}`)
      .then(res => res.json())
      .then(messages => {
        const shouldScroll = chatBox.scrollTop + chatBox.clientHeight >= chatBox.scrollHeight - 20; // Check if near bottom before loading
        chatBox.innerHTML = ''; // Clear existing

        messages.forEach(msg => {
          const div = document.createElement('div');
          // Check sender_type to assign 'user' or 'admin' class
          div.classList.add('message', msg.sender_type === 'user' ? 'user' : 'admin');
          div.textContent = msg.message;
          chatBox.appendChild(div);
        });

        // Scroll to bottom only if user was already near the bottom
        if(shouldScroll) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
      })
      .catch(err => console.error("Error loading messages:", err)); // Add error handling
  }

  function sendMessage() {
    const messageInput = document.getElementById('messageText');
    const message = messageInput.value.trim();
    if (!message) return;

    const formData = new FormData();
    formData.append('sender_id', userId);
    formData.append('receiver_id', adminId);
    formData.append('sender_type', 'user'); // User is sending
    formData.append('message', message);

    // Immediately display the sent message (optimistic update)
    const div = document.createElement('div');
    div.classList.add('message', 'user');
    div.textContent = message;
    chatBox.appendChild(div);
    chatBox.scrollTop = chatBox.scrollHeight; // Scroll down after sending

    messageInput.value = ''; // Clear input

    fetch('../api/send_message.php', { method: 'POST', body: formData })
      .then(res => res.json())
      .then(data => {
          if (!data.success) { // Optional: handle potential send errors
              console.error("Error sending message:", data.error);
              // Maybe add visual feedback that message failed to send
          }
          // No need to call loadMessages() immediately due to optimistic update
      })
      .catch(err => console.error("Error sending message:", err));
  }

  // Send message on Enter key
  document.getElementById('messageText').addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
          e.preventDefault(); // Prevent default form submission/newline
          sendMessage();
      }
  });


  // Initial load and periodic refresh
  loadMessages();
  setInterval(loadMessages, 5000); // Refresh chat every 5 seconds
</script>

<script>
  // Profile dropdown toggle
  const profileToggle = document.getElementById('profileToggle');
  const profileMenu = document.getElementById('profileMenu');
  if (profileToggle && profileMenu) {
    profileToggle.addEventListener('click', (e) => {
        e.stopPropagation(); // Prevent triggering window click listener
        profileMenu.style.display = profileMenu.style.display === 'block' ? 'none' : 'block';
    });
  }
  // Notification Bell toggle
  const bell = document.getElementById('notifBell');
  const notifDropdown = document.getElementById('notifDropdown');
  if (bell && notifDropdown) {
      bell.addEventListener('click', (e) => {
          e.stopPropagation(); // Prevent triggering window click listener
          notifDropdown.classList.toggle('active');
      });
  }

  // Close dropdowns if clicking outside
  window.addEventListener('click', (event) => {
    if (profileMenu && !profileToggle.contains(event.target) && !profileMenu.contains(event.target)) {
      profileMenu.style.display = 'none';
    }
    if (notifDropdown && !bell.contains(event.target) && !notifDropdown.contains(event.target)) {
        notifDropdown.classList.remove('active');
    }
  });

  <?php if (isset($_SESSION['user_id'])): ?>
    // Notifications Fetch Logic (if bell exists)
    if (bell && notifDropdown) {
  const notifCountSpan = document.getElementById('notifCount');

  async function loadNotifications() {
    try {
      const res = await fetch('../api/get_notifications.php');
      const data = await res.json();

      notifDropdown.innerHTML = ''; // Clear old notifications

      if (data.length === 0) {
        notifDropdown.innerHTML = '<p style="text-align:center; color:#888;">No notifications</p>';
        notifCountSpan.style.display = 'none';
      } else {
        data.forEach(notif => {
          const p = document.createElement('p');
          p.textContent = notif.message;
          notifDropdown.appendChild(p);
        });

        notifCountSpan.textContent = data.length;
        notifCountSpan.style.display = 'inline-block';
      }
    } catch (error) {
      console.error('Error fetching notifications:', error);
    }
  }

  loadNotifications();
  setInterval(loadNotifications, 5000);

        function fetchNotifications() {
          fetch('../php/fetch_notifications.php') // Verify this path
            .then(res => {
                if (!res.ok) { throw new Error(`HTTP error! status: ${res.status}`); }
                return res.json();
            })
            .then(data => {
              notifDropdown.innerHTML = ''; // clear old

              if (data.length > 0) {
                notifCountSpan.textContent = data.length;
                notifCountSpan.style.display = 'inline-block';
                data.forEach(item => {
                  const p = document.createElement('p');
                  p.textContent = item.message;
                  // Optional: Add click handler to notification items
                  // p.onclick = () => { /* maybe redirect or mark read */ };
                  notifDropdown.appendChild(p);
                });
              } else {
                notifCountSpan.style.display = 'none';
                notifDropdown.innerHTML = '<p style="color:#777;text-align:center;">No new notifications</p>';
              }
            })
            .catch(err => {
              console.error('Notification fetch error:', err);
              notifDropdown.innerHTML = '<p style="color:red;text-align:center;">Error loading</p>';
            });
        }
        fetchNotifications();
        setInterval(fetchNotifications, 10000); // Refresh every 10 seconds
    }
  <?php endif; ?>
</script>

</body>
</html>