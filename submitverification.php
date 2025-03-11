<?php
session_start();
require 'db_connection.php'; // Include your DB connection

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["message" => "You must be logged in."]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if user already has a pending or approved request
$checkQuery = "SELECT * FROM verification_requests WHERE user_id = ? AND status IN ('pending', 'approved')";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["message" => "You already have a pending or approved request."]);
    exit;
}

// Insert new request
$insertQuery = "INSERT INTO verification_requests (user_id) VALUES (?)";
$stmt = $conn->prepare($insertQuery);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    echo json_encode(["message" => "Verification request submitted."]);
} else {
    echo json_encode(["message" => "Error submitting request."]);
}

$stmt->close();
$conn->close();
?>
