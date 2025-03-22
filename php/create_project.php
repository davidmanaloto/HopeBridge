<?php
require 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $project_name = $_POST['project_name'];
    $project_summary = $_POST['project_summary'];
    $donation_goal = $_POST['donation_goal'];
    $image_url = isset($_POST['image_url']) ? $_POST['image_url'] : null;

    $stmt = $conn->prepare("INSERT INTO projects (user_id, project_name, project_summary, donation_goal, image_url) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $project_name, $project_summary, $donation_goal, $image_url);

    if ($stmt->execute()) {
        echo $conn->insert_id;  // Return the newly created project ID
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
