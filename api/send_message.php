<?php
require_once 'db_connect.php';

$sender_id   = $_POST['sender_id'] ?? null;
$receiver_id = $_POST['receiver_id'] ?? null;
$sender_type = $_POST['sender_type'] ?? '';
$message     = trim($_POST['message'] ?? '');

if ($sender_id && $receiver_id && !empty($message)) {
    $stmt = $conn->prepare("
        INSERT INTO messages (sender_id, receiver_id, sender_type, message, sent_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("iiss", $sender_id, $receiver_id, $sender_type, $message);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'error' => $stmt->error]);
    }
} else {
    echo json_encode(['status' => 'error', 'error' => 'Missing data']);
}
?>
