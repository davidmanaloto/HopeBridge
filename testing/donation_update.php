<?php
require 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $donation_id = $_POST['donation_id'] ?? null;
    $amount = $_POST['amount'] ?? null;
    $status = $_POST['status'] ?? null;

    if ($donation_id && $amount && $status) {
        $stmt = $conn->prepare("UPDATE donations SET amount = ?, status = ? WHERE id = ?");
        $stmt->bind_param("dsi", $amount, $status, $donation_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Donation updated successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to update donation."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Invalid data provided."]);
    }
}
?>
