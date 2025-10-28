<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Get admin ID (assuming only one)
$admin_query = "SELECT id FROM admins LIMIT 1";
$admin_result = $conn->query($admin_query);
$admin = $admin_result->fetch_assoc();
$admin_id = $admin ? (int)$admin['id'] : 0;

if ($admin_id === 0) {
     echo json_encode(['success' => false, 'error' => 'Admin not found']);
    exit();
}

// Mark all unread messages *from the admin* to *this user* as read
$sql = "UPDATE messages 
        SET is_read = 1 
        WHERE receiver_id = ? 
          AND sender_id = ? 
          AND sender_type = 'admin' 
          AND is_read = 0";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ii", $user_id, $admin_id);
    $stmt->execute();
    echo json_encode(['success' => true, 'marked_read' => $stmt->affected_rows]);
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Database prepare failed']);
}

$conn->close();
?>