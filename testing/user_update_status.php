<?php
session_start();
require 'db_connection.php';

// Ensure only admins can perform this action
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit();
}

// Ensure request is POST and required parameters are provided
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['user_id']) && isset($_POST['action'])) {
    $userId = intval($_POST['user_id']);
    $action = $_POST['action'];

    if ($action === "block") {
        $newStatus = "Blocked";
    } elseif ($action === "active") {
        $newStatus = "Active";
    } else {
        echo json_encode(["success" => false, "message" => "Invalid action"]);
        exit();
    }

    // Update the status in the database
    $stmt = $conn->prepare("UPDATE user_table SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $userId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "new_status" => $newStatus]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update user status"]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Missing required parameters"]);
}
?>
