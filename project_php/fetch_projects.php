<?php

require 'db_connection.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch all projects
$sql = "SELECT project_name, project_summary, image_url, donation_goal FROM projects";
$result = $conn->query($sql);

// Check if there are any results
if ($result->num_rows > 0) {
    // Create an array to hold all the projects
    $projects = array();

    // Fetch all rows and push into the $projects array
    while($row = $result->fetch_assoc()) {
        $projects[] = array(
            'projectName' => $row['project_name'],
            'projectSummary' => $row['project_summary'],
            'imageUrl' => $row['image_url'],
            'donationGoal' => $row['donation_goal']
        );
    }

    // Send JSON response
    echo json_encode($projects);
} else {
    // No projects found
    echo json_encode([]);
}

// Close connection
$conn->close();
?>
