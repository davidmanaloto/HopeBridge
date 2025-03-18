<?php
require 'db_connection.php';

echo json_encode($users);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = $_POST['id'];
    $status = $_POST['status'];

    // Validate input
    if (!in_array($status, ['Verified', 'Rejected'])) {
        echo json_encode(["success" => false, "error" => "Invalid status"]);
        exit;
    }

    // Update verification status
    $stmt = $conn->prepare("UPDATE user_table SET verification_status = ?, is_verified = ? WHERE id = ?");
    $isVerified = ($status === "Verified") ? 1 : 0;
    $stmt->bind_param("sii", $status, $isVerified, $userId);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $conn->error]);
    }

    $stmt->close();
}
?>
