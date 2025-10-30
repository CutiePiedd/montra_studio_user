<?php
require_once 'db_connect.php';

$sender_id   = $_POST['sender_id'] ?? null;
$receiver_id = $_POST['receiver_id'] ?? null;
$sender_type = $_POST['sender_type'] ?? '';
$message     = trim($_POST['message'] ?? '');

if ($sender_id && $receiver_id && !empty($message)) {
    // Insert message
    $stmt = $conn->prepare("
        INSERT INTO messages (sender_id, receiver_id, sender_type, message, sent_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("iiss", $sender_id, $receiver_id, $sender_type, $message);

    if ($stmt->execute()) {
        // If the sender is the admin, notify the user
        if ($sender_type === 'admin') {
            $notif_message = "New message from Montra Admin: " . $message;
            $stmtNotif = $conn->prepare("
                INSERT INTO notifications (user_id, message, created_at, is_read)
                VALUES (?, ?, NOW(), 0)
            ");
            $stmtNotif->bind_param("is", $receiver_id, $notif_message);
            $stmtNotif->execute();
            $stmtNotif->close();
        }

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Missing data']);
}
?>
