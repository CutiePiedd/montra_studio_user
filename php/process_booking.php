<?php
session_start();
require_once '../api/db_connect.php';

// redirect if user not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// get form data
$package = $_POST['package'];
$base_price = floatval($_POST['base_price']);
$preferred_date = $_POST['preferred_date'];
$preferred_time = $_POST['preferred_time'];
$contact_person = $_POST['contact_person'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$special_request = $_POST['special_request'];

// handle add-ons
$addons = $_POST['addons'] ?? [];
$total_price = $base_price;

foreach ($addons as $addon) {
    if ($addon == 'instant_photo') $total_price += 500;
    if ($addon == 'custom_frame') $total_price += 300;
    if ($addon == 'extended_time') $total_price += 100;
}
$addons_str = implode(", ", $addons);

// insert into bookings table with pending status
$status = "pending";

$stmt = $conn->prepare("INSERT INTO bookings (user_id, package_name, preferred_date, preferred_time, contact_person, email, phone, special_request, addons, total_price, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssssssdss", $user_id, $package, $preferred_date, $preferred_time, $contact_person, $email, $phone, $special_request, $addons_str, $total_price, $status);

if ($stmt->execute()) {
    // redirect to profile page after successful booking
    header("Location: profile.php?success=1");
    exit();
} else {
    echo "Error saving booking: " . $conn->error;
}
$stmt->close();
$conn->close();
?>
