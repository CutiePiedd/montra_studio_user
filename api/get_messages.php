<?php
require_once 'db_connect.php';

$user_id  = $_GET['user_id'];
$admin_id = $_GET['admin_id'];

// Fetch all messages exchanged between user and admin
$query = "
  SELECT sender_id, receiver_id, sender_type, message, sent_at
  FROM messages
  WHERE 
    (sender_id = ? AND receiver_id = ? AND sender_type = 'user')
    OR 
    (sender_id = ? AND receiver_id = ? AND sender_type = 'admin')
  ORDER BY sent_at ASC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("iiii", $user_id, $admin_id, $admin_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
  $messages[] = $row;
}

echo json_encode($messages);
?>
