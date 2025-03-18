<?php
session_start();
require 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["verification_doc"])) {
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session
    $target_dir = "uploads/docs/";
    $file_name = basename($_FILES["verification_doc"]["name"]);
    $target_file = $target_dir . time() . "_" . $file_name;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Allow only PDF, JPG, and PNG
    if (!in_array($file_type, ['pdf', 'jpg', 'png'])) {
        die("Invalid file type. Only PDF, JPG, and PNG allowed.");
    }

    // Move uploaded file
    if (move_uploaded_file($_FILES["verification_doc"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO verification_requests (user_id, status, document_path, created_at) VALUES (?, 'pending', ?, NOW())");
        $stmt->bind_param("is", $user_id, $target_file);
        $stmt->execute();
        $stmt->close();

        echo "Verification request submitted successfully.";
    } else {
        echo "File upload failed.";
    }
}
?>

<form action="request_verification.php" method="POST" enctype="multipart/form-data">
    <input type="file" name="verification_doc" required>
    <button type="submit">Submit Verification Request</button>
</form>
