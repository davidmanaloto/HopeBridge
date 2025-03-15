<?php
require 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'];
    $amount = $_POST['amount'];
    $status = $_POST['status'];

    if (!is_numeric($amount) || $amount <= 0) {
        echo json_encode(["success" => false, "message" => "Invalid donation amount."]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE donations SET amount = ?, status = ? WHERE id = ?");
    $stmt->bind_param("dsi", $amount, $status, $id);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Donation updated successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update donation."]);
    }

    $stmt->close();
}
?>
