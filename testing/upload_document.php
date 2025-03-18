/* upload_document.php */
<?php
session_start();
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['document'])) {
    $user_id = $_SESSION['user_id']; // Ensure user is logged in
    $target_dir = "verification_documents/";
    $file_name = basename($_FILES["document"]["name"]);
    $target_file = $target_dir . time() . "_" . $file_name;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Allow only PDF, JPG, and PNG
    $allowed_types = ['pdf', 'jpg', 'png'];
    if (!in_array($file_type, $allowed_types)) {
        die("Invalid file type. Only PDF, JPG, and PNG allowed.");
    }

    if (move_uploaded_file($_FILES["document"]["tmp_name"], $target_file)) {
        // Insert request into DB
        $stmt = $conn->prepare("INSERT INTO verification_requests (user_id, document_path, status) VALUES (?, ?, 'Pending')");
        $stmt->bind_param("is", $user_id, $target_file);
        $stmt->execute();
        echo "Document uploaded successfully!";
    } else {
        echo "Error uploading file.";
    }
}
?>
