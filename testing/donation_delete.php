<?php
require 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $donation_id = $_POST['donation_id'] ?? null;

    if ($donation_id) {
        $stmt = $conn->prepare("DELETE FROM donations WHERE id = ?");
        $stmt->bind_param("i", $donation_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Donation deleted successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to delete donation."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Invalid donation ID."]);
    }
}
?>
