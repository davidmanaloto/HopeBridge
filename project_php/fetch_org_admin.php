<?php

require 'db_connection.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch donations where organization_id = 1 AND project_id = 1
$sql = "SELECT user_id, organization_id, project_id, amount, date_created 
        FROM donations 
        WHERE organization_id = 1 AND project_id = 1";

$result = $conn->query($sql);

// Check if there are any results
if ($result->num_rows > 0) {
    // Create an array to hold the donations
    $donations = array();

    // Fetch all rows and push into the $donations array
    while ($row = $result->fetch_assoc()) {
        $donations[] = array(
            'username' => $row['user_id'],  // Still using ID, consider joining with user_table
            'organizationname' => $row['organization_id'],  // Consider joining with organization table
            'project_id' => $row['project_id'],
            'amount' => $row['amount'],
            'date_created' => $row['date_created']
        );
    }

    // Send JSON response
    echo json_encode($donations);
} else {
    // No matching donations found
    echo json_encode([]);
}

// Close connection
$conn->close();
?>
