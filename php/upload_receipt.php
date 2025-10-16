<?php
session_start();
require_once '../api/db_connect.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['receipt']) && isset($_POST['booking_id'])) {
    $booking_id = intval($_POST['booking_id']);
    $user_id = $_SESSION['user_id'];
    
    // Create upload directory if it doesn’t exist
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // File upload setup
    $fileName = basename($_FILES['receipt']['name']);
    $fileTmp = $_FILES['receipt']['tmp_name'];
    $targetPath = $uploadDir . time() . '_' . $fileName;
    $fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));

    // Only allow certain file types
    $allowedTypes = ['jpg', 'jpeg', 'png'];
    if (!in_array($fileType, $allowedTypes)) {
        die("Only JPG, JPEG, and PNG files are allowed.");
    }

    // Move file to upload directory
    if (move_uploaded_file($fileTmp, $targetPath)) {
        // Save file path in database
        $fileNameForDB = basename($targetPath);

        $stmt = $conn->prepare("UPDATE bookings SET receipt_image = ? WHERE id = ? AND user_id = ?");
        if (!$stmt) {
            die("Database error: " . $conn->error);
        }
        $stmt->bind_param("sii", $fileNameForDB, $booking_id, $user_id);
        $stmt->execute();

        $stmt->close();
        $conn->close();

        header("Location: profile.php?upload=success");
        exit();
    } else {
        die("Error uploading file.");
    }
} else {
    die("Invalid request.");
}
