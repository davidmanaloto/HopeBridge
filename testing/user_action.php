<?php
session_start();
require 'db_connection.php';

header("Content-Type: application/json");

// Ensure only admins can perform actions
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}

// Validate input
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['user_id'], $_POST['status'])) {
    $userId = $_POST['user_id'];
    $newStatus = ($_POST['status'] === 'Blocked') ? 'Blocked' : 'Active';

    $stmt = $conn->prepare("UPDATE user_table SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $userId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "new_status" => $newStatus]);
    } else {
        echo json_encode(["success" => false, "message" => "Database update failed"]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}

$conn->close();
