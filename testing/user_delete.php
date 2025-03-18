<?php
session_start();
require 'db_connection.php';

// Ensure only admins can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

// Check if request is POST and user_id is provided
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);

    // Check if the user exists
    $stmt = $conn->prepare("SELECT * FROM user_table WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "User not found"]);
        exit();
    }

    // Delete related donation records first
    $stmt = $conn->prepare("DELETE FROM donations WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // Now delete the user
    $stmt = $conn->prepare("DELETE FROM user_table WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "User deleted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to delete user"]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    $conn->close();
}
?>
