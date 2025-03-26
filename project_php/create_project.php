<?php
include 'db_connection.php';
session_start();  // Start the session to get logged-in user

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $project_name = $_POST['project_name'];
    $project_summary = $_POST['project_summary'];
    $donation_goal = $_POST['donation_goal'];
    $image_url = $_POST['image_url'];
    $organization_id = $_POST['organization_id']; 

    $stmt = $conn->prepare("INSERT INTO projects (user_id, project_name, project_summary, donation_goal, image_url, organization_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssi", $user_id, $project_name, $project_summary, $donation_goal, $image_url, $organization_id); 

    if ($stmt->execute()) {
        echo $conn->insert_id;  
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
