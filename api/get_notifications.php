<?php
require_once 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch latest notifications (including new messages)
$query = "
    SELECT 
        id,
        message,
        is_read,
        sent_at
    FROM full_texts
    WHERE receiver_id = ? AND sender_type = 'admin'
    ORDER BY sent_at DESC
    LIMIT 10
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

echo json_encode($notifications);
